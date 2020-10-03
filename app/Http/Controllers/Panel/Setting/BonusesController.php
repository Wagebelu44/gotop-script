<?php

namespace App\Http\Controllers\Panel\Setting;

use App\Http\Controllers\Controller;
use App\Models\G\GlobalPaymentMethod;
use App\Models\SettingBonuse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BonusesController extends Controller
{

    public function index()
    {
        if (Auth::user()->can('bonus setting')) {
            $bonuses = SettingBonuse::with('globalPaymentMethod')->where('panel_id', Auth::user()->panel_id)->get();
            $methodsName = GlobalPaymentMethod::get();
            return view('panel.settings.bonuses', compact('bonuses', 'methodsName'));
        } else {
            return view('panel.permission');
        }
    }

    public function store(Request $request)
    {
        if (Auth::user()->can('bonus setting')) {
            $this->validate($request, [
                'bonus_amount'              => 'required|numeric|between:0,999.99',
                'global_payment_method_id'  => 'required|integer',
                'deposit_from'              => 'required|numeric|between:0,99999.99',
                'status'                    => 'required',
            ]);

            SettingBonuse::create([
                'panel_id'                 => Auth::user()->panel_id,
                'bonus_amount'             => $request->bonus_amount,
                'global_payment_method_id' => $request->global_payment_method_id,
                'deposit_from'             => $request->deposit_from,
                'status'                   => $request->status,
                'created_by'               => Auth::user()->id,
            ]);

            return redirect()->back()->with('success', 'Bonuses has been successfully created');
        } else {
            return view('panel.permission');
        }
    }

    public function edit($id)
    {
        if (Auth::user()->can('bonus setting')) {
            $editBonus = SettingBonuse::where('panel_id', Auth::user()->panel_id)->where('id',$id)->first();
            return response()->json([
                'status' => 'success',
                'data' => $editBonus,
            ], 200);
        } else {
            return view('panel.permission');
        }
    }

    public function update(Request $request, $id)
    {
        if (Auth::user()->can('bonus setting')) {
            $this->validate($request, [
                'bonus_amount'              => 'required|numeric|between:0,999.99',
                'global_payment_method_id'  => 'required|integer',
                'deposit_from'              => 'required|numeric|between:0,99999.99',
                'status'                    => 'required',
            ]);

            SettingBonuse::find($id)->update([
                'panel_id'                 => Auth::user()->panel_id,
                'bonus_amount'             => $request->bonus_amount,
                'global_payment_method_id' => $request->global_payment_method_id,
                'deposit_from'             => $request->deposit_from,
                'status'                   => $request->status,
                'updated_by'               => Auth::user()->id,
            ]);

            return redirect()->back()->with('success', 'Bonuses has been successfully updated');
        } else {
            return view('panel.permission');
        }
    }

}
