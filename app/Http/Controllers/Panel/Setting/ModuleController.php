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
        $panelId = 1;
        if ($request->type === 'affiliate') {
            SettingModule::updateOrCreate(
                ['panel_id' => $panelId, 'type' => 'affiliate'],
                [
                    'commission_rate' => $data['commission_rate'],
                    'amount'          => $data['amount'],
                    'approve_payout'  => $data['approve_payout'],
                    'updated_by'      => auth()->guard('panelAdmin')->id(),
                ]
            );
            return redirect()->back()->with('success', 'Affiliate Setting update successfully!');
        }

        if ($request->type === 'child_panels') {
            SettingModule::updateOrCreate(
                ['panel_id' => $panelId, 'type' => 'child_panels'],
                [
                    'amount'          => $data['amount'],
                    'updated_by'      => auth()->guard('panelAdmin')->id(),
                ]
            );
            return redirect()->back()->with('success', 'Child panels Setting update successfully!');
        }
        if ($request->type === 'free_balance') {
            SettingModule::updateOrCreate(
                ['panel_id' => $panelId, 'type' => 'free_balance'],
                [
                    'amount'          => $data['amount'],
                    'updated_by'      => auth()->guard('panelAdmin')->id(),
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
