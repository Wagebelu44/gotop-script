<?php

namespace App\Http\Controllers\Panel\Setting;

use App\Http\Controllers\Controller;
use App\Models\SettingModule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ModuleController extends Controller
{
    public function index()
    {
        return view('panel.settings.modules');
    }

    public function update(Request $request)
    {
        $data = $request->all();
        $panelId = Auth::user()->panel_id;
        if ($request->type === 'affiliate') {
            SettingModule::updateOrCreate(
                ['panel_id' => $panelId, 'type' => 'affiliate'],
                [
                    'commission_rate' => $request->commission_rate,
                    'amount'          => $request->amount,
                    'approve_payout'  => $request->approve_payout,
                    'updated_by'      => Auth::user()->id,
                ]
            );
            return redirect()->back()->with('success', 'Affiliate Setting update successfully!');
        }

        if ($request->type === 'child_panels') {
            SettingModule::updateOrCreate(
                ['panel_id' => $panelId, 'type' => 'child_panels'],
                [
                    'amount'          => $request->amount,
                    'updated_by'      => Auth::user()->id,
                ]
            );
            return redirect()->back()->with('success', 'Child panels Setting update successfully!');
        }
        if ($request->type === 'free_balance') {
            SettingModule::updateOrCreate(
                ['panel_id' => $panelId, 'type' => 'free_balance'],
                [
                    'amount'          => $request->amount,
                    'updated_by'      => Auth::user()->id,
                ]
            );
            return redirect()->back()->with('success', 'Free balance Setting update successfully!');
        }
    }

    public function getModuleData(Request $request)
    {
        $data = SettingModule::where('panel_id', Auth::user()->panel_id)->where('type', $request->type)->first();
        return response()->json([
            'status' => 'success',
            'data' => $data
        ], 200);
    }
}
