<?php

namespace App;

use App\User;
use App\Models\Service;
use App\Models\PaymentMethod;
use App\Models\UserPaymentMethod;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable, SoftDeletes, LogsActivity;

    protected $table = 'users';

    protected $fillable = [
        'panel_id', 'username', 'skype_name', 'phone', 'balance', 'email', 'api_key', 'referral_key', 'email_verified_at', 'password', 'status',
    ];

    protected $appends = ['balance'];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function balance()
    {
        return 5000;
    }
    public function getBalanceAttribute()
    {
        return 50000;
    }
    public function servicesList()
    {
        return $this->belongsToMany(Service::class, 'service_price_user', 'user_id', 'service_id')->withPivot('price', 'panel_id');
    }

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

    public function paymentMethods()
    {
        return $this->hasMany(UserPaymentMethod::class, 'user_id', 'id');
    }
}
