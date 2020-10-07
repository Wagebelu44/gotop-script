<?php

namespace App\Http\Controllers\Payment;

use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\PaymentController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class PerfectMoneyController extends Controller
{
    private $globalMethodId = 4;
    private $action_link  = 'https://perfectmoney.is/api/step1.asp';
    
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
            
            $validator = Validator::make($request->all(), [
                'amount' => 'required|numeric|min:' . $settings->minimum,
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $secret = bcrypt(Auth::user()->email . 'PM' . time() . rand(1, 90000));
            $transaction = (new PaymentController())->transactionStore($request->amount, $this->globalMethodId, $secret);
            if ($transaction) {
                echo 'redirecting ..................';
                echo '<form action="'.$this->action_link.'" method="POST" id="perfectMoneyForm">'; 
                echo '<input type="hidden" name="PAYEE_ACCOUNT" value="'.$account_id.'">';
                echo '<input type="hidden" name="PAYEE_NAME" value="'.$account_name.'">';
                echo '<input type="hidden" name="PAYMENT_AMOUNT" value="'.$request->amount.'">';
                echo '<input type="hidden" name="PAYMENT_UNITS" value="USD">';
                echo '<input type="hidden" name="PAYMENT_URL" value="'.route('payment.perfectmoney.success').'">';
                echo '<input type="hidden" name="NOPAYMENT_URL" value="'.route('payment.perfectmoney.cancel').'">';
                echo '<input type="hidden" name="BAGGAGE_FIELDS" value="">';
                echo '</form>';
                echo '<script> document.getElementById("perfectMoneyForm").submit(); </script>';
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
