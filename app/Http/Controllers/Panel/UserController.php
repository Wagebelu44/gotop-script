<?php

namespace App\Http\Controllers\Panel;

use Auth;
use App\User;
use Illuminate\Http\Request;
use App\Models\ServiceCategory;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {
        return view('panel.users.index');
    }

    public function getUsers(Request $request)
    {
        $users = User::where('panel_id', Auth::user()->panel_id)
                ->where(function($q) use($request) {
                    if (isset($request->status) && $request->status != '') {
                        $q->where('status', $request->status);
                    }
                    
                    if (isset($request->search) && $request->search != '') {
                        $q->where('username', $request->search);
                        $q->orWhere('email', $request->search);
                    }
        })->orderBy('id', 'DESC')->paginate(10);

        return response()->json([
            'status' => 200,
            'data' => $users,
        ]);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $credentials = $request->only('username', 'email', 'skype_name', 'status', 'password', 'password_confirmation');
        $rules = [
            'username'    => 'nullable|string|max:255|unique:users',
            'email'       => 'required|string|email|max:255|unique:users',
            'skype_name'  => 'nullable|string|max:255|unique:users',
            'status'      => 'required|in:pending,active,inactive',
            'password'    => 'required|string|min:8|confirmed',
        ];
        $validator = Validator::make($credentials, $rules);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors'=> $validator->messages()], 422);
        }

        try {
            $data = $request->except('password_confirmation');
            $data['password'] = Hash::make($data['password']);
            $user = User::create($data);
            return response()->json(['status' => true, 'data'=> $user], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'errors'=> $e->getMessage()], 422);
        }
    }

    public function show($id)
    {
        return User::where('panel_id', Auth::user()->panel_id)->where('id', $id)->first();
    }

    public function edit($id)
    {
        //
    }

    public function suspend(Request $request)
    {
        $credentials = $request->only('user_id');
        $rules = [
            'user_id' => 'required',
        ];
        $validator = Validator::make($credentials, $rules);
        if($validator->fails()) {
            return response()->json(['status' => false, 'errors'=> $validator->messages()], 422);
        }

        $user_id = $credentials['user_id'];

        try {
            $user = User::where('panel_id', Auth::user()->panel_id)->where('id', $user_id)->first();
            $user->update(['status' => $user->status == 'active' ? 'inactive' : 'active']);
            return response()->json(['status' => true, 'data'=> $user], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'errors'=> $e->getMessage()], 422);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $data = $request->except('email', 'password','password_confirmation', 'created_at', 'updated_at');
            $user = User::where('panel_id', Auth::user()->panel_id)->where('id', $id)->update($data);
            return response()->json(['status' => true, 'data'=> $request->all()], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'errors'=> $e->getMessage()], 200);
        }
    }

    public function destroy($id)
    {
        //
    }



    public function updatePassword(Request $request)
    {
        $credentials = $request->only('user_id', 'password', 'password_confirmation');
        $rules = [
            'user_id'    => 'required',
            'password'    => 'required|string|min:8|confirmed',
        ];
        $validator = Validator::make($credentials, $rules);
        if($validator->fails()) {
            return response()->json(['status' => false, 'errors'=> $validator->messages()], 422);
        }
        
        try {
            User::where('panel_id', Auth::user()->panel_id)->where('id', $credentials['user_id'])->update([
                'password' => Hash::make($credentials['password'])
            ]);
            return response()->json(['status' => true, 'data'=> null], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'errors'=> $e->getMessage()], 200);
        }
    }

   
    /* service custom price */
    public function getCategoryService()
    {
        return ServiceCategory::with(['services'=>function($q){
            $q->where('status', 'active');
        }])->where('status', 'active')->orderBy('id', 'ASC')->get();
    }
    public function getUserServices($user_id)
    {
        $user = User::find($user_id);
        return $user->servicesList()->get();
    }
    public function serviceUpdate(User $user, Request $request)
    {
        $request->validate([
            'price.*' => 'required|numeric',
            'percentage.*' => 'required|numeric',
        ]);

        Gate::authorize('view', $user);

        try {
            foreach ($request->price as $index => $value) {
                if ($user->servicesList->contains('id', $index)) {
                    $user->servicesList()->detach($index);
                    $user->servicesList()->attach($index, ['price' => $request->percentage[$index] ? ($value * Service::find($index)->price) / 100 : $value]);
                } else {
                    $user->servicesList()->attach($index, ['price' => $request->percentage[$index] ? ($value * Service::find($index)->price) / 100 : $value]);
                }
            }

            return redirect()->back()->withSuccess('Service custom rate updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
