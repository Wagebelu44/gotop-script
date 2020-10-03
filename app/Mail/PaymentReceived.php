<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use App\Models\SettingNotification;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PaymentReceived extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $transaction;

    /**
     * The notification instance.
     *
     * @var CmsSettingNotification $notification
     */
    public $notification;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($transaction, SettingNotification $notification)
    {
        $this->transaction = $transaction;
        $this->notification = $notification;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.payments.received');
    }
}
