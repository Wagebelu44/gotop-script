<?php

namespace App\Http\Controllers\Payment;

use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class WebmoneyController extends Controller
{
    private $action_link  = 'https://merchant.webmoney.ru/lmi/payment_utf.asp';
    private $payment_method_id = 5;

    public function store(Request $request)
    {
        try {
            $settings =  PaymentMethod::where('panel_id', auth()->user()->panel_id)->where('global_payment_method_id', $this->payment_method_id)->first();
            if ($settings) {
                $details = json_decode($settings->details,  true);
                $account_id = '';
                $account_name = 'No Name set';
                foreach ($details as $detail) {
                    if ($detail['key'] == 'LMI_PAYEE_PURSE') {
                        $account_id = $detail['value'];
                    }

                    if ($detail['key'] == 'LMI_PAYMENT_DESC') {
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
                    return redirect()->back()->withErrors($validator)->withInput();
                }

                Transaction::create([
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

                $successURL  = route('payment.webmoney.success');
                $failURL  = route('payment.webmoney.cancel');

                echo 'redirecting ..................';
                echo '<form action="'.$this->action_link.'" method="POST" id="webMoneyForm">'; 
                echo '<input type="hidden" name="LMI_PAYMENT_AMOUNT" value="'.$request->input('amount').'">';
                echo '<input type="hidden" name="LMI_PAYMENT_DESC" value="'.$account_name.'">';
                echo '<input type="hidden" name="LMI_PAYMENT_NO" value="1234">';
                echo '<input type="hidden" name="LMI_PAYEE_PURSE" value="'.$account_id.'">';
                echo '<input type="hidden" name="LMI_SIM_MODE" value="0">';
                echo '<input type="hidden" name="FIELD_1" value="VALUE_1">';
                echo '<input type="hidden" name="FIELD_2" value="VALUE_2">';
                echo '<input type="hidden" name="FIELD_N" value="VALUE_N">';
                echo '</form>';
                echo '<script>
                    document.getElementById("webMoneyForm").submit();
                    </script>';
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
