<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PerfectMoneyController extends Controller
{
    private $action_link  = 'https://perfectmoney.is/api/step1.asp';
    private $payment_method_id = 4;
    public function getPaymentProcsseded()
    {
        
    }
}
