<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\User;
use Illuminate\Http\Request;
use Auth;

class TicketController extends Controller
{

    public function index()
    {
        $users = User::where('panel_id', Auth::user()->id)->get();
        $tickets = Ticket::with('user')->where('panel_id', Auth::user()->id)->paginate(2);
        return view('panel.tickets.index', compact('users', 'tickets'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {

        $this->validate($request, [
            'subject' => 'required',
            'user_id' => 'required',
        ]);

        $s_ids = null;
        if (isset($request->order_ids)) {
            $s_ids = $request->order_ids;
        }elseif (isset($request->transaction_id)) {
            $s_ids = $request->transaction_id;
        }
        $payment_types = null;
        if (isset($request->order_types)) {
            $payment_types = $request->order_types;
        }elseif (isset($request->payment_types)) {
            $payment_types = $request->payment_types;
        }

        $ticketsData = [];
        foreach ($request->user_id as $key => $user_id) {
            $ticketsData[] = [
                'panel_id'       => Auth::user()->panel_id,
                'subject'        => $request->subject,
                'subject_ids'    => $s_ids,
                'payment_type'   => $payment_types,
                'description'    => $request->message,
                'user_id'        => $user_id,
                'send_by'        => Auth::user()->id,
                'sender_role'    => 'reseller',
                'status'         => 'pending',
                'created_by'     => Auth::user()->id,
            ];
        }

        $s_tickets = Ticket::insert($ticketsData);
        if ($s_tickets) {
            return redirect()->back()->with('success', 'Ticket has been sent');
        } else {
            return redirect()->back()->with('error', 'There is an error');
        }
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
