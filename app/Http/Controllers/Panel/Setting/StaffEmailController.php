<?php

namespace App\Http\Controllers\Panel\Setting;

use App\Http\Controllers\Controller;
use App\Models\StaffEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffEmailController extends Controller
{

    public function store(Request $request)
    {
        if (Auth::user()->can('notification setting')) {
            $this->validate($request, [
                'email' => 'required|email|max:191',
            ]);

            $checkEmail = StaffEmail::where('panel_id', Auth::user()->panel_id)->where('email', $request->email)->get();
            if (count($checkEmail) > 0) {
                return redirect()->back()->withErrors(['email' => 'This staff email already exists']);
            }

            StaffEmail::create([
                'panel_id'          => Auth::user()->panel_id,
                'email'             => $request->email,
                'payment_received'  => $request->payment_received == "on" ? '1' : '0',
                'new_manual_orders' => $request->new_manual_orders  == "on" ? '1' : '0',
                'fail_orders'       => $request->fail_orders  == "on" ? '1' : '0',
                'new_messages'      => $request->new_messages  == "on" ? '1' : '0',
                'new_manual_payout' => $request->new_manual_payout  == "on" ? '1' : '0',
                'created_by'        => Auth::user()->id,
            ]);

            return redirect()->back()->with('success', 'Staff Email save successfully');
        } else {
            return view('panel.permission');
        }
    }

    public function edit($id)
    {
        if (Auth::user()->can('notification setting')) {
            $data = StaffEmail::where('panel_id', Auth::user()->panel_id)->where('id', $id)->first();
            if (!empty($data)){
                return response()->json([
                    'status' => 'success',
                    'data'   => $data
                ], 200);
            }
        } else {
            return response()->json([
                'status' => 'error',
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        if (Auth::user()->can('notification setting')) {
            $this->validate($request, [
                'email' => 'required|max:191|unique:staff_emails,email,' . $id,
            ]);

            StaffEmail::find($id)->update([
                'email'             => $request->email,
                'payment_received'  => $request->payment_received == "on" ? '1' : '0',
                'new_manual_orders' => $request->new_manual_orders  == "on" ? '1' : '0',
                'fail_orders'       => $request->fail_orders  == "on" ? '1' : '0',
                'new_messages'      => $request->new_messages  == "on" ? '1' : '0',
                'new_manual_payout' => $request->new_manual_payout  == "on" ? '1' : '0',
                'updated_by'        => Auth::user()->id,
            ]);

            return redirect()->back()->with('success', 'Staff Email update successfully');
        } else {
            return view('panel.permission');
        }
    }

    public function destroy($id)
    {
        if (Auth::user()->can('notification setting')) {
            $data = StaffEmail::where('panel_id', Auth::user()->panel_id)->where('id', $id)->first();
            if (empty($data)) {
                return redirect()->back()->with('error', 'Please try again something went wrong !!');
            }
            $data->delete();
            return redirect()->back()->with('success', 'This email delete successfully !!');
        } else {
            return view('panel.permission');
        }
    }
}
