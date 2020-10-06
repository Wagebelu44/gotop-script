<?php

namespace App\Http\Controllers\Panel;

use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\DripFeedOrders;
use App\Models\DripFeedOrderLists;
use App\Http\Controllers\Controller;

class DripFeedController extends Controller
{

    public function index()
    {
        return view('panel.drip_feed.index');
    }

    public function getDripFeedLists(Request $request)
    {
        $date = (new \DateTime())->format('Y-m-d H:i:s');
        $d_feeds = DripFeedOrders::select('drip_feed_orders.*','users.username as user_name', 'A.service_name', 'A.orders_link','A.service_quantity as service_quantity',  'B.runOrders as runOrders')
            ->join('users','users.id','=','drip_feed_orders.user_id')
            ->join(\DB::raw('(SELECT COUNT(orders.drip_feed_id) AS totalOrders, orders.drip_feed_id, GROUP_CONCAT(DISTINCT(orders.link)) AS orders_link,
            GROUP_CONCAT(DISTINCT(services.name)) AS service_name, GROUP_CONCAT(DISTINCT(orders.quantity)) AS service_quantity FROM orders INNER JOIN services
            ON services.id = orders.service_id GROUP BY orders.drip_feed_id) as A'), 'drip_feed_orders.id', '=', 'A.drip_feed_id') 
            ->leftJoin(\DB::raw("(SELECT drip_feed_id, COUNT(drip_feed_id) AS runOrders FROM orders
            WHERE order_viewable_time <='".$date."' GROUP BY drip_feed_id) AS B"), 'drip_feed_orders.id', '=', 'B.drip_feed_id');

        if (isset($request->status))
        {
            if ($request->status != 'all')
            $d_feeds->where('drip_feed_orders.status',$request->status);
        }

        $drip_feeds = $d_feeds->OrderBy('id', 'DESC')->paginate(100);
        $data= [
            'feed_lists' => $drip_feeds, 
        ];
        return response()->json($data, 200);
    }
    public function updateDripOrder(Request $r, $id)
    {
        if (Auth::user()->can('change drip-feed status') || Auth::user()->can('cancel and refund drip-feed') ) {
            try {
                $data = $r->all();
                $date = (new \DateTime())->format('Y-m-d H:i:s');
                $or =DripFeedOrders::select('drip_feed_orders.*','users.username as user_name', 'A.service_name', 'A.orders_link','A.service_quantity as service_quantity',  'B.runOrders as runOrders')
                ->join('users','users.id','=','drip_feed_orders.user_id')
                ->join(\DB::raw('(SELECT COUNT(orders.drip_feed_id) AS totalOrders, orders.drip_feed_id, GROUP_CONCAT(DISTINCT(orders.link)) AS orders_link,
                GROUP_CONCAT(DISTINCT(services.name)) AS service_name, GROUP_CONCAT(DISTINCT(orders.quantity)) AS service_quantity FROM orders INNER JOIN services
                ON services.id = orders.service_id GROUP BY orders.drip_feed_id) as A'), 'drip_feed_orders.id', '=', 'A.drip_feed_id') 
                ->leftJoin(\DB::raw("(SELECT drip_feed_id, COUNT(drip_feed_id) AS runOrders FROM orders
                WHERE order_viewable_time <='".$date."' GROUP BY drip_feed_id) AS B"), 'drip_feed_orders.id', '=', 'B.drip_feed_id')
                ->where('drip_feed_orders.id', $id)->first();
                if (isset($data['status']) && $data['status']== 'canceled')
                {
                    $total_run = $or->runs;
                $d_lists  =  DripFeedOrderLists::where('order_posted', 0)
                ->where('drip_feed_id', $id);
                $noCompletedOrderMoney = $d_lists->sum('charges');
                if ($noCompletedOrderMoney>0) 
                {
                    Transaction::create([
                        'transaction_type' => 'deposit',
                        'amount' => $noCompletedOrderMoney,
                        'transaction_flag' => 'drip_feed_cancel',
                        'user_id' =>  $or->user_id,
                        'admin_id' => auth()->user()->id,
                        'status' => 'done',
                        'memo' => null,
                        'fraud_risk' => null,
                        'transaction_detail' => json_encode(
                            [
                                'drip_feed_id'=> $id, 
                                'total'=> $total_run,
                                'incompleted'=> $d_lists->count(),
                            ]
                        ),
                        'payment_gateway_response' => null,
                        'reseller_payment_methods_setting_id' =>  null,
                        'reseller_id' => 1,
                        ]);
                }
                }
                $or->status = $data['status']??'completed';
                $order = $or->save();
                if ($order)
                    return response()->json(['status'=>200, 'data'=> $or, 'success'=>'successfully updated']);
                else return response()->json(['status'=>401, 'error'=>'Could not updated']);
            } catch (\Exception $e) {
                return response()->json(['status'=>500, 'error'=>$e->getMessage()]);
            }
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
