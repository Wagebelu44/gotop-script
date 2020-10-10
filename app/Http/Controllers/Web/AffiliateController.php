<?php

namespace App\Http\Controllers\Web;

use App\User;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\SettingModule;
use App\Mail\ManualPayoutPlaced;
use App\Models\UserReferralAmount;
use App\Models\UserReferralPayout;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

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
                        'global_payment_method_id' => 0,
                    ]);
    
                    if ($transaction) {
                        $user = User::find(Auth::user()->id);
                        $user->update(['balance' => ($user->balance+$payout->amount)]);
    
                        return redirect()->back()->with('success', 'Affiliate payout amount successfully added in your balance.');
                    }
                    return redirect()->back()->with('success', "Affiliate payout amount request sent to admin. Please wait for admin approval!");                
                }

                //Payout mail...
                $staffmails = staffEmails('new_manual_payout', auth()->user()->panel_id);
                if (count($staffmails)>0) {
                    $notification =  $notification = notification('New manual payout', 2, auth()->user()->panel_id);
                    if ($notification) {
                        if ($notification->status =='Active') {
                            Mail::to($staffmails)->send(new ManualPayoutPlaced($notification));
                        }
                    }
                }

                return redirect()->back()->with('success', 'Affiliate payout amount request sent to admin. Please wait for admin approval.');
            }

            return redirect()->back()->with('error', "Payout process failed for unknown reason!");
        } else {
            return redirect()->back()->with('error', "Minimum payout amount is ".$affiliate->amount."!");
        }
    }
}
