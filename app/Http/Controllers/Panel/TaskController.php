<?php

namespace App\Http\Controllers\Panel;

use App\User;
use App\Models\Order;
use App\Models\Service;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TaskController extends Controller
{

    public function index()
    {
        if (Auth::user()->can('view task')) {
            return view('panel.tasks.index');
        } else {
            return view('panel.permission');
        }
    }

    public function refillChnageStatus(Request $r)
    {
        if (Auth::user()->can('change task status')) {
            try {
                $refill_status =  Order::where([
                    ['id', '=', $r->order_table_id],
                    ['order_id', '=', $r->order_id],
                ])->first();
                $refill_status->refill_order_status = $r->refill_order_status;
                if ($refill_status->save()) {
                    return redirect()->back()->with('success', 'status has been changed');
                }
                else
                {
                    return redirect()->back()->with('error', 'Error occured');
                }
            } catch (\Exception $e) {
                return redirect()->back()->with('error', $e->getMessage());
            }
        } else {
            return response()->json(['status' => false, 'errors'=> 'permission denied!'], 200);
        }
    }
    
    public function getTasksOrders(Request $request)
    {
        if (Auth::user()->can('view task')) {
            $taskOrders = Order::select('orders.*', 'users.username as username' ,'services.name as service_name', 'services.service_type as service_type')
            ->where('orders.panel_id', auth()->user()->panel_id)
            ->join('services','orders.service_id','=','services.id')
            ->where('orders.refill_status', '!=', 0)
                ->leftJoin('drip_feed_orders', function($q) {
                    $q->on( 'drip_feed_orders.id', '=', 'orders.drip_feed_id');
                    $q->where('orders.order_viewable_time','<=', (new \DateTime())->format('Y-m-d H:i:s'));
                })
                ->join('users','users.id','=','orders.user_id')
                ->where(function ($q) {
                    if (request()->query('status') && request()->query('status') != 'all') {
                        $q->where('orders.refill_order_status', request()->query('status'));
                    }

                    if (request()->query('user')) {
                        $q->where('orders.user_id', request()->query('user'));
                    }

                    if (request()->query('services')) {
                        $q->where('orders.service_id', request()->query('services'));
                    }
                })->orderBy('id','DESC')->paginate(100);
            $role =  'admin';
            $page_name =  'tasks';
            $users = User::where('panel_id', auth()->user()->panel_id)->get();
            $services = Service::select('services.id', 'services.name', 'A.totalOrder')
            ->leftJoin(\DB::raw('( SELECT service_id, count(id) as totalOrder From orders GROUP BY service_id) as A'), 'services.id', '=','A.service_id')
            ->where('services.panel_id', auth()->user()->panel_id)
            ->orderBy('sort','ASC')->get();
            $failed_order = 0;
            $order_mode=['auto'=>0, 'manual'=>0];
            $auto_order_statuss = [];
            $orders = Order::where('panel_id', auth()->user()->panel_id)->get();
            foreach ($orders as $order)
            {
                if ($order->status == 'failed') 
                {
                    $failed_order++;
                }
                if ($order->mode =='auto') 
                {
                    $order_mode['auto']++;
                }
                elseif ($order->mode =='manual')
                {
                    $order_mode['manual']++;
                }
            }
            $data = [
                'orders' => $taskOrders,
                'users' => $users,
                'services' => $services,
                'order_mode_count' => $order_mode,
            ];
            return response()->json($data, 200);
        } else {
            return response()->json(['status' => false, 'errors'=> 'permission denied!'], 200);
        }
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
