<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\UserChildPanel;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class ChildPanelController extends Controller
{
    public function index(Request $request){
        if (isset($request->status)){
            $status = $request->status;
            $data = UserChildPanel::where('panel_id', Auth::user()->panel_id)
                ->where('status', $request->status)
                ->orderBy('id')->get();
        }elseif ($request->search){
            $status = null;
            $data = UserChildPanel::where('panel_id', Auth::user()->panel_id)
                ->where('domain', 'like', '%' . $request->search . '%')
                ->orWhere('status', 'like', '%' . $request->search . '%')
                ->orderBy('id')->get();
        }else{
            $status = 'All';
            $data = UserChildPanel::where('panel_id', Auth::user()->panel_id)->orderBy('id')->get();
        }
        return view('panel.child_panel.index', compact('data', 'status'));
    }

    public function cancelAndRefund($childId){
        $child = UserChildPanel::find($childId);
        $child->status = 'Canceled';
        $child->save();

        $amount = $child->price;
        if ($child){
            $transaction = Transaction::create([
                'panel_id' => Auth::user()->panel_id,
                'transaction_type' => 'deposit',
                'amount' => $amount,
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
                $user->balance = $user->balance + $amount;
                $user->save();
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
                                    return redirect()->back()->with('success', 'Child panel created successfully. Wait for activation.');
                                } else {
                                    return redirect()->back()->with('error', "Child panel saving failed for server error!");
                                }
                            } else {
                                return redirect()->back()->with('error', "Child panel saving failed for server error!");
                            }
                        } else {
                            return redirect()->back()->with('error', "Child panel saving failed for server error!");
                        }
                    } catch(Exception $e) {
                        return redirect()->back()->with('error', "Child panel saving failed for server error!");
                    }
                }

                return redirect()->back()->with('success', 'Child panel created successfully. Wait for activation.');
            } else {
                return redirect()->back()->with('error', "Child panel canceled successfully!");
            }
        }

    }
}
