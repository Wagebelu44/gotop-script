<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Payment\PayPalController;
use App\Http\Controllers\Payment\WebmoneyController;
use App\Http\Controllers\Payment\PerfectMoneyController;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        if ($request->payment_method == 1) {
            $paypal = new PayPalController;
            return $paypal->store($request);
        } elseif ($request->payment_method == 4) {
            $pm = new PerfectMoneyController;
            return $pm->store($request);
        } elseif ($request->payment_method == 5) {
            $pm = new WebmoneyController;
            return $pm->store($request);
        }

        return redirect()->back()->withError('No Payment method found to requested one');
    }
}
