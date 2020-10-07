<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\PaymentController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PayOpController extends Controller
{
    private $globalMethodId = 2;

    public function store(Request $request)
    {
        try {
            $settings = PaymentMethod::where('panel_id', auth()->user()->panel_id)->where('global_payment_method_id', $this->globalMethodId)->first();
            if (empty($settings)) {
                return redirect()->back()->withError('No setting found, contact with your provider.');
            }

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

            $validator = Validator::make($request->all(), [
                'amount' => 'required|numeric|min:' . $settings->minimum,
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }


            $secret = bcrypt(Auth::user()->email . 'PayOp' . time() . rand(1, 90000));
            $transaction = (new PaymentController())->transactionStore($request->amount, $this->globalMethodId, $secret);
            if ($transaction) {                 
                $order = ['id' => $transaction->id, 'amount' => $request->input('amount'), 'currency' => 'USD'];
                ksort($order, SORT_STRING);
                $dataSet = array_values($order);
                $dataSet[] = $secret_key;
                $signature = hash('sha256', implode(':', $dataSet));

                $data = json_encode(array(
                    'publicKey' => $public_key,
                    'order' => array(
                        'id' => $transaction->id,
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
                        "name" => Auth::user()->name,
                        "extraFields" => array()
                    ),
                    'paymentMethod' => 381,
                    'language' => 'en',
                    "resultUrl" => route('payment.payop.success'),
                    "failPath" => route('payment.payop.cancel')
                ));

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
                if ($header[0] == 'HTTP/1.1 200 OK') {
                    $identifierData = explode(': ', $identifierHeader[0]);
                    return redirect()->away('https://payop.com/en/payment/invoice-preprocessing/' . $identifierData[1]);
                } else {
                    return redirect()->back()->with('error' ,'Whoops! Something went wrong! Please try again or contact support' . '<br>Technical info: ' . $body['message']);
                }
            } else {
                return redirect()->back()->withError('Something is wrong, please contact with your provider.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->withError($e->getMessage());
        }
    }
    
    public function success(Request $request)
    {
        if ($request->query('skey') && $request->query('tranID')) {
            Session::flash('success', 'Payment is succesfully added');
            return redirect('/add-funds');
        } else {
            abort(404);
        }
    }
    
    public function cancel(Request $request)
    {
        if ($request->query('skey') && $request->query('tranID')) {
            Session::flash('error', 'Payment is cancelled');
            return redirect('/add-funds');
        } else {
            abort(404);
        }
    }

    public function logWrite($data)
    {
        if (Storage::disk('local')->exists('paymentLog.txt')) {
            Storage::disk('local')->append('paymentLog.txt', json_encode($data));
        } else {
            Storage::disk('local')->put('paymentLog.txt', json_encode($data));
        }
    }

    public function ipn(Request $request)
    {
        $data = $request->all();
        if (!isset($data['transaction'])) {
            return false;
        }

        $settings =  PaymentMethod::where('panel_id', auth()->user()->panel_id)->where('global_payment_method_id', $this->globalMethodId)->first();
        $jwt_token = '';
        if ($settings) {
            $details = json_decode($settings->details,  true);
            foreach ($details as $detail) {
                if ($detail['key'] == 'PAYOP_JWT_TOKEN') {
                    $jwt_token = $detail['value'];
                }
            }
        }

        if ($jwt_token == '') {
            return false;
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
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
            $this->logWrite(['error' => 'Error:' . curl_error($ch)]);
        }
        curl_close($ch);
        $result = json_decode($result, 1);

        if ($result['data']['state'] == 2 && $result['data']['error'] == '') {
            $PaidData = [
                'detail' => json_encode(['txn_id' => $data['transaction']['id']]),
                'status' => 'done',
            ];
            $transaction = (new PaymentController())->transactionPay($this->globalMethodId, $data['transaction']['id'], $PaidData);
        }
    }
}
