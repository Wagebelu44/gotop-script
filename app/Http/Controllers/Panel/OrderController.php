<?php

namespace App\Http\Controllers\Panel;

use App\User;
use App\Models\Order;
use App\Models\Service;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\ProviderService;
use App\Models\SettingProvider;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{

    public function index()
    {
        if (Auth::user()->can('view order')) {
            return view('panel.orders.index');
        } else {
            return view('panel.permission');
        }
    }
    public function makeOrderUnseen(Request $request)
    {
        Order::where('panel_id', auth()->user()->panel_id)->where('admin_seen', 'Unseen')
        ->update([
            'admin_seen' => 'Seen'
        ]);
        return response()->json(['status'=>true], 200);
    }

    public function getSubscription()
    {
        return view('panel.orders.subscription');
    }

    public function getSubsciptionLists(Request $request)
    {
        return Order::select('orders.*', \DB::raw('services.name as service_name'), 'users.username')
        ->join('services', function($q){
            $q->on('services.id', '=', 'orders.service_id');
            $q->where('service_type', 'Subscriptions');
        })
        ->join('users', 'users.id', '=', 'orders.user_id')
        ->paginate(100);
    }

    public function updateOrder(Request $request, $id)
    {
        try {
            $data = $request->all();
            $order = Order::select('orders.*', 'users.username as username','services.name as service_name', 'services.service_type as service_type')
            ->where('orders.id', $id)
            ->join('services','orders.service_id','=','services.id')
            ->join('users','users.id','=','orders.user_id')
            ->first();
            if (isset($data['status']) && $data['status'] == 'cancel_refund') {
                    $user = User::find($order->user_id);
                    $user->balance = $user->balance + $order->charges;
                    $user->save();
                    Transaction::create([
                        'transaction_type' => 'deposit',
                        'amount' => $order->charges,
                        'transaction_flag' => 'refund',
                        'user_id' =>  $order->user_id,
                        'admin_id' => auth()->user()->id,
                        'status' => 'done',
                        'memo' => null,
                        'fraud_risk' => null,
                        'transaction_detail' => json_encode(['order_id'=> $order->id, 'quantity_history'=> [$order->quantity]]),
                        'payment_gateway_response' => null,
                        'reseller_payment_methods_setting_id' =>  null,
                        'reseller_id' => 1,
                        ]);
                        $order->status = 'cancelled';
                        $order->save();
            } elseif (isset($data['partial']) && !empty($data['partial'])) {

                $current_b = $order->charges;
                $now_b = $data['partial'] * ($order->unit_price / 1000);
                $updateable_balance =  $current_b - $now_b;

                $user = User::find($order->user_id);
                $user->balance = $user->balance + $updateable_balance;
                $user->save();

                Transaction::create([
                    'transaction_type' => 'deposit',
                    'amount' => $updateable_balance,
                    'transaction_flag' => 'refund',
                    'user_id' =>  $order->user_id,
                    'admin_id' => auth()->user()->id,
                    'status' => 'done',
                    'memo' => null,
                    'fraud_risk' => null,
                    'transaction_detail' => json_encode(['order_id'=> $order->id, 'quantity_history'=> [$order->quantity]]),
                    'payment_gateway_response' => null,
                    'reseller_payment_methods_setting_id' =>  null,
                    'reseller_id' => 1,
                ]);
                $order->update([
                    'quantity' => $data['partial'],
                    'status'   => 'partial',
                    'charges'  => $now_b,
                ]);
            } else {
                $order->update($data);
            }

            if ($order) {
                return response()->json(['status'=>200, 'data'=>$order,  'success'=>'successfully updated']);
            } else {
                return response()->json(['status'=>401,  'data'=>$order, 'error'=>'Could not updated']);
            }
        } catch (\Exception $e) {
            return response()->json(['status'=>500, 'error'=>$e->getMessage()]);
        }
    }

    public function bulkStatusChange(Request $request)
    {
        if ($request->status == 'cancel_refund') {
            $orders = Order::whereIn('id',explode(',', $request->service_ids))->get();
            foreach ($orders as $order) {
                $transaction = Transaction::create([
                    'transaction_type' => 'deposit',
                    'amount' => $order->charges,
                    'transaction_flag' => 'refund',
                    'user_id' =>  $order->user_id,
                    'admin_id' => auth()->user()->id,
                    'status' => 'done',
                    'memo' => null,
                    'fraud_risk' => null,
                    'transaction_detail' => json_encode(['order_id'=> $order->id, 'quantity_history'=> [$order->quantity]]),
                    'payment_gateway_response' => null,
                    'reseller_payment_methods_setting_id' =>  null,
                    'reseller_id' => 1,
                ]);

                if ($transaction) {
                    $user = User::find($order->user_id);
                    $user->balance = $user->balance + $order->charges;
                    $user->save();
                    
                    $order->status = 'cancelled';
                    $order->save();
                }
            }
        } elseif ($request->status == 'failed_resend') {
            $orders = Order::whereIn('id',explode(',',$request->service_ids))->get();
            foreach ($orders as $order) {
                $this->resendMultipleOrders($order->order_id);
            }
        } else {
            Order::whereIn('id',explode(',',$request->service_ids))->update([
                'status' => $request->status
            ]);
        }

        return response()->json(['status' => 200, 'data' => 'null', 'message' => 'successfully status changed']);
    }

    public function resendMultipleOrders($orderId)
    {
        $make_order = Order::where('order_id', $orderId)->where('refill_status', 0)->first();
        if ($make_order) {
            $ps = ProviderService::where('service_id', $make_order->service_id)->first();
            $provider_info = null;
            if ($ps != null) {
                $provider_info = SettingProvider::find($ps->provider_id);
            } else {
                $make_order->status =  "cancelled";
                $make_order->auto_order_response  =  json_encode(['error'=> 'service ID has some issue, not found, ID'.$make_order->service_id]);
                $make_order->save();
                return true;
            }
    
            if ($provider_info == null) {
                $make_order->status =  "cancelled";
                $make_order->auto_order_response  =  json_encode(['error'=> 'provider not found']);
                $make_order->save();
                return true;
            }
    
            $make_order->original_charges = ($ps->rate/1000) * $make_order->quantity;
            $make_order->original_unit_price = $ps->rate;
            $dataArray = array();
            if ($ps->type == 'Default') {
                    $dataArray = array(
                    'key' =>$provider_info->api_key,
                    'action' => 'add',
                    'service' => $ps->provider_service_id,
                    'link' => $make_order->link,
                    'quantity' => $make_order->quantity,
                    );
            } elseif ($ps->type == 'Package') {
                $dataArray = array(
                    'key' =>$provider_info->api_key,
                    'action' => 'add',
                    'service' => $ps->provider_service_id,
                    'link' => $make_order->link,
                    );
            } elseif ($ps->type == 'Custom Comments' || $ps->type == 'Custom Comments Package') {
                $dataArray = array(
                    'key' =>$provider_info->api_key,
                    'action' => 'add',
                    'service' => $ps->provider_service_id,
                    'link' => $make_order->link,
                    'comments' => $make_order->text_area_1,
                    );
            } elseif ($ps->type ==  'Mentions Custom List') {
                $dataArray = array(
                    'key' =>$provider_info->api_key,
                    'action' => 'add',
                    'service' => $ps->provider_service_id,
                    'link' => $make_order->link,
                    'usernames' => $make_order->text_area_1,
                    );
            } elseif ($ps->type == 'Mentions Hashtag') {
                $dataArray = array(
                    'key' =>$provider_info->api_key,
                    'action' => 'add',
                    'service' => $ps->provider_service_id,
                    'link' => $make_order->link,
                    'quantity' => $make_order->quantity,
                    'hashtag' => $make_order->additional_inputs,
                    );
            } elseif ($ps->type == 'Comment Likes') {
                $dataArray = array(
                    'key' =>$provider_info->api_key,
                    'action' => 'add',
                    'service' => $ps->provider_service_id,
                    'link' => $make_order->link,
                    'quantity' => $make_order->quantity,
                    'username' => $make_order->additional_inputs,
                    );
            } elseif ($ps->type == 'Poll') {
                $dataArray = array(
                    'key' =>$provider_info->api_key,
                    'action' => 'add',
                    'service' => $ps->provider_service_id,
                    'link' => $make_order->link,
                    'quantity' => $make_order->quantity,
                    'answer_number' => $make_order->additional_inputs,
                    );
            } elseif ($ps->type == 'Subscriptions' || $ps->type == 'Mentions User Followers') {
                $make_order->status =  "cancelled";
                $make_order->auto_order_response  =  json_encode(['error'=> 'subscription is not implemented yet']);
                $make_order->save();
                return true;
            }
    
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $provider_info->api_url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($dataArray));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $server_output = curl_exec($ch);
            curl_close ($ch);
            $result  =  json_decode($server_output, true);
            $make_order->auto_order_response  =  json_encode($result);
            if (isset($result['order'])) {
                $make_order->status =  "pending";
                $make_order->provider_order_id = $result['order'];
            } else {
                $make_order->status =  "failed";
            }

            if ( $make_order->save() ) {
                return true;
            }
            return false;
        }
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
                    } else {
                        $q->where('orders.status', strtolower(request()->query('status')));
                    }
                }

                if (request()->query('user')) {
                    $q->where('orders.user_id', request()->query('user'));
                }

                if (request()->query('service')) {
                    $q->where('orders.service_id', request()->query('service'));
                }

                if (request()->query('mode') && request()->query('mode') != 'all') {
                    $q->where('orders.mode', strtolower(request()->query('mode')));
                }

                if (request()->query('filter_type')) {
                    $filte_type = request()->query('filter_type');
                    $search_input = request()->query('data');
                    if ($search_input != null) {
                        if ($filte_type == 'order_id') {
                            $q->where('orders.order_id', '=', $search_input);
                        } elseif ($filte_type == 'link') {
                            $q->where('orders.link', '=', $search_input);
                        } elseif ($filte_type == 'service_id') {
                            $q->where('orders.service_id', '=', $search_input);
                        } elseif ($filte_type == 'username') {
                            if (request()->has('query_service') && !empty(request()->query('query_service'))) {
                                $q->whereHas('user', function($q) use($search_input) {
                                    $q->where('username','like', '%' . $search_input . '%');
                                })->where('orders.service_id', request()->query('query_service'));
                            } else {
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
                    if ($search_input != null) {
                        if ($filte_type == 'order_id') {
                            $q->where('orders.order_id', '=', $search_input)
                            ->where('orders.service_id', request()->query('services'));
                        }elseif ($filte_type == 'link') {
                            $q->where('orders.link', '=', $search_input)
                            ->where('orders.service_id', request()->query('services'));
                        } elseif ($filte_type == 'service_id') {
                            $q->where('orders.service_id', '=', $search_input)
                            ->where('orders.service_id', request()->query('services'));
                        } elseif ($filte_type == 'username') {
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
        $users = User::where('panel_id', auth()->user()->panel_id)->orderBy('id', 'DESC')->get();
        $services = Service::select('services.id', 'services.name', 'A.totalOrder')
        ->leftJoin(\DB::raw('( SELECT service_id, count(id) as totalOrder From orders GROUP BY service_id) as A'), 'services.id', '=','A.service_id')
        ->where('panel_id', auth()->user()->panel_id)
        ->orderBy('sort','ASC')->get();
        $failed_order = 0;
        $order_mode=['auto'=>0, 'manual'=>0];
        $auto_order_statuss = [];
        foreach ($orders as $order) {
            if ($order->status == 'failed') {
                $failed_order++;
            }

            if ($order->mode =='auto') {
                $order_mode['auto']++;
            } elseif ($order->mode =='manual') {
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

    // seamless functions
    public function getUnseenOrderCount()
    {
       return Order::where('panel_id', auth()->user()->panel_id)->where('admin_seen', 'Unseen')->count();
    }
}
