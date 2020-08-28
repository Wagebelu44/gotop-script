<?php

namespace App\Http\Controllers\PanelAdmin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::DASHBOARD;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest:panelAdmin')->except('logout');
    }

    public function showLoginForm()
    {
        return view('panel.auth.login');
    }

    protected function guard()
    {
        return Auth::guard('panelAdmin');
    }

    public function logout()
    {
        auth('panelAdmin')->logout();
        return redirect()->route('panel.login');
    }
}
