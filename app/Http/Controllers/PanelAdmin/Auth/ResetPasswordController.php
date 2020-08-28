<?php

namespace App\Http\Controllers\PanelAdmin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Foundation\Auth\ResetsPasswords;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::DASHBOARD;

    public function __construct()
    {
        $this->middleware('guest:panelAdmin');
    }

    public function showResetForm(Request $request, $token = null) {
        return view('panel.auth.passwords.reset')->with(['token' => $token, 'email' => null]);
    }


    //returns Password broker
    public function broker()
    {
        return Password::broker('panelAdmins');
    }

    //returns authentication guard
    protected function guard()
    {
        return Auth::guard('panelAdmin');
    }
}
