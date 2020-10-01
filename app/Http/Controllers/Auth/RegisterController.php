<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SettingModule;
use App\Models\UserReferral;
use App\Models\UserReferralVisit;
use App\Providers\RouteServiceProvider;
use App\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
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
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        $panelId = session('panel');
        $user =  User::create([
            'uuid' => Str::uuid(),
            'panel_id' => $panelId,
            'name' => $data['name'],
            'username' => Str::slug($data['name']),
            'email' => $data['email'],
            'api_key' => Str::random(20),
            'referral_key' => substr(md5(microtime()), 0, 6),
            'password' => Hash::make($data['password']),
        ]);

        if (request()->cookie('referral_id') > 0 && $user) {
            $aff = SettingModule::where('panel_id', $panelId)->where('type', 'affiliate')->first();
            UserReferral::create([
                'panel_id' => $panelId,
                'referral_id' => request()->cookie('referral_id'),
                'user_id' => $user->id,
                'commission_rate' => round($aff->commission_rate),
                'minimum_payout' => $aff->amount,
            ]);
            Cookie::queue(Cookie::forget('referral_id'));
            Cookie::queue(Cookie::forget('referral'));
        }

        return $user;
    }

    public function referralLink(Request $request, $code)
    {
        $user = User::where('referral_key', $code)->first();
        if ($request->cookie('referral') == null) {
            Cookie::queue(Cookie::make('referral', 'yes', 525600)); //1 Year ((60 minutes * 24 hours) * 365)
            UserReferralVisit::create([
                'panel_id' => session('panel'),
                'referral_id' => $user->id,
                'visitor_ip' => $request->ip(),
            ]);
        }

        if (!$request->cookie('referral_id')) {
            Cookie::queue(Cookie::make('referral_id', $user->id, 1440)); //1 Days (60 minutes * 24 hours)
        }
        return redirect('/');
    }
}
