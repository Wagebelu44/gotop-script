<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StripeController extends Controller
{
      // Stripe payment id in table `payment_methods`
      private $payment_method_id = 2;
      private $stripe_secret = '';
      private $stripe_key = '';
  
      /* public function __construct()
      {
          $this->stripe_secret = PaymentMethod::where(['config_key' => 'stripe_secret'])->first()->config_value;
          $this->stripe_key = PaymentMethod::where(['config_key' => 'stripe_key'])->first()->config_value;
      } */
  
      public function showForm(Request $request)
      {
          // check if payment method is not enabled then abort
          $paymentMethod = PaymentMethod::where(['id' => $this->payment_method_id, 'status' => 'ACTIVE'])->first();
          if (is_null($paymentMethod)) {
              abort(403);
          }
  
          // User have assigned payment methods?
          if (empty(Auth::user()->enabled_payment_methods)) {
              abort(403);
          }
          // Get users enabled payment methods & see if this method is enabled for him.
          $enabled_payment_methods = explode(',', Auth::user()->enabled_payment_methods);
          if (!in_array($this->payment_method_id, $enabled_payment_methods)) {
              abort(403);
          }
  
          return view('payments.stripe', ['stripe_key' => $this->stripe_key]);
      }
  
      public function store(Request $request)
      {
          $minimum_deposit_amount = getOption('minimum_deposit_amount');
          $validator = Validator::make($request->all(), [
              'amount' => 'required|numeric|min:' . $minimum_deposit_amount,
              'stripeToken' => 'required|string'
          ]);
  
          if ($validator->fails()) {
              return redirect()
                  ->back()
                  ->withErrors($validator)
                  ->withInput();
          }
  
          try {
              //charge stripe amount
              $stripe = Stripe::make($this->stripe_secret);
              $charge = $stripe->charges()->create([
                  'amount' => $request->input('amount'),
                  'currency' => strtolower(getOption('currency_code')),
                  'source' => $request->input('stripeToken'),
                  'description' => 'Balance Recharge',
                  'expand' => ['balance_transaction']
              ]);
  
              $fee = number_format(($charge['balance_transaction']['fee'] / 100), 2, '.', ' ');
  //            $totalAmount = $request->input('amount') - $fee;
  
              // Create payment logs
              PaymentLog::create([
                  'details' => json_encode($charge),
                  'currency_code' => strtoupper($charge['currency']),
                  'total_amount' => $request->input('amount'),
                  'payment_method_id' => $this->payment_method_id,
                  'user_id' => Auth::user()->id
              ]);
  
              // Create Transaction logs
              $transaction = [
                  'amount' => $request->input('amount'),
                  'payment_method_id' => $this->payment_method_id,
                  'details' => $charge['id'],
                  'user_id' => Auth::user()->id
              ];
  
              transaction($transaction);
  
              Session::flash('alert', __('messages.payment_success'));
              Session::flash('alertClass', 'success');
              return redirect()->back();
          } catch (\Exception $exception) {
              Session::flash('alert', $exception->getMessage());
              Session::flash('alertClass', 'danger no-auto-close');
              return redirect()->back();
          }
  
          Session::flash('alert', __('messages.payment_failed_error'));
          Session::flash('alertClass', 'danger no-auto-close');
          return redirect()->back();
      }
}
