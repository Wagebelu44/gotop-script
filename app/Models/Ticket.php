<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class Ticket extends Model
{
    use SoftDeletes, LogsActivity, Notifiable;

    protected $table = 'tickets';

    protected $fillable = [
        'panel_id', 'user_id', 'send_by', 'sender_role', 'subject', 'subject_ids', 'payment_type', 'description', 'status', 'seen_by_admin', 'seen_by_user', 'created_by', 'updated_by',
    ];

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
        return  timezone(request()->session()->get('timezone'), $value); 
    }

    public function getUpdatedAtAttribute($value)
    {
        return  timezone(request()->session()->get('timezone'), $value); 
    }

    /*Relationship*/

    public function comments()
    {
        return $this->morphMany(TicketComment::class, 'commentable');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
    public function sender()
    {
        return $this->belongsTo('App\User', 'send_by');
    }
}
