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

class PayeerController extends Controller
{
    private $globalMethodId = 8;
    private $action_link  = 'https://payeer.com/merchant/';

    public function store(Request $request)
    {
        try {
            $settings =  PaymentMethod::where('panel_id', auth()->user()->panel_id)->where('global_payment_method_id', $this->globalMethodId)->first();
            if (empty($settings)) {
                return redirect()->back()->withError('No setting found, contact with your provider.');
            }

            $details = json_decode($settings->details,  true);
            $account_id = '';
            $secret_key = '';
            foreach ($details as $detail) {
                if ($detail['key'] == 'MARCHENT_ID') {
                    $account_id = $detail['value'];
                }

                if ($detail['key'] == 'SECRET_KEY') {
                    $secret_key = $detail['value'];
                }
            }
            
            if ($account_id ==null || $account_id=='') {
                return redirect()->back()->with('error' , 'Payeer Marchent ID not found');
            }

            if ($secret_key ==null || $secret_key=='') {
                return redirect()->back()->with('error' , 'Payeer Secrete Key not found');
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
                $successURL  = route('payment.payeer.success');
                $failURL  = route('payment.payeer.cancel');

                echo 'redirecting ..................';
                echo '<form action="'.$this->action_link.'" method="POST" id="payeer">'; 
                echo '<input type="hidden" name="m_shop" value="'.$account_id.'">';
                echo '<input type="hidden" name="m_orderid" value="1">';
                echo '<input type="hidden" name="m_amount" value="'.$request->amount.'">';
                echo '<input type="hidden" name="m_curr" value="USD">';
                echo '<input type="hidden" name="m_desc" value="dGVzdA==">';
                echo '<input type="hidden" name="m_sign" value="'.$secret_key.'">';
                echo '</form>';
                echo '<script> document.getElementById("payeer").submit(); </script>';
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
