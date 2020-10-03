<?php
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

if ( ! function_exists('getTimezone')) {
    function getTimezone()
    {
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
    function getCurrencyFormat()
    {
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
    function getRateFormat()
    {
        $arr = [
            '0' => 'Ones (1)',
            '1' => 'Hundredth (1.11)',
            '2' => 'Thousandth (1.111)',
        ];
        return $arr;
    }
}

if ( ! function_exists('getTicketPerUser')) {
    function getTicketPerUser()
    {
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

if ( ! function_exists('getFileSizeUnits')) {
    function getFileSizeUnits($bytes)
    {
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            $bytes = $bytes . ' bytes';
        } elseif ($bytes == 1) {
            $bytes = $bytes . ' byte';
        } else {
            $bytes = '0 bytes';
        }
        return $bytes;
    }
}

if ( ! function_exists('accountStatusKeys')) {
    function accountStatusKeys()
    {
        $data = [
            '24/7 TICKETS SUPPORT',
            '5% PAYMENTS BONUS**',
            '500$ MONTHLY LOTTERY',
            'FREE SMM PANEL',
            'PRIORITY TICKET SUPPORT',
            'CUSTOM SERVICE',
            'EARLY NOTIFICATION ON NEW',
            'SERVICES',
            'SUPPORT HANDLED BY ADMINS',
        ];
        return $data;
    }
}

if ( ! function_exists('accountPointKeys')) {
    function accountPointKeys()
    {
        $data = [
            'AVAILABLE',
            'CAN REDEEM >= 500 POINTS',
            'CAN REDEEM >= 250 POINTS',
            'CAN REDEEM >= 100 POINTS',
            'LESS THAN 4 DAYS CONVERSATION',
        ];
        return $data;
    }
}

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

if ( ! function_exists('apiServiceJson')) {
    function apiServiceJson()
    {
       $data = [
           [
               "service" => 1,
               "name" => "Followers",
               "type" => "Default",
               "category" => "First Category",
               "rate" => "0.90",
               "min" => "50",
               "max" => "10000"
           ],
           [
               "service" =>  2,
                "name" =>  "Comments",
                "type" =>  "Custom Comments",
                "category" =>  "Second Category",
                "rate" =>  "8",
                "min" =>  "10",
                "max" =>  "1500"
           ]
       ];

       return $data;

    }
}

if ( ! function_exists('apiOrderResponse')) {
    function apiOrderResponse()
    {
       $data = [
           "charge"=>  "0.27819",
           "start_count"=>  "3572",
           "status"=>  "Partial",
           "remains"=>  "157",
           "currency"=>  "USD"
       ];
       return $data;
    }
}

if ( ! function_exists('apiMultiOrderResponse')) {
    function apiMultiOrderResponse()
    {
       $data = [
           [
                1 => [
                    "charge" =>  "0.27819",
                   "start_count" =>  "3572",
                   "status" =>  "Partial",
                   "remains" =>  "157",
                   "currency" =>  "USD"
                ]
           ],
           [
               10 => [
                   "error" =>  "Incorrect order ID"
               ]
           ],
           [
               100 => [
                   "charge" =>  "1.44219",
                   "start_count" =>  "234",
                   "status" =>  "In progress",
                   "remains" =>  "10",
                   "currency" =>  "USD"
               ]
           ]
       ];
       return $data;
    }
}

if ( ! function_exists('apiUserBalance')) {
    function apiUserBalance()
    {
        $data = [
            "balance"=>  "100.84292",
            "currency"=>  "USD"
        ];
        return $data;
    }
}

if ( ! function_exists('apiAddOrder')) {
    function apiAddOrder()
    {
        $data = [
            'name' => [
                "0" => 'Default',
                "1" => 'Package',
                "2" => 'SEO',
                "3" => 'Custom Comments',
                "4" => 'Mentions with Hashtags',
                "5" => 'Mentions Custom List',
                "6" => 'Mentions Hashtag',
                "7" => 'Mentions User Followers',
                "8" => 'Custom Comments Package',
                "9" => 'Comment Likes',
                "10" => 'Poll',
                "11" => 'Subscriptions',
            ],
            'data' => [
                "0" => [
                    ['parameters' => 'key', 'description' => 'Your API key'],
                    ['parameters' => 'action', 'description' => 'add'],
                    ['parameters' => 'service', 'description' => 'Service ID'],
                    ['parameters' => 'link', 'description' => 'Link to page'],
                    ['parameters' => 'quantity', 'description' => 'Needed quantity'],
                    ['parameters' => 'runs (optional)', 'description' => 'Runs to deliver'],
                    ['parameters' => 'interval (optional)', 'description' => 'Interval in minutes'],
                ],
                "1" => [
                    ['parameters' => 'key', 'description' => 'Your API key'],
                    ['parameters' => 'action', 'description' => 'add'],
                    ['parameters' => 'service', 'description' => 'Service ID'],
                    ['parameters' => 'link', 'description' => 'Link to page'],
                ],
                "2" => [
                    ['parameters' => 'key', 'description' => 'Your API key'],
                    ['parameters' => 'action', 'description' => 'add'],
                    ['parameters' => 'service', 'description' => 'Service ID'],
                    ['parameters' => 'link', 'description' => 'Link to page'],
                    ['parameters' => 'quantity', 'description' => 'Needed quantity'],
                    ['parameters' => 'keywords', 'description' => 'Keywords list separated by \r\n or \n'],
                ],
                "3" => [
                    ['parameters' => 'key', 'description' => 'Your API key'],
                    ['parameters' => 'action', 'description' => 'add'],
                    ['parameters' => 'service', 'description' => 'Service ID'],
                    ['parameters' => 'link', 'description' => 'Link to page'],
                    ['parameters' => 'comments', 'description' => 'Comments list separated by \r\n or \n'],
                ],
                "4" => [
                    ['parameters' => 'key', 'description' => 'Your API key'],
                    ['parameters' => 'action', 'description' => 'add'],
                    ['parameters' => 'service', 'description' => 'Service ID'],
                    ['parameters' => 'link', 'description' => 'Link to page'],
                    ['parameters' => 'quantity', 'description' => 'Needed quantity'],
                    ['parameters' => 'usernames', 'description' => 'Usernames list separated by \r\n or \n'],
                    ['parameters' => 'hashtags', 'description' => 'Hashtags list separated by \r\n or \n'],
                ],
                "5" => [
                    ['parameters' => 'key', 'description' => 'Your API key'],
                    ['parameters' => 'action', 'description' => 'add'],
                    ['parameters' => 'service', 'description' => 'Service ID'],
                    ['parameters' => 'link', 'description' => 'Link to page'],
                    ['parameters' => 'quantity', 'description' => 'Needed quantity'],
                    ['parameters' => 'usernames', 'description' => 'Usernames list separated by \r\n or \n'],
                ],
                "6" => [
                    ['parameters' => 'key', 'description' => 'Your API key'],
                    ['parameters' => 'action', 'description' => 'add'],
                    ['parameters' => 'service', 'description' => 'Service ID'],
                    ['parameters' => 'link', 'description' => 'Link to page'],
                    ['parameters' => 'quantity', 'description' => 'Needed quantity'],
                    ['parameters' => 'hashtag', 'description' => 'Hashtag to scrape usernames from'],
                ],
                "7" => [
                    ['parameters' => 'key', 'description' => 'Your API key'],
                    ['parameters' => 'action', 'description' => 'add'],
                    ['parameters' => 'service', 'description' => 'Service ID'],
                    ['parameters' => 'link', 'description' => 'Link to page'],
                    ['parameters' => 'quantity', 'description' => 'Needed quantity'],
                    ['parameters' => 'usernames', 'description' => 'URL to scrape followers from'],
                ],
                "8" => [
                    ['parameters' => 'key', 'description' => 'Your API key'],
                    ['parameters' => 'action', 'description' => 'add'],
                    ['parameters' => 'service', 'description' => 'Service ID'],
                    ['parameters' => 'link', 'description' => 'Link to page'],
                    ['parameters' => 'comments', 'description' => 'Comments list separated by \r\n or \n'],
                ],
                "9" => [
                    ['parameters' => 'key', 'description' => 'Your API key'],
                    ['parameters' => 'action', 'description' => 'add'],
                    ['parameters' => 'service', 'description' => 'Service ID'],
                    ['parameters' => 'link', 'description' => 'Link to page'],
                    ['parameters' => 'quantity', 'description' => 'Needed quantity'],
                    ['parameters' => 'username', 'description' => 'Username of the comment owner'],
                ],
                "10" => [
                    ['parameters' => 'key', 'description' => 'Your API key'],
                    ['parameters' => 'action', 'description' => 'add'],
                    ['parameters' => 'service', 'description' => 'Service ID'],
                    ['parameters' => 'link', 'description' => 'Link to page'],
                    ['parameters' => 'quantity', 'description' => 'Needed quantity'],
                    ['parameters' => 'answer_number', 'description' => 'Answer number of the poll'],
                ],
                "11" => [
                    ['parameters' => 'key', 'description' => 'Your API key'],
                    ['parameters' => 'action', 'description' => 'add'],
                    ['parameters' => 'service', 'description' => 'Service ID'],
                    ['parameters' => 'username', 'description' => 'Username'],
                    ['parameters' => 'min', 'description' => 'Quantity min'],
                    ['parameters' => 'max', 'description' => 'Quantity max'],
                    ['parameters' => 'posts', 'description' => 'New posts count'],
                    ['parameters' => 'delay', 'description' => 'Delay in minutes. Possible values: 0, 5, 10, 15, 30, 60, 90'],
                    ['parameters' => 'expiry (optional)', 'description' => 'Expiry date. Format d/m/Y'],
                ],
            ],
        ];
        return $data;
    }
}

if (! function_exists('notification')) {
    function notification($title, $type, $panel_id)
    {
        return \App\Models\SettingNotification::where(['title' => $title, 'type' => $type, 'panel_id' => $panel_id])->first();
    }
}

if (! function_exists('staffEmails')) {
    function staffEmails($action, $panel_id)
    {
        return \App\Models\StaffEmail::where($action, '1')->where('panel_id', $panel_id)->pluck('email');
    }
}


