<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\PanelAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function profile(){
        return view('panel.profile');
    }

    public function passwordUpdate(Request $request){
        $request->validate([
            'password' => 'required|string|min:8',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        try {
            $authId = Auth::guard('admin')->id();
            $admin = PanelAdmin::find($authId);
            if (!Hash::check($request->password, $admin->password)) {
                return redirect()->back()->withErrors(['password' => 'Current password does not match!']);
            }
            $data['password'] = Hash::make($request->password);
            Admin::find($authId)->update($data);

            return redirect()->back()->withSuccess('Password updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
