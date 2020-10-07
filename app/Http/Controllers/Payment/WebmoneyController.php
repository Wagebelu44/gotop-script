<?php

namespace App\Http\Controllers\Payment;

use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\PaymentController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class WebmoneyController extends Controller
{
    private $globalMethodId = 5;
    private $action_link  = 'https://merchant.webmoney.ru/lmi/payment_utf.asp';

    public function store(Request $request)
    {
        try {
            $settings =  PaymentMethod::where('panel_id', auth()->user()->panel_id)->where('global_payment_method_id', $this->globalMethodId)->first();
            if (empty($settings)) {
                return redirect()->back()->withError('No setting found, contact with your provider.');
            }

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
            
            $validator = Validator::make($request->all(), [
                'amount' => 'required|numeric|min:' . $settings->minimum,
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $secret = bcrypt(Auth::user()->email . 'WM' . time() . rand(1, 90000));
            $transaction = (new PaymentController())->transactionStore($request->amount, $this->globalMethodId, $secret);
            if ($transaction) {
                $successURL  = route('payment.webmoney.success');
                $failURL  = route('payment.webmoney.cancel');

                echo 'redirecting ..................';
                echo '<form action="'.$this->action_link.'" method="POST" id="webMoneyForm">'; 
                echo '<input type="hidden" name="LMI_PAYMENT_AMOUNT" value="'.$request->amount.'">';
                echo '<input type="hidden" name="LMI_PAYMENT_DESC" value="'.$account_name.'">';
                echo '<input type="hidden" name="LMI_PAYMENT_NO" value="1234">';
                echo '<input type="hidden" name="LMI_PAYEE_PURSE" value="'.$account_id.'">';
                echo '<input type="hidden" name="LMI_SIM_MODE" value="0">';
                echo '<input type="hidden" name="FIELD_1" value="VALUE_1">';
                echo '<input type="hidden" name="FIELD_2" value="VALUE_2">';
                echo '<input type="hidden" name="FIELD_N" value="VALUE_N">';
                echo '</form>';
                echo '<script> document.getElementById("webMoneyForm").submit(); </script>';
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
