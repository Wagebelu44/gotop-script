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

    public function getTicketCount(Request $request)
    {
        $ticket = new \App\Http\Controllers\Panel\TicketController;
        return response()->json(['status'=> true, 'data'=> $ticket->getAdminUnreadCount()], 200);
    }

}
