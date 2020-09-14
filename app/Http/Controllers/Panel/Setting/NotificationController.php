<?php

namespace App\Http\Controllers\Panel\Setting;

use App\Http\Controllers\Controller;
use App\Models\SettingNotification;
use App\Models\StaffEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('notification setting')) {
            $page = 'index';
            $data = SettingNotification::where('panel_id', Auth::user()->panel_id)->get();
            $staffEmails = StaffEmail::where('panel_id', Auth::user()->panel_id)->get();
            return view('panel.settings.notifications', compact('data', 'page', 'staffEmails'));
        } else {
            return view('panel.permission');
        }
    }

    public function edit($id){
        if (Auth::user()->can('notification setting')) {
            $page = 'edit';
            $data = SettingNotification::where('panel_id', Auth::user()->panel_id)->where('id', $id)->first();
            if (empty($data)) {
                return redirect()->route('admin.setting.notification');
            }
            return view('panel.settings.notifications', compact('data', 'page'));
        } else {
            return view('panel.permission');
        }
    }

    public function update(Request $request, $id){
        if (Auth::user()->can('notification setting')) {
            $this->validate($request, [
                'subject' => 'required|max:191',
                'body' => 'required',
                'status' => 'required'
            ]);

            SettingNotification::find($id)->update([
                'subject'     => $request->subject,
                'body'        => $request->body,
                'status'      => $request->status,
                'updated_by'  => Auth::user()->id,
            ]);

            return redirect()->back()->with('success', 'Notification update successfully !!');
        } else {
            return view('panel.permission');
        }
    }


}
