<?php

namespace App\Models;

use App\Models\G\GlobalTheme;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class Theme extends Model
{
    use SoftDeletes, LogsActivity;

    protected $table = 'themes';

    protected $fillable = [
        'panel_id', 'global_theme_id', 'name', 'location', 'snapshot', 'status', 'activated_at',
    ];

    function pages()
    {
        return $this->hasMany(ThemePage::class)->where('page_id', '>', '0');
    }

    function globalTheme()
    {
        return $this->belongsTo(GlobalTheme::class);
    }

    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;
    protected static $logName = 'Theme'; //custom_log_name_for_this_model

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
