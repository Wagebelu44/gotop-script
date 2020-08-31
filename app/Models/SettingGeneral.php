<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SettingGeneral extends Model
{
    protected $table = 'setting_generals';
    protected $fillable = [
        'panel_id', 'updated_by', 'logo', 'favicon', 'timezone', 'currency_format', 'rates_rounding', 'ticket_system', 'tickets_per_user',
        'signup_page', 'email_confirmation', 'skype_field', 'name_fields', 'terms_checkbox', 'reset_password', 'average_time',
        'drip_feed_interval', 'custom_header_code', 'custom_footer_code'
    ];
}
