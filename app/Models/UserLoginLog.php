<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserLoginLog extends Model
{
    protected $table = 'user_login_logs';
    protected $fillable = ['id', 'panel_id', 'user_id', 'ip', 'name', 'location', 'created_at', 'updated_at'];


    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;
    protected static $logName = 'Tickets'; //custom_log_name_for_this_model

    public function getDescriptionForEvent(string $eventName): string
    {
        return self::$logName. " {$eventName}";
    }

    public function tapActivity(Activity $activity)
    {
        $activity->ip = \request()->ip();
        $activity->panel_id = auth()->user()->panel_id;
    }

    public function getCreatedAtAttribute($value)
    {
        $setting = request()->session()->get('timezone');
        if (!\Request::is('admin/*') &&  $this->timezone != null) {
            $setting = $this->timezone;
        }
        return  timezone($setting, $value); 
    }

    
}
