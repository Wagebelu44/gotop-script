<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class Menu extends Model
{
    use SoftDeletes, LogsActivity;

    protected $table = 'menus';

    protected $fillable = [
        'panel_id', 'menu_name', 'external_link', 'menu_link_id', 'menu_link_type', 'sort', 'status', 'updated_by', 'created_by',
    ];

    public function page() 
    {
        return $this->belongsTo(Page::class, 'menu_link_id');
    }

    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;
    protected static $logName = 'Menu'; //custom_log_name_for_this_model

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
