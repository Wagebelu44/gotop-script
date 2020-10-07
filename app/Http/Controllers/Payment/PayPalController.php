<?php

namespace App\Http\Controllers\Payment;

use GuzzleHttp\Client;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\PaymentController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class PayPalController extends Controller
{
    private $globalMethodId = 1;
    private $paypal_email;
    private $paypal_mode = '';
    const PAYPAL_URL = "https://www.paypal.com/cgi-bin/webscr?";
    const PAYPAL_SANDBOX_URL = "https://www.sandbox.paypal.com/cgi-bin/webscr?test_ipn=1&";
    const PAYPAL_IPN_URL = 'https://ipnpb.paypal.com/cgi-bin/webscr';
    const PAYPAL_SANDBOX_IPN_URL = 'https://ipnpb.sandbox.paypal.com/cgi-bin/webscr';

    public function store(Request $request)
    {
        try {
            $settings = PaymentMethod::where('panel_id', auth()->user()->panel_id)->where('global_payment_method_id', $this->globalMethodId)->first();
            if (empty($settings)) {
                return redirect()->back()->withError('No setting found, contact with your provider.');
            }
          
            $this->paypal_mode  = 'sandbox'; // live / sandbox
            $details = current(json_decode($settings->details,  true));
            if ($details['key'] == 'PAYPAL_EMAIL') {
                if (!empty($details['value'])) {
                    $validator = Validator::make($request->all(), [
                        'amount' => 'required|numeric|min:' . $settings->minimum,
                    ]);

                    if ($validator->fails()) {
                        return redirect()->back()->withErrors($validator)->withInput();
                    }

                    $this->paypal_email = $details['value'];

                    $secret = bcrypt(Auth::user()->email . 'PayPal' . time() . rand(1, 90000));
                    $transaction = (new PaymentController())->transactionStore($request->amount, $this->globalMethodId, [], $secret);

                    if ($transaction) {                    
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
                            'custom' => $secret,
                        ];
                        $url = self::PAYPAL_URL;
                        if (strtolower($this->paypal_mode) == 'sandbox') {
                            $url = self::PAYPAL_SANDBOX_URL;
                        }
                        $url .= http_build_query($params);
                        return redirect()->away($url);
                    } else {
                        return redirect()->back()->withError('Something is wrong, please contact with your provider.');
                    }
                } else {
                    return  redirect()->back()->withError('No paypal email found, please contact with your provider');
                }
            } else {
                return redirect()->back()->withError('No paypal email found, contact with your provider.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->withError($e->getMessage());
        }
    }
    
    public function ipn(Request $request)
    {
        $all = $request->all();
        foreach($all as $key){
            $rawPostedData = $key;
        }
        
        $rawPostedDataArray = $all;
        $this->store_Log('response::: '.json_encode($rawPostedData).':: '.json_encode($request->all()));
        // Decode all url parameters and store in $myPost
        $myPost =  $rawPostedDataArray;
        if (substr_count($rawPostedDataArray['payment_date'], '+') === 1) {
            $myPost['payment_date'] = str_replace('+', '%2B', $rawPostedDataArray['payment_date']);
        }
        
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
                if ($resp == 'VERIFIED') {

                    // Get paymentLog where details = $custom
                    if (!empty($myPost['custom'])) {
                        $custom = $myPost['custom'];
                        $paymentLog = Transaction::where(['tnx_id' => $custom])->get();
                        
                        if (count($paymentLog) == 0) {
                            Log::error('Payment not found. details: '.json_encode($myPost));
                            return false;
                        }
                    }

                    // Check payment status, if not sandbox then it should be 'complete' for processing.
                    if (strtolower($this->paypal_mode) != 'sandbox') {
                        if (strtolower($myPost['payment_status']) != 'completed') {
                            Log::error('Payment status not complete. Status is : ' .json_encode($myPost));
                            return false;
                        }
                    }

                    $PaidData = [
                        'amount' => ($myPost['mc_gross'] - $myPost['mc_fee']),
                        'detail' => json_encode($myPost),
                        'status' => 'done',
                    ];
                    $transaction = (new PaymentController())->transactionPay($this->globalMethodId, $custom, $PaidData);
                } else {
                    Log::error("IPN Unverified, responses status code:".json_encode($res));
                    return false;
                }
            } else {
                Log::error("Invalid response from paypal. response code:".json_encode($res));
                return false;
            }
        } catch (\ClientException $e) {
            Log::error("Issue in sending data back to paypal.".$e->getMessage());
            return false;
        }
    }

    public function success()
    {
        Session::flash('success', 'Payment is succesfully added');
        return redirect('/add-funds');
    }

    public function cancel()
    {
        Session::flash('error', 'Payment is cancelled');
        return redirect('/add-funds');
    }
}
