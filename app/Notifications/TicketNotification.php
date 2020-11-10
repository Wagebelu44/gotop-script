<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Support\HtmlString;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class TicketNotification extends Notification
{
    use Queueable;


    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public $noti;
    public $ticket;
    public function __construct($noti, $ticket)
    {
        $this->noti = $noti;
        $this->ticket = $ticket;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $body = str_replace("\n", '<br>', $this->noti->body);
        $body = str_replace('{{ ticket.url }}', '<a href="'.url('tickets?id='.$this->ticket->id).'">'.url('tickets?id='.$this->ticket->id).'</a>', $body);
        return (new MailMessage)
            ->subject($this->noti->subject)
            ->markdown(
                'emails.support_ticket.reply', ['body' => $body]
            );
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
