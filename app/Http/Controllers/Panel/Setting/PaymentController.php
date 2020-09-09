<?php

namespace App\Http\Controllers\Panel\Setting;

use App\Http\Controllers\Controller;
use App\Models\GlobalPaymentMethod;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Auth;

class PaymentController extends Controller
{
    public function index()
    {
        $paymentMethodList = PaymentMethod::where('panel_id', Auth::user()->panel_id)
            ->select('payment_methods.*', 'global_payment_methods.name')
            ->join('global_payment_methods', 'global_payment_methods.id', '=', 'payment_methods.global_payment_method_id')
            ->orderBy('visibility','desc')->orderBy('sort')->get();

        $globalPaymentList = GlobalPaymentMethod::select('global_payment_methods.*')
            ->whereNotIn('id', function ($query) {
                $query->select('global_payment_method_id')->from('payment_methods')->where('panel_id', Auth::user()->panel_id);
            })
            ->where('global_payment_methods.status', 'active')
            ->select('*')
            ->get();

        return view('panel.settings.payments', compact('globalPaymentList', 'paymentMethodList'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
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
        }else{
            return redirect()->back()->with('error', 'Something went wrong, Please try again !!');
        }

    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }

    public function updateStatus(Request $request)
    {
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
    }

    public function paymentEdit(Request $request){
        $payment_method = PaymentMethod::where(['panel_id' => Auth::user()->panel_id, 'created_by' => Auth::user()->panel_id, 'id' => $request->id])->first();
        return response()->json([
           'payment_method'  => $payment_method,
           'payment_details' => !empty($payment_method->details) ? json_decode($payment_method->details, true):'',
        ], 200);
    }

    public function paymentUpdate(Request $request){
        $this->validate($request, [
            'payment_id' => 'required|integer',
            'global_methods_id' => 'required|integer',
            'method_name' => 'required|max:255',
            'minimum' => 'required',
            'maximum' => 'required',
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

        return redirect()->back()->with('success', 'Payment method update successfully !!');
    }


}
