<?php

namespace App\Http\Controllers\Payment;

use App\User;
use GuzzleHttp\Client;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use App\Mail\ManualOrderPlaced;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class PayPalController extends Controller
{
    private $payment_method_id = 1;
    private $paypal_email;
    private $paypal_mode = '';
    const PAYPAL_URL = "https://www.paypal.com/cgi-bin/webscr?";
    const PAYPAL_SANDBOX_URL = "https://www.sandbox.paypal.com/cgi-bin/webscr?test_ipn=1&";
    const PAYPAL_IPN_URL = 'https://ipnpb.paypal.com/cgi-bin/webscr';
    const PAYPAL_SANDBOX_IPN_URL = 'https://ipnpb.sandbox.paypal.com/cgi-bin/webscr';

    public function showForm(Request $request)
    {
       //PaymentMethod::where(['config_key' => 'paypal_mode'])->first()->config_value;
        // check if payment method is not enabled then abort
        /* $paymentMethod = PaymentMethod::where(['id' => $this->payment_method_id, 'status' => 'ACTIVE'])->first();
        if (is_null($paymentMethod)) {
            abort(403);
        } */

        // User have assigned payment methods?
        /* if (empty(Auth::user()->enabled_payment_methods)) {
            abort(403);
        } */
        // Get users enabled payment methods & see if this method is enabled for him.
        /* $enabled_payment_methods = explode(',', Auth::user()->enabled_payment_methods);
        if (!in_array($this->payment_method_id, $enabled_payment_methods)) {
            abort(403);
        } */

        return view('frontend.payments.paypal');
    }

    public function store(Request $request)
    {
      try {
          /* get PayPal Email */
          $settings =  PaymentMethod::where('panel_id', auth()->user()->panel_id)->where('global_payment_method_id', 1)->first();
          
          if ($settings) 
          {
          
                $this->paypal_mode  = 'sandbox'; // live / sandbox
                $details = current(json_decode($settings->details,  true));
                if ($details['key'] == 'PAYPAL_EMAIL') 
                {
                    if (!empty($details['value'])) 
                    {

                        //dd('her ', $request->all(), $settings);
                      
                        $min_amount = $settings->minimum;
                        $validator = Validator::make($request->all(), [
                            'amount' => 'required|numeric|min:' . $min_amount,
                        ]);

                        $this->paypal_email = $details['value'];

                        if ($validator->fails()) {
                            return redirect()
                                ->back()
                                ->withErrors($validator)
                                ->withInput();
                        }


                        $paymentLogSecret = bcrypt(Auth::user()->email . 'PayPal' . time() . rand(1, 90000));

                        $log = Transaction::create([
                            'transaction_type' => 'deposit',
                            'transaction_detail' => json_encode(['payment_secrete'=>  $paymentLogSecret, 'currency_code'=> 'USD']),
                            'tnx_id' => $paymentLogSecret,
                            'amount' => $request->input('amount'),
                            'transaction_flag' => 'payment_gateway',
                            'user_id' =>  Auth::user()->id,
                            'admin_id' => null,
                            'status' => 'hold',
                            'memo' => null,
                            'fraud_risk' => null,
                            'payment_gateway_response' => null,
                            'reseller_payment_methods_setting_id' =>  $this->payment_method_id,
                            'reseller_id' => 1,
                        ]);

                        $staffmails = staffEmails('payment_received', auth()->user()->panel_id);
                        if (count($staffmails)>0) {
                            $notification =  $notification = notification('Payment received', 2, auth()->user()->panel_id);
                            if ($notification) {
                                if ($notification->status =='Active') {
                                    Mail::to($staffmails)->send(new ManualOrderPlaced($log, $notification));
                                }
                            }
                        }
                        $params = [
                            'cmd' => '_xclick',
                            'business' => $this->paypal_email,
                            'no_note' => 1,
                            'item_name' => 'Add Funds',
                            'item_number' => '160',
                            'amount' => $request->input('amount'),
                            'currency_code' => 'USD',
                            'charset' => 'utf-8',
                            'return' => route('payment.paypal.success'),
                            'cancel_return' => route('payment.paypal.cancel'),
                            'notify_url' => route('payment.paypal.ipn'),
                            'no_shipping' => 1,
                            'quantity' => 1,
                            'custom' => $paymentLogSecret,
                        ];
                        $url = self::PAYPAL_URL;
                        if (strtolower($this->paypal_mode) == 'sandbox') {
                            $url = self::PAYPAL_SANDBOX_URL;
                        }
                        $url .= http_build_query($params);
                        return redirect()->away($url);
                    }
                    else
                    {
                        return  redirect()->back()->withError('NO Email setting found, please contact administrator');
                    }
                }
          }
          else
          {
            return redirect()->back()->withError('No setting found, contact your reseller');
          }
      } catch (\Exception $e) {
        return redirect()->back()->withError($e->getMessage());
          dd($e->getMessage());
      }



    }

    public function store_Log($data)
    {
        $dir = public_path("payment_log/");
        if (!file_exists( $dir ) && !is_dir( $dir ))
        {
            $md = mkdir($dir, 0777, true);
            if ($md)
            {
                $this->putResultData($dir, $data);
            }
        }
        else
        {
            $this->putResultData($dir, $data);
        }
    }

    public function putResultData($dir, $data)
    {
       
        $file_path = $dir.time().'_result.json';
        $myfile = fopen($file_path,'w');
        $sports_data = json_decode(json_encode($data),true);
        fwrite($myfile,json_encode($sports_data));
        fclose($myfile);
    }
    
    public function ipn(Request $request)
    {

      $all = $request->all();
      foreach($all as $key){
          $rawPostedData = $key;
      }

     
        /* 
        return json_encode($all);
        exit; */
        // Get rawp POST data from php://input
        //$rawPostedData = $request->a; //$request->getContent();
        $rawPostedDataArray = $all;
        $this->store_Log('response::: '.json_encode($rawPostedData).':: '.json_encode($request->all()));
        // Decode all url parameters and store in $myPost
        $myPost =  $rawPostedDataArray;
        if (substr_count($rawPostedDataArray['payment_date'], '+') === 1) 
        {
          $myPost['payment_date'] = str_replace('+', '%2B', $rawPostedDataArray['payment_date']);
      }
      
       /*  foreach ($rawPostedDataArray as $keyval) {
            $keyval = explode('=', $keyval);
            if (count($keyval) == 2) {
                // Since we do not want the plus in the datetime string to be encoded to a space, we manually encode it.
                if ($keyval[0] === 'payment_date') {
                    if (substr_count($keyval[1], '+') === 1) {
                        $keyval[1] = str_replace('+', '%2B', $keyval[1]);
                    }
                }
                $myPost[$keyval[0]] = urldecode($keyval[1]);
            }
        } */
        /* return json_encode($myPost);
        exit;  */
        $client = new Client();

        try {

            $params = ['cmd' => '_notify-validate'];

            // Create request params to send to paypal
            foreach ($myPost as $key => $value) {
                $params[$key] = $value;
            }

            $url = self::PAYPAL_IPN_URL;
            if (strtolower($this->paypal_mode) == 'sandbox') {
                $url = self::PAYPAL_SANDBOX_IPN_URL;
            }

            $res = $client->request('POST', $url, [
                'form_params' => $params
            ]);

            if ($res->getStatusCode() === 200) {
                $resp = $res->getBody()->getContents();
                //$resp = 'VERIFIED';
                if ($resp == 'VERIFIED') {

                    // custom data missing
                    /* if (empty($myPost['custom'])) {

                      echo 'Missing custom data from POST.'
                        activity('paypal')
                            ->withProperties(['ip' => $request->ip()])
                            ->log('Missing custom data from POST.');
                        die();
                    } */

                    // Get paymentLog where details = $custom
                    if (!empty($myPost['custom']))
                    {
                      $custom = $myPost['custom'];
                      //$paymentLog = PaymentLog::where(['details' => $custom])->first();
                      $paymentLog = Transaction::where(['tnx_id' => $custom])->get();
                      
                      if (count($paymentLog) == 0) {
                          /* activity('paypal')
                              ->withProperties(['ip' => $request->ip()])
                              ->log('Payment Log not found. details: ' . $custom); */
                          echo 'Payment Log not found. details: ' . $custom;
                          $this->store_Log('Payment Log not found. details: '.json_encode($myPost));
                          die();
                      }
                    }
                    

                    // Check amount against order total
                   /*  if ($myPost['mc_gross'] != $paymentLog->total_amount) {
                        activity('paypal')
                            ->withProperties([
                                'ip' => $request->ip()
                            ])->log('Amount is less than order total! mc_gross:' . $myPost['mc_gross']);
                        die();
                    } */

                    // Check currency match
                    /* if (strcasecmp(trim(strtoupper($myPost['mc_currency'])), trim(getOption('currency_code'))) != 0) {
                        activity('paypal')
                            ->withProperties([
                                'ip' => $request->ip()
                            ])->log('Currency mismatch. mc_currency:' . $myPost['mc_currency']);
                        die();
                    } */

                    // Check PayPal emails is same added by admin in panel
                    /* if (strcasecmp(trim($this->paypal_email), trim($myPost['receiver_email'])) != 0) {
                        activity('paypal')
                            ->withProperties([
                                'ip' => $request->ip()
                            ])->log('IPN Response is not for paypal email in added in panel. receiver_email:' . $myPost['receiver_email']);
                        die();
                    } */

                    // Check for a valid transaction types.
                    /* $accepted_types = array('cart', 'instant', 'express_checkout', 'web_accept', 'masspay', 'send_money', 'paypal_here');
                    if (!in_array(strtolower($myPost['txn_type']), $accepted_types)) {
                        activity('paypal')
                            ->withProperties([
                                'ip' => $request->ip()
                            ])->log('Invalid transaction type. txn_type:' . $myPost['txn_type']);
                        die();
                    } */

                    // Check payment status, if not sandbox then it should be 'complete' for processing.
                    if (strtolower($this->paypal_mode) != 'sandbox') {
                        if (strtolower($myPost['payment_status']) != 'completed') {
                            echo 'Payment status not complete. Status is : ' . $myPost['payment_status'];
                            $this->store_Log('Payment status not complete. Status is : ' .json_encode($myPost));
                            /* activity('paypal')
                                ->withProperties([
                                    'ip' => $request->ip()
                                ])->log('Payment status not complete. Status is : ' . $myPost['payment_status']); */
                            die();
                        }
                    }

                    // Amount after fees deduction
                    $amount_after_fee = $myPost['mc_gross'] - $myPost['mc_fee'];
                    $total = 0;
                    $paymentLog = Transaction::where(['tnx_id' => $custom])->first();
                    $paymentLog->amount = $amount_after_fee;
                    $paymentLog->reseller_payment_methods_setting_id = $this->payment_method_id;
                    $paymentLog->transaction_detail = json_encode($myPost);
                    $paymentLog->status = 'done';
                    $saved = $paymentLog->save();

                    if ($saved) {
                      \DB::statement('UPDATE transactions t
                      CROSS JOIN (SELECT MAX(sequence_number) + 1 as new_sequence_number
                      FROM transactions) s
                      SET t.sequence_number = s.new_sequence_number
                      WHERE t.id='.$paymentLog->id);
                      $total += $amount_after_fee;
                    }

                    $bonus = SettingBonuse::where('global_payment_method_id', $this->payment_method_id)->get()->last();
                    if ($bonus !=null) {
                        if ( floatval($amount_after_fee) >= floatval($bonus->deposit_from)) {
                            $bonus = ($bonus->bonus_amount / 100) * floatval($amount_after_fee);
                            $tran = Transaction::create([
                                'transaction_type' => 'deposit',
                                'transaction_detail' => json_encode([
                                    'payment_secrete'=>  $custom,
                                    'currency_code'=> 'USD',
                                    'actual_amount'=> floatval($amount_after_fee),]),
                                'tnx_id' => $custom,
                                'amount' =>  $bonus,
                                'transaction_flag' => 'bonus_deposit',
                                'user_id' =>  $paymentLog->user_id,
                                'admin_id' => null,
                                'status' => 'done',
                                'memo' => null,
                                'fraud_risk' => null,
                                'payment_gateway_response' => null,
                                'reseller_payment_methods_setting_id' =>  $this->payment_method_id,
                                'reseller_id' => 1,
                                ]);

                              if ($tran) {
                                  \DB::statement('UPDATE transactions t
                                  CROSS JOIN (SELECT MAX(sequence_number) + 1 as new_sequence_number
                                  FROM transactions) s
                                  SET t.sequence_number = s.new_sequence_number
                                  WHERE t.id='.$tran->id);
                                  $total += $bonus;
                              }
                        }
                    }

                    $user = User::find($paymentLog->user_id);

                    $user->balance += $total;
                    $user->save();

                    //transaction($transaction, $user);

                    // important to uncomment
                    //$notification = notification('Payment received', 2);

                    /* if ($notification && $notification->status) {
                        Mail::to(staffEmails('payment_received'))->send(new PaymentReceived($paymentLog, $notification));
                    } */

                    /**
                     * NOTE:
                     * not checking txn_id for next time process, because if once proceed $custom variable will not be able to
                     * fetch from payment_log table and next ipn hit will be ignored by logic
                     */

                    // Payment successful, load fund and update payment log
                    /* Transaction::where(['details' => $custom])->update([
                        'details' => json_encode($myPost),
                    ]); */

                    /* activity('paypal')
                        ->withProperties([
                            'ip' => $request->ip()
                        ])->log('Payment Successful for user_id:' . $paymentLog->user_id . ' amount:' . $amount_after_fee); */

                } 
                else 
                {
                    /* activity('paypal')
                        ->withProperties([
                            'ip' => $request->ip()
                        ])->log("IPN Unverified, responses status code:".$res->getStatusCode()); */
                        echo "IPN Unverified, responses status code:".json_encode($res);
                        $this->store_Log("IPN Unverified, responses status code:".json_encode($res));
                    die();
                }

            } else {
              /*   activity('paypal')
                    ->withProperties([
                        'ip' => $request->ip()
                    ])->log('Invalid response from paypal. response code: ' . json_encode($res)); */
                    echo "Invalid response from paypal. response code:".json_encode($res);
                    $this->store_Log("Invalid response from paypal. response code:".json_encode($res));
                die();
            }

        } catch (\ClientException $e) {
           /*  activity('paypal')
                ->withProperties([
                    'ip' => $request->ip()
                ])->log('Issue in sending data back to paypal.'); */
                echo "Issue in sending data back to paypal.".json_encode($res);
                $this->store_Log("Issue in sending data back to paypal.".json_encode($res));
            die();
        }
    }

    public function success(Request $request)
    {
        Session::flash('success', 'Payment is succesfully added');
        return redirect('/add-funds');
    }

    public function cancel(Request $request)
    {
        Session::flash('error', 'Payment is cancelled');
        return redirect('/add-funds');
    }
}
