<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class Newsfeed extends Model
{
    use SoftDeletes, LogsActivity;

    protected $table = 'newsfeeds';

    protected $fillable = [
        'panel_id', 'title', 'image', 'content', 'important_news', 'service_update', 'news_feed', 'status', 'updated_by', 'created_by',
    ];

    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;
    protected static $logName = 'Newsfeed'; //custom_log_name_for_this_model

    public function getDescriptionForEvent(string $eventName): string
    {
        return self::$logName. " {$eventName}";
    }

    public function tapActivity(Activity $activity)
    {
        $activity->ip = \request()->ip();
        $activity->panel_id = auth()->user()->panel_id;
    }

    public function getCategories()
    {
        return $this->hasMany(NewsfeedRelation::class, 'newsfeed_id', 'id');
    }
}
