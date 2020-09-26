<?php

namespace App\Http\Controllers\Panel;

use App\User;
use App\Models\Order;
use App\Models\Service;
use Illuminate\Http\Request;
use App\Exports\OrdersExport;
use App\Models\ExportedOrder;
use Spatie\ArrayToXml\ArrayToXml;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class ExportedOrderController extends Controller
{
    public function index()
    {
        $panel_users = User::where('panel_id', auth()->user()->panel_id)->orderBy('id', 'DESC')->get();
        $panel_services = Service::where('panel_id', auth()->user()->panel_id)->orderBy('id', 'DESC')->get();
        $exported_order = ExportedOrder::where('panel_id', auth()->user()->panel_id)->orderBy('id', 'DESC')->get();
        return view('panel.orders.order_export', compact('panel_users', 'panel_services', 'exported_order'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validate form data
        $request->validate([
            'from' => 'required|date',
            'to' => 'required|date|after_or_equal:from',
            'user_ids' => 'required|array',
            'service_ids' => 'required|array',
            'provider_ids' => 'required|array',
            'user_ids.*' => 'required',
            'service_ids.*' => 'required',
            'provider_ids.*' => 'required',
            'status' => 'required|array|in:all,pending,waiting,hold,completed,failed,expired',
            'mode' => 'required|in:all,auto,manual',
            'format' => 'required|in:xml,json,csv',
            'include_columns' => 'required|array|in:id,order_id,user_username,charges,cost,link,status,start_counter,quantity,service_id,service_name,created_at,remains,provider_domain,mode',
        ]);

        
        try {
            $data = $request->except('_token');
            $data['include_columns'] = serialize($request->include_columns);
            $data['user_ids'] = serialize($request->user_ids);
            $data['service_ids'] = serialize($request->service_ids);
            $data['provider_ids'] = serialize($request->provider_ids);
            $data['status'] = serialize($request->status);
            $data['panel_id'] = auth()->user()->panel_id;
            ExportedOrder::create($data);

            return redirect()->back()->withSuccess('Order exported successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withError($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\ExportedOrder  $exportedOrder
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download(ExportedOrder $exportedOrder)
    {
        try {
            $columns = unserialize($exportedOrder->include_columns);
            $include_columns = [];
            foreach ($columns as $value) {
                if ($value == 'user_username') {
                    $include_columns[] = 'users.username AS user_name';
                } elseif ($value == 'service_id' || $value == 'service_name') {
                    $include_columns[] = 'services.' . explode('service_', $value, 2)[1] . ' AS ' . $value;
                } elseif ($value == 'provider_domain') {
                    $include_columns[] = 'setting_providers.domain AS provider';
                } else {
                    $include_columns[] = 'orders.' . $value;
                }
            }
            $orders = Order::join('users', 'users.id', '=', 'orders.user_id')
                ->join('services', 'services.id', '=', 'orders.service_id')
                ->leftJoin('setting_providers', 'setting_providers.id', '=', 'orders.provider_id')
                ->select($include_columns)
                ->whereBetween('orders.created_at', [$exportedOrder->from, $exportedOrder->to])
                ->where(function ($q) use ($exportedOrder) {
                    if (!in_array('all', unserialize($exportedOrder->status))) {
                        $q->whereIn('orders.status', unserialize($exportedOrder->status));
                    }
                    if ($exportedOrder->mode != 'all') {
                        $q->where('orders.mode', $exportedOrder->mode);
                    }
                    if (!in_array('all', unserialize($exportedOrder->user_ids))) {
                        $q->whereIn('users.id', unserialize($exportedOrder->user_ids));
                    }
                    if (!in_array('all', unserialize($exportedOrder->service_ids))) {
                        $q->whereIn('services.id', unserialize($exportedOrder->service_ids));
                    }
                    if (!in_array('all', unserialize($exportedOrder->provider_ids))) {
                        $q->whereIn('setting_providers.id', unserialize($exportedOrder->provider_ids));
                    }
                })
                ->get();
            if ($exportedOrder->format == 'json') {
                $filename = "public/exportedData/orders.json";
                Storage::disk('local')->put($filename, $orders->toJson(JSON_PRETTY_PRINT));
                $headers = array('Content-type' => 'application/json');
                return response()->download('storage/exportedData/orders.json', 'orders.json', $headers);
            } elseif ($exportedOrder->format == 'xml') {
                $data = ArrayToXml::convert(['__numeric' => $orders->toArray()]);
                $filename = "public/exportedData/orders.xml";
                Storage::disk('local')->put($filename, $data);
                $headers = array('Content-type' => 'application/xml');
                return response()->download('storage/exportedData/orders.xml', 'orders.xml', $headers);
            } else {
                return Excel::download(new OrdersExport($orders, unserialize($exportedOrder->include_columns)), 'orders.xlsx');
            }
        } catch (\Exception $e) {
            dd($e->getMessage());
            return redirect()->back()->withError($e->getMessage());
        }
    }
}
