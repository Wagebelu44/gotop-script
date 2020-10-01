<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\User\PayPalController;
use App\Http\Controllers\Payment\PerfectMoneyController;

class PaymentController extends Controller
{
    public function makePayment(Request $request)
    {
        if ($request->payment_method == 1) {
            $paypal = new PayPalController;
            return $paypal->store($request);
        }
        elseif ($request->payment_method == 2) {
            $pm = new PerfectMoneyController;
            return $pm->getPaymentProcsseded($request);
        }
        return redirect()->back()->withError('No Payment method found to requested one');
    }
}
