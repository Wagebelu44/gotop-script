<?php

namespace App\Http\Controllers\Panel;

use App\Models\Order;
use App\Models\Ticket;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\SettingGeneral;
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
        $year = $request->year ?? date('Y');
        $sql = Transaction::select(\DB::raw('COUNT(id) AS countId'), \DB::raw('SUM(amount) AS amount'), \DB::raw('DATE(created_at) AS date'))
            ->whereYear('created_at', $year)
            ->where('panel_id',  auth()->user()->panel_id)
            ->where('status', 'done')
            ->where(function($q){
                $q->where('transaction_flag', 'admin_panel');
                $q->orWhere('transaction_flag', 'payment_gateway');
            })
            ->groupBy(\DB::raw('DATE(created_at)'));
            if ($request->status) {
                $sql->where('status', $request->status);
            }
            if ($request->user_ids  && !in_array('all', $request->user_ids)) {
                $q->whereIn('user_id', $request->user_ids);
            }
            $data = $sql->get();

        $payments = [];
        $gs = SettingGeneral::where('panel_id', auth()->user()->panel_id)->first();
        foreach ($data as $qr) {
            $dd = explode('-', $qr->date);
            $payments[intVal($dd[1])][intVal($dd[2])] = ($request->show == 'amount') ? $qr->amount : $gs->currency.$qr->amount;
        }
        return  $payments;
    }
    public function order(Request $request)
    {
        $year = $request->year ?? date('Y');
        $sql = Order::select(\DB::raw('COUNT(id) AS countId'), \DB::raw('DATE(created_at) AS date'))
            ->whereYear('created_at', $year)
            ->where('panel_id',  auth()->user()->panel_id)
            ->groupBy(\DB::raw('DATE(created_at)'));
            if ($request->status) {
                $sql->where('status', $request->status);
            }
            if ($request->user_ids  && !in_array('all', $request->user_ids)) {
                $q->whereIn('user_id', $request->user_ids);
            }
            if ($request->query('service_id') && !in_array('all', $request->query('service_id'))) {
                $q->whereIn('service_id', $request->query('service_id'));
            }
            if ($request->query('status') && !in_array('all', $request->query('status'))) {
                $q->whereIn('status', $request->query('status'));
            }
            if ($request->query('show') == 'charge') 
            {
                $result = $sql->sum('charges');
            } 
            elseif ($request->query('show') == 'quantity') 
            {
                $result = $sql->sum('quantity');
            } 
            else 
            {
                $result = $sql->count();
            }
            $data = $sql->get();

        $orders = [];
        foreach ($data as $qr) {
            $dd = explode('-', $qr->date);
            $orders[intVal($dd[1])][intVal($dd[2])] = $qr->countId;
        }
        return view('panel.reports.order', compact('orders'));
    }

    public function ticket(Request $request)
    {
        $year = $request->year ?? date('Y');
        $sql = Ticket::select(\DB::raw('COUNT(id) AS countId'), \DB::raw('DATE(created_at) AS date'))
            ->whereYear('created_at', $year)
            ->where('panel_id',  auth()->user()->panel_id)
            ->groupBy(\DB::raw('DATE(created_at)'));
            if ($request->status) {
                $sql->where('status', $request->status);
            }
            $data = $sql->get();

        $tickets = [];
        foreach ($data as $qr) {
            $dd = explode('-', $qr->date);
            $tickets[intVal($dd[1])][intVal($dd[2])] = $qr->countId;
        }
        return view('panel.reports.ticket', compact('tickets'));
    }

    public function profits(Request $request)
    {
        $year = $request->year ?? date('Y');
        $sql = Order::select(\DB::raw('COUNT(id) AS countId'), \DB::raw('DATE(created_at) AS date'), 
        \DB::raw('sum(charges - original_charges) AS total'))
            //->whereNotNull('provider_order_id')
            ->whereYear('created_at', $year)
            ->where('panel_id',  auth()->user()->panel_id)
            ->groupBy(\DB::raw('DATE(created_at)'));
            if ($request->status) {
                $sql->where('status', $request->status);
            }
            if ($request->query('service_id') && !in_array('all', $request->query('service_id'))) {
                $q->whereIn('service_id', $request->query('service_id'));
            }
            if ($request->query('status') && !in_array('all', $request->query('status'))) {
                $q->whereIn('status', $request->query('status'));
            }
            $data = $sql->get();

        $profits = [];
        foreach ($data as $qr) {
            $dd = explode('-', $qr->date);
            $profits[intVal($dd[1])][intVal($dd[2])] = $qr->total;
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
