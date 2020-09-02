<?php

namespace App\Http\Controllers\Panel\Setting;

use App\Http\Controllers\Controller;
use App\Models\GlobalPaymentMethod;
use App\Models\SettingBonuse;
use Illuminate\Http\Request;
use Validator;
use Auth;

class BonusesController extends Controller
{

    public function index()
    {
        $bonuses = SettingBonuse::with('globalPaymentMethod')->where('panel_id', Auth::user()->panel_id)->get();
        $methodsName = GlobalPaymentMethod::get();
        return view('panel.settings.bonuses', compact('bonuses', 'methodsName'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'bonus_amount'              => 'required',
            'global_payment_method_id'  => 'required|integer',
            'deposit_from'              => 'required',
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
    }

    public function edit($id)
    {
        $editBonus = SettingBonuse::where('panel_id', Auth::user()->panel_id)->where('id',$id)->first();
        return response()->json([
            'status' => 'success',
            'data' => $editBonus,
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'bonus_amount'              => 'required',
            'global_payment_method_id'  => 'required|integer',
            'deposit_from'              => 'required',
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
    }

    public function destroy($id)
    {
        //
    }
}
