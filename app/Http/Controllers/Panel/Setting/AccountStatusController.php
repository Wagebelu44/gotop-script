<?php

namespace App\Http\Controllers\Panel\Setting;

use App\Http\Controllers\Controller;
use App\Models\AccountStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountStatusController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('account status setting')) {
            $page = 'index';
            $data = AccountStatus::where('panel_id', Auth::user()->panel_id)->orderBy('id', 'desc')->get();
            return view('panel.settings.account-status', compact('page', 'data'));
        } else {
            return view('panel.permission');
        }
    }

    public function create()
    {
        if (Auth::user()->can('account status setting')) {
            $page = 'create';
            $statusKeys = [];
            $pointKeys = [];
            return view('panel.settings.account-status', compact('page','statusKeys', 'pointKeys'));
        } else {
            return view('panel.permission');
        }
    }

    public function store(Request $request)
    {
        if (Auth::user()->can('account status setting')) {
            $this->validate($request, [
                'name' => 'required',
                'minimum_spent_amount' => 'required|numeric',
                'point' => 'required|numeric',
            ]);

            AccountStatus::create([
                'panel_id'              => Auth::user()->panel_id,
                'name'                  => $request->name,
                'minimum_spent_amount'  => $request->minimum_spent_amount,
                'point'                 => $request->point,
                'status_keys'           => isset($request->status_keys) ? json_encode($request->status_keys):'',
                'point_keys'            => isset($request->point_keys) ? json_encode($request->point_keys):'',
                'created_by'            => Auth::user()->id,
            ]);

            return redirect()->back()->with('success', 'Account status save successfully !!');
        } else {
            return view('panel.permission');
        }
    }

    public function edit($id)
    {
        if (Auth::user()->can('account status setting')) {
            $page = 'edit';
            $data = AccountStatus::where('panel_id', Auth::user()->panel_id)->where('id', $id)->first();
            if (empty($data)) {
                return redirect()->route('admin.setting.account-status.index');
            }
            $statusKeys = json_decode($data->status_keys, true);
            $pointKeys = json_decode($data->point_keys, true);
            return view('panel.settings.account-status', compact('data', 'page', 'statusKeys', 'pointKeys'));
        } else {
            return view('panel.permission');
        }
    }

    public function update(Request $request, $id)
    {
        if (Auth::user()->can('account status setting')) {
            $this->validate($request, [
                'name' => 'required',
                'minimum_spent_amount' => 'required|numeric',
                'point' => 'required|numeric',
            ]);

            AccountStatus::find($id)->update([
                'name'                  => $request->name,
                'minimum_spent_amount'  => $request->minimum_spent_amount,
                'point'                 => $request->point,
                'status_keys'           => isset($request->status_keys) ? json_encode($request->status_keys):'',
                'point_keys'            => isset($request->point_keys) ? json_encode($request->point_keys):'',
                'updated_by'            => Auth::user()->id,
            ]);

            return redirect()->back()->with('success', 'Account status update successfully !!');
        } else {
            return view('panel.permission');
        }
    }

    public function destroy($id)
    {
        if (Auth::user()->can('account status setting')) {
            $data = AccountStatus::where('panel_id', Auth::user()->panel_id)->where('id', $id)->first();
            if (empty($data)) {
                return redirect()->route('admin.setting.account-status.index');
            }
            $data->delete();
            return redirect(route('admin.setting.account-status.index'))->with('success', 'Account status delete successfully !!');
        } else {
            return view('panel.permission');
        }
    }
}
