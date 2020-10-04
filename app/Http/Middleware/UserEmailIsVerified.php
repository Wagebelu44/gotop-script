<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Auth;
use App\Models\SettingGeneral;

class UserEmailIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            if (Auth::user()->email_confirmation_status == 1) {
                if (!$request->is('email-verify') && !$request->is('logout')) { 
                    if (!$request->user('web') || !$request->user('web')->hasVerifiedEmail()) {
                        return $request->expectsJson() ? abort(403, 'Your email address is not verified.') : redirect('email-verify');
                    }
                }
            }
        }
        return $next($request);
    }
}
