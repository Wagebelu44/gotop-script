<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\User\PayPalController;

class PaymentController extends Controller
{
    public function makePayment(Request $request)
    {
        if ($request->payment_method == 1) {
            $paypal = new PayPalController;
            return $paypal->store($request);
        }
    }
}
