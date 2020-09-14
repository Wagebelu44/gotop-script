<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Mail\TicketRepliedMail;
use App\Models\Ticket;
use App\Models\TicketComment;
use App\Notifications\TicketNotification;
use App\User;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class TicketController extends Controller
{

    public function index()
    {
        if(Auth::user()->can('ticket')) {
            $inputSearch = request()->all();
            $inputSearch['keyword'] = isset($inputSearch['keyword']) ? $inputSearch['keyword'] : '';
            $users = User::where('panel_id', Auth::user()->id)->get();
            $tickets = Ticket::with('user')->where('panel_id', Auth::user()->id)
                ->where(function ($q) use ($inputSearch) {
                    if ($inputSearch['keyword'])
                    {
                        $q->orWhereHas('user', function($q) use ($inputSearch)
                        {
                            $q->where('name', 'like', '%' . $inputSearch['keyword'] . '%');
                        });
                    }
                })->paginate(15);
            return view('panel.tickets.index', compact('users', 'tickets'));
        } else {
            return view('panel.permission');
        }
    }

    public function store(Request $request)
    {
        if(Auth::user()->can('create ticket')) {
            $this->validate($request, [
                'subject' => 'required',
                'user_id' => 'required',
            ]);

            $sIds = null;
            if (isset($request->order_ids)) {
                $sIds = $request->order_ids;
            }elseif (isset($request->transaction_id)) {
                $sIds = $request->transaction_id;
            }
            $paymentTypes = null;
            if (isset($request->order_types)) {
                $paymentTypes = $request->order_types;
            }elseif (isset($request->payment_types)) {
                $paymentTypes = $request->payment_types;
            }

            $ticketsData = [];
            foreach ($request->user_id as $key => $user_id) {
                $ticketsData[] = [
                    'panel_id'       => Auth::user()->panel_id,
                    'subject'        => $request->subject,
                    'subject_ids'    => $sIds,
                    'payment_type'   => $paymentTypes,
                    'description'    => $request->message,
                    'user_id'        => $user_id,
                    'send_by'        => Auth::user()->id,
                    'sender_role'    => 'reseller',
                    'status'         => 'pending',
                    'created_by'     => Auth::user()->id,
                ];
            }

            $tickets = Ticket::insert($ticketsData);
            if ($tickets) {
                return redirect()->back()->with('success', 'Ticket has been sent');
            } else {
                return redirect()->back()->with('error', 'There is an error');
            }
        }else{
            return view('panel.permission');
        }
    }

    public function show(Ticket $ticket)
    {
        if(Auth::user()->can('view ticket')) {
            if (Auth::user()->id != $ticket->user->panel_id) {
                abort(403, 'You do not own this ticket.');
            }
            $ticket->update(['seen_by_admin' => true]);
            return view('panel.tickets.show', compact('ticket'));
        }else{
            return view('panel.permission');
        }
    }

    public function update(Request $request)
    {
        if(Auth::user()->can('edit ticket message')) {
            Ticket::whereIn('id', $request->tickets)->update($request->only('status'));
            return redirect()->back()->with('Tickets status updated successfully.');
        }else{
            return view('panel.permission');
        }
    }

    public function destroy(Request $request)
    {
        if(Auth::user()->can('delete ticket message')) {
            if (count($request->tickets)>0) {
                foreach ($request->tickets as $id) {
                    Ticket::where('id', $id)->delete();
                }
            }
            return redirect()->back()->with('Tickets deleted successfully.');
        }else{
            return view('panel.permission');
        }
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
            Notification::send($ticket, new TicketNotification($comment));
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
