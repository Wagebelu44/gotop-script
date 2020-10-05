<?php

namespace App\Http\Controllers\Payment;

use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use App\Models\SettingBonuse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class CoinPaymentsController extends Controller
{
    private $url = 'https://www.coinpayments.net/index.php?';
    private $payment_method_id = 3; // Bitcoin payment id in table `payment_methods`

    /* public function __construct()
    {
        $this->merchantId = PaymentMethod::where(['config_key' => 'merchant_id'])->first()->config_value;
        $this->secretKey = PaymentMethod::where(['config_key' => 'secret_key'])->first()->config_value;
    } */

    public function showForm()
    {
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

        return view('frontend.payments.bitcoin');
    }

    public function store(Request $request)
    {

        try {

            $settings =  PaymentMethod::where('panel_id', auth()->user()->panel_id)->where('global_payment_method_id', 3)->first();
            if ($settings) 
            {
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
                }
                else if ($secret_key == '' || $secret_key == null) {
                    return redirect()->back()->withErrors(['error' => 'No setting found, contact your reseller']);
                }
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

                $params = [
                    'merchant' => $marcent_id,
                    'cmd' => '_pay_simple',
                    'reset' => 1,
                    'currency' => 'USD',
                    'amountf' => $request->input('amount'),
                    'item_name' => 'Add Funds',
                    'email' => Auth::user()->email,
                    'success_url' => route('payment.bitcoin.success'),
                    'cancel_url' => route('payment.bitcoin.cancel'),
                    'ipn_url' => route('payment.bitcoin.ipn'),
                    'first_name' => Auth::user()->name,
                    'last_name' => Auth::user()->name,
                    'want_shipping' => 0,
                ];

                    $paymentLogSecret = bcrypt(Auth::user()->email . 'PayPal' . time() . rand(1, 90000));
                    // Create payment logs
                    $log = Transaction::create([
                        'transaction_type' => 'deposit',
                        'transaction_detail' => json_encode(['payment_secrete'=>  $paymentLogSecret, 'currency_code'=> 'USD']),
                        'amount' => $request->input('amount'),
                        'transaction_flag' => 'payment_gateway',
                        'user_id' =>  Auth::user()->id,
                        'admin_id' => null,
                        'status' => 'hold',
                        'memo' => null,
                        'fraud_risk' => null,
                        'payment_gateway_response' => null,
                        'reseller_payment_methods_setting_id' => $this->payment_method_id,
                        'reseller_id' => 1,
                    ]);

                    $bonus = SettingBonuse::where('global_payment_method_id', $this->payment_method_id)->get()->last();
                    if ($bonus !=null) {
                        if ( floatval($request->input('amount')) >= floatval($bonus->deposit_from)) {
                            $bonus = ($bonus->bonus_amount / 100) * floatval($request->input('amount'));
                            Transaction::create([
                                'transaction_type' => 'deposit',
                                'transaction_detail' => json_encode([
                                    'payment_secrete'=>  $paymentLogSecret,
                                    'currency_code'=> 'USD',
                                    'actual_amount'=> floatval($request->input('amount')),
                                    'actual_payment_id'=>$log->id]),
                                'amount' =>  $bonus,
                                'transaction_flag' => 'bonus_deposit',
                                'user_id' =>  Auth::user()->id,
                                'admin_id' => null,
                                'status' => 'hold',
                                'memo' => null,
                                'fraud_risk' => null,
                                'payment_gateway_response' => null,
                                'reseller_payment_methods_setting_id' =>  $this->payment_method_id,
                                'reseller_id' => 1,
                                ]);
                        }
                    }

                    $params['custom'] = $paymentLogSecret;
                    $this->url .= http_build_query($params);
                    return redirect()->away($this->url);
            }
            else
            {
              return redirect()->back()->withErrors(['error' => 'No setting found, contact your reseller']);
            }
        } catch (\Exception $e) {
              dd($e->getMessage());
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

    public function ipn(Request $request)
    {
        if (!$request->filled('ipn_mode') || !$request->filled('merchant')) {
            activity('coinpayments')
                ->withProperties(['ip' => $request->ip()])
                ->log('Missing POST data from callback.');
            die();
        }

        if ($request->input('ipn_mode') == 'httpauth') {
            //Verify that the http authentication checks out with the users supplied information
            if ($request->server('PHP_AUTH_USER') != $this->merchantId || $request->server('PHP_AUTH_PW') != $this->secretKey) {
                activity('coinpayments')
                    ->withProperties(['ip' => $request->ip()])
                    ->log('Unauthorized HTTP Request');
                die();
            }

        } elseif ($request->input('ipn_mode') == 'hmac') {
            // Create the HMAC hash to compare to the recieved one, using the secret key.
            $hmac = hash_hmac("sha512", $request->all(), $this->secretKey);

            if ($hmac != $request->server('HTTP_HMAC')) {
                activity('coinpayments')
                    ->withProperties(['ip' => $request->ip()])
                    ->log('Unauthorized HMAC Request');
                die();
            }

        } else {
            activity('coinpayments')
                ->withProperties(['ip' => $request->ip()])
                ->log('Unauthorized HMAC Request');
            die();
        }

        // Passed initial security test - now check the status
        $status = intval($request->input('status'));
        $statusText = $request->input('status_text');

        if ($request->input('merchant') != $this->merchantId) {
            activity('coinpayments')
                ->withProperties(['ip' => $request->ip()])
                ->log('Mismatching merchant ID. MerchantID:' . $request->input('merchant'));
            die();
        }

        if ($status < 0) {
            activity('coinpayments')
                ->withProperties([
                    'ip' => $request->ip(),
                    'status' => $status,
                    'StatusText' => $statusText
                ])
                ->log('Payment Failed');
            die();

        } elseif ($status == 0) {
            activity('coinpayments')
                ->withProperties([
                    'ip' => $request->ip()
                ])->log('Payment is in Pending, Waiting for buyer funds');
            die();
        } elseif ($status >= 100 || $status == 2) {

            if (!$request->filled('custom')) {
                activity('coinpayments')
                    ->withProperties([
                        'ip' => $request->ip()
                    ])->log('custom data is missing from request');
                die();
            }

            $custom = $request->input('custom');

            $paymentLog = Transaction::where(['transaction_detail' => $custom])->first();

            if (!is_null($paymentLog)) {
                $txn_id = $request->input('txn_id');
                $item_name = $request->input('item_name');
                $amount1 = $request->input('amount1');
                $amount2 = $request->input('amount2');
                $fee = $request->input('fee');
                $tax = $request->input('tax');
                $currency1 = $request->input('currency1');
                $currency2 = $request->input('currency2');

                // Check the original currency to make sure the buyer didn't change it.
                if (strtolower($currency1) != strtolower(getOption('currency_code'))) {
                    activity('coinpayments')
                        ->withProperties([
                            'ip' => $request->ip()
                        ])->log('Original currency mismatch. Currency:' . $currency1);
                    die();
                }

                // Check amount against order total
                if ($amount1 < $paymentLog->total_amount) {
                    activity('coinpayments')
                        ->withProperties([
                            'ip' => $request->ip()
                        ])->log('Amount is less than order total. Amount:' . $amount1);
                    die();
                }

                // Payment successful, load fund and update payment log
                Transaction::where(['transaction_detail' => $custom])->update([
                    'transaction_detail' => json_encode($request->all()),
                ]);

                $amountAfterTax = $amount1 - $tax;

                // Create Transaction logs
                $transaction = [
                    'amount' => $amountAfterTax,
                    'payment_method_id' => $this->payment_method_id,
                    'user_id' => $paymentLog->user_id,
                    'details' => json_encode($request->all()),
                ];
                $user = User::find($paymentLog->user_id);

                transaction($transaction, $user);

                $notification = notification('Payment received', 2);

                if ($notification && $notification->status) {
                    Mail::to(staffEmails('payment_received'))->send(new PaymentReceived($paymentLog, $notification));
                }

                activity('coinpayments')
                    ->withProperties([
                        'ip' => $request->ip()
                    ])->log('Payment Loaded successfully for user_id:' . $paymentLog->user_id . ' amount:' . $amountAfterTax);
                die();

            }

            activity('coinpayments')
                ->withProperties([
                    'ip' => $request->ip()
                ])->log('PaymentLog Object not found, might be payment already loaded.');
            die();

        }

        activity('coinpayments')
            ->withProperties([
                'ip' => $request->ip()
            ])->log('Unkown error, no condition matched.');
        die();
    }
}
