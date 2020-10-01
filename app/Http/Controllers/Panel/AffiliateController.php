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

class AffiliateController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::user()->can('see affiliate')) {
            $status = 'All';

            $sql = UserChildPanel::where('panel_id', Auth::user()->panel_id);
            if (isset($request->status)) {
                $status = $request->status;
                $sql->where('status', $request->status);
            }
            
            if (isset($request->search)) {
                $status = null;
                $sql->where('domain', 'like', '%' . $request->search . '%')
                    ->orWhere('status', 'like', '%' . $request->search . '%');
            }
            
            $data = $sql->orderBy('id', 'DESC')->get();
            return view('panel.affiliate.affiliate', compact('data', 'status'));
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
