<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Mail\TicketRepliedMail;
use App\Models\Ticket;
use App\Models\TicketComment;
use App\User;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\Mail;

class TicketController extends Controller
{

    public function index()
    {
        $users = User::where('panel_id', Auth::user()->id)->get();
        $tickets = Ticket::with('user')->where('panel_id', Auth::user()->id)->paginate(15);
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

    public function show(Ticket $ticket)
    {
        if (Auth::user()->id != $ticket->user->panel_id) {
            abort(403, 'You do not own this ticket.');
        }

        $ticket->update(['seen_by_admin' => true]);

        return view('panel.tickets.show', compact('ticket'));
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request)
    {
        Ticket::whereIn('id', $request->tickets)->update($request->only('status'));

        return redirect()->back()->with('Tickets status updated successfully.');
    }

    public function destroy(Request $request)
    {
        if (count($request->tickets)>0) {
            foreach ($request->tickets as $id) {
                Ticket::where('id', $id)->delete();
            }
        }
        return redirect()->back()->with('Tickets deleted successfully.');
    }

    public function comment(Ticket $ticket, Request $request)
    {
        $data = $request->all();
        $this->validate($request, [
            'content' => 'required'
        ]);

        $ticket = Ticket::find($data['ticket_id']);
        $comment = new TicketComment();
        $comment->panel_id = Auth::user()->panel_id;
        $comment->message = $data['content'];
        $comment->comment_by = Auth::user()->id;
        $comment->commentor_role = "reseller";
        $comment->created_by = Auth::user()->id;
        $ticket->status = 'answered';
        $ticket->comments()->save($comment);

        if ($ticket) {
            $ticket->save();
            Mail::to($ticket->user->email)->send(new TicketRepliedMail($ticket));
            return redirect()->back()->with('success', 'Reply has been sent successfully');
        }
    }

    public function changeTicketStatus($status, $ticket_id)
    {
        $ticketStatus = Ticket::find($ticket_id);
            $ticketStatus->status = $status;
            $ticketStatus->save();
            return redirect()->back()->with('success', 'Status Changed Successfully');
    }

    public function changeBulkStatus(Request $request)
    {
            if ($request->status == 'delete') {
                $data = Ticket::whereIn('id', explode(',',  $request->ids))->delete();
            }
            else {
                $data = Ticket::whereIn('id', explode(',',  $request->ids))->update([
                    'status' => $request->status,
                ]);
            }
            return response()->json([
                'status' => 200,
                'data' => $data,
            ]);
    }

    /* seamless functions */
    public function getAdminUnreadCount()
    {
        return Ticket::where('seen_by_admin', 0)->count();
    }
}
