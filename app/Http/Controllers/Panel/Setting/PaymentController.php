<?php

namespace App\Http\Controllers\Panel\Setting;

use App\User;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use App\Models\UserPaymentMethod;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\G\GlobalPaymentMethod;

class PaymentController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('payment setting')) {
            $paymentMethodList = PaymentMethod::where('panel_id', Auth::user()->panel_id)
                ->select('payment_methods.*', 'global_payment_methods.name')
                ->join('global_payment_methods', 'global_payment_methods.id', '=', 'payment_methods.global_payment_method_id')
                ->orderBy('visibility','desc')->orderBy('sort', 'ASC')->get();

            $globalPaymentList = GlobalPaymentMethod::select('global_payment_methods.*')
                ->whereNotIn('id', function ($query) {
                    $query->select('global_payment_method_id')->from('payment_methods')->where('panel_id', Auth::user()->panel_id);
                })
                ->where('global_payment_methods.status', 'Active')
                ->select('*')
                ->get();

            return view('panel.settings.payments', compact('globalPaymentList', 'paymentMethodList'));

        } else {
            return view('panel.permission');
        }
    }

    public function store(Request $request)
    {
        if (Auth::user()->can('payment setting')) {
            $this->validate($request, [
                'payment_method' => 'required|integer',
            ]);

            $globalPayment = GlobalPaymentMethod::select('id', 'name', 'fields')->where('id',$request->payment_method)->first();

            if (!empty($globalPayment)){
                $fields = (array)json_decode($globalPayment->fields);
                $detailsData = [];
                if  (count($fields) > 0){
                    foreach ($fields as $key => $value){
                        $detailsData [] = [
                            'form_label' => $value,
                            'key'=> $key,
                            'value'=> '',
                        ];
                    }
                }
                PaymentMethod::create([
                    'panel_id'                  => Auth::user()->panel_id,
                    'global_payment_method_id'  => $globalPayment->id,
                    'method_name'               => $globalPayment->name,
                    'minimum'                   => 10,
                    'details'                   => isset($detailsData) ? json_encode($detailsData):null,
                    'created_by'                => Auth::user()->id,
                    'created_at'                => date('Y-m-d h:i:s'),
                ]);

                return redirect()->back()->with('success', 'Payment method added successfully !!');
            } else {
                return redirect()->back()->with('error', 'Something went wrong, Please try again !!');
            }
        } else {
            return view('panel.permission');
        }
    }

    public function updateStatus(Request $request)
    {
        if (Auth::user()->can('payment setting')) {
            $request->validate([
                'id' => 'required|numeric',
                'status' => 'required',
            ]);

            $status = '';
            if ($request->status == 'enabled') :
                $status = 'disabled';
            elseif ($request->status == 'disabled') :
                $status = 'enabled';
            endif;
            PaymentMethod::find($request->id)->update([
                'visibility' => $status,
                'updated_at' => date('Y-m-d h:i:s'),
                'updated_by' => Auth::user()->id,
            ]);
            return response()->json([
                'status' => 200,
                'message' => 'Payment status updated successfully.'
            ]);
        } else {
            return view('panel.permission');
        }
    }

    public function paymentEdit(Request $request)
    {
        if (Auth::user()->can('payment setting')) {
            $payment_method = PaymentMethod::where(['panel_id' => Auth::user()->panel_id, 'created_by' => Auth::user()->panel_id, 'id' => $request->id])->first();
            return response()->json([
                'payment_method'  => $payment_method,
                'payment_details' => !empty($payment_method->details) ? json_decode($payment_method->details, true):'',
            ], 200);
        } else {
            return view('panel.permission');
        }
    }

    public function paymentUpdate(Request $request)
    {
        if (Auth::user()->can('payment setting')) {
            try {
                $this->validate($request, [
                    'payment_id' => 'required|integer',
                    'global_methods_id' => 'required|integer',
                    'method_name' => 'required|max:255',
                    'minimum' => 'required|numeric|min:0',
                    'maximum' => 'required|numeric|gte:minimum',
                    'new_users' => 'required',
                ]);

                PaymentMethod::find($request->payment_id)->update([
                    'minimum'         => $request->minimum,
                    'maximum'         => $request->maximum,
                    'new_user_status' => $request->new_users,
                    'details'         => isset($request->payment_details) ? json_encode($request->payment_details['payment']):null,
                    'updated_at'      => date('Y-m-d h:i:s'),
                    'updated_by'      => Auth::user()->id,
                ]);

                $users = User::where('panel_id', auth()->user()->panel_id)->get();
                foreach ($users as $user) {
                    $user_methods = UserPaymentMethod::select('payment_id')->where('panel_id', auth()->user()->panel_id)->where('user_id', $user->id)->get()->toArray();
                    if (!in_array($request->payment_id, $user_methods)) { 
                        UserPaymentMethod::create(['panel_id'=> auth()->user()->panel_id, 'user_id'=>$user->id, 'payment_id' => $request->payment_id]);
                    }
                }
                return redirect()->back()->with('success', 'Payment method update successfully !!');
            } catch (\Exception $e) {
                return redirect()->back()->with('error', $e->getMessage());
            }
           
        } else {
            return view('panel.permission');
        }
    }

    public function sortable(Request $request)
    {
        if (Auth::user()->can('payment setting')) {
            $data = PaymentMethod::where('panel_id', Auth::user()->panel_id)->get();
            foreach ($data as $pay) {
                $pay->timestamps = false; // To disable update_at field updation
                $id = $pay->id;
                foreach ($request->order as $order) {
                    if ($order['id'] == $id) {
                        $pay->update(['sort' => $order['position']]);
                    }
                }
            }
            return response()->json([
                'status' => 'success',
                'message' => 'Update successfully',
            ], 200);
        } else {
            return view('panel.permission');
        }
    }
}
