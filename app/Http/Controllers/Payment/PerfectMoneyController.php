<?php

namespace App\Http\Controllers\Payment;

use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class PerfectMoneyController extends Controller
{
    private $action_link  = 'https://perfectmoney.is/api/step1.asp';
    private $payment_method_id = 4;
    public function getPaymentProcsseded(Request $request)
    {
        try {
            $settings =  PaymentMethod::where('panel_id', auth()->user()->panel_id)->where('global_payment_method_id', $this->payment_method_id)->first();
            if ($settings) 
            {
                $details = json_decode($settings->details,  true);
                $account_id = '';
                $account_name = 'No Name set';
                foreach ($details as $detail) {
                    if ($detail['key'] == 'PAYEE_ACCOUNT') {
                        $account_id = $detail['value'];
                    }
                    if ($detail['key'] == 'PAYEE_NAME') {
                        $account_name = $detail['value'];
                    }
                }
                
                if ($account_id ==null || $account_id=='') {
                    return redirect()->back()->with('error' , 'Perfect Marchent ID not found');
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

                $log = Transaction::create([
                    'transaction_type' => 'deposit',
                    'transaction_detail' => json_encode(['payment_secrete'=>  null, 'currency_code'=> 'USD']),
                    'amount' => $request->input('amount'),
                    'transaction_flag' => 'payment_gateway',
                    'user_id' =>  auth()->user()->id,
                    'admin_id' => null,
                    'status' => 'hold',
                    'tnx_id' => rand(0, round(microtime(true))),
                    'memo' => null,
                    'fraud_risk' => null,
                    'payment_gateway_response' => null,
                    'reseller_payment_methods_setting_id' => $this->payment_method_id,
                    'reseller_id' => 1,
                    ]);
                    $successURL  = url('perfectmoney/success');
                    $failURL  = url('perfectmoney/failed');
                    echo 'redirecting ..................';
                    echo '<form action="'.$this->action_link.'" method="POST" id="perfectMoneyForm">'; 
                    echo '<input type="hidden" name="PAYEE_ACCOUNT" value="'.$account_id.'">';
                    echo '<input type="hidden" name="PAYEE_NAME" value="'.$account_name.'">';
                    echo '<input type="hidden" name="PAYMENT_AMOUNT" value="'.$request->input('amount').'">';
                    echo '<input type="hidden" name="PAYMENT_UNITS" value="USD">';
                    echo '<input type="hidden" name="PAYMENT_URL" value="'.$successURL.'">';
                    echo '<input type="hidden" name="NOPAYMENT_URL" value="'.$failURL.'">';
                    echo '<input type="hidden" name="BAGGAGE_FIELDS" value="">';
                    echo '</form>';
                    echo '<script>
                        document.getElementById("perfectMoneyForm").submit();
                        </script>';
            }
        } catch (\Exception $e) 
        {
            return redirect()->back()->withError($e->getMessage());
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
