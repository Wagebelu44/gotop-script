<?php

namespace App\Http\Controllers\Panel;

use App\User;
use App\Exports\UsersExport;
use App\Models\ExportedUser;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use App\Models\ServiceCategory;
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
        return view('panel.users.index');
    }

    public function getUsers(Request $request)
    {
        $users = User::with('servicesList')->where('panel_id', Auth::user()->panel_id)
                ->where(function($q) use($request) {
                    if (isset($request->status) && $request->status != '') {
                        $q->where('status', $request->status);
                    }

                    if (isset($request->username) && $request->username != '') {
                        $q->where('username', 'LIKE', "%$request->username%");
                        $q->orWhere('email', 'LIKE', "%$request->username%");
                    }
        })->orderBy('id', 'DESC')->paginate(10);
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
        $credentials = $request->only('username', 'email', 'skype_name', 'status', 'password', 'password_confirmation');
        $rules = [
            'username'    => 'nullable|string|max:255|unique:users',
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
                    UserPaymentMethod::insert($paymentIds);
                }
            }
            $user->services_list = [];
            return response()->json(['status' => true, 'data'=> $user], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'errors'=> $e->getMessage()], 422);
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
    }

    public function update(Request $request, $id)
    {
        try {
            $data = $request->except('email', 'password','password_confirmation', 'payment_methods', 'created_at', 'updated_at');
            $user = User::where('panel_id', Auth::user()->panel_id)->where('id', $id)->update($data);
            if ($user){
                UserPaymentMethod::where('user_id', $id)->delete();
                if ($request->has('payment_methods')){
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
            }
            $returnData = $request->all();
            $returnData['services_list'] = [];
            return response()->json(['status' => true, 'data'=> $returnData], 200);
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
    public function serviceUpdate(Request $request)
    {

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
    }
    public function deleteUsersService(Request $request)
    {
        $request->validate([
            'user_id' => 'required|numeric'
        ]);
        $data = $request->all();
        $user_id  = $data['user_id'];
        $user = User::find($user_id);
        $user->servicesList()->detach();
        return response()->json(['status' => true, 'data'=> 'user services reset'], 200);
    }

    public function bulkUserUpdate(Request $request)
    {
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
    }

       /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function export()
    {
        $exported_users = ExportedUser::where('panel_id', auth()->user()->panel_id)->orderBy('id', 'DESC')->get();
  
        return view('panel.users.user_export', compact('exported_users'));
    }

    /**
     * Export users.
     *
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function exportUsers(Request $request)
    {
        // Validate form data
        $request->validate([
            'from' => 'required|date',
            'to' => 'required|date|after_or_equal:from',
            'status' => 'required|array|in:all,pending,active,inactive',
            'format' => 'required|in:xml,json,csv',
            'include_columns' => 'required|array|in:id,username,email,name,skype_name,balance,spent,status,created_at,last_login_at',
        ]);

        try {
            $data = $request->except('_token');
            $data['include_columns'] = serialize($request->include_columns);
            $data['status'] = serialize($request->status);
            $data['panel_id'] = auth()->user()->panel_id;
            $data['from'] = date('Y-m-d H:i:s',  strtotime($request->from));
            $data['to'] = date('Y-m-d H:i:s',  strtotime($request->to));
            ExportedUser::create($data);
            return redirect()->back()->withSuccess('Users exported successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withError($e->getMessage());
        }
    }

    /**
     * Download exported users.
     *
     * @param \App\ExportedUser $exportedUser
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadExportedUser(ExportedUser $exportedUser)
    {
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
    }
}
