<?php

namespace App\Http\Controllers\Panel;

use App\User;
use App\Models\Order;
use App\Models\Service;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{

    public function index()
    {
        return view('panel.orders.index');
    }

    public function getOrderLists(Request $request)
    {
        $date_time = new \DateTime();
        $la_time = new \DateTimeZone('Europe/Belgrade');
        $show_page = 100;
        if (isset($request->page_size)) {
            $show_page = $request->page_size;
        }
        $date_time->setTimezone($la_time);
        $orders = Order::select('orders.*', 'users.username as username','services.name as service_name', 'services.service_type as service_type')
            ->leftJoin('drip_feed_orders', function($q) {
                $q->on('drip_feed_orders.id', '=', 'orders.drip_feed_id');
            })
            ->join('users','users.id','=','orders.user_id')
            ->join('services','orders.service_id','=','services.id')
            ->where('orders.refill_status', 0)
            ->where('orders.panel_id', auth()->user()->panel_id)
            ->where(function ($q) {
                if (request()->query('status') && request()->query('status') != 'all') {
                    if (request()->query('status') =='INPROGRESS') {
                        $q->where(function($q){
                            $q->where('orders.status', 'inprogress');
                            $q->orWhere('orders.status', 'In progress');
                        });
                    }
                    else
                    {

                        $q->where('orders.status', strtolower(request()->query('status')));
                    }
                }

                if (request()->query('user')) {
                    $q->where('orders.user_id', request()->query('user'));
                }

                if (request()->query('services')) {
                    $q->where('orders.service_id', request()->query('services'));
                }
                if (request()->query('mode') && request()->query('mode') != 'all') {
                    $q->where('orders.mode', strtolower(request()->query('mode')));
                }

                if (request()->query('filter_type')) {
                    $filte_type = request()->query('filter_type');
                    $search_input = request()->query('search');
                    if ($search_input != null) {
                        if ($filte_type == 'order_id') {
                            $q->where('orders.order_id', '=', $search_input);
                        }
                        elseif ($filte_type == 'link') {
                            $q->where('orders.link', '=', $search_input);
                        }
                        elseif ($filte_type == 'service_id') {
                            $q->where('orders.service_id', '=', $search_input);
                        }
                        elseif ($filte_type == 'username') {
                            if (request()->has('query_service') && !empty(request()->query('query_service'))) 
                            {
                                $q->whereHas('user', function($q) use($search_input) {
                                    $q->where('username','like', '%' . $search_input . '%');
                                })->where('orders.service_id', request()->query('query_service'));
                            }
                            else
                            {
                                $q->whereHas('user', function($q) use($search_input) {
                                    $q->where('username','like', '%' . $search_input . '%');
                                });
                            }
                        }
                    }
                }
                if (request()->query('filter_type') && request()->query('services')) {
                    $filte_type = request()->query('filter_type');
                    $search_input = request()->query('search');
                    if ($search_input != null) 
                    {
                        if ($filte_type == 'order_id') 
                        {
                            $q->where('orders.order_id', '=', $search_input)
                            ->where('orders.service_id', request()->query('services'));
                        }
                        elseif ($filte_type == 'link') {
                            $q->where('orders.link', '=', $search_input)
                            ->where('orders.service_id', request()->query('services'));
                        }
                        elseif ($filte_type == 'service_id') {
                            $q->where('orders.service_id', '=', $search_input)
                            ->where('orders.service_id', request()->query('services'));
                        }
                        elseif ($filte_type == 'username') {
                            $q->whereHas('user', function($q) use($search_input) {
                                $q->where('username','like', '%' . $search_input . '%');
                            })->where('orders.service_id', request()->query('services'));
                        }
                    }
                }
            })
            ->orderBy('id','DESC')->paginate($show_page);

        $role =  'admin';
        $page_name =  'order_index';
        $users = User::where('panel_id', auth()->user()->panel_id)->get();
        $services = Service::select('services.id', 'services.name', 'A.totalOrder')
        ->leftJoin(\DB::raw('( SELECT service_id, count(id) as totalOrder From orders GROUP BY service_id) as A'), 'services.id', '=','A.service_id')
        ->where('panel_id', auth()->user()->panel_id)
        ->orderBy('sort','ASC')->get();
        $failed_order = 0;
        $order_mode=['auto'=>0, 'manual'=>0];
        $auto_order_statuss = [];
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
            elseif($order->mode =='manual')
            {
                $order_mode['manual']++;
            }
        }
        $data = [
            'orders' => $orders,
            'users' => $users,
            'services' => $services,
            'order_mode_count' => $order_mode,
        ];
        return response()->json($data, 200);
    }

    public function create()
    {
        //
    }

    
    public function store(Request $request)
    {

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
