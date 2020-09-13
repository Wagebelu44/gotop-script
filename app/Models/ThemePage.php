<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class ThemePage extends Model
{
    use SoftDeletes, LogsActivity;

    protected $table = 'theme_pages';

    protected $fillable = [
        'panel_id', 'page_id', 'theme_id', 'group', 'name', 'content', 'sort',
    ];

    function groupPages()
    {
        return $this->hasMany(ThemePage::class, 'group', 'group');
    }

    function page()
    {
        return $this->belongsTo(Page::class);
    }

    function theme()
    {
        return $this->belongsTo(Theme::class);
    }

    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;
    protected static $logName = 'Theme Page'; //custom_log_name_for_this_model

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
