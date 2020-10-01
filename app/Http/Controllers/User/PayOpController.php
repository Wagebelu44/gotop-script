<?php

namespace App\Http\Controllers\User;

use App\User;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use App\Models\SettingBonuse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PayOpController extends Controller
{
    /*
     * Create a PayOp invoice.
     *
     * @param Request $request
     * @return redirect response
     */
    public function store(Request $request)
    {
        try {
            $settings =  PaymentMethod::where('panel_id', auth()->user()->panel_id)->where('global_payment_method_id', 2)->first();
            if ($settings) 
            {
                $details = json_decode($settings->details,  true);
                $pk = '';
                $sk = '';
                foreach ($details as $detail) {
                    if ($detail['key'] == 'PAYOP_SECRET_KEY') {
                        $pk = $detail['value'];
                    }
                    if ($detail['key'] == 'PAYOP_PUBLIC_KEY') {
                        $sk = $detail['value'];
                    }
                }

                if ($pk ==null || $pk=='') {
                    return redirect()->back()->with('error' , 'public key not found');
                }
                if ($sk ==null || $sk=='' ) {
                    return redirect()->back()->with('error' , 'Secret key not found');
                }

                $public_key  = $pk;
                $secret_key  = $sk;

                $min_amount = $settings->minimum;
                $validator = Validator::make($request->all(), [
                    'amount' => 'required|numeric|min:' . $min_amount,
                ]);

                if ($validator->fails()) {
                    return redirect()
                        ->back()
                        ->withErrors($validator)
                        ->withInput();
                }


                $paymentLogSecret = bcrypt(Auth::user()->email . 'PayOp' . time() . rand(1, 90000));
                //dd($paymentLogSecret);

                // Create payment logs
               // $log = PaymentLog::create([]);

                $log = Transaction::create([
                    'transaction_type' => 'deposit',
                    'transaction_detail' => json_encode(['payment_secrete'=>  $paymentLogSecret, 'currency_code'=> 'USD']),
                    'amount' => $request->input('amount'),
                    'transaction_flag' => 'payment_gateway',
                    'user_id' =>  Auth::user()->id,
                    'admin_id' => null,
                    'status' => 'hold',
                    'tnx_id' => rand(0, round(microtime(true))),
                    'memo' => null,
                    'fraud_risk' => null,
                    'payment_gateway_response' => null,
                    'reseller_payment_methods_setting_id' => 2,
                    'reseller_id' => 1,
                    ]);
                    
                $order = ['id' => $log->id, 'amount' => $request->input('amount'), 'currency' => 'USD'];
                ksort($order, SORT_STRING);
                $dataSet = array_values($order);
                $dataSet[] = $secret_key;//'cdd39154b3c095345c3b57ef'; //env('PAYOP_SECRET_KEY');
                $signature = hash('sha256', implode(':', $dataSet));

                $data = json_encode(array(
                    'publicKey' => $public_key,//'application-4eabf98b-3da3-424b-a695-c3a9f2623c2a',//env('PAYOP_PUBLIC_KEY'),
                    'order' => array(
                        'id' => $log->id,
                        'amount' => $request->input('amount'),
                        'currency' => 'USD',
                        'items' => [
                            array(
                                'id' => 1111,
                                'name' => 'Add Fund',
                                'price' => $request->input('amount'),
                            ),
                        ],
                        'description' => 'Balance Recharge'
                    ),
                    'signature' => $signature,
                    'payer' => array(
                        'email' => Auth::user()->email,
                        "phone" => "",
                        "name" => "",
                        "extraFields" => array()
                    ),
                    'paymentMethod' => 381,
                    'language' => 'en',
                    "resultUrl" => url('thank-you'),
                    "failPath" => url('add-funds')
                ));
               // dd($data);
               $ch = curl_init();

               curl_setopt($ch, CURLOPT_URL, 'https://payop.com/v1/invoices/create');
               curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
               curl_setopt($ch, CURLOPT_VERBOSE, 1);
               curl_setopt($ch, CURLOPT_HEADER, 1);
               curl_setopt($ch, CURLOPT_POST, 1);
               curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

               $headers = array();
               $headers[] = 'Content-Type: application/json';
               curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

               $result = curl_exec($ch);
               if (curl_errno($ch)) {
                   echo 'Error:' . curl_error($ch);
               }
               curl_close($ch);

               list($header, $body) = explode("\r\n\r\n", $result, 2);
               $body = json_decode($body, 1);
               $header = explode("\r\n", $header);

               $identifierHeader = preg_grep('/^identifier/', $header);
               $identifierHeader = array_values($identifierHeader);
               if ($header[0] == 'HTTP/1.1 200 OK') 
               {
                   $identifierData = explode(': ', $identifierHeader[0]);
                   return redirect()->away('https://payop.com/en/payment/invoice-preprocessing/' . $identifierData[1]);
               } else {
                   return redirect()->back()->with('error' ,'Whoops! Something went wrong! Please try again or contact support' . '<br>Technical info: ' . $body['message']);
               }

            }
            else 
            {
                return redirect()->back()->with('error', 'No setting found, contact your reseller');
            }

        }
        catch (\Exception $e) {
            dd($e->getMessage());
        }

    }

    /*
     * Show success page.
     *
     * @param Request $request
     * @return view
     */
    public function success(Request $request)
    {
        if ($request->query('skey') && $request->query('tranID')) {
            Session::flash('success', 'Payment is succesfully added');
            return redirect('/add-funds');
            //return redirect('/thank-you')->with(['alert' => __('messages.payment_success'), 'alertClass' => 'success']);
        } else {
            abort(404);
        }
    }

    /*
     * Show fail page.
     *
     * @param Request $request
     * @return view
     */
    public function fail(Request $request)
    {
        if ($request->query('skey') && $request->query('tranID')) {
            Session::flash('error', 'Payment is cancelled');
            return redirect('/add-funds');
            //return redirect('/addfunds')->with(['alert' => __('messages.payment_failed'), 'alertClass' => 'danger no-auto-close']);
        } else {
            abort(404);
        }
    }

    /*
     * PayOp IPN.
     *
     * @param Request $request
     * @return void
     */
    public function logWrite($data)
    {
        if (Storage::disk('local')->exists('paymentLog.txt')) {
            Storage::disk('local')->append('paymentLog.txt', json_encode($data));
        } else {
            Storage::disk('local')->put('paymentLog.txt', json_encode($data));
        }
    }
    public function payopcallback(Request $request)
    {
        $data = $request->all();
        if (!isset($data['transaction'])) {
            abort(404);
        }
        //Log::info($request);
        $data['test'] = 'request 1';
        $this->logWrite($data);

        $paymentLog = Transaction::find($data['transaction']['order']['id']); // PaymentLog::find($data['transaction']['order']['id']);
        $user = User::find($paymentLog->user_id);
        $settings =  PaymentMethod::where('panel_id', auth()->user()->panel_id)->where('global_payment_method_id', 2)->first();
        $jwt_token = '';
        if ($settings) 
        {
            $details = json_decode($settings->details,  true);
            foreach ($details as $detail) {
                if ($detail['key'] == 'PAYOP_JWT_TOKEN') {
                    $jwt_token = $detail['value'];
                }
            }
        }
        if ($jwt_token == '') {
            abort(404);
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://payop.com/v1/transactions/'.$data['invoice']['txid']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: Bearer '.$jwt_token;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);
        //Log::info($result);
        $arr= [];
        $arr['test_res'] = 'response2'; 
        $arr['data'] = $data;
        $this->logWrite($arr);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
            $this->logWrite(['error' => 'Error:' . curl_error($ch)]);
        }
        curl_close($ch);
        $result = json_decode($result, 1);
        if ($result['data']['state'] == 2 && $result['data']['error'] == '') {
            $paymentLog->transaction_detail = json_encode(['txn_id' => $data['transaction']['id']]);
            $paymentLog->tnx_id = $data['transaction']['id'];
            $paymentLog->status = 'done';
            $sss = $paymentLog->save();
            if ($sss)
            {
                \DB::statement('UPDATE transactions t
                        CROSS JOIN (SELECT MAX(sequence_number) + 1 as new_sequence_number
                        FROM transactions) s
                        SET t.sequence_number = s.new_sequence_number
                        WHERE t.id='.$paymentLog->id);
                         $bonus = SettingBonuse::where('global_payment_method_id', 2)->get()->last();
                         if ($bonus !=null) 
                            {

                                if ( floatval($paymentLog->amount)  >= floatval($bonus->deposit_from)) 
                                {
                                    $bonus = ($bonus->bonus_amount / 100) * floatval($paymentLog->amount);
                                    $tran =  Transaction::create([
                                        'transaction_type' => 'deposit',
                                        'transaction_detail' => json_encode([
                                            'payment_secrete'=> $data['transaction']['id'],
                                            'currency_code'=> 'USD',
                                            'actual_amount'=> floatval($paymentLog->amount),
                                            'actual_payment_id'=>2]),
                                        'amount' =>  $bonus,
                                        'transaction_flag' => 'bonus_deposit',
                                        'user_id' =>  $user->id,
                                        'admin_id' => null,
                                        'status' => 'done',
                                        'tnx_id' =>  $data['transaction']['id'],
                                        'memo' => null,
                                        'fraud_risk' => null,
                                        'payment_gateway_response' => null,
                                        'reseller_payment_methods_setting_id' =>  2,
                                        'reseller_id' => 1,
                                        ]);
                                        if ($tran) {
                                            \DB::statement('UPDATE transactions t
                                            CROSS JOIN (SELECT MAX(sequence_number) + 1 as new_sequence_number
                                            FROM transactions) s
                                            SET t.sequence_number = s.new_sequence_number
                                            WHERE t.id='.$tran->id);
                                        }
                                }
                            }
                $user->balance = $user->balance + $paymentLog->amount;
                $user->save();
            }
            /* they are needed, commenting for temporal */

            /* $notification = notification('Payment received', 2);
            if ($notification && $notification->status) {
                Mail::to(staffEmails('payment_received'))->send(new PaymentReceived($paymentLog, $notification));
            } */
            //transaction($transaction, $user);
        }
    }
}
