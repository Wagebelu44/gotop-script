<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'paypal-ipn',
        '/payment/*',
        '/payment/add-funds/paypal/ipn',
        '/payment/add-funds/bitcoin/bit-ipn',
        '/payopcallback',
    ];
}
