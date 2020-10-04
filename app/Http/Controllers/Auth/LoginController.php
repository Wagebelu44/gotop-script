<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

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
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest:web')->except('logout');
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required',
            'password' => 'required|min:8'
        ]);

        if(filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            Auth::guard('web')->attempt(['email' => $request->email, 'password' => $request->password, 'panel_id' => session('panel')], $request->remember);
        } else {
            Auth::guard('web')->attempt(['username' => $request->email, 'password' => $request->password, 'panel_id' => session('panel')], $request->remember);
        }

        if ( Auth::check() ) {
            return redirect('/new-order');
        } else {
            return redirect()->back()->with('Input', $request->only('email', 'remember'))->with('error', 'Invalid credencial!');
        }
    }

    protected function guard()
    {
        return Auth::guard('web');
    }

    public function logout()
    {
        auth('web')->logout();
        return redirect()->route('home');
    }
}
