<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Contracts\Activity;

class SettingGeneral extends Model
{
    use SoftDeletes, LogsActivity;

    protected $table = 'setting_generals';
    protected $fillable = [
        'panel_id', 'updated_by', 'logo', 'favicon', 'timezone', 'currency_format', 'rates_rounding', 'ticket_system', 'tickets_per_user',
        'signup_page', 'email_confirmation', 'skype_field', 'name_fields', 'terms_checkbox', 'reset_password', 'average_time',
        'drip_feed_interval', 'custom_header_code', 'custom_footer_code'
    ];

    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;
    protected static $logName = 'General Setting'; //custom_log_name_for_this_model

    public function getDescriptionForEvent(string $eventName): string
    {
        return self::$logName. " {$eventName}";
    }

    public function tapActivity(Activity $activity)
    {
        $activity->ip = \request()->ip();
        $activity->panel_id = auth()->user()->panel_id;
    }
}
