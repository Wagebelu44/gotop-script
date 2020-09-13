<?php

namespace App\Http\Controllers\User;

use App\User;
use App\Models\Order;
use App\Models\Service;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\DripFeedOrders;
use App\Models\ProviderService;
use App\Models\ServiceCategory;
use App\Models\SettingProvider;
use App\Models\DripFeedOrderLists;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('user.order.index');
    }

    public function getCateServices(Request $request)
    {
        return ServiceCategory::with(['services' => function($q){
            $q->where('status', 'active');
        }, 'services.provider'])
        ->where('status', 'active')
        ->where('panel_id', auth()->user()->panel_id)
        ->where('panel_id', auth()->user()->panel_id)->orderBy('id', 'ASC')->get();
    }

    public function orderLists(Request $r)
    {
        $input = $r->all();
        $order = Order::where('user_id', auth()->user()->id)->where('refill_status', 0);
        if(isset($input['query']))
        {
            $orders =  $order->where('status', $input['query'])->orderBy('id', 'DESC')->paginate(10);
        }
        elseif (isset($input['user_search_keyword'])) {
            if ($input['status'] !='all') {
                $order->where('status', $input['status']);
            }
            $q_string = $input['user_search_keyword'];
            $orders =  $order->where(function($q) use($q_string) {
                    $q->where('id', 'LIKE', '%' . $q_string . '%');
                    $q->orWhere('link', 'LIKE', '%' . $q_string . '%');
                    $q->orWhere('service_id', 'LIKE', '%' . $q_string . '%');
                    $q->orWhere('status', 'LIKE', '%' . $q_string . '%');
            })->orderBy('id', 'DESC')->paginate(10);
        }
        else
        {
            $orders = $order->orderBy('id', 'DESC')->paginate(10);
        }
        $role =  'user';
        $page_name =  'orders';
        $auto_order_statuss = [];
        return view('user.order.lists',compact('orders','role','page_name', 'auto_order_statuss'));
    }

   

    public function storeMassOrder(Request $r)
    {
        try {
            $data = $r->all();
            $validate = Validator::make($data, [
                'content' => 'required',
            ]);
            if ($validate->fails()) {
                return redirect()->back()
                    ->withErrors($validate)
                    ->withInput();
            }
            $each_line = preg_split("/\r\n|\n|\r/", $data['content']);
            $orders = [];
            $total_amount = 0;
            foreach ($each_line as $line) {
                $input_line = explode('|', $line);
                if (count($input_line) != 3) return redirect()->back()->with('error', 'Please follow the instruction for filling up the form')->withInput();
                $ser = Service::find($input_line[0]);
                if ($ser==null) {
                     return redirect()->back()->with('error', 'No service found with this ID'. $input_line[0]);
                }
                /* sdfsd */
                $order_status = 'pending';
                $provider_id = null;
                $original_unit_price = 0;
                $original_charges = 0;
                $auto_order_response = null;
                if ($ser->mode == 'auto') {
                    $ps = ProviderService::where('service_id', $ser->id)->first();
                    $provider_info = null;
                    if ($ps != null) {
                        $provider_info = Provider::find($ps->provider_id);
                    }
                    if ($provider_info == null) {
                        $order_status =  "cancelled";
                        $original_unit_price = $ps->rate;
                        $auto_order_response  =  json_encode(['error'=> 'provider not found, provider_id '.$ps->provider_id]);
                        $original_charges = ($ps->rate / 1000) *  $input_line[1];
                    }
                    else
                    {
                        $original_unit_price = $ps->rate;
                        $original_charges = ($ps->rate / 1000) *  $input_line[1];
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $provider_info->api_url);
                        curl_setopt($ch, CURLOPT_POST, 1);
                        curl_setopt($ch, CURLOPT_POSTFIELDS,
                                http_build_query(array(
                                    'key' => $provider_info->api_key,
                                    'action' => 'add',
                                    'service' => $ps->provider_service_id,
                                    'link' => $input_line[2],
                                    'quantity' => $input_line[1],
                                    )));
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        $server_output = curl_exec($ch);
                        curl_close ($ch);
                        $result  =  json_decode($server_output, true);
                        $auto_order_response = json_encode($result);
                        if (isset($result['order'])) {
                            $provider_id = $result['order'];
                        }
                        else
                        {
                            $order_status =  "failed";
                        }
                    }
                }
                /* asdfasdf */
                $service_for_price = auth()->user()->servicesList()->where('id', $input_line[0])->first();
                $s_price = $ser->price;
                if ($service_for_price !=null) {
                    $s_price = $service_for_price->pivot->price;
                }
                $total_amount += ($s_price / 1000) * $input_line[1];
                $orders[] = [
                    'order_id' => rand(0, round(microtime(true))),
                    'status' => $order_status,
                    'charges' => ($s_price / 1000) * $input_line[1],
                    'unit_price' => $s_price,
                    'original_charges' => ($ser->price / 1000) * $input_line[1],
                    'original_unit_price' => $ser->price,
                    'provider_order_id' => $provider_id,
                    'link' => $input_line[2],
                    'quantity' => $input_line[1],
                    'user_id' => auth()->user()->id,
                    'category_id' => $ser->category_id,
                    'service_id' => $input_line[0],
                    'created_at' => now(),
                    'order_viewable_time' => now(),
                    'panel_id' => auth()->user()->panel_id,
                    'auto_order_response' => $auto_order_response,
                    'mode' =>  $ser->mode,
                ];
            }
            // if (auth()->user()->user_balance < $total_amount) {
            //     return redirect()->back()->with('error', 'You do not have sufficient Balance');
            // }
            if (Order::insert($orders))
            {

                \DB::statement('UPDATE `orders` SET order_id=id where refill_status=0');
                $update_balance = auth()->user()->balance() - $total_amount;
                User::where('id', auth()->user()->id)->update(['balance'=> $update_balance ]);
                $log = Transaction::create([
                    'transaction_type' => 'withdraw',
                    'amount' => $total_amount,
                    'transaction_flag' => 'order_place',
                    'user_id' =>  auth()->user()->id,
                    'admin_id' => null,
                    'status' => 'done',
                    'memo' => null,
                    'fraud_risk' => null,
                    'payment_gateway_response' => null,
                    'reseller_payment_methods_setting_id' =>  null,
                    'reseller_id' => 1,
                    ]);
                return redirect()->back()->with('success', 'Successfully saved');
            }
            else return redirect()->back()->with('error', 'Please make sure you are following correct format.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
   

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {

            $data = $request->all();
            if (isset($data['drip_feed']) && $data['drip_feed']=='on')
            {
                $validate = Validator::make($data, [
                    'category_id' => 'required',
                    'service_id' => 'required',
                    'quantity' => 'required|numeric',
                    'runs' => 'required|numeric',
                    'interval' => 'required|numeric',
                    'link' => 'required',
                ]);
            }
            else
            {
                $validate = Validator::make($data, [
                    'category_id' => 'required',
                    'service_id' => 'required',
                    'quantity' => 'required|numeric',
                    'link' => 'required',
                ]);
            }

            if ($validate->fails()) {
                return redirect()->back()
                    ->withErrors($validate)
                    ->withInput();
            }
            /* commented for now but important line of code */
            /* if (auth()->user()->user_balance < $data['charge']) {
                return redirect()->back()->with('error', 'You do not have sufficient Balance');
            } */

            $service = Service::find($data['service_id']);
            if ($service != null) {
                if ($data['quantity'] < $service->min_quantity ||  $data['quantity'] >  $service->max_quantity) {
                    return redirect()->back()->with('error', 'Quantity Limit exceeds min = '.$service->min_quantity.' max = '.$service->max_quantity);
                }

                if ($service->link_duplicates == 'disallow') {
                   $or =  Order::where('service_id', $data['service_id'])->where('link', $data['link'])->where(function($q){
                        $q->where('status', 'pending');
                        $q->orWhere('status', 'In progress');
                        $q->orWhere('status', 'Processing');
                    })->first();
                    if ($or) {
                        return redirect()->back()->with('error', 'Link duplication disallowed! you can not place same link');
                    }
                }
            }

            if (isset($data['drip_feed']) && $data['drip_feed']=='on')
            {
                    $drip_feed = DripFeedOrders::create([
                        'user_id' => auth()->user()->id,
                        'runs'  => $data['runs'],
                        'interval'=> $data['interval'],
                        'total_quantity'=> $data['total_quantity'],
                        'total_charges'=> $data['charge'],
                        'panel_id'=> auth()->user()->panel_id,
                        'status'=> 'active',
                    ]);

                    if ($drip_feed)
                    {
                        $drip_feed_data = [];
                        for ($i=0; $i<$data['runs']; $i++)
                        {
                            $date = new \DateTime();
                            $date->add(new \DateInterval("PT".$i * $data['interval']."M"));
                            $service_for_price = auth()->user()->servicesList()->where('id', $data['service_id'])->first();
                            $s_price = $service->price;
                            if ($service_for_price !=null) {
                                $s_price = $service_for_price->pivot->price;
                            }

                            $custom_charges = ( $s_price / 1000) *  $data['quantity'];
                            $custom_original_charges = ( $service->price / 1000) *  $data['quantity'];

                            if ($service->service_type == 'Package') {
                                $custom_charges =  $data['charge'];
                                $custom_original_charges = $service->price;
                            } 
                            $my_order = [
                                'order_id' => rand(0, round(microtime(true))),
                                'charges' => $custom_charges,
                                'unit_price' =>  $s_price,
                                'original_unit_price' => $service->price,
                                'original_charges' => $custom_original_charges,
                                'link' => $data['link'],
                                'quantity' => $data['quantity'],
                                'user_id' => auth()->user()->id,
                                'service_id' => $data['service_id'],
                                'category_id' => $data['category_id'],
                                'drip_feed_id' => $drip_feed->id,
                                'order_viewable_time' => $date->format('Y-m-d H:i:s'),
                                'created_at' => date('Y-m-d H:i:s'),
                                'mode' => $data['service_mode'],
                                'text_area_1' => $data['text_area_1'] ?? null,
                                'text_area_2' => $data['text_area_2'] ?? null,
                                'panel_id'=> auth()->user()->panel_id,
                                'additional_inputs' => $data['additional_inputs'] ?? null,
                            ];
                            $my_order['order_posted'] = 0;
                            if ($i==0) 
                            {
                                $make_order = Order::create($my_order);
                                $my_order['order_posted'] = 1;
                            }
                            $drip_feed_data[]= $my_order;
                        }
                        // dd($drip_feed_data);
                        $drip_feed_store =  DripFeedOrderLists::insert($drip_feed_data);
                        
                        if ($drip_feed_store) 
                        {
                            User::where('id', auth()->user()->id)->update(['balance'=> auth()->user()->balance() - $data['charge'] ]);
                            $dripFeedOrdersArr = [
                                'transaction_type' => 'withdraw',
                                'amount' => $data['charge'],
                                'transaction_flag' => 'order_place',
                                'user_id' =>  auth()->user()->id,
                                'admin_id' => null,
                                'status' => 'done',
                                'memo' => null,
                                'fraud_risk' => null,
                                'payment_gateway_response' => null,
                                'reseller_payment_methods_setting_id' =>  null,
                                'panel_id'=> auth()->user()->panel_id,
                            ];
                            $log = Transaction::create($dripFeedOrdersArr);
                        }
                        
                    }
            }
            else
            {
                $service_for_price = auth()->user()->servicesList()->where('id', $data['service_id'])->first();
                $s_price = $service->price;
                if ($service_for_price !=null) {
                    $s_price = $service_for_price->pivot->price;
                }
                $custom_charges = ( $s_price / 1000) *  $data['quantity'];
                $custom_original_charges = ( $service->price / 1000) *  $data['quantity'];
                if ($service->service_type == 'Package') {
                    $custom_charges =  $data['charge'];
                    $custom_original_charges = $service->price;
                } 

                $make_order = Order::create([
                    'order_id' => rand(0, round(microtime(true))),
                    'charges' => $custom_charges,
                    'unit_price' =>  $s_price,
                    'original_unit_price' => $service->price,
                    'original_charges' => $custom_original_charges,
                    'link' => $data['link'],
                    'quantity' => $data['quantity'],
                    'user_id' => auth()->user()->id,
                    'service_id' => $data['service_id'],
                    'category_id' => $data['category_id'],
                    'order_viewable_time' => (new \DateTime())->format('Y-m-d H:i:s'),
                    'mode' => $data['service_mode'],
                    'text_area_1' => $data['text_area_1'] ?? null,
                    'text_area_2' => $data['text_area_2'] ?? null,
                    'panel_id'=> auth()->user()->panel_id,
                    'additional_inputs' => $data['additional_inputs'] ?? null,
                ]);
            }
            \DB::statement('UPDATE `orders` SET order_id=id where refill_status=0');
            if (isset($make_order))
            {
                if (!(isset($data['drip_feed']) && $data['drip_feed']=='on'))
                {

                    //User::where('id', auth()->user()->id)->update(['balance'=> auth()->user()->balance() - $data['charge'] ]);
                    User::where('id', auth()->user()->id)->update(['balance'=> 1021 ]);
                    $log = Transaction::create([
                        'transaction_type' => 'withdraw',
                        'amount' => $data['charge'],
                        'transaction_flag' => 'order_place',
                        'user_id' =>  auth()->user()->id,
                        'admin_id' => null,
                        'status' => 'done',
                        'memo' => null,
                        'fraud_risk' => null,
                        'payment_gateway_response' => null,
                        'reseller_payment_methods_setting_id' =>  null,
                        'panel_id'=> auth()->user()->panel_id,
                        ]);
                }
                if (gettype($make_order) == 'object' && $make_order->mode == 'auto') {
                    $ps = ProviderService::where('service_id', $make_order->service_id)->first();
                    $provider_info = null;
                    if ($ps != null) {
                        $provider_info = Provider::find($ps->provider_id);
                    }
                    else
                    {
                        $make_order->status =  "cancelled";
                        $make_order->auto_order_response  =  json_encode(['error'=> 'service ID has some issue, not found, ID'.$make_order->service_id]);
                        $make_order->save();
                        return redirect()->back()->with('success', 'Successfully saved');
                    }

                    if ($provider_info == null) {
                        $make_order->status =  "cancelled";
                        $make_order->auto_order_response  =  json_encode(['error'=> 'provider not found']);
                        $make_order->save();
                        return redirect()->back()->with('success', 'Successfully saved');
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
                        $make_order->provider_order_id = $result['order'];
                    }
                    else
                    {
                        $make_order->status =  "failed";
                    }
                    $make_order->save();
                }
                return redirect()->route('order', ['order_id'=>$make_order->id])
                ->with('success', 'Successfully saved');
            }
            else return redirect()->back()->with('error', 'Error Occuered');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function storeMassOrder(Request $r)
    {
        try {
            $data = $r->all();
            $validate = Validator::make($data, [
                'content' => 'required',
            ]);
            if ($validate->fails()) {
                return redirect()->back()
                    ->withErrors($validate)
                    ->withInput();
            }
            $each_line = preg_split("/\r\n|\n|\r/", $data['content']);
            $orders = [];
            $total_amount = 0;
            foreach ($each_line as $line) {
                $input_line = explode('|', $line);
                if (count($input_line) != 3) return redirect()->back()->with('error', 'Please follow the instruction for filling up the form')->withInput();
                $ser = Service::find($input_line[0]);
                if ($ser==null) {
                     return redirect()->back()->with('error', 'No service found with this ID'. $input_line[0]);
                }
                /* sdfsd */
                $order_status = 'pending';
                $provider_id = null;
                $original_unit_price = 0;
                $original_charges = 0;
                $auto_order_response = null;
                if ($ser->mode == 'auto') {
                    $ps = ProviderService::where('service_id', $ser->id)->first();
                    $provider_info = null;
                    if ($ps != null) {
                        $provider_info = SettingProvider::find($ps->provider_id);
                    }
                    if ($provider_info == null) {
                        $order_status =  "cancelled";
                        $original_unit_price = $ps->rate;
                        $auto_order_response  =  json_encode(['error'=> 'provider not found, provider_id '.$ps->provider_id]);
                        $original_charges = ($ps->rate / 1000) *  $input_line[1];
                    }
                    else
                    {
                        $original_unit_price = $ps->rate;
                        $original_charges = ($ps->rate / 1000) *  $input_line[1];
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $provider_info->api_url);
                        curl_setopt($ch, CURLOPT_POST, 1);
                        curl_setopt($ch, CURLOPT_POSTFIELDS,
                                http_build_query(array(
                                    'key' => $provider_info->api_key,
                                    'action' => 'add',
                                    'service' => $ps->provider_service_id,
                                    'link' => $input_line[2],
                                    'quantity' => $input_line[1],
                                    )));
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        $server_output = curl_exec($ch);
                        curl_close ($ch);
                        $result  =  json_decode($server_output, true);
                        $auto_order_response = json_encode($result);
                        if (isset($result['order'])) {
                            $provider_id = $result['order'];
                        }
                        else
                        {
                            $order_status =  "failed";
                        }
                    }
                }
                /* asdfasdf */
                $service_for_price = auth()->user()->servicesList()->where('id', $input_line[0])->first();
                $s_price = $ser->price;
                if ($service_for_price !=null) {
                    $s_price = $service_for_price->pivot->price;
                }
                $total_amount += ($s_price / 1000) * $input_line[1];
                $orders[] = [
                    'order_id' => rand(0, round(microtime(true))),
                    'status' => $order_status,
                    'charges' => ($s_price / 1000) * $input_line[1],
                    'unit_price' => $s_price,
                    'original_charges' => ($ser->price / 1000) * $input_line[1],
                    'original_unit_price' => $ser->price,
                    'provider_order_id' => $provider_id,
                    'link' => $input_line[2],
                    'quantity' => $input_line[1],
                    'user_id' => auth()->user()->id,
                    'category_id' => $ser->category_id,
                    'service_id' => $input_line[0],
                    'created_at' => now(),
                    'order_viewable_time' => now(),
                    'panel_id' => auth()->user()->panel_id,
                    'auto_order_response' => $auto_order_response,
                    'mode' =>  $ser->mode,
                ];
            }
            if (auth()->user()->user_balance < $total_amount) {
                return redirect()->back()->with('error', 'You do not have sufficient Balance');
            }
            if (Order::insert($orders))
            {

                \DB::statement('UPDATE `orders` SET order_id=id where refill_status=0');
                $update_balance = auth()->user()->balance() - $total_amount;
                User::where('id', auth()->user()->id)->update(['balance'=> $update_balance ]);
                $log = Transaction::create([
                    'transaction_type' => 'withdraw',
                    'amount' => $total_amount,
                    'transaction_flag' => 'order_place',
                    'user_id' =>  auth()->user()->id,
                    'admin_id' => null,
                    'status' => 'done',
                    'memo' => null,
                    'fraud_risk' => null,
                    'payment_gateway_response' => null,
                    'reseller_payment_methods_setting_id' =>  null,
                    'panel_id' => auth()->user()->panel_id,
                    ]);
                return redirect()->back()->with('success', 'Successfully saved');
            }
            else return redirect()->back()->with('error', 'Please make sure you are following correct format.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
