<?php

namespace App\Http\Controllers\Web;

use App\Models\Ticket;
use Illuminate\Http\Request;
use App\Models\TicketComment;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class TicketController extends Controller
{
    public function store(Request $r)
    {
        //dd($r->all());
        try {
            // important to uncomment. but for now 
            // if (generalSetting('tickets_per_user') && generalSetting('tickets_per_user')->value && (auth()->guard('web')->user()->tickets()->where('status', 'open')->count() == generalSetting('tickets_per_user')->value || auth()->guard('web')->user()->tickets()->where('status', 'open')->count() > generalSetting('tickets_per_user')->value)) {
            //     return redirect()->back()->withError('You can\'t submit ticket. Please contact admin.');
            // }

            $data = $r->all();
            $validator = Validator::make($data, [
                'subject' => 'required'
            ]);

            if ($validator->fails()) {
                return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
            }
            $s_ids = null;
            if (isset($data['order_ids'])) {
                $s_ids = $data['order_ids'];
            } elseif (isset($data['transaction_id'])) {
                $s_ids = $data['transaction_id'];
            }

            $payment_types = null;
            if (isset($data['order_type'])) {
                $payment_types = $data['order_type'];
            } elseif (isset($data['payment_types'])) {
                $payment_types = $data['payment_types'];
            }

            $s_tickets = Ticket::create([
                'subject' => $data['subject'],
                'subject_ids' => $s_ids,
                'payment_type' => $payment_types,
                'description' => $data['description'],
                'panel_id' => auth()->user()->panel_id,
                'user_id' => auth()->user()->id,
                'send_by' => auth()->user()->id,
                'sender_role' => 'user',
                'status' => 'pending',
            ]);

            if ($s_tickets) {
                //Mail::to("thesocialmediagrowthh@gmail.com")->send(new SupportTickets($s_tickets));
                //Notification::send(auth()->user(),  new SupportTicketCreated);
                /* if (count(Mail::failures()) > 0){
                    $errors = 'Failed to send password reset email, please try again.';
                } */
                return redirect()->back()->with('success', 'Ticket has been created successfully');
            }
            else
            {
                return redirect()->back()->with('error', 'There is an error');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
    public function show($id)
    {
        return Ticket::find($id);
    }

    public function makeComment(Request $request)
    {
        try {
            $data = $request->all();
            $validator = Validator::make($data, [
                'content' => 'required'
            ]);

            if ($validator->fails()) {
                return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
            }
            $ticket = Ticket::find($data['ticket_id']);
            $comment = new TicketComment;
            $comment->message = $data['content'];
            $comment->comment_by = auth()->user()->id;
            $comment->panel_id = auth()->user()->panel_id;
            $comment->commentor_role = "user";
            $ticket->status = 'pending';

            $ticket->comments()->save($comment);

            if ($ticket) {
                $ticket->save();
                // Mail::to("thesocialmediagrowthh@gmail.com")->send(new SupportTickets($ticket));
                // Notification::send(auth()->user(),  new SupportTicketCreated);
                return redirect()->back()->with('success', 'Reply has been sent successfully');
            }
            else
            {
                return redirect()->back()->with('error', 'There is an error');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
