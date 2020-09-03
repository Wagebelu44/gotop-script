<?php

namespace App\Http\Controllers\Panel\Setting;

use App\Http\Controllers\Controller;
use App\Models\SettingNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $page = 'index';
        $data = SettingNotification::where('panel_id', Auth::user()->panel_id)->get();
        if (count($data) != 8){
            SettingNotification::where('panel_id', Auth::user()->panel_id)->delete();
            SettingNotificationTableSeeder();
        }
        return view('panel.settings.notifications', compact('data', 'page'));
    }

    public function edit($id){
        $page = 'edit';
        $data = SettingNotification::where('panel_id', Auth::user()->panel_id)->where('id', $id)->first();
        if (empty($data)) {
            return redirect()->route('admin.setting.notification');
        }
        return view('panel.settings.notifications', compact('data', 'page'));
    }

    public function update(Request $request, $id){
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
    }


}
