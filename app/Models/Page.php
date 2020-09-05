<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class Page extends Model
{
    use SoftDeletes, LogsActivity;

    protected $table = 'pages';

    protected $fillable = [
        'panel_id', 'deleted_at', 'updated_by', 'created_by', 'page_name', 'content', 'url', 'public', 'page_title', 'meta_keyword', 'meta_description', 'status', 'non_editable'
    ];

    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;
    protected static $logName = 'Page'; //custom_log_name_for_this_model

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
