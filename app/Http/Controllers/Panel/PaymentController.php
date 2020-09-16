<?php

namespace App\Http\Controllers\Panel;

use App\User;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\SettingBonuse;
use App\Models\G\GlobalPaymentMethod;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;

class PaymentController extends Controller
{

    public function index()
    {
        return view('panel.payments.index');
    }

    public function getPaymentLists(Request $request)
    {
        try {
            $input = request()->all();
            $input['keyword'] = isset($input['keyword']) ? $input['keyword'] : '';
            $sort_by = isset($input['sort_by']) ? $input['sort_by'] : 'id';
            $order_by = isset($input['order_by']) ? $input['order_by'] : 'desc';
    
            $show_page = 100;
            if (isset($request->page_size)) {
                $show_page = $request->page_size;
            }
            $local_payments = Transaction::select('transactions.*', 'users.username', 'global_payment_methods.name as payment_method_name')
            ->where('transactions.panel_id', auth()->user()->panel_id)
            ->where(function ($q) use ($input) 
            {
                if ($input['keyword'] && $input['keyword']=='user') 
                {
                    $q->whereHas('user', function($q) use ($input){
                            $q->where('username', 'like', '%' . $input['search'] . '%');
                    });
                }
    
                if ($input['keyword'] && $input['keyword']=='memo') 
                {
                    $q->where('transactions.memo', 'like', '%' . $input['search'] . '%');
                    $q->orWhere('transactions.tnx_id', 'like', '%' . $input['search'] . '%');
                }
            })
            ->where(function ($q) use ($input) {
                if (isset($input['columns']) && is_array($input['columns'])) {
                    foreach ($input['columns'] as $index => $value) {
                        $q->where($index, $value);
                    }
                }
            })
                ->where('transactions.status', 'done')
                ->where('transactions.transaction_type', 'deposit')
                ->where(function($query){
                    $query->where('transactions.transaction_flag', 'admin_panel');
                    $query->orWhere('transactions.transaction_flag', 'payment_gateway');
                    $query->orWhere('transactions.transaction_flag', 'bonus_deposit');
                })
                ->join('users', 'users.id', '=', 'transactions.user_id')
                ->join('global_payment_methods', 'global_payment_methods.id', '=', 'transactions.reseller_payment_methods_setting_id')
                ->orderBy($sort_by, $order_by);
                $payments = $local_payments->paginate($show_page);
                $total_payments = $local_payments->count();
                $globalMethods = GlobalPaymentMethod::where('status', 'Active')->get();
                $users = User::where('panel_id', auth()->user()->panel_id)->orderBy('id', 'DESC')->get(); 
            $data = [
                'payments' => $payments,
                'total_payments' => $total_payments,
                'globalMethods' => $globalMethods,
                'users' => $users,
            ];
            return response()->json($data, 200);
        } catch (\Exception $e) {
            $data = [
                'status' => 401,
                'message' => $e->getMessage()
            ];
            return response()->json($data, 200);
        }
       
    }
    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        // Validate form data
        if (isset($request->edit_mode) && $request->edit_mode == true) {
            $request->validate([
                'user_id' => 'required|integer|exists:users,id',
                'reseller_payment_methods_setting_id' => 'required|integer',
                'amount' => 'required|numeric',
            ]);
        }
        else
        {

            $request->validate([
                'user_id' => 'required|integer|exists:users,id',
                'reseller_payment_methods_setting_id' => 'required|integer',
                'amount' => 'required|numeric',
                'memo' => 'required|string|max:255',
            ]);
        }
        try {
          
            $data['panel_id'] =  auth()->user()->panel_id;
            $data['mode'] = 'manual';
            $payment_data = [
                'transaction_type' => 'deposit',
                'amount' => $request->amount,
                'transaction_flag' => 'admin_panel',
                'user_id' => $request->user_id,
                'admin_id' => auth()->user()->id,
                'status' => 'done',
                'memo' => $request->memo?? '',
                'fraud_risk' => null,
                'payment_gateway_response' => null,
                'reseller_payment_methods_setting_id' => $request->reseller_payment_methods_setting_id,
                'panel_id' => auth()->user()->panel_id,
            ];
            if (isset($request->edit_mode) && $request->edit_mode == true)
            {
                $log =  Transaction::find($request->edit_id);
                $log->update($payment_data);
            }
            else
            {
                $log =  Transaction::create($payment_data);
            }
            if ($log) {
                \DB::statement('UPDATE transactions t
                        CROSS JOIN (SELECT MAX(sequence_number) + 1 as new_sequence_number
                        FROM transactions) s
                        SET t.sequence_number = s.new_sequence_number
                        WHERE t.id='.$log->id);
            }
            if (!isset($request->edit_mode))
            {
                $bonus = SettingBonuse::where('global_payment_method_id', $request->reseller_payment_methods_setting_id)->get()->last();
                if ($bonus !=null) {
                    if ( floatval($request->input('amount')) >= floatval($bonus->deposit_from)) {
                        $bonus = ($bonus->bonus_amount / 100) * floatval($request->amount);
                        $tran = Transaction::create([
                            'transaction_type' => 'deposit',
                            'transaction_detail' => json_encode([
                                'payment_secrete'=>  '',
                                'currency_code'=> 'USD',
                                'actual_amount'=> floatval($request->amount),
                                'actual_payment_id'=>$log->id]),
                            'amount' =>  $bonus,
                            'transaction_flag' => 'bonus_deposit',
                            'user_id' =>  $request->user_id,
                            'admin_id' => auth()->user()->panel_id,
                            'status' => 'done',
                            'memo' => null,
                            'fraud_risk' => null,
                            'payment_gateway_response' => null,
                            'reseller_payment_methods_setting_id' => $request->reseller_payment_methods_setting_id,
                            'reseller_id' => auth()->user()->panel_id,
                        ]);
                        if ($tran) {
                            \DB::statement('UPDATE transactions t
                            CROSS JOIN (SELECT MAX(sequence_number) + 1 as new_sequence_number
                            FROM transactions) s
                            SET t.sequence_number = s.new_sequence_number
                            WHERE t.id='.$tran->id);
                        }
                    }
                }
            }
            

            $user = User::find($request->user_id);
            $user->balance += $request->amount;
            $user->save();

           // $notification = notification('Payment received', 2);

            // temporary put off , but needed must
            // if ($notification && $notification->status) {
            //     Mail::to(staffEmails('payment_received'))->send(new PaymentReceived($log, $notification));
            // }
            $gp = GlobalPaymentMethod::find($request->reseller_payment_methods_setting_id);

            $log->username = $user->username??'Not Found';
            $log->payment_method_name = $gp?$gp->name:'Not Found';
            return response()->json(['status'=>200,'data'=> $log, 'message'=>'Payment created successfully.']);
        } catch (\Exception $e) {
            return response()->json(['status'=>500,'data'=> $e->getMessage(), 'message'=>'Somethig went wrong.']);
        }
    }

    public function show($id)
    {
        return Transaction::find($id);
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
}
