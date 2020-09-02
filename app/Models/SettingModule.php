<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class SettingModule extends Model
{
    use SoftDeletes, LogsActivity;

    protected $table = 'setting_modules';

    protected $fillable = [
        'panel_id', 'amount', 'commission_rate', 'approve_payout', 'title', 'description', 'type', 'status', 'updated_by', 'created_by',
    ];

    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;
    protected static $logName = 'module'; //custom_log_name_for_this_model

    public function getDescriptionForEvent(string $eventName): string
    {
        return self::$logName. " {$eventName}";
    }
}
