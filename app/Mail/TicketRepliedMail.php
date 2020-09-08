<?php

namespace App\Mail;

use App\Models\SettingNotification;
use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TicketRepliedMail extends Mailable
{
    use Queueable, SerializesModels;


    /**
     * The notification instance.
     *
     * @var SettingNotification $notification
     */
    public $notification;

    /**
     * The ticket instance.
     *
     * @var Ticket $ticket
     */
    public $ticket;


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Ticket $ticket, SettingNotification $notification)
    {
        $this->ticket = $ticket;
        $this->notification = $notification;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.tickets.replied');
    }
}
