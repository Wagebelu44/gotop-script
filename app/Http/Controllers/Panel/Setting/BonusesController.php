<?php

namespace App\Http\Controllers\Panel\Setting;

use App\Http\Controllers\Controller;
use App\Models\SettingBonuse;
use Illuminate\Http\Request;
use Validator;

class BonusesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $bonuses = SettingBonuse::all();
        return view('panel.settings.bonuses', compact('bonuses'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $panelId = 1;
            $data = $request->all();
            $validator = Validator::make($data, [
                'bonus_amount'              => 'required',
                'global_payment_method_id'  => 'required|integer',
                'deposit_from'              => 'required',
                'status'                    => 'required',
            ]);
            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }
            SettingBonuse::create([
                'panel_id'                 => $panelId,
                'bonus_amount'             => $data['bonus_amount'],
                'global_payment_method_id' => $data['global_payment_method_id'],
                'deposit_from'             => $data['deposit_from'],
                'status'                   => $data['status'],
                'created_by'               => auth()->guard('panelAdmin')->id(),
            ]);
            return redirect()->back()->with('success', 'Bonuses has been successfully created');
        }catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $editBonus = SettingBonuse::where('id',$id)->first();
            return response()->json([
                'status' => 'success',
                'data' => $editBonus,
            ], 200);
        }catch (\Exception $exception){
            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $panelId = 1;
            $data = $request->all();
            $validator = Validator::make($data, [
                'bonus_amount'              => 'required',
                'global_payment_method_id'  => 'required|integer',
                'deposit_from'              => 'required',
                'status'                    => 'required',
            ]);
            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }
            SettingBonuse::find($id)->update([
                'panel_id'                 => $panelId,
                'bonus_amount'             => $data['bonus_amount'],
                'global_payment_method_id' => $data['global_payment_method_id'],
                'deposit_from'             => $data['deposit_from'],
                'status'                   => $data['status'],
                'updated_by'               => auth()->guard('panelAdmin')->id(),
            ]);
            return redirect()->back()->with('success', 'Bonuses has been successfully updated');
        }catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
