<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class UserChildPanel extends Model
{
    use SoftDeletes, LogsActivity;

    protected $table = 'user_child_panels';

    protected $fillable = [
        'panel_id', 'user_id', 'domain', 'currency', 'email', 'password', 'price', 'expired_at', 'invoice_sent_at', 'status',
    ];

    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;
    protected static $logName = 'Child panels'; //custom_log_name_for_this_model

    public function getDescriptionForEvent(string $eventName): string
    {
        return self::$logName. " {$eventName}";
    }

    public function tapActivity(Activity $activity)
    {
        $activity->ip = \request()->ip();
        $activity->panel_id = auth()->user()->panel_id;
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
