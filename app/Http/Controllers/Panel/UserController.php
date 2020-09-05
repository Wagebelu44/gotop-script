<?php

namespace App\Http\Controllers\Panel;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    public function index(Request $r)
    {
        return view('panel.users.index');
    }
    public function getUsers(Request $r)
    {
        return response()->json([
            'status' => 200,
            'data' => User::orderBy('id', 'DESC')->paginate(10),
        ]);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
         /* $request->validate([
            'username' => 'nullable|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'skype_name' => 'nullable|string|max:255|unique:users',
            'status' => 'required|in:pending,active,inactive',
            'password' => 'required|string|min:8|confirmed',
            'payment_methods' => 'required|array',
            'payment_methods.*' => 'required|integer|exists:reseller_payment_methods_settings,id',
        ]); */
        $credentials = $request->only('username', 'email', 'skype_name', 'status', 'password', 'password_confirmation');
        $rules = [
            'username'    => 'nullable|string|max:255|unique:users',
            'email'       => 'required|string|email|max:255|unique:users',
            'skype_name'  => 'nullable|string|max:255|unique:users',
            'status'      => 'required|in:pending,active,inactive',
            'password'    => 'required|string|min:8|confirmed',
        ];
        $validator = Validator::make($credentials, $rules);
        if($validator->fails()) {
            return response()->json(['status' => false, 'errors'=> $validator->messages()], 422);
        }
        try {
            $data = $request->except('password_confirmation');
            $data['password'] = Hash::make($data['password']);
            $user = User::create($data);
            return response()->json(['status' => true, 'data'=> $user], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'errors'=> $e->getMessage()], 200);
        }
    }

    public function show($id)
    {
        return User::find($id);
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
       /*  $credentials = $request->only('username', 'email', 'skype_name', 'status', 'password', 'password_confirmation');
        $rules = [
            'username'    => 'nullable|string|max:255|unique:users',
            'email'       => 'required|string|email|max:255|unique:users',
            'skype_name'  => 'nullable|string|max:255|unique:users',
            'status'      => 'required|in:pending,active,inactive',
            'password'    => 'required|string|min:8|confirmed',
        ];
        $validator = Validator::make($credentials, $rules);
        if($validator->fails()) {
            return response()->json(['status' => false, 'errors'=> $validator->messages()], 422);
        } */
        try {
            $data = $request->except('email', 'password','password_confirmation', 'created_at', 'updated_at');
            $user = User::where('id', $id)->update($data);
            return response()->json(['status' => true, 'data'=> $request->all()], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'errors'=> $e->getMessage()], 200);
        }
    }

    public function destroy($id)
    {
        //
    }
}
