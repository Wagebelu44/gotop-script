<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class ExportedOrder extends Model
{
    use SoftDeletes, LogsActivity;

    protected $table = 'exported_orders';
    protected $fillable = ['id', 'from', 'to', 'status', 'mode', 'format', 'include_columns', 'user_ids', 'service_ids', 'provider_ids', 'panel_id', 'created_at', 'updated_at'];
    
    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;
    protected static $logName = 'Exported Orders'; //custom_log_name_for_this_model

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
