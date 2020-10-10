<?php

namespace App\Http\Controllers;

use App\User;
use App\Models\Order;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\DripFeedOrders;
use App\Models\ProviderService;
use App\Models\SettingProvider;
use App\Models\DripFeedOrderLists;

class CronController extends Controller
{
    public function index()
    {
        
        $orders = Order::where('status', '!=', 'completed')
            ->where('status', '!=', 'cancelled')
            ->where('status', '!=', 'Canceled')
            ->where('mode', 'auto')
            ->whereNotNull('provider_order_id')
            ->get();
        
        
        $order_arr = [];
        foreach ($orders as $key => $order) {
            $provider_info = ProviderService::select('providers.*')
                ->where('service_id', $order->service_id)
                ->join('providers', 'providers.id', '=', 'provider_services.provider_id')
                ->first();

            if (!empty($provider_info)) 
            {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $provider_info->api_url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS,
                        http_build_query(array(
                            'key' =>$provider_info->api_key,
                            'action' => 'status',
                            'order' => $order->provider_order_id,
                            )));
                // Receive server response ...
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $server_output = curl_exec($ch);
                curl_close ($ch);
                $result  =  json_decode($server_output, true);
                if (isset($result['status'])) {
                    $order->status = $result['status'];
                    if ( strtolower($result['status']) == 'canceled' || strtolower($result['status']) == 'cancelled') 
                    {
                        $user = User::find($order->user_id);
                        $user->balance = $user->balance + $order->charges;
                        $user->save();
                        Transaction::create([
                            'transaction_type' => 'deposit',
                            'amount' => $order->charges,
                            'transaction_flag' => 'refund',
                            'user_id' =>  $order->user_id,
                            'admin_id' => auth()->user()->id??1,
                            'status' => 'done',
                            'memo' => null,
                            'fraud_risk' => null,
                            'transaction_detail' => json_encode(['order_id'=> $order->id, 'quantity_history'=> [$order->quantity]]),
                            'payment_gateway_response' => null,
                            'global_payment_method_id' =>  null,
                            'reseller_id' => 1,
                            ]);
                            $order->status = 'cancelled';
                            $order->save();
                    }
                    $order->start_counter = $result['start_count'];
                    $order->remains = $result['remains'];
                    $order_arr[$order->id] = $result['status'];
                    $order->save();
                    dump($order->id.' --- '. $order->status.'--------'.$order->start_counter.'---------------'.$order->remains);
                }
            } else {
                dump('Provider Not found: '. $order->id);
            }
        }
        return $order_arr;
    }

    public function runApiOrders()
    {
        $orders = Order::select("orders.*", \DB::raw('services.mode as service_mode'))->where('orders.status', '!=', 'completed')
        ->where('orders.status', '!=', 'cancelled')
        ->where('orders.status', '!=', 'Canceled')
        ->where('orders.source', 'API')
        ->whereNull('orders.provider_order_id')
        ->where(\DB::raw('date(orders.created_at)'), '>=', '2020-09-01')
        ->join('services', 'services.id', '=', 'orders.service_id')
        ->get();
        foreach ($orders as $key => $order) {
            if ($order->service_mode == 'auto') 
            {
                $this->resendOrders($order);
            }
        }
    }
    public function DripFeedRuns()
    {
        $date_time = new \DateTime();
        $la_time = new \DateTimeZone('Europe/Belgrade');
        $feedOrders = DripFeedOrders::where('status', 'active')->orderBy('id', 'DESC')->get();
        foreach ($feedOrders as $feedorder) 
        {
            
           $feedOrderLists =  DripFeedOrderLists::where('order_posted', 0)
            ->where('drip_feed_id', $feedorder->id)
            ->where('order_viewable_time', '<=', $date_time->format('Y-m-d H:i:s'))
            ->get();
            if (count($feedOrderLists)>0) 
            {
                foreach ($feedOrderLists as $fedOrder) 
                {
                    $make_order = Order::create([
                        'order_id' => rand(0, round(microtime(true))),
                        'charges' => $fedOrder->charges,
                        'unit_price' =>  $fedOrder->unit_price,
                        'original_unit_price' =>$fedOrder->original_unit_price,
                        'original_charges' => $fedOrder->original_charges,
                        'link' => $fedOrder->link,
                        'quantity' => $fedOrder->quantity,
                        'user_id' => $fedOrder->user_id,
                        'service_id' => $fedOrder->service_id,
                        'category_id' => $fedOrder->category_id,
                        'order_viewable_time' => $fedOrder->order_viewable_time,
                        'drip_feed_id' => $fedOrder->drip_feed_id,
                        'mode' => $fedOrder->mode,
                        'text_area_1' => $fedOrder->text_area_1,
                        'text_area_2' => $fedOrder->text_area_2,
                        'additional_inputs' => $fedOrder->additional_inputs,
                    ]);
                    $this->resendOrders($make_order);
                    $fedOrder->order_posted = 1;
                    $fedOrder->save();
                }
            }
            $completed_order = Order::where('drip_feed_id', $feedorder->id)->count();
            if ($feedorder->runs == $completed_order || $completed_order>$feedorder->runs) 
            {
                $feedorder->status = 'completed';
                $feedorder->save();
            }
        }
        \DB::statement('UPDATE `orders` SET order_id=id where refill_status=0');
    }

    public function resendOrders($order_id)
    {
        $make_order = Order::where('order_id', $order_id->order_id)->where('refill_status', 0)->first();
        if ($make_order) 
        {
            $ps = ProviderService::where('service_id', $make_order->service_id)->first();
            $provider_info = null;
            if ($ps != null) {
                $provider_info = SettingProvider::find($ps->provider_id);
            }
            else
            {
                $make_order->status =  "cancelled";
                $make_order->auto_order_response  =  json_encode(['error'=> 'service ID has some issue, not found, ID'.$make_order->service_id]);
                $make_order->save();
                return redirect()->back()->with('success', 'Successfully Resend');
            }
    
            if ($provider_info == null) {
                $make_order->status =  "cancelled";
                $make_order->auto_order_response  =  json_encode(['error'=> 'provider not found']);
                $make_order->save();
                return redirect()->back()->with('success', 'Successfully Resend');
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
            }
            elseif($ps->type == 'Package')
            {
                $dataArray = array(
                    'key' =>$provider_info->api_key,
                    'action' => 'add',
                    'service' => $ps->provider_service_id,
                    'link' => $make_order->link,
                    );
            }
            elseif($ps->type == 'Custom Comments' || $ps->type == 'Custom Comments Package')
            {
                $dataArray = array(
                    'key' =>$provider_info->api_key,
                    'action' => 'add',
                    'service' => $ps->provider_service_id,
                    'link' => $make_order->link,
                    'comments' => $make_order->text_area_1,
                    );
            }
            elseif($ps->type ==  'Mentions Custom List')
            {
                $dataArray = array(
                    'key' =>$provider_info->api_key,
                    'action' => 'add',
                    'service' => $ps->provider_service_id,
                    'link' => $make_order->link,
                    'usernames' => $make_order->text_area_1,
                    );
            }
            elseif($ps->type == 'Mentions Hashtag')
            {
                $dataArray = array(
                    'key' =>$provider_info->api_key,
                    'action' => 'add',
                    'service' => $ps->provider_service_id,
                    'link' => $make_order->link,
                    'quantity' => $make_order->quantity,
                    'hashtag' => $make_order->additional_inputs,
                    );
            }
            elseif($ps->type == 'Comment Likes')
            {
                $dataArray = array(
                    'key' =>$provider_info->api_key,
                    'action' => 'add',
                    'service' => $ps->provider_service_id,
                    'link' => $make_order->link,
                    'quantity' => $make_order->quantity,
                    'username' => $make_order->additional_inputs,
                    );
            }
            elseif($ps->type == 'Poll')
            {
                $dataArray = array(
                    'key' =>$provider_info->api_key,
                    'action' => 'add',
                    'service' => $ps->provider_service_id,
                    'link' => $make_order->link,
                    'quantity' => $make_order->quantity,
                    'answer_number' => $make_order->additional_inputs,
                    );
            }
            elseif($ps->type == 'Subscriptions' || $ps->type == 'Mentions User Followers')
            {
                $make_order->status =  "cancelled";
                $make_order->auto_order_response  =  json_encode(['error'=> 'subscription is not implemented yet']);
                $make_order->save();
                return redirect()->back()->with('success', 'Successfully saved');
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
            }
            else
            {
                $make_order->status =  "failed";
            }

            if( $make_order->save() )
            {
                return true;
            }
            return false;
        }
    }
}
