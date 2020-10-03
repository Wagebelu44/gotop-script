<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;
use App\Models\UserReferral;
use App\Models\UserReferralPayout;
use App\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class AffiliateController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::user()->can('see affiliate')) {
            $panelId = Auth::user()->panel_id;
            $sql = User::where('panel_id', Auth::user()->panel_id)
            ->select('users.id', 'users.username', 'users.affiliate_status', 'A.total_visits', 'B.total_earnings', 'C.total_payouts', DB::raw('(B.total_earnings-C.total_payouts) AS unpaid_earnings'), 'D.unpaid_referrals', 'E.paid_referrals', DB::raw('((A.total_visits*100)/E.paid_referrals) AS conversion_rate'))

            ->join(DB::raw("(SELECT referral_id, COUNT(id) AS total_visits FROM user_referral_visits WHERE panel_id=$panelId GROUP BY referral_id) AS A"), 'users.id', '=', 'A.referral_id')
            
            ->leftJoin(DB::raw("(SELECT referral_id, SUM(amount) AS total_earnings FROM user_referral_amounts WHERE panel_id=$panelId GROUP BY referral_id) AS B"), 'users.id', '=', 'B.referral_id')
            
            ->leftJoin(DB::raw("(SELECT referral_id, SUM(amount) AS total_payouts FROM user_referral_payouts WHERE panel_id=$panelId AND status='Approved' GROUP BY referral_id) AS C"), 'users.id', '=', 'C.referral_id')
            
            ->leftJoin(DB::raw("(SELECT X.referral_id, COUNT(X.id) AS unpaid_referrals FROM user_referrals AS X LEFT JOIN(SELECT user_id FROM transactions WHERE transaction_flag='payment_gateway' AND status='done' GROUP BY user_id) AS Y ON X.user_id=Y.user_id WHERE Y.user_id IS NULL AND X.panel_id=$panelId GROUP BY X.referral_id) AS D"), 'users.id', '=', 'D.referral_id')
            
            ->leftJoin(DB::raw("(SELECT X.referral_id, COUNT(X.id) AS paid_referrals FROM user_referrals AS X INNER JOIN(SELECT user_id FROM transactions WHERE transaction_flag='payment_gateway' AND status='done' GROUP BY user_id) AS Y ON X.user_id=Y.user_id WHERE X.panel_id=$panelId GROUP BY X.referral_id) AS E"), 'users.id', '=', 'E.referral_id');

            if (isset($request->q)) {
                $sql->where('users.id', $request->q);
                $sql->orWhere('users.username', $request->q);
            }
            
            $affiliates = $sql->orderBy('users.id', 'DESC')->paginate(50);
            return view('panel.affiliate.affiliate', compact('affiliates'));
        } else {
            return view('panel.permission');
        }
    }

    public function affiliateStatus(Request $request)
    {
        if (Auth::user()->can('change affiliate status')) {
            $credentials = $request->only('user_id', 'affiliate_status');
            $rules = [
                'user_id'           => 'required',
                'affiliate_status'  => 'required|string',
            ];
            $validator = Validator::make($credentials, $rules);
            if ($validator->fails()) {
                return response()->json(['status' => false, 'errors'=> implode(", " , $validator->messages()->all())], 200);
            }

            try {
                User::where('panel_id', Auth::user()->panel_id)->where('id', $request->user_id)->update([
                    'affiliate_status' => $request->affiliate_status
                ]);
                return response()->json(['status' => true], 200);
            } catch (\Exception $e) {
                return response()->json(['status' => false, 'errors'=> $e->getMessage()], 200);
            }
        } else {
            return response()->json(['status' => false, 'errors'=> 'permission denied!'], 200);
        }
    }

    public function referrals(Request $request)
    {
        if (Auth::user()->can('see affiliate referrals')) {
            $panelId = Auth::user()->panel_id;

            $sql = UserReferral::select('user_referrals.user_id', 'user_referrals.referral_id', 'A.commissions', 'A.payments')->with(['referral', 'user'])->where('panel_id', Auth::user()->panel_id)

            ->leftJoin(DB::raw("(SELECT user_id, SUM(fund_amount) AS payments, SUM(amount) AS commissions FROM user_referral_amounts WHERE panel_id=$panelId GROUP BY user_id) AS A"), 'user_referrals.user_id', '=', 'A.user_id');

            if (isset($request->q)) {
                $sql->where('user_referrals.user_id', $request->q)
                ->orWhereHas('referral', function($q) use($request) {
                    $q->where('username', $request->q);
                })
                ->orWhereHas('user', function($q) use($request) {
                    $q->where('username', $request->q);
                });
            }
            $referrals = $sql->orderBy('user_referrals.id', 'DESC')->paginate(50);

            return view('panel.affiliate.referral', compact('referrals'));
        } else {
            return view('panel.permission');
        }
    }

    public function payouts(Request $request)
    {
        if (Auth::user()->can('see affiliate payouts')) {
            $panelId = Auth::user()->panel_id;

            $sql = UserReferralPayout::with(['referral'])->where('panel_id', Auth::user()->panel_id);

            if (isset($request->q)) {
                $sql->where('user_id', $request->q)
                ->orWhereHas('referral', function($q) use($request) {
                    $q->where('username', $request->q);
                });
            }
            $payouts = $sql->orderBy('id', 'DESC')->paginate(50);

            return view('panel.affiliate.payout', compact('payouts'));
        } else {
            return view('panel.permission');
        }
    }

    public function affiliatePayout(Request $request)
    {
        if (Auth::user()->can('approve or reject affiliate payout')) {
            $credentials = $request->only('id', 'status');
            $rules = [
                'id'           => 'required',
                'status'  => 'required|string',
            ];
            $validator = Validator::make($credentials, $rules);
            if ($validator->fails()) {
                return response()->json(['status' => false, 'errors'=> implode(", " , $validator->messages()->all())], 200);
            }

            try {
                $payout = UserReferralPayout::where('panel_id', Auth::user()->panel_id)->where('id', $request->id)->first();
                if (empty($payout)) {
                    return response()->json(['status' => false, 'errors'=> 'payout not found!'], 200);
                }                

                if ($request->status == 'Approved') {
                    $transaction = Transaction::create([
                        'panel_id' => Auth::user()->panel_id,
                        'transaction_type' => 'deposit',
                        'amount' => $payout->amount,
                        'transaction_flag' => 'affiliate',
                        'user_id' => $payout->referral_id,
                        'admin_id' => null,
                        'status' => 'done',
                        'memo' => 'Affiliate payout',
                        'fraud_risk' => null,
                        'payment_gateway_response' => null,
                        'reseller_payment_methods_setting_id' => 0,
                    ]);
    
                    if ($transaction) {
                        $user = User::find($payout->referral_id);
                        $user->update(['balance' => ($user->balance+$payout->amount)]);
                        
                        $payout->update(['status' => $request->status]);
    
                        return response()->json(['status' => true, 'message' => 'Affiliate payout amount successfully added in user balance.'], 200);
                    }

                    return response()->json(['status' => false, 'errors' => 'Affiliate payout status changing failed!'], 200);

                } else if ($request->status == 'Canceled') {
                    if ($payout->status == 'Approved') {
                        $transaction = Transaction::create([
                            'panel_id' => Auth::user()->panel_id,
                            'transaction_type' => 'withdraw',
                            'amount' => $payout->amount,
                            'transaction_flag' => 'affiliate',
                            'user_id' => $payout->referral_id,
                            'admin_id' => null,
                            'status' => 'done',
                            'memo' => 'Affiliate payout',
                            'fraud_risk' => null,
                            'payment_gateway_response' => null,
                            'reseller_payment_methods_setting_id' => 0,
                        ]);
        
                        if ($transaction) {
                            $user = User::find($payout->referral_id);
                            $user->update(['balance' => ($user->balance-$payout->amount)]);
                        
                            $payout->update(['status' => $request->status]);
        
                            return response()->json(['status' => true, 'message' => 'Affiliate payout request is canceled!'], 200);
                        }

                        return response()->json(['status' => false, 'errors' => 'Affiliate payout status changing failed!'], 200);
                    }
                        
                    $payout->update(['status' => $request->status]);

                    return response()->json(['status' => true, 'message' => 'Affiliate payout request is canceled!'], 200);
                }

            } catch (\Exception $e) {
                return response()->json(['status' => false, 'errors'=> $e->getMessage()], 200);
            }
        } else {
            return response()->json(['status' => false, 'errors'=> 'permission denied!'], 200);
        }
    }
}
