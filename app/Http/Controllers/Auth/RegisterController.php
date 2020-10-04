<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Models\Transaction;
use Illuminate\Support\Str;
use App\Mail\UserRegistered;
use App\Models\UserReferral;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use App\Models\SettingModule;
use App\Models\UserPaymentMethod;
use App\Models\UserReferralVisit;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cookie;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

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
            'username' => ['required', 'string', 'max:255', 'unique:users'],
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
        $first_name = isset($data['first_name']) ? $data['first_name'] : '';
        $last_name = isset($data['last_name']) ? $data['last_name'] : '';
        $panelId = session('panel');
        $user =  User::create([
            'uuid' => Str::uuid(),
            'panel_id' => $panelId,
            'username' => $data['username'],
            'first_name' => $first_name,
            'last_name' => $last_name,
            'name' => $first_name.' '.$last_name,
            'skype_name' => isset($data['skype_name']) ? $data['skype_name'] : null,
            'email' => $data['email'],
            'api_key' => Str::random(20),
            'referral_key' => substr(md5(microtime()), 0, 6),
            'password' => Hash::make($data['password']),
        ]);

        if ($user) {
            activity()->disableLogging();

            $notification =  $notification = notification('Welcome', 1, $panelId);
            if ($notification) {
                if ($notification->status =='Active') {
                    Mail::to($user->email)->send(new UserRegistered($user, $notification));
                }
            }
            //Set user payment Method...
            $paymentMethods = PaymentMethod::where('panel_id', $panelId)->where('new_user_status', 'Active')->get();
            if (!empty($paymentMethods)) {
                $userPaymentMethods = [];
                foreach ($paymentMethods as $pm) {
                    $userPaymentMethods [] = [
                        'panel_id'   => $pm->panel_id,
                        'payment_id' => $pm->id,
                        'user_id'    => $user->id,
                    ];
                }
                if (!empty($userPaymentMethods)) {
                    UserPaymentMethod::insert($userPaymentMethods);
                }
            }

            //Add Free Balance for user...
            $freeBalance = SettingModule::where('panel_id', $panelId)->where('type', 'free_balance')->first();
            if (!empty($freeBalance)) {
                $transaction = Transaction::create([
                    'panel_id' => $panelId,
                    'transaction_type' => 'deposit',
                    'amount' => $freeBalance->amount,
                    'transaction_flag' => 'free_balance',
                    'user_id' => $user->id,
                    'admin_id' => null,
                    'status' => 'done',
                    'memo' => 'Get Free balance on registration',
                    'fraud_risk' => null,
                    'payment_gateway_response' => null,
                    'reseller_payment_methods_setting_id' => 0,
                ]);

                if ($transaction) {
                    $user->update(['balance' => $freeBalance->amount]);
                }
            }

            //Set referral user if referral exists...
            if (request()->cookie('referral_id') > 0) {
                $affiliate = SettingModule::where('panel_id', $panelId)->where('type', 'affiliate')->first();
                if (!empty($affiliate)) {
                    UserReferral::create([
                        'panel_id' => $panelId,
                        'referral_id' => request()->cookie('referral_id'),
                        'user_id' => $user->id,
                        'commission_rate' => round($affiliate->commission_rate),
                        'minimum_payout' => $affiliate->amount,
                    ]);
                }
                Cookie::queue(Cookie::forget('referral_id'));
                Cookie::queue(Cookie::forget('referral'));
            }
    
            return $user;
        }
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
