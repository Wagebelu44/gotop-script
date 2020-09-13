<?php
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

if ( ! function_exists('defaultThemePageContent')) {
    function defaultThemePageContent()
    {
        return '{% if content %}
            <div class="container">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <div class="well">
                            {{ content }}
                        </div>
                    </div>
                </div>
            </div>
        {% endif %}';
    }
}

//Delete existing file
if ( ! function_exists('deleteFile')) {
    function deleteFile($path, $name)
    {
        if (file_exists($path.'/'.$name)) {
            unlink($path.'/'.$name);
        }
    }
}

//Search string get and set an url
if ( ! function_exists('qString')) {
    function qString($query = null)
    {
        if (isset($_SERVER['QUERY_STRING'])) {
            return '?'.$_SERVER['QUERY_STRING'].$query;
        } else {
            if ($query) {
                return '?'.$query;
            }
        }
    }
}

//Date View
if ( ! function_exists('dateFormat')) {
    function dateFormat($date, $time = null)
    {
        if ($time) {
            return date('d/M/Y h:i A',(strtotime($date)));
        } else {
            return date('d/M/Y',strtotime($date));
        }
    }
}

//Time View
if ( ! function_exists('timeFormat')) {
    function timeFormat($date)
    {
        return date('h:i A',(strtotime($date)));
    }
}

//Two Digit Number Format Function
if ( ! function_exists('numberFormat')) {
    function numberFormat($amount=0, $coma=null)
    {
        if ($coma) {
            if ($amount==0)
                return '-';
            else
                return number_format($amount,2);
        } else {
            return number_format($amount,2,'.','');
        }
    }
}

if ( ! function_exists('getTimezone')) {
    function getTimezone() {
        $arr = [
            '-43200' => '(UTC -12:00) Baker/Howland Island',
            '-39600' => '(UTC -11:00) Niue',
            '-36000' => '(UTC -10:00) Hawaii-Aleutian Standard Time, Cook Islands, Tahiti',
            '-34200' => '(UTC -9:30) Marquesas Islands',
            '-32400' => '(UTC -9:00) Alaska Standard Time, Gambier Islands',
            '-28800' => '(UTC -8:00) Pacific Standard Time, Clipperton Island',
            '-25200' => '(UTC -7:00) Mountain Standard Time',
            '-21600' => '(UTC -6:00) Central Standard Time',
            '-18000' => '(UTC -5:00) Eastern Standard Time, Western Caribbean Standard Time',
            '-16200' => '(UTC -4:30) Venezuelan Standard Time',
            '-14400' => '(UTC -4:00) Atlantic Standard Time, Eastern Caribbean Standard Time',
            '-12600 ' => '(UTC -3:30) Newfoundland Standard Time',
            '-10800 ' => '(UTC -3:00) Argentina, Brazil, French Guiana, Uruguay',
            '-7200 ' => '(UTC -2:00) South Georgia/South Sandwich Islands',
            '-3600 ' => '(UTC -1:00) Azores, Cape Verde Islands',
            '0' => '(UTC) Greenwich Mean Time, Western European Time',
            '3600' => '(UTC +1:00) Central European Time, West Africa Time',
            '7200' => '(UTC +2:00) Central Africa Time, Eastern European Time, Kaliningrad Time',
            '10800' => '(UTC +3:00) Moscow Time, East Africa Time, Arabia Standard Time',
            '12600' => '(UTC +3:30) Iran Standard Time',
            '14400' => '(UTC +4:00) Azerbaijan Standard Time, Samara Time',
            '16200' => '(UTC +4:30) Afghanistan',
            '18000' => '(UTC +5:00) Pakistan Standard Time, Yekaterinburg Time',
            '19800' => '(UTC +5:30) Indian Standard Time, Sri Lanka Time',
            '20700' => '(UTC +5:45) Nepal Time',
            '21600' => '(UTC +6:00) Bangladesh Standard Time, Bhutan Time, Omsk Time',
            '23400' => '(UTC +6:30) Cocos Islands, Myanmar',
            '25200' => '(UTC +7:00) Krasnoyarsk Time, Cambodia, Laos, Thailand, Vietnam',
            '28800' => '(UTC +8:00) Australian Western Standard Time, Beijing Time, Irkutsk Time',
            '31500' => '(UTC +8:45) Australian Central Western Standard Time',
            '32400' => '(UTC +9:00) Japan Standard Time, Korea Standard Time, Yakutsk Time',
            '34200' => '(UTC +9:30) Australian Central Standard Time',
            '36000' => '(UTC +10:00) Australian Eastern Standard Time, Vladivostok Time',
            '37800' => '(UTC +10:30) Lord Howe Island',
            '39600' => '(UTC +11:00) Srednekolymsk Time, Solomon Islands, Vanuatu',
            '41400' => '(UTC +11:30) Norfolk Island',
            '43200' => '(UTC +12:00) Fiji, Gilbert Islands, Kamchatka Time, New Zealand Standard Time',
            '45900' => '(UTC +12:45) Chatham Islands Standard Time',
            '46800' => '(UTC +13:00) Samoa Time Zone, Phoenix Islands Time, Tonga',
            '50400' => '(UTC +14:00) Line Island'
        ];
        return $arr;
    }
}

if ( ! function_exists('getCurrencyFormat')) {
    function getCurrencyFormat() {
        $arr = [
            '0' => '1000.00',
            '1' => '1000,00',
            '2' => '1,000.12',
            '3' => '1,000',
        ];
        return $arr;
    }
}

if ( ! function_exists('getRateFormat')) {
    function getRateFormat() {
        $arr = [
            '0' => 'Ones (1)',
            '1' => 'Hundredth (1.11)',
            '2' => 'Thousandth (1.111)',
        ];
        return $arr;
    }
}

if ( ! function_exists('getTicketPerUser')) {
    function getTicketPerUser(){
        $arr = [];
        for ($i = 0; $i <= 9; $i++) :
            if ($i == 0) :
                $arr[$i] = 'Unlimited';
            else:
                $arr[$i] = $i . ' tickets';
            endif;
        endfor;
        return $arr;
    }
}

if ( ! function_exists('SettingNotificationTableSeeder')) {
    function SettingNotificationTableSeeder(){
        DB::table('setting_notifications')->insert([
            [
                'panel_id' => Auth::user()->panel_id,
                'subject' => 'Welcome',
                'body' =>  'Hello,
Thank you for signing up.
Your username is: {{ user.username }}
Use it to sign in to {{ panel.url }}',
                'title' => 'Welcome',
                'description' => 'Sent to new users when their account is created.',
                'type' => '1',
                'status' => 'inactive',
                'created_by' => Auth::user()->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'panel_id' => Auth::user()->panel_id,
                'subject' => 'Welcome',
                'body' =>  'Hello,
You requested a password change. To change your password follow the link below: {{ resetpassword.url }}',
                'title' => 'Forgot password',
                'description' => 'Sent to users when they request a password reset.',
                'type' => '1',
                'status' => 'inactive',
                'created_by' => Auth::user()->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'panel_id' => Auth::user()->panel_id,
                'subject' => 'New message',
                'body' =>   'Hello,
You have a new message in the ticket.
Follow the link below to see the message: {{ ticket.url }}',
                'title' => 'New message',
                'description' => 'Sent to users when they receive a new message',
                'type' => '1',
                'status' => 'inactive',
                'created_by' => Auth::user()->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'panel_id' => Auth::user()->panel_id,
                'subject' => 'Payment received',
                'body' => 'New payment #{{ payment.id }} received.
View payment in admin panel: {{ payment.admin_url }}',
                'title' => 'Payment received',
                'description' => 'Sent to staff when a user adds funds automatically.',
                'type' => '2',
                'status' => 'inactive',
                'created_by' => Auth::user()->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'panel_id' => Auth::user()->panel_id,
                'subject' => 'New manual orders',
                'body' =>   'New manual order(s) received. Total pending manual orders: {{ orders.manual.pending_number }}
View all manual orders in admin panel: {{ orders.manual.url }}',
                'title' => 'New manual orders',
                'description' => 'Periodically sent to staff if new manual orders received.',
                'type' => '2',
                'status' => 'inactive',
                'created_by' => Auth::user()->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'panel_id' => Auth::user()->panel_id,
                'subject' => 'Fail orders',
                'body' =>   'Order(s) got Fail status. Total orders with Fail status: {{ orders.fail_number }}
View Fail orders in admin panel: {{ orders.fail_url }}',
                'title' => 'Fail orders',
                'description' => 'Periodically sent to staff if some orders got Fail status.',
                'type' => '2',
                'status' => 'inactive',
                'created_by' => Auth::user()->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'panel_id' => Auth::user()->panel_id,
                'subject' => 'New messages',
                'body' =>   'New message(s) received. Total unread tickets: {{ tickets.unread_number }}
View tickets in admin panel: {{ tickets.url }}',
                'title' => 'New messages',
                'description' => 'Periodically sent to staff if new messages received.',
                'type' => '2',
                'status' => 'inactive',
                'created_by' => Auth::user()->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'panel_id' => Auth::user()->panel_id,
                'subject' => 'New manual payout',
                'body' =>   'New manual payout request received.
View Payouts in admin panel: {{ affiliates.payouts }}',
                'title' => 'New manual payout',
                'description' => 'Sent to staff when a user create manual payout.',
                'type' => '2',
                'status' => 'inactive',
                'created_by' => Auth::user()->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        return true;
    }
}

