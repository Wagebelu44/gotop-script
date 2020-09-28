<?php

namespace App\Models;

use App\Models\PaymentMethod;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use SoftDeletes, LogsActivity;

    protected $table = 'transactions';
    protected $fillable = ['id', 'transaction_type', 'amount', 'transaction_flag', 'user_id', 'admin_id', 'status', 'memo', 'fraud_risk', 'payment_gateway_response', 'transaction_detail', 'tnx_id', 'reseller_payment_methods_setting_id', 'panel_id', 'sequence_number', 'created_at', 'updated_at', 'deleted_at'];


    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;
    protected static $logName = 'Tickets'; //custom_log_name_for_this_model

    public function getDescriptionForEvent(string $eventName): string
    {
        return self::$logName. " {$eventName}";
    }

    public function tapActivity(Activity $activity)
    {
        $activity->ip = \request()->ip();
        $activity->panel_id = auth()->user()->panel_id;
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
    public function resellerPaymentMethodsSetting()
    {
        return $this->belongsTo(PaymentMethod::class);
    }
}
