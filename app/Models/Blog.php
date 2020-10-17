<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class Blog extends Model
{
    use SoftDeletes, LogsActivity;

    protected $table = 'blogs';

    protected $fillable = [
        'panel_id', 'category_id', 'title', 'slug', 'image', 'content', 'meta_title', 'meta_keyword', 'meta_description', 'type', 'status', 'updated_by', 'created_by',
    ];

    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;
    protected static $logName = 'Blog'; //custom_log_name_for_this_model

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
}
