<?php

namespace App\Http\Controllers\Payment;

use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use App\Models\SettingBonuse;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\PaymentController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class CoinPaymentsController extends Controller
{
    private $globalMethodId = 3;
    private $url = 'https://www.coinpayments.net/index.php?';

    public function store(Request $request)
    {
        try {
            $settings =  PaymentMethod::where('panel_id', auth()->user()->panel_id)->where('global_payment_method_id', $this->globalMethodId)->first();
            if (empty($settings)) {
                return redirect()->back()->withError('No setting found, contact with your provider.');
            }
            
            $details = json_decode($settings->details,  true);
            $marcent_id = '';
            $secret_key = '';
            foreach ($details as $detail) {
                if ($detail['key'] == 'MERCHANT_ID') {
                    $marcent_id = $detail['value'];
                }
                if ($detail['key'] == 'COINBASE_SECRET_KEY') {
                    $secret_key = $detail['value'];
                }
            }

            if ($marcent_id == '' || $marcent_id == null) {
                return redirect()->back()->withErrors(['error' => 'No setting found, contact your reseller']);
            } else if ($secret_key == '' || $secret_key == null) {
                return redirect()->back()->withErrors(['error' => 'No setting found, contact your reseller']);
            }

            $validator = Validator::make($request->all(), [
                'amount' => 'required|numeric|min:' . $settings->minimum,
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $secret = bcrypt(Auth::user()->email . 'CoinPayments' . time() . rand(1, 90000));
            $transaction = (new PaymentController())->transactionStore($request->amount, $this->globalMethodId, $secret);
            if ($transaction) {
                $params = [
                    'merchant' => $marcent_id,
                    'cmd' => '_pay_simple',
                    'reset' => 1,
                    'currency' => 'USD',
                    'amountf' => $request->input('amount'),
                    'item_name' => 'Add Funds',
                    'email' => Auth::user()->email,
                    'success_url' => route('payment.coinpayments.success'),
                    'cancel_url' => route('payment.coinpayments.cancel'),
                    'ipn_url' => route('payment.coinpayments.ipn'),
                    'first_name' => Auth::user()->name,
                    'last_name' => Auth::user()->name,
                    'want_shipping' => 0,
                    'custom' => $secret,
                ];
                $this->url .= http_build_query($params);
                return redirect()->away($this->url);
            } else {
                return redirect()->back()->withError('Something is wrong, please contact with your provider.');
            } 
        } catch (\Exception $e) {
            return redirect()->back()->withError($e->getMessage());
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

    public function ipn(Request $request)
    {
        if (!$request->filled('ipn_mode') || !$request->filled('merchant')) {
            Log::critical("Missing POST data from callback.");
            return false;
        }

        $settings =  PaymentMethod::where('panel_id', auth()->user()->panel_id)->where('global_payment_method_id', $this->globalMethodId)->first();
        if (empty($settings)) {
            Log::critical("No setting found in IPN, contact with your provider.");
            return false;
        }

        $details = json_decode($settings->details,  true);
        $marcent_id = '';
        $secret_key = '';
        foreach ($details as $detail) {
            if ($detail['key'] == 'MERCHANT_ID') {
                $marcent_id = $detail['value'];
            }
            if ($detail['key'] == 'COINBASE_SECRET_KEY') {
                $secret_key = $detail['value'];
            }
        }

        if ($request->input('ipn_mode') == 'httpauth') {
            //Verify that the http authentication checks out with the users supplied information
            if ($request->server('PHP_AUTH_USER') != $marcent_id || $request->server('PHP_AUTH_PW') != $secret_key) {
                Log::critical("Unauthorized HTTP Request.");
                return false;
            }

        } elseif ($request->input('ipn_mode') == 'hmac') {
            // Create the HMAC hash to compare to the recieved one, using the secret key.
            $hmac = hash_hmac("sha512", $request->all(), $secret_key);

            if ($hmac != $request->server('HTTP_HMAC')) {
                Log::critical("Unauthorized HMAC Request.");
                return false;
            }
        } else {
            Log::critical("Unauthorized HMAC Request.");
            return false;
        }

        // Passed initial security test - now check the status
        $status = intval($request->input('status'));
        $statusText = $request->input('status_text');

        if ($request->input('merchant') != $marcent_id) {
            Log::critical('Mismatching merchant ID. MerchantID:' . $request->input('merchant'));
            return false;
        }

        if ($status < 0) {
            Log::critical('Payment Failed' . json_encode(['ip' => $request->ip(), 'status' => $status, 'statusText' => $statusText]));
            return false;

        } elseif ($status == 0) {
            Log::critical('Payment is in Pending, Waiting for buyer funds');
            return false;
        } elseif ($status >= 100 || $status == 2) {
            Log::critical('Custom data is missing from request');
            return false;
        }

        $custom = $request->input('custom');

        $PaidData = [
            'detail' => json_encode(['txn_id' => $custom]),
            'status' => 'done',
        ];
        $transaction = (new PaymentController())->transactionPay($this->globalMethodId, $custom, $PaidData);
    }
}
