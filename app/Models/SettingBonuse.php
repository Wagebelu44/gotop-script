<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Contracts\Activity;

class SettingBonuse extends Model
{
    use SoftDeletes, LogsActivity;

    protected $table = 'setting_bonuses';

    protected $fillable = [
      'panel_id', 'global_payment_method_id', 'bonus_amount', 'deposit_from', 'status', 'created_by', 'updated_by'
    ];

    public function globalPaymentMethod() 
    {
        return $this->belongsTo(GlobalPaymentMethod::class, 'global_payment_method_id', 'id');
    }

    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;
    protected static $logName = 'Bonus'; //custom_log_name_for_this_model

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
