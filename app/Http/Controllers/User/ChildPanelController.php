<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\UserChildPanel;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ChildPanelController extends Controller
{

    public function store(Request $request)
    {
        $data = $request->all();
        $validate = Validator::make($data, [
            'domain'   => 'required',
            'currency' => 'required',
            'email'    => 'required|unique:user_child_panels',
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        if ($validate->fails()) {
            return redirect()->back()->withErrors($validate)->withInput();
        }

        $user = User::find(Auth::user()->id);
        if ($user->balance > "25"){
            $child = UserChildPanel::create([
                'panel_id' => Auth::user()->panel_id,
                'user_id'  => Auth::user()->id,
                'domain'   => $request->domain,
                'currency' => $request->currency,
                'email'    => $request->email,
                'password' => bcrypt($request->password),
                'price'    => "25.00",
            ]);

            if ($child){
                $user->balance = $user->balance - "25.00";
                $user->save();
                Transaction::create([
                    'transaction_type' => 'withdraw',
                    'amount' => "25.00",
                    'transaction_flag' => 'child_panel_create',
                    'user_id' => Auth::user()->id,
                    'admin_id' => null,
                    'status' => 'done',
                    'memo' => 'Child panel created',
                    'fraud_risk' => null,
                    'payment_gateway_response' => null,
                    'reseller_payment_methods_setting_id' => 0,
                    'panel_id' => Auth::user()->panel_id,
                ]);
            }

            return redirect()->back()->with('success', 'Child save successfully');
        }else{
            return redirect()->back()->with('error', "You have't enough balance");
        }
    }

}
