<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class User extends Authenticatable
{
    use Notifiable, SoftDeletes, LogsActivity;

    protected $table = 'users';
    
    protected $fillable = [
        'panel_id'
        , 'username'
        , 'skype_name'
        , 'phone'
        , 'balance'
        , 'email'
        , 'api_key'
        , 'referral_key'
        , 'email_verified_at'
        , 'password'
        , 'status'
        , 'status'
    ];
    
    protected $hidden = [
        'password', 'remember_token',
    ];
    
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];  

    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;
    protected static $logName = 'User'; //custom_log_name_for_this_model

    public function getDescriptionForEvent(string $eventName): string
    {
        return self::$logName. " {$eventName}";
    }

    public function tapActivity(Activity $activity)
    {
        $activity->ip = \request()->ip();
        $activity->panel_id = auth()->user()->panel_id??1;
    }
}
