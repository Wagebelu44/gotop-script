<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use CoinbaseCommerce\ApiClient;
use CoinbaseCommerce\Resources\Charge;
use Illuminate\Http\Request;

class CoinbaseController extends Controller
{
    public function install(Request $request)
    {
        ApiClient::init('b2c23a6a-4ab2-43a1-8fbb-458a26cc038f');
        $chargeData = [
            'name' => auth()->user()->id,
            'description' => 'Payment to GoFans app',
            'local_price' => [
                'amount' =>  100, //$request->CoinBaseAmount,
                'currency' => 'USD'
            ],
            'pricing_type' => 'fixed_price',
            "redirect_url" => route('coinbase.success'),
            "cancel_url" => route('coinbase.failed'),
        ];
        $charges = Charge::create($chargeData);
        $d =  $charges['addresses'];
        $hostedUrl =  $charges['hosted_url'];
        echo 'redirecting ..................';
        echo '<form action="'.$hostedUrl.'" method="get" id="coinbaseForm"></form>';
        echo '<script>
               document.getElementById("coinbaseForm").submit();
            </script>';
        dd($d,$charges);
    }
    public function success(Request $request)
    {
        dd("dss",$request->all());
    }
    public function failed(Request $request)
    {
        dd("dff",$request->all());
    }
}
