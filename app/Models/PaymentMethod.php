<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentMethod extends Model
{
    use SoftDeletes, LogsActivity;

    protected $table = 'payment_methods';

    protected $fillable = [
        'panel_id', 'global_payment_method_id', 'method_name', 'minimum', 'maximum', 'new_user_status', 'visibility', 'details', 'sort', 'created_by', 'updated_by'
    ];

    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;
    protected static $logName = 'Payment methods'; //custom_log_name_for_this_model

    public function getDescriptionForEvent(string $eventName): string
    {
        return self::$logName. " {$eventName}";
    }

    public function tapActivity(Activity $activity)
    {
        $activity->ip = \request()->ip();
        $activity->panel_id = auth()->user()->panel_id;
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_payment_methods', 'user_id', 'payment_id');
    }
}
