<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GlobalNotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('global_notifications')->insert([
            [
                'type' => '1',
                'title' => 'Welcome',
                'description' => 'Sent to new users when their account is created.',
                'subject' => 'Welcome',
                'body' =>  'Hello,
Thank you for signing up.
Your username is: {{ user.username }}
Use it to sign in to {{ panel.url }}',
                'status' => 'Active',
            ],
            [
                'type' => '1',
                'title' => 'Forgot password',
                'description' => 'Sent to users when they request a password reset.',
                'subject' => 'Welcome',
                'body' =>  'Hello,
You requested a password change. To change your password follow the link below: {{ resetpassword.url }}',
                'status' => 'Active',
            ],
            [
                'type' => '1',
                'title' => 'New message',
                'description' => 'Sent to users when they receive a new message',
                'subject' => 'New message',
                'body' =>   'Hello,
You have a new message in the ticket.
Follow the link below to see the message: {{ ticket.url }}',
                'status' => 'Active',
            ],
            [
                'type' => '2',
                'title' => 'Payment received',
                'description' => 'Sent to staff when a user adds funds automatically.',
                'subject' => 'Payment received',
                'body' => 'New payment #{{ payment.id }} received.
View payment in admin panel: {{ payment.admin_url }}',
                'status' => 'Active',
            ],
            [
                'type' => '2',
                'title' => 'New manual orders',
                'description' => 'Periodically sent to staff if new manual orders received.',
                'subject' => 'New manual orders',
                'body' =>   'New manual order(s) received. Total pending manual orders: {{ orders.manual.pending_number }}
View all manual orders in admin panel: {{ orders.manual.url }}',
                'status' => 'Active',
            ],
            [
                'type' => '2',
                'title' => 'Fail orders',
                'description' => 'Periodically sent to staff if some orders got Fail status.',
                'subject' => 'Fail orders',
                'body' =>   'Order(s) got Fail status. Total orders with Fail status: {{ orders.fail_number }}
View Fail orders in admin panel: {{ orders.fail_url }}',
                'status' => 'Active',
            ],
            [
                'type' => '2',
                'title' => 'New messages',
                'description' => 'Periodically sent to staff if new messages received.',
                'subject' => 'New messages',
                'body' =>   'New message(s) received. Total unread tickets: {{ tickets.unread_number }}
View tickets in admin panel: {{ tickets.url }}',
                'status' => 'Active',
            ],
            [
                'type' => '2',
                'title' => 'New manual payout',
                'description' => 'Sent to staff when a user create manual payout.',
                'subject' => 'New manual payout',
                'body' =>   'New manual payout request received.
View Payouts in admin panel: {{ affiliates.payouts }}',
                'status' => 'Active',
            ]
		]);
    }
}
