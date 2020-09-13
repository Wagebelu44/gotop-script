<?php

namespace App\Http\Controllers\Panel;

use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PaymentController extends Controller
{

    public function index()
    {
        return view('panel.payments.index');
    }

    public function getPaymentLists(Request $request)
    {
        $input = request()->all();
        $input['keyword'] = isset($input['keyword']) ? $input['keyword'] : '';
        $sort_by = isset($input['sort_by']) ? $input['sort_by'] : 'id';
        $order_by = isset($input['order_by']) ? $input['order_by'] : 'desc';

        $show_page = 100;
        if (isset($request->page_size)) {
            $show_page = $request->page_size;
        }
        $local_payments = Transaction::where('panel_id', auth()->user()->panel_id)->where(function ($q) use ($input) 
        {
            if ($input['keyword'] && $input['keyword']=='user') 
            {
                $q->whereHas('user', function($q) use ($input){
                        $q->where('username', 'like', '%' . $input['search'] . '%');
                });
            }

            if ($input['keyword'] && $input['keyword']=='memo') 
            {
                $q->where('memo', 'like', '%' . $input['search'] . '%');
                $q->orWhere('tnx_id', 'like', '%' . $input['search'] . '%');
            }
        })->where(function ($q) use ($input) {
            if (isset($input['columns']) && is_array($input['columns'])) {
                foreach ($input['columns'] as $index => $value) {
                    $q->where($index, $value);
                }
            }
        })
            ->where('status', 'done')
            ->where('transaction_type', 'deposit')
            ->where(function($query){
                $query->where('transaction_flag', 'admin_panel');
                $query->orWhere('transaction_flag', 'payment_gateway');
                $query->orWhere('transaction_flag', 'bonus_deposit');
            })
            ->orderBy($sort_by, $order_by);
            $payments = $local_payments->paginate($show_page);
            $total_payments = $local_payments->count();
        $data = [
            'payments' => $payments,
            'total_payments' => $total_payments,
        ];
        return response()->json($data, 200);
    }
    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
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
}
