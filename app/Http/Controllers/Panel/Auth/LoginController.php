<?php

namespace App\Http\Controllers\Panel\Auth;

use App\Http\Controllers\Controller;
use App\Models\SettingGeneral;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
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
        $panelId = session('panel');
        $setting = SettingGeneral::where('panel_id', $panelId)->first();
        return view('panel.auth.login', compact('setting'));
    }

    public function login(Request $request){
        $this->validate($request, [
            'email' => 'required',
            'password' => 'required|min:8'
        ]);

        //attempt to log the admin in
        if (Auth::guard('panelAdmin')->attempt(['email' => $request->email, 'password' => $request->password, 'panel_id' => session('panel')], $request->remember)) {
            return redirect()->intended(route('admin.panel.dashboard'));
        } else {
            return redirect()->back()->with('Input', $request->only('email', 'remember'))->with('error', 'Admin Login invalid !!');
        }
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
