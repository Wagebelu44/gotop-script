<?php

namespace App\Http\Controllers\Panel;

use App\User;
use Illuminate\Support\Str;
use App\Exports\UsersExport;
use App\Models\ExportedUser;
use App\Models\UserLoginLog;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use App\Models\ServiceCategory;
use App\Models\ServicePriceUser;
use App\Models\UserPaymentMethod;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {
        $counts = \DB::select("SELECT
        (SELECT COUNT(id) FROM users)  AS all_count,
        (SELECT COUNT(id) FROM users WHERE status = 'Pending') AS pending_count,
        (SELECT COUNT(id) FROM users WHERE status = 'Active') AS active_count,
        (SELECT COUNT(id) FROM users WHERE status = 'Deactivated') AS deactive_count");
        return view('panel.users.index', compact('counts'));
    }

    public function getUserLoginLog($user_id) {
        return UserLoginLog::where('user_id', $user_id)->where('panel_id', auth()->user()->panel_id)->get();
    }

    public function getUsers(Request $request)
    {
        $users = User::select('users.*', 'A.spent')->with('servicesList')->where('panel_id', Auth::user()->panel_id)
                ->where(function($q) use($request) {
                    if (isset($request->status) && $request->status != '') {
                        $q->where('status', $request->status);
                    }

                    if (isset($request->username) && $request->username != '') {
                        $q->where('username', 'LIKE', "%$request->username%");
                        $q->orWhere('email', 'LIKE', "%$request->username%");
                    }
        })
        ->leftJoin(\DB::raw('(SELECT user_id, SUM(charges) as spent FROM orders GROUP BY user_id) AS A'), 'A.user_id', '=', 'users.id')
        ->orderBy('id', 'DESC')->paginate(10);
        $globalMethods = PaymentMethod::where('panel_id', auth()->user()->panel_id)
        ->where('visibility', 'enabled')
        ->get();

        return response()->json([
            'status' => 200,
            'data' => $users,
            'global_payment_methods' => $globalMethods,
        ]);
    }

    public function store(Request $request)
    {
        if (Auth::user()->can('add user')) {
            $credentials = $request->only('username', 'email', 'skype_name', 'status', 'password', 'password_confirmation');
            $rules = [
                'username'    => 'required|string|max:255|unique:users|regex:/^\S*$/u',
                'email'       => 'required|string|email|max:255|unique:users',
                'skype_name'  => 'nullable|string|max:255|unique:users',
                'status'      => 'required|in:Pending,Active,Deactivated',
                'password'    => 'required|string|min:8|confirmed',
            ];
            $validator = Validator::make($credentials, $rules);
            if ($validator->fails()) {
                return response()->json(['status' => false, 'errors'=> $validator->messages()], 422);
            }

            try {
                $data = $request->except('password_confirmation');
                $user = User::create([
                    'panel_id'   => Auth::user()->panel_id,
                    'uuid' => Str::uuid(),
                    'email'      => $data['email'],
                    'username'   => $data['username'],
                    'skype_name' => $data['skype_name'],
                    'password'   => Hash::make($data['password']),
                    'status'     => $data['status'],
                    'phone'      => null,
                    'balance'    => 0,
                ]);

                if ($request->has('payment_methods')){
                    if ($user){
                        $paymentIds = [];
                        foreach ($request->payment_methods as $key => $payment){
                            $paymentIds [] = [
                                'panel_id'   => Auth::user()->panel_id,
                                'user_id'    => $user->id,
                                'payment_id' => $payment,
                            ];
                        }
                    }
                    $user->services_list = [];
                    return response()->json(['status' => true, 'data'=> $user], 200);
                }
            } catch (\Exception $e) {
                return response()->json(['status' => false, 'errors'=> $e->getMessage()], 422);
            }
        } else {
            return response()->json(['status' => false, 'errors'=> 'permission denied!'], 200);
        }
    }

    public function show($id)
    {
        return User::with('paymentMethods')->where('panel_id', Auth::user()->panel_id)->where('id', $id)->first();
    }

    public function edit($id)
    {
        //
    }

    public function suspend(Request $request)
    {
        if (Auth::user()->can('change user status')) {
            $credentials = $request->only('user_id');
            $rules = [
                'user_id' => 'required',
            ];
            $validator = Validator::make($credentials, $rules);
            if ($validator->fails()) {
                return response()->json(['status' => false, 'errors'=> $validator->messages()], 422);
            }

            $user_id = $credentials['user_id'];

            try {
                $user = User::where('panel_id', Auth::user()->panel_id)->where('id', $user_id)->first();
                $user->update(['status' => $user->status == 'Active' ? 'Deactivated' : 'Active']);
                return response()->json(['status' => true, 'data'=> $user], 200);
            } catch (\Exception $e) {
                return response()->json(['status' => false, 'errors'=> $e->getMessage()], 422);
            }
        } else {
            return response()->json(['status' => false, 'errors'=> 'permission denied!'], 200);
        }
    }

    public function update(Request $request, $id)
    {
        if (Auth::user()->can('edit user')) {
            try {
                $credentials = $request->only('username', 'email', 'skype_name', 'status', 'password', 'password_confirmation');
                $rules = [
                    'username'    => 'required|string|max:255|unique:users,username,'.$id.',id|regex:/^\S*$/u',
                    'email'       => 'required|string|email|max:255|unique:users,email,'.$id.',id',
                    'skype_name'  => 'nullable|string|max:255',
                    'status'      => 'required|in:Pending,Active,Deactivated',
                ];
                
                if ($request->password) {
                    $rules['password'] = 'required|string|min:8|confirmed';
                }

                $validator = Validator::make($credentials, $rules);
                if ($validator->fails()) {
                    return response()->json(['status' => false, 'errors'=> $validator->messages()], 422);
                }

                $data = [
                    'email' => $request->email,
                    'skype_name' => $request->skype_name,
                    'status' => $request->status,
                    'username' => $request->username,
                ];
                
                if ($request->password) {
                    $data['password'] = Hash::make($request->password);
                }

                $user = User::where('panel_id', Auth::user()->panel_id)->where('id', $id)->first();

                if ($request->status == 'Pending') {
                    $user->sendEmailVerificationNotification();
                }
                
                $user->update($data);
                if ($user) {
                    UserPaymentMethod::where('user_id', $id)->delete();
                    if ($request->has('payment_methods')) {
                        $paymentIds = [];
                        foreach ($request->payment_methods as $key => $payment){
                            $paymentIds [] = [
                                'panel_id'   => Auth::user()->panel_id,
                                'user_id'    => $id,
                                'payment_id' => $payment,
                            ];
                        }
                        UserPaymentMethod::insert($paymentIds);
                    }
                    $returnData = $user->toArray();
                    $returnData['services_list'] = [];
                    return response()->json(['status' => true, 'data'=> $returnData], 200);
                }
                $returnData = $user->toArray();
                $returnData['services_list'] = [];
                return response()->json(['status' => true, 'data'=> $returnData], 200);
            } catch (\Exception $e) {
                return response()->json(['status' => false, 'errors'=> $e->getMessage()], 200);
            }
        } else {
            return response()->json(['status' => false, 'errors'=> 'permission denied!'], 200);
        }        
    }

    public function destroy($id)
    {
        //
    }

    public function updatePassword(Request $request)
    {
        if (Auth::user()->can('edit user')) {
            $credentials = $request->only('user_id', 'password', 'password_confirmation');
            $rules = [
                'user_id'    => 'required',
                'password'    => 'required|string|min:8|confirmed',
            ];
            $validator = Validator::make($credentials, $rules);
            if ($validator->fails()) {
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
        } else {
            return response()->json(['status' => false, 'errors'=> 'permission denied!'], 200);
        }
    }

    /* service custom price */
    public function getCategoryService()
    {
        return ServiceCategory::with(['services'=>function($q){
            $q->where('status', 'Active');
        }])->where('status', 'Active')->orderBy('id', 'ASC')->get();
    }

    public function getUserServices($user_id)
    {
        $user = User::find($user_id);
        return $user->servicesList()->get();
    }

    public function getCustomRatedUsers(Request $request) {
        return User::select('users.id', 'users.username')->with('servicesList')
        ->join('service_price_user', 'users.id', '=', 'service_price_user.user_id')
        ->distinct('users.id')
        ->where('users.panel_id', auth()->user()->panel_id)->get();
    }
    public function copyCustomRatedUsers(Request $request) {
        $credentials = $request->all();
        $rules = [
            'from_user'    => 'required|integer|exists:users,id',
            'to_user'       => 'required|integer|exists:users,id',
        ];
        $validator = Validator::make($credentials, $rules);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors'=> $validator->messages()], 422);
        }
        try {
            $assingedService = ServicePriceUser::where('user_id', $credentials['from_user'])->get();
            if (count($assingedService) > 0) {
                foreach ($assingedService as $value)
                {
                    $s = ServicePriceUser::where('user_id', $credentials['to_user'])
                    ->where('service_id', $value->service_id)
                    ->where('panel_id', $value->panel_id)->first();
                    if ($s) {
                        $replicated = $s;
                    } else {
                        $replicated = new ServicePriceUser;
                    }
                    $replicated->panel_id = $value->panel_id;
                    $replicated->service_id = $value->service_id;
                    $replicated->price = $value->price;
                    $replicated->user_id = $credentials['to_user'];
                    $replicated->save();
                }
            }
            return response()->json(['status' => true, 'data'=> ''], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'data'=> $e->getMessage()], 401);
        }
    }
    public function serviceUpdate(Request $request)
    {

        if (Auth::user()->can('edit user custom rates')) {
            $request->validate([
                'user_id' => 'required|numeric',
                'services' => 'required',
            ]);

            try {
                $data  = $request->all();
                $user = User::find($data['user_id']);
                if ($user->servicesList()->count()>0) {
                    $user->servicesList()->detach();
                }
                $panel_id = auth()->user()->panel_id;
                $serviceLists  = json_decode($data['services']);

                foreach ($serviceLists as $index => $value)
                {
                    $price = isset($value->update_price)?$value->update_price:$value->price;
                $user->servicesList()->attach($value->service_id, ['price' => $price, 'panel_id'=>$panel_id]);
                }
                return response()->json(['status' => true, 'data'=> $user->servicesList()->get()], 200);
            } catch (\Exception $e) {
                return response()->json(['status' => false, 'data'=> $e->getMessage()], 401);
            }
        } else {
            return response()->json(['status' => false, 'errors'=> 'permission denied!'], 200);
        }
    }

    public function deleteUsersSingleService(Request $request)
    {
        if (Auth::user()->can('edit user custom rates')) {
            $request->validate([
                'user_id' => 'required|numeric',
                'panel_id' => 'required|numeric',
                'service_id' => 'required|numeric',
            ]);
            ServicePriceUser::where('user_id', $request->user_id)
            ->where('service_id', $request->service_id)
            ->where('panel_id', $request->panel_id)
            ->delete();
            return response()->json(['status' => true, 'data'=> 'user services reset'], 200);
        } else {
            return response()->json(['status' => false, 'errors'=> 'permission denied!'], 200);
        }
    }
    public function deleteUsersService(Request $request)
    {
        if (Auth::user()->can('edit user custom rates')) {
            $request->validate([
                'user_id' => 'required|numeric'
            ]);
            $data = $request->all();
            $user_id  = $data['user_id'];
            $user = User::find($user_id);
            $user->servicesList()->detach();
            return response()->json(['status' => true, 'data'=> 'user services reset'], 200);
        } else {
            return response()->json(['status' => false, 'errors'=> 'permission denied!'], 200);
        }
    }

    public function bulkUserUpdate(Request $request)
    {
        if (Auth::user()->can('change user status')) {
            $request->validate([
                'user_ids' => 'required',
                'status' => 'required',
            ]);
            $data = $request->all();
            if ($data['status'] == 'rate_reset') {
                $users = User::whereIn('id', $data['user_ids'])->get();
                foreach ($users as $user) {
                    $user->servicesList()->detach();
                }
            }
            elseif ($data['status'] == 'Active') {
                User::whereIn('id', $data['user_ids'])->update([
                    'status' => 'Active'
                ]);
            }
            elseif ($data['status'] == 'Deactivated') {
                User::whereIn('id', $data['user_ids'])->update([
                    'status' => 'Deactivated'
                ]);
            }
            return response()->json(['status' => true, 'data'=> 'Bulk users update'], 200);
        } else {
            return response()->json(['status' => false, 'errors'=> 'permission denied!'], 200);
        }
    }
    
    public function export()
    {
        if (Auth::user()->can('export user')) {
            $exported_users = ExportedUser::where('panel_id', auth()->user()->panel_id)->orderBy('id', 'DESC')->get();
            return view('panel.users.user_export', compact('exported_users'));
        } else {
            return view('panel.permission');
        }
    }
    
    public function exportUsers(Request $request)
    {
        if (Auth::user()->can('export user')) {
        // Validate form data
            $request->validate([
                'from' => 'required|date',
                'to' => 'required|date|after_or_equal:from',
                'status' => 'required|array|in:all,Unconfirmed,Active,Suspended',
                'format' => 'required|in:xml,json,csv',
                'include_columns' => 'required|array|in:id,username,email,name,skype_name,balance,spent,status,created_at,last_login_at',
            ]);

            try {
                $data = $request->except('_token');
                $data['include_columns'] = serialize($request->include_columns);
                $statuses = array_map(function($val){
                    if ($val == 'Unconfirmed') {
                        $val = 'Pending';
                    } else if ($val == 'Suspended') {
                        $val = 'Deactivated';
                    }
                  return $val;
                }, $request->status);
                $data['status'] = serialize($statuses);
                $data['panel_id'] = auth()->user()->panel_id;
                $data['from'] = date('Y-m-d H:i:s',  strtotime($request->from));
                $data['to'] = date('Y-m-d H:i:s',  strtotime($request->to));
                ExportedUser::create($data);
                return redirect()->back()->withSuccess('Users exported successfully.');
            } catch (\Exception $e) {
                return redirect()->back()->withError($e->getMessage());
            }
        } else {
            return view('panel.permission');
        }
    }
    
    public function downloadExportedUser(ExportedUser $exportedUser)
    {
        if (Auth::user()->can('export user')) {
            try {
                $users = User::whereBetween('created_at', [$exportedUser->from, $exportedUser->to])
                    ->where(function ($q) use ($exportedUser) {
                        if (!in_array('all', unserialize($exportedUser->status))) {
                            $q->whereIn('status', unserialize($exportedUser->status));
                        }
                    })
                    ->leftJoin(\DB::raw("(SELECT sum(charges) as spent, user_id from orders where 
                    status!='cancelled' AND  status!='Canceled' AND  status!='Refunded' group by user_id) as A"), 'users.id', '=', 'A.user_id')
                    ->get(unserialize($exportedUser->include_columns));
                if ($exportedUser->format == 'json') {
                    $filename = "public/exportedData/users.json";
                    Storage::disk('local')->put($filename, $users->toJson(JSON_PRETTY_PRINT));
                    $headers = array('Content-type' => 'application/json');

                    return response()->download('storage/exportedData/users.json', 'users.json', $headers);
                } elseif ($exportedUser->format == 'xml') {
                    $data = ArrayToXml::convert(['__numeric' => $users->toArray()]);
                    $filename = "public/exportedData/users.xml";
                    Storage::disk('local')->put($filename, $data);
                    $headers = array('Content-type' => 'application/xml');

                    return response()->download('storage/exportedData/users.xml', 'users.xml', $headers);
                } else {
                    return Excel::download(new UsersExport($users, unserialize($exportedUser->include_columns)), 'users.xlsx');
                }
            } catch (\Exception $e) {
                return redirect()->back()->withError(['error' => $e->getMessage()]);
            }
        } else {
            return view('panel.permission');
        }
    }
}
