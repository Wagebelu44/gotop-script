<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\PanelAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function profile()
    {
        return view('panel.profile');
    }

    public function passwordUpdate(Request $request)
    {
        $this->validate($request, [
            'password' => 'required|string|min:8',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $admin = PanelAdmin::find(Auth::user()->id);
        if (!Hash::check($request->password, $admin->password)) {
            return redirect()->back()->withErrors(['password' => 'Current password does not match!']);
        }
        $admin->update(['password' => Hash::make($request->password)]);

        return redirect()->back()->withSuccess('Password updated successfully.');
    }
}
