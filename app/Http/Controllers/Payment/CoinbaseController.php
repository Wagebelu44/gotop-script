<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\PaymentController;
use App\Models\PaymentMethod;
use CoinbaseCommerce\ApiClient;
use CoinbaseCommerce\Resources\Charge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class CoinbaseController extends Controller
{
    private $globalMethodId = 5;

    public function store(Request $request)
    {
        try {
            $settings = PaymentMethod::where('panel_id', auth()->user()->panel_id)->where('global_payment_method_id', $this->globalMethodId)->first();
            if (empty($settings)) {
                return redirect()->back()->withError('No setting found, contact with your provider.');
            }

            $details = json_decode($settings->details,  true);
            $secret_key = '';
            foreach ($details as $detail) {
                if ($detail['key'] == 'SECRET_KEY') {
                    $secret_key = $detail['value'];
                }
            }

            if ($secret_key == null || $secret_key == '') {
                return redirect()->back()->with('error' , 'Secret key not found');
            }

            $validator = Validator::make($request->all(), [
                'amount' => 'required|numeric|min:' . $settings->minimum,
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $secret = bcrypt(Auth::user()->email . 'CoinBase' . time() . rand(1, 90000));
            $transaction = (new PaymentController())->transactionStore($request->amount, $this->globalMethodId, $secret);
            if ($transaction) {
                ApiClient::init($secret_key);
                $chargeData = [
                    'name' => auth()->user()->id,
                    'description' => 'Payment to GoFans app',
                    'local_price' => [
                        'amount' =>  $request->amount,
                        'currency' => 'USD'
                    ],
                    'pricing_type' => 'fixed_price',
                    "redirect_url" => route('payment.coinbase.success'),
                    "cancel_url" => route('payment.coinbase.cancel'),
                ];
                $charges = Charge::create($chargeData);
                $d =  $charges['addresses'];
                $hostedUrl =  $charges['hosted_url'];
                echo 'redirecting ..................';
                echo '<form action="'.$hostedUrl.'" method="get" id="coinbaseForm"></form>';
                echo '<script> document.getElementById("coinbaseForm").submit(); </script>';
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
}
