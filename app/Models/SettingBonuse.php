<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class SettingBonuse extends Model
{
    use SoftDeletes, LogsActivity;

    protected $table = 'setting_bonuses';

    protected $fillable = [
      'panel_id', 'global_payment_method_id', 'bonus_amount', 'deposit_from', 'status', 'created_by', 'updated_by'
    ];

    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;
    protected static $logName = 'bonuses'; //custom_log_name_for_this_model

    public function getDescriptionForEvent(string $eventName): string
    {
        return self::$logName. " {$eventName}";
    }

    public function globalPaymentMethod() {
        return $this->belongsTo(GlobalPaymentMethod::class, 'global_payment_method_id', 'id');
    }


}
