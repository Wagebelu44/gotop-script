<?php

namespace App;

use App\Models\Service;
use App\Models\PaymentMethod;
use App\Models\UserPaymentMethod;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\UserResetPasswordNotification;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\UserEmailVerificationNotification;

class User extends Authenticatable
{
    use Notifiable, SoftDeletes, LogsActivity;

    protected $table = 'users';

    protected $fillable = [
        'uuid', 'panel_id', 'first_name', 'last_name', 'skype_name', 'phone', 'balance', 'email', 'username', 'api_key', 'referral_key', 'email_verified_at', 'email_confirmation_status', 'password', 'affiliate_status', 'status',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

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

    public function methods()
    {
        return $this->belongsToMany(PaymentMethod::class, 'user_payment_methods', 'payment_id', 'user_id');
    }

    //Send email verify notification
    public function sendEmailVerificationNotification()
    {
        $this->notify(new UserEmailVerificationNotification());
    }

    //Send password reset notification
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new UserResetPasswordNotification($token));
    }
}
