<?php

namespace App\Http\Controllers\Panel;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        return redirect(route('admin.users.index'));
    }

    public function getHeaderCountData(Request $request)
    {
        $ticket = Ticket::where('seen_by_admin', 0)->where('panel_id', auth()->user()->panel_id)->count();
        $order = Order::where('panel_id', auth()->user()->panel_id)->where('admin_seen', 'Unseen')->count();
        return response()->json(['status'=> true, 
            'tickets'=> $ticket,
            'orders'=> $order,
        ], 200);
    }
}
