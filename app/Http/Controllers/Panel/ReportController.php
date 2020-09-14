<?php

namespace App\Http\Controllers\Panel;

use App\Models\Order;
use App\Models\Ticket;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $payments = $this->payments($request);
        return view('panel.reports.index', compact('payments'));
    }

    public function payments(Request $request)
    {
        $request = request();
        $payments = collect();
        for ($i = 1; $i < 32; $i++) {
            $data = collect();
            for ($j = 1; $j < 13; $j++) {
                $year = $request->query('year') ?? date('Y');
                $query = Transaction::where(\DB::raw('Date(created_at)'), $year . '-' . $j . '-' . $i)
                    ->where('status', 'done')
                    ->where(function($q){
                        $q->where('transaction_flag', 'admin_panel');
                        $q->orWhere('transaction_flag', 'payment_gateway');
                    })
                    ->where(function ($q) use ($request) {
                        if ($request->query('user_ids') && !in_array('all', $request->query('user_ids'))) {
                            $q->whereIn('user_id', $request->query('user_ids'));
                        }
                    });
                $data->put($j, !$request->query('show') 
                || $request->query('show') == 'amount' ? $query->sum('amount') : $query->count());
            }
            $payments->push($data);
        }
        return  $payments;
    }
    public function order()
    {
        $request = request();
        $orders = collect();

        for ($i = 1; $i < 32; $i++) {
            $data = collect();

            for ($j = 1; $j < 13; $j++) {
                $year = $request->query('year') ?? date('Y');
                $query = Order::whereDate('created_at', $year . '-' . $j . '-' . $i)
                    ->where(function ($q) use ($request) {
                        if ($request->query('user_ids') && !in_array('all', $request->query('user_ids'))) {
                            $q->whereIn('user_id', $request->query('user_ids'));
                        }
                        if ($request->query('service_id') && !in_array('all', $request->query('service_id'))) {
                            $q->whereIn('service_id', $request->query('service_id'));
                        }
                        if ($request->query('status') && !in_array('all', $request->query('status'))) {
                            $q->whereIn('status', $request->query('status'));
                        }
                    });

                if ($request->query('show') == 'charge') {
                    $result = $query->sum('charges');
                } elseif ($request->query('show') == 'quantity') {
                    $result = $query->sum('quantity');
                } else {
                    $result = $query->count();
                }

                $data->put($j, $result);
            }

            $orders->push($data);
        }

        return view('panel.reports.order', compact('orders'));
    }

    public function ticket()
    {
        $request = request();
        $tickets = collect();

        for ($i = 1; $i < 32; $i++) {
            $data = collect();

            for ($j = 1; $j < 13; $j++) {
                $year = $request->query('year') ?? date('Y');
                $query = Ticket::whereDate('created_at', $year . '-' . $j . '-' . $i)
                    ->where(function ($q) use ($request) {
                        if ($request->query('status')) {
                            $q->where('status', $request->query('status'));
                        } else {
                            $q->where('status', 1);
                        }
                    })->count();
                $data->put($j, $query);
            }

            $tickets->push($data);
        }

        return view('panel.reports.ticket', compact('tickets'));
    }

    public function profits()
    {
        $request = request();
        $profits = collect();

        for ($i = 1; $i < 32; $i++) {
            $data = collect();

            for ($j = 1; $j < 13; $j++) {
                $year = $request->query('year') ?? date('Y');
                $result = Order::select(\DB::raw('sum(charges - original_charges) AS total'))
                    ->whereDate('created_at', $year . '-' . $j . '-' . $i)
                    ->whereNotNull('provider_order_id')
                    ->where(function ($q) use ($request) {
                        if ($request->query('service_id') && !in_array('all', $request->query('service_id'))) {
                            $q->whereIn('service_id', $request->query('service_id'));
                        }
                        if ($request->query('status') && !in_array('all', $request->query('status'))) {
                            $q->whereIn('status', $request->query('status'));
                        }
                    })
                    ->get();

                $data->put($j, $result[0]->total);
            }

            $profits->push($data);
        }

        return view('panel.reports.profit', compact('profits'));
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
