<?php

namespace App\Http\Controllers\Web;

use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use App\Models\Transaction;
use App\Models\UserChildPanel;
use App\Models\SettingModule;
use App\Models\UserReferralAmount;
use App\Models\UserReferralPayout;
use App\User;

class AffiliateController extends Controller
{
    public function payout(Request $request)
    {
        $affiliate = SettingModule::where('panel_id', Auth::user()->panel_id)->where('type', 'affiliate')->first();
        if (empty($affiliate)) {
            return redirect()->back()->with('error', "Affiliate payout is disabled!");
        }
            
        $total_earnings = UserReferralAmount::where('panel_id', Auth::user()->panel_id)->where('referral_id', Auth::user()->id)->sum('amount');
        $total_payouts = UserReferralPayout::where('panel_id', Auth::user()->panel_id)->where('referral_id', Auth::user()->id)->sum('amount');
        $unpaid_earnings = ($total_earnings-$total_payouts);
        if ($unpaid_earnings >= $affiliate->amount) {
            $payout = UserReferralPayout::create([
                'panel_id' => Auth::user()->panel_id,
                'referral_id' => Auth::user()->id,
                'amount' => $unpaid_earnings,
                'date' => date('Y-m-d'),
                'mode' => $affiliate->approve_payout,
                'status' => ($affiliate->approve_payout == 'auto') ? 'Approved' : 'Pending',
            ]);

            if ($payout) {
                if ($affiliate->approve_payout == 'auto') {
                    $transaction = Transaction::create([
                        'panel_id' => Auth::user()->panel_id,
                        'transaction_type' => 'deposit',
                        'amount' => $payout->amount,
                        'transaction_flag' => 'affiliate',
                        'user_id' => Auth::user()->id,
                        'admin_id' => null,
                        'status' => 'done',
                        'memo' => 'Affiliate payout',
                        'fraud_risk' => null,
                        'payment_gateway_response' => null,
                        'reseller_payment_methods_setting_id' => 0,
                    ]);
    
                    if ($transaction) {
                        $user = User::find(Auth::user()->id);
                        $user->update(['balance' => ($user->balance+$payout->amount)]);
    
                        return redirect()->back()->with('success', 'Affiliate payout amount successfully added in your balance.');
                    }
                    return redirect()->back()->with('success', "Affiliate payout amount request sent to admin. Please wait for admin approval!");                
                }

                //Payout mail...

                return redirect()->back()->with('success', 'Affiliate payout amount request sent to admin. Please wait for admin approval.');
            }

            return redirect()->back()->with('error', "Payout process failed for unknown reason!");
        } else {
            return redirect()->back()->with('error', "Minimum payout amount is ".$affiliate->amount."!");
        }
    }
}
