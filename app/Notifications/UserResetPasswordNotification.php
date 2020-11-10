<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserResetPasswordNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    
      //Token handler
    public $token;

    public function __construct($token)
    {
        $this->token = $token;
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
        $notification = notification('Forgot password', 1, session('panel'));
        if ($notification) {
            if ($notification->status =='Active') {
                $body = str_replace("\n", '<br>', $notification->body);
                $body = str_replace('{{ resetpassword.url }}', '<a href="'.url('password-set', $this->token).'">'.url('password-set', $this->token).'</a>', $body);
                return (new MailMessage)
                    ->subject($notification->subject)
                    ->markdown(
                        'emails.notification', ['body' => $body]
                    );
            }
        } else {
            return (new MailMessage)
                ->line('You are receiving this email because we received a password reset request for your account.')
                ->action('Reset Password', url('password-set', $this->token))
                ->line('If you did not request a password reset, no further action is required.');
        }
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
