<?php

namespace App\Http\Controllers\Payment;

use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Web\PaymentController;

class CashmaalController extends Controller
{
    private $globalMethodId = 7;
    private $action_link  = 'https://www.cashmaal.com/Pay/';

    public function store(Request $request)
    {
        try {
            $settings =  PaymentMethod::where('panel_id', auth()->user()->panel_id)->where('global_payment_method_id', $this->globalMethodId)->first();
            if (empty($settings)) {
                return redirect()->back()->withError('No setting found, contact with your provider.');
            }

            $details = json_decode($settings->details,  true);
            $account_email = '';
            $account_web_id = '';
            foreach ($details as $detail) {
                if ($detail['key'] == 'email') {
                    $account_email = $detail['value'];
                }

                if ($detail['key'] == 'web_id') {
                    $account_web_id = $detail['value'];
                }
            }
            
            if ($account_email ==null || $account_email=='') {
                return redirect()->back()->with('error' , 'Cashmaal Email is not set');
            }

            if ($account_web_id ==null || $account_web_id=='') {
                return redirect()->back()->with('error' , 'Cashmaal Web ID is not set');
            }
            
            $validator = Validator::make($request->all(), [
                'amount' => 'required|numeric|min:' . $settings->minimum,
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $secret = bcrypt(Auth::user()->email . 'CM' . time() . rand(1, 90000));
            $transaction = (new PaymentController())->transactionStore($request->amount, $this->globalMethodId, $secret);
            if ($transaction) {
                $successURL  = route('payment.cashmaal.success');
                $failURL  = route('payment.cashmaal.cancel');

                echo 'redirecting ..................';
                echo '<form action="'.$this->action_link.'" method="POST" id="cashmaalForm">'; 
                echo '<input type="hidden" name="pay_method" value="">';
                echo '<input type="hidden" name="amount" value="'.$request->amount.'">';
                echo '<input type="hidden" name="currency" value="USD">';
                echo '<input type="hidden" name="succes_url" value="'.$successURL.'">';
                echo '<input type="hidden" name="cancel_url" value="'.$failURL.'">';
                echo '<input type="hidden" name="client_email" value="'.$account_email.'">';
                echo '<input type="hidden" name="web_id" value="'.$account_web_id.'">';
                echo '<input type="hidden" name="order_id" value="">';
                echo '<input type="hidden" name="addi_info" value="eg. John Domain renewal payment">';
                echo '<input type="submit" name="Submit" value="Pay With Cash-Maal">';
                echo '</form>';
                echo '<script> document.getElementById("cashmaalForm").submit(); </script>';
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
