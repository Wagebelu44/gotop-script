<?php

namespace App\Http\Controllers\Panel;

use App\User;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use App\Models\SettingBonuse;
use App\Exports\PaymentsExport;
use App\Models\ExportedPayment;
use Spatie\ArrayToXml\ArrayToXml;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Models\G\GlobalPaymentMethod;
use Illuminate\Support\Facades\Storage;

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
                $globalMethods = PaymentMethod::where('panel_id', auth()->user()->panel_id)
                ->where('visibility', 'enabled')
                ->get();
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
        } else {
            $request->validate([
                'user_id' => 'required|integer|exists:users,id',
                'reseller_payment_methods_setting_id' => 'required|integer',
                'amount' => 'required|numeric',
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
            if (isset($request->edit_mode) && $request->edit_mode == true) {
                $log =  Transaction::find($request->edit_id);
                $log->update($payment_data);
            } else {
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
      /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function export()
    {
        $users = User::where('panel_id', auth()->user()->panel_id)->orderBy('id', 'DESC')->get();
        $globalMethods = PaymentMethod::where('panel_id', auth()->user()->panel_id)
        ->where('visibility', 'enabled')
        ->get();
        $exported_payments = ExportedPayment::where('panel_id', auth()->user()->panel_id)->orderBy('id', 'DESC')->get();
        return view('panel.payments.export', compact('users', 'globalMethods', 'exported_payments'));
    }

    /**
     * Export payments.
     *
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function exportPayment(Request $request)
    {
        // Validate form data
        $request->validate([
            'from' => 'required|date',
            'to' => 'required|date|after_or_equal:from',
            'user_ids' => 'required|array',
            'payment_method_ids' => 'required|array',
            'user_ids.*' => 'required',
            'payment_method_ids.*' => 'required',
            'status' => 'required|array|in:all,pending,waiting,hold,completed,failed,expired',
            'mode' => 'required|in:all,auto,manual',
            'format' => 'required|in:xml,json,csv',
            'include_columns' => 'required|array|in:id,user_username,user_balance,amount,payment_method_name,status,memo,completed,created_at,ip_address,mode',
        ]);

        try {
            $data = $request->except('_token');
            $data['include_columns'] = serialize($request->include_columns);
            $data['user_ids'] = serialize($request->user_ids);
            $data['payment_method_ids'] = serialize($request->payment_method_ids);
            $data['status'] = serialize($request->status);
            $data['panel_id'] = auth()->user()->panel_id;
            $data['from'] = date('Y-m-d H:i:s',  strtotime($request->from));
            $data['to'] = date('Y-m-d H:i:s',  strtotime($request->to));
            ExportedPayment::create($data);

            return redirect()->back()->withSuccess('Payment exported successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withError($e->getMessage());
        }
    }

    /**
     * Download exported payments.
     *
     * @param \App\ExportedPayment $exportedPayment
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadExportedPayment(ExportedPayment $exportedPayment)
    {
        try {
            $columns = unserialize($exportedPayment->include_columns);
            $include_columns = [];

            foreach ($columns as $value) {
                if ($value != 'user_username' && $value != 'user_balance' && $value != 'payment_method_name') {
                    $include_columns[] = 'payments.' . $value;
                } elseif ($value == 'payment_method_name') {
                    $include_columns[] = 'reseller_payment_methods_settings.method_name';
                } else {
                    $include_columns[] = 'users.' . explode('user_', $value, 2)[1] . ' AS ' . $value;
                }
            }

            $payments = Transaction::join('users', 'users.id', '=', 'payments.user_id')
                ->join('reseller_payment_methods_settings', 'reseller_payment_methods_settings.id', '=', 'payments.reseller_payment_methods_setting_id')
                ->select($include_columns)
                ->whereBetween('payments.created_at', [$exportedPayment->from, $exportedPayment->to])
                ->where(function ($q) use ($exportedPayment) {
                    if (!in_array('all', unserialize($exportedPayment->status))) {
                        $q->whereIn('payments.status', unserialize($exportedPayment->status));
                    }
                    if ($exportedPayment->mode != 'all') {
                        $q->where('payments.mode', $exportedPayment->mode);
                    }
                    if (!in_array('all', unserialize($exportedPayment->user_ids))) {
                        $q->whereIn('users.id', unserialize($exportedPayment->user_ids));
                    }
                    if (!in_array('all', unserialize($exportedPayment->payment_method_ids))) {
                        $q->whereIn('reseller_payment_methods_settings.id', unserialize($exportedPayment->payment_method_ids));
                    }
                })
                ->get();

            if ($exportedPayment->format == 'json') {
                $filename = "public/exportedData/payments.json";
                Storage::disk('local')->put($filename, $payments->toJson(JSON_PRETTY_PRINT));
                $headers = array('Content-type' => 'application/json');

                return response()->download('storage/exportedData/payments.json', 'payments.json', $headers);
            } elseif ($exportedPayment->format == 'xml') {
                $data = ArrayToXml::convert(['__numeric' => $payments->toArray()]);
                $filename = "public/exportedData/payments.xml";
                Storage::disk('local')->put($filename, $data);
                $headers = array('Content-type' => 'application/xml');

                return response()->download('storage/exportedData/payments.xml', 'payments.xml', $headers);
            } else {
                return Excel::download(new PaymentsExport($payments, unserialize($exportedPayment->include_columns)), 'payments.xlsx');
            }
        } catch (\Exception $e) {
            return redirect()->back()->withError($e->getMessage());
        }
    }
}
