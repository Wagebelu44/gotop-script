<?php

namespace App\Http\Controllers\Web\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(),
        [
            'email' => 'required',
            'password' => 'required'
        ]);
        if ($validator->fails()) {
             return redirect()->back()->withError(true)->with('errorMessage', 'validation fails');
        }
    }
}
