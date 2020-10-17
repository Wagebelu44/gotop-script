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
    protected $fillable = ['id', 'panel_id', 'user_id', 'admin_id', 'global_payment_method_id', 'tnx_id', 'transaction_type', 'transaction_flag', 'amount', 'memo', 'fraud_risk', 'transaction_detail', 'payment_gateway_response', 'sequence_number', 'status', 'created_at', 'updated_at', 'deleted_at'];


    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;
    protected static $logName = 'Tickets'; //custom_log_name_for_this_model

    public function getDescriptionForEvent(string $eventName): string
    {
        return self::$logName. " {$eventName}";
    }

    public function getAmountAttribute($value)
    {
        return currencyFormat(request()->session()->get('currency_format'), $value); 
    }

    public function getCreatedAtAttribute($value)
    {
        return  timezone(request()->session()->get('timezone'), $value); 
    }

    public function getUpdatedAtAttribute($value)
    {
        return  timezone(request()->session()->get('timezone'), $value); 
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
