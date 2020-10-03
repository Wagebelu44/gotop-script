<?php

namespace App\Http\Controllers\Panel;

use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\UserChildPanel;
use App\Models\Transaction;
use App\User;
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

            if (isset($request->status)) {
                $status = $request->status;
                $sql->where('status', $request->status);
            }
            
            $affiliates = $sql->orderBy('users.id', 'DESC')->get();
            return view('panel.affiliate.affiliate', compact('affiliates'));
        } else {
            return view('panel.permission');
        }
    }

    public function cancelAndRefund($childId)
    {
        if (Auth::user()->can('cancel and refund child-panels')) {
            $child = UserChildPanel::find($childId);
            $child->update(['status' => 'Canceled']);

            if ($child) {
                $panelCreate = false;
                if (env('PROJECT') == 'live') {
                    try {
                        $response = Http::post(env('PROJECT_LIVE_URL').'/api/child-panel-canceled', [
                            'child' => $child->toArray(),
                            'token' => env('PANLE_REQUEST_TOKEN'),
                        ]);

                        if ($response->ok()) {
                            if ($response->successful()) {
                                $data = json_decode($response->body());
                                if ($data->success) {
                                    $panelCreate = true;
                                } else {
                                    return redirect()->back()->with('error', "Child panel cancelling failed for server error!");
                                }
                            } else {
                                return redirect()->back()->with('error', "Child panel cancelling failed for server error!");
                            }
                        } else {
                            return redirect()->back()->with('error', "Child panel cancelling failed for server error!");
                        }
                    } catch(Exception $e) {
                        return redirect()->back()->with('error', "Child panel cancelling failed for server error!");
                    }
                } else {
                    $panelCreate = true;
                }

                if ($panelCreate) {
                    $transaction = Transaction::create([
                        'panel_id' => Auth::user()->panel_id,
                        'transaction_type' => 'deposit',
                        'amount' => $child->price,
                        'transaction_flag' => 'child_panel',
                        'user_id' => Auth::user()->id,
                        'admin_id' => null,
                        'status' => 'done',
                        'memo' => 'Child panel cancel refund',
                        'fraud_risk' => null,
                        'payment_gateway_response' => null,
                        'reseller_payment_methods_setting_id' => 0,
                    ]);
                    if ($transaction) {
                        $user = User::find($child->user_id);
                        $user->balance = $user->balance + $child->price;
                        $user->save();
        
                        return redirect()->back()->with('success', 'Child panel cancelled successfully.');
                    } else {
                        return redirect()->back()->with('error', "Child panel canceled successfully. But payment not deposited. Please contact with admin!");
                    }
                }
            }
        } else {
            return view('panel.permission');
        }
    }
}
