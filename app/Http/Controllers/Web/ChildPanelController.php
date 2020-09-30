<?php

namespace App\Http\Controllers\Web;

use App\Models\SettingModule;
use Exception;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\UserChildPanel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use App\User;

class ChildPanelController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->all();
        $validate = Validator::make($data, [
            'domain'   => 'required|max:255|unique:user_child_panels|regex:/^(?!\-)(?:(?:[a-zA-Z\d][a-zA-Z\d\-]{0,61})?[a-zA-Z\d]\.){1,126}(?!\d+)[a-zA-Z\d]{1,63}$/i',
            'currency' => 'required',
            'email'    => 'required|email|unique:user_child_panels',
            'password' => 'required|string|min:8|confirmed',
        ]);
        if ($validate->fails()) {
            return redirect()->back()->withErrors($validate)->withInput();
        }

        $childSelling = SettingModule::select('amount')->where('panel_id', Auth::user()->panel_id)->where('type', 'child_panels')->first();
        $amount = $childSelling->amount;
        $user = User::find(Auth::user()->id);
        if ($user->balance > $amount){
            $child = UserChildPanel::create([
                'panel_id' => Auth::user()->panel_id,
                'user_id'  => Auth::user()->id,
                'domain'   => $request->domain,
                'currency' => $request->currency,
                'email'    => $request->email,
                'password' => bcrypt($request->password),
                'price'    => $amount,
                'status'   => 'Pending',
            ]);

            if ($child) {
                $transaction = Transaction::create([
                    'panel_id' => Auth::user()->panel_id,
                    'transaction_type' => 'withdraw',
                    'amount' => $amount,
                    'transaction_flag' => 'child_panel',
                    'user_id' => Auth::user()->id,
                    'admin_id' => null,
                    'status' => 'done',
                    'memo' => 'Child panel created',
                    'fraud_risk' => null,
                    'payment_gateway_response' => null,
                    'reseller_payment_methods_setting_id' => 0,
                ]);

                if ($transaction) {
                    $user->balance = $user->balance - $amount;
                    $user->save();

                    if (env('PROJECT') == 'live') {
                        try {
                            $response = Http::post(env('PROJECT_LIVE_URL').'/api/child-panel-store', [
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
                    return redirect()->back()->with('error', "Child panel saving failed for payment transaction issue!");
                }
            } else {
                return redirect()->back()->with('error', "Child panel saving failed!");
            }
        } else {
            return redirect()->back()->with('error', "You have't enough balance!");
        }
    }
}
