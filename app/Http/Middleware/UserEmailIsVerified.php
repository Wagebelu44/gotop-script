<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Auth;

class UserEmailIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $redirectToRoute = null)
    {
        if (Auth::check()) {
            if (Auth::user()->email_verified_at == null) {
                if (!$request->user('web') ||
                ($request->user('web') instanceof MustVerifyEmail &&
                !$request->user('web')->hasVerifiedEmail())) {
                return $request->expectsJson()
                ? abort(403, 'Your email address is not verified.')
                //: Redirect::route($redirectToRoute ?: 'verification.notice');
                : redirect('email-verify');
                }
            }
        }
        return $next($request);
    }
}
