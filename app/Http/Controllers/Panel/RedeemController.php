<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\AccountStatus;
use App\Models\Order;
use App\Models\Redeem;
use App\Models\Transaction;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedeemController extends Controller
{

    public function store(Request $request)
    {
        $totalSpent = Order::where('panel_id', Auth::user()->panel_id)->where('user_id', $request->user_id)->sum('charges');
        $accountStatusData = AccountStatus::where('panel_id', Auth::user()->panel_id)->where('minimum_spent_amount', '>=', $totalSpent)->orderBy('minimum_spent_amount', 'ASC')->first();
        if (!empty($accountStatusData)){
            $redeemSpent = Redeem::where('panel_id', Auth::user()->panel_id)->where('user_id', $request->user_id)->sum('spent_amount');
            $redeemAmount = ((($totalSpent-$redeemSpent)*$accountStatusData->point)/100);
        } else {
            return redirect()->back()->with('error', "Sorry insufficient your balance !!");
        }

        if ($redeemAmount > 0){
            $redeem = Redeem::create([
                'panel_id' => Auth::user()->panel_id,
                'user_id' => $request->user_id,
                'account_status' => $accountStatusData->name,
                'redeem_point' => $accountStatusData->point,
                'spent_amount' => $totalSpent,
                'redeem_amount' => $redeemAmount,
                'created_by' => Auth::user()->id,
            ]);

            if ($redeem) {
                $transaction = Transaction::create([
                    'transaction_type' => 'deposit',
                    'amount' => $redeemAmount,
                    'transaction_flag' => 'redeem',
                    'user_id' => $request->user_id,
                    'admin_id' => Auth::user()->id,
                    'status' => 'done',
                    'memo' => '',
                    'fraud_risk' => null,
                    'payment_gateway_response' => null,
                    'global_payment_method_id' => 0,
                    'panel_id' => auth()->user()->panel_id,
                ]);

                if ($transaction) {
                    $user = User::find($request->user_id);
                    $user->balance += $redeemAmount;
                    $user->save();
                }
            }
            return redirect()->back()->with('success', "Redeem amount $$redeemAmount save successfully");
        }else{
            return redirect()->back()->with('error', "Sorry insufficient your balance !!");
        }
    }
}
