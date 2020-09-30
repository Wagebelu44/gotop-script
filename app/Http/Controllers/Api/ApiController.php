<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\DripFeedOrders;
use App\Models\Order;
use App\Models\Service;
use App\User;

class ApiController extends Controller
{

    public function index(Request $request)
    {
        if (isset($request->action)) {
            if ($request->action == 'services') {
                $services = Service::join('categories', 'categories.id', '=', 'services.category_id')
                ->select([DB::raw('services.id as service'), 'services.name', DB::raw('services.service_type as type'), DB::raw('categories.name as category'), DB::raw('services.price as rate'), DB::raw('services.min_quantity as min'), DB::raw('services.max_quantity as max')])
                ->get();
                return response()->json($services);
            } elseif ($request->action == 'status') {
                if (isset($request->order)) {
                    try {
                        $order = Order::where('order_id', $request->order)
                            ->select([DB::raw('start_counter AS start_count'), 'status', 'remains'])
                            ->latest('id')
                            ->first();
                        return response()->json($order);
                    } catch (\Exception $e) {
                        return response()->json(["error" => "internal_server_error", "error_description" => "There was an error during processing the request.", "message" => $e->getMessage()], 500);
                    }
                }
                elseif (isset($request->orders)) {
                    try {
                        $orders = array();
                        $orderIds = explode(",", $request->orders);
                        foreach ($orderIds as $orderId) {
                            $orders[$orderId] = Order::where('order_id', $orderId)
                                ->select([DB::raw('start_counter AS start_count'), 'status', 'remains'])
                                ->latest('id')
                                ->first() ?? array("error" => "Incorrect order ID");
                        }
                        return response()->json($orders);
                    } catch (\Exception $e) {
                        return response()->json(["error" => "internal_server_error", "error_description" => "There was an error during processing the request.", "message" => $e->getMessage()], 500);
                    }
                }

                return response()->json(["error" => "internal_server_error", "error_description" => "No status is specified", "message" => 'Nothing is found for status'], 500);
            }
            elseif ($request->action == 'balance') 
            {
                try {
                    $user = User::where('api_key', $request->api_token)->first(['balance']);
                    $user->currency = 'USD';
                    return response()->json($user);
                } catch (\Exception $e) {
                    return response()->json(["error" => "internal_server_error", "error_description" => "There was an error during processing the request.", "message" => $e->getMessage()], 500);
                }
            }
            elseif ($request->action == 'add') 
            {

                try {
                    $user = User::where('api_key', $request->api_token)->first();
                    $service = Service::find($request->service);
                    $data = $request->only('link');
                    $data['order_id'] = rand(0, round(microtime(true)));
                    $data['quantity'] = $request->input('quantity', 0);
                    $data['user_id'] = $user->id;
                    $data['source'] = 'API';
                    $data['mode'] = $request->comments || ($request->usernames && !$request->quantity) ? 'auto' : 'manual';
                    $data['service_id'] = $request->service;
                    $data['charges'] = $service->service_type == 'Package' ? $data['quantity'] * $service->price : $data['quantity'] * ($service->price/1000);
        
                    if ($request->username) 
                    {
                        $data['additional_inputs'] = $request->username;
                    } 
                    else 
                    {
                        $data['additional_inputs'] = $request->answer_number;
                    }
        
                    if ($request->comments) 
                    {
                        $data['text_area_1'] = $request->comments;
                    } 
                    elseif ($request->usernames) 
                    {
                        $data['text_area_1'] = $request->usernames;
                    }
        
                    if ($request->input('quantity') && $data['quantity'] < $service->min_quantity ||  $data['quantity'] >  $service->max_quantity) {
                        return response()->json(["error" => "quantity_mismatch", "error_description" => 'Quantity Limit exceeds min = '.$service->min_quantity.' max = '.$service->max_quantity, "message" => 'Quantity Limit exceeds min = '.$service->min_quantity.' max = '.$service->max_quantity]);
                    }
                    $order_res= null;
                    if ($request->runs && $request->interval) 
                    {
                        $totalQuantity = $request->runs * $data['quantity'];
                        $totalCharges = $service->service_type == 'Package' ? $totalQuantity * $service->price : $totalQuantity * ($service->price/1000);
        
                        if ($user->balance < $totalCharges) {
                            return response()->json(["error" => "insufficient_balance", "error_description" => "The user doesn't have sufficient balance.", "message" => "The user doesn't have sufficient balance."]);
                        }
        
                        $dripFeed = DripFeedOrders::create([
                            'user_id' => $user->id,
                            'runs' => $request->runs,
                            'interval' => $request->interval,
                            'total_quantity' => $totalQuantity,
                            'total_charges' => $totalCharges,
                        ]);
        
                        $orders = [];
                        $now = new Carbon();
                        $data['drip_feed_id'] = $dripFeed->id;
        
                        for ($i = 0; $i < $request->runs; $i ++) {
                            $temp = $data;
                            $temp['created_at'] = $now->copy()->addMinutes($i * $request->interval);
                            $temp['updated_at'] = $now->copy()->addMinutes($i * $request->interval);
                            $orders[] = $temp;
                        }
        
                        $order_res = Order::insert($orders);
                        User::where('id', $user->id)->update(['balance' => $user->balance - $totalCharges]);
                    } 
                    else 
                    {
                        if ($user->balance < $data['charges']) 
                        {
                            return response()->json(["error" => "insufficient_balance", "error_description" => "The user doesn't have sufficient balance.", "message" => "The user doesn't have sufficient balance."]);
                        }
        
                        $order_res = Order::create($data);
                        User::where('id', $user->id)->update(['balance' => $user->balance - $data['charges']]);
                    }
                    \DB::statement('UPDATE `orders` SET order_id=id where refill_status=0');
                    return response()->json(array('order' => isset($order_res->id)?$order_res->id: $order_res), 201);
                } catch (\Exception $e) {
                    return response()->json(["error" => "internal_server_error", "error_description" => "There was an error during processing the request.", "message" => $e->getMessage()], 500);
                }
            }
            else
            {
                return response()->json(["error" => "internal_server_error", "error_description" => "Bad request", "message" => 'No action is specified'], 500);
            }
        }
        return response()->json(["error" => "internal_server_error", "error_description" => "Bad request", "message" => 'No action is specified'], 500);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */

    
    public function services(Request $request)
    {
        // Validate form data
        $rules = array(
            'action' => 'required|string|in:services',
        );
        $validator = validator($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(array('errors'=> $validator->getMessageBag()->toarray()));
        }
        try {
            $services = Service::join('categories', 'categories.id', '=', 'services.category_id')
                ->select([DB::raw('services.id as service'), 'services.name', DB::raw('services.service_type as type'), DB::raw('categories.name as category'), DB::raw('services.price as rate'), DB::raw('services.min_quantity as min'), DB::raw('services.max_quantity as max')])
                ->get();
            return response()->json($services);
        } catch (\Exception $e) {
            return response()->json(["error" => "internal_server_error", "error_description" => "There was an error during processing the request.", "message" => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeOrder(Request $request)
    {
        // Validate form data
        $rules = array(
            'action' => 'required|string|in:add',
            'package' => 'required|integer|exists:services,id',
            'link' => 'required|active_url',
            'quantity' => $request->username || $request->answer_number ? 'required|integer|min:1' : 'nullable|integer|min:1',
            'runs' => 'nullable|integer|min:1',
            'interval' => 'nullable|integer|min:1',
            'comments' => 'nullable|string',
            'usernames' => 'nullable|string',
            'username' => 'nullable|string',
            'answer_number' => 'nullable|string',
        );

        $validator = validator($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(array('errors'=> $validator->getMessageBag()->toarray()));
        }

        try {
            $user = User::where('api_key', $request->api_token)->first();
            $service = Service::find($request->service);
            $data = $request->only('link');
            $data['order_id'] = rand(0, round(microtime(true)));
            $data['quantity'] = $request->input('quantity', 0);
            $data['user_id'] = $user->id;
            $data['source'] = 'API';
            $data['mode'] = $request->comments || ($request->usernames && !$request->quantity) ? 'auto' : 'manual';
            $data['service_id'] = $request->service;
            $data['charges'] = $service->service_type == 'Package' ? $data['quantity'] * $service->price : $data['quantity'] * ($service->price/1000);

            if ($request->username) {
                $data['additional_inputs'] = $request->username;
            } else {
                $data['additional_inputs'] = $request->answer_number;
            }

            if ($request->comments) {
                $data['text_area_1'] = $request->comments;
            } elseif ($request->usernames) {
                $data['text_area_1'] = $request->usernames;
            }

            if ($request->input('quantity') && $data['quantity'] < $service->min_quantity ||  $data['quantity'] >  $service->max_quantity) {
                return response()->json(["error" => "quantity_mismatch", "error_description" => 'Quantity Limit exceeds min = '.$service->min_quantity.' max = '.$service->max_quantity, "message" => 'Quantity Limit exceeds min = '.$service->min_quantity.' max = '.$service->max_quantity]);
            }

            if ($request->runs && $request->interval) {
                $totalQuantity = $request->runs * $data['quantity'];
                $totalCharges = $service->service_type == 'Package' ? $totalQuantity * $service->price : $totalQuantity * ($service->price/1000);

                if ($user->balance < $totalCharges) {
                    return response()->json(["error" => "insufficient_balance", "error_description" => "The user doesn't have sufficient balance.", "message" => "The user doesn't have sufficient balance."]);
                }

                $dripFeed = DripFeedOrders::create([
                    'user_id' => $user->id,
                    'runs' => $request->runs,
                    'interval' => $request->interval,
                    'total_quantity' => $totalQuantity,
                    'total_charges' => $totalCharges,
                ]);

                $orders = [];
                $now = new Carbon();
                $data['drip_feed_id'] = $dripFeed->id;

                for ($i = 0; $i < $request->runs; $i ++) {
                    $temp = $data;
                    $temp['created_at'] = $now->copy()->addMinutes($i * $request->interval);
                    $temp['updated_at'] = $now->copy()->addMinutes($i * $request->interval);
                    $orders[] = $temp;
                }

                Order::insert($orders);
                User::where('id', $user->id)->update(['balance' => $user->balance - $totalCharges, 'spent' => $user->spent + $totalCharges]);
            } else {
                if ($user->balance < $data['charges']) {
                    return response()->json(["error" => "insufficient_balance", "error_description" => "The user doesn't have sufficient balance.", "message" => "The user doesn't have sufficient balance."]);
                }

                Order::create($data);
                User::where('id', $user->id)->update(['balance' => $user->balance - $data['charges'], 'spent' => $user->spent + $data['charges']]);
            }

            return response()->json(array('order' => $data['order_id']), 201);
        } catch (\Exception $e) {
            return response()->json(["error" => "internal_server_error", "error_description" => "There was an error during processing the request.", "message" => $e->getMessage()], 500);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function orderStatus(Request $request)
    {
        // Validate form data
        $rules = array(
            'action' => 'required|string|in:status',
            'order' => 'required|integer|exists:orders,order_id',
        );

        $validator = validator($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(array('errors'=> $validator->getMessageBag()->toarray()));
        }

        try {
            $order = Order::where('order_id', $request->order)
                ->select([DB::raw('start_counter AS start_count'), 'status', 'remains'])
                ->latest('id')
                ->first();

            return response()->json($order);
        } catch (\Exception $e) {
            return response()->json(["error" => "internal_server_error", "error_description" => "There was an error during processing the request.", "message" => $e->getMessage()], 500);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function ordersStatus(Request $request)
    {
        // Validate form data
        $rules = array(
            'action' => 'required|string|in:status',
            'orders' => 'required|regex:~^[0-9 A-Z]+(,[0-9 A-Z]+)*$~i',
        );

        $validator = validator($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(array('errors'=> $validator->getMessageBag()->toarray()));
        }

        try {
            $orders = array();
            $orderIds = explode(",", $request->orders);

            foreach ($orderIds as $orderId) {
                $orders[$orderId] = Order::where('order_id', $orderId)
                    ->select([DB::raw('start_counter AS start_count'), 'status', 'remains'])
                    ->latest('id')
                    ->first() ?? array("error" => "Incorrect order ID");
            }

            return response()->json($orders);
        } catch (\Exception $e) {
            return response()->json(["error" => "internal_server_error", "error_description" => "There was an error during processing the request.", "message" => $e->getMessage()], 500);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function userBalance(Request $request)
    {
        // Validate form data
        $rules = array(
            'action' => 'required|string|in:balance',
        );

        $validator = validator($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(array('errors'=> $validator->getMessageBag()->toarray()));
        }

        try {
            $user = User::where('api_key', $request->api_token)->first(['balance']);
            $user->currency = 'USD';

            return response()->json($user);
        } catch (\Exception $e) {
            return response()->json(["error" => "internal_server_error", "error_description" => "There was an error during processing the request.", "message" => $e->getMessage()], 500);
        }
    }
}
