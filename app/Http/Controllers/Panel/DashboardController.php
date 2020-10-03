<?php

namespace App\Http\Controllers\Panel;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        return redirect(route('admin.users.index'));
        //return view('panel.dashboard');
    }

    public function getHeaderCountData(Request $request)
    {
        $ticket = new \App\Http\Controllers\Panel\TicketController;
        $order = new \App\Http\Controllers\Panel\OrderController;
        return response()->json(['status'=> true, 
        'tickets'=> $ticket->getAdminUnreadCount(),
        'orders'=> $order->getUnseenOrderCount(),
    ], 200);
    }

}
