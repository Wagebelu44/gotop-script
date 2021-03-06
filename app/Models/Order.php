<?php

namespace App\Models;

use App\Models\Service;
use App\Models\SettingProvider;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes, LogsActivity;

    protected $table = 'orders';

    protected $fillable = [
        'panel_id', 'user_id', 'order_type', 'order_id',  'service_id',  'drip_feed_id',  'category_id',  'provider_id',  'provider_order_id',  'provider_order_id',
        'charges',  'original_charges',  'unit_price',  'original_unit_price',  'link',  'start_counter',  'remains',  'quantity',  'auto_order_response',
        'custom_comments',  'mode',  'source',  'order_viewable_time',  'text_area_1',  'text_area_2',  'additional_inputs',  'refill_status',  'refill_order_status',
        'admin_seen', 'status', 'created_by', 'updated_by', 'completed_at', 'duration'
    ];

    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;
    protected static $logName = 'Orders'; //custom_log_name_for_this_model

    public function getDescriptionForEvent(string $eventName): string
    {
        return self::$logName. " {$eventName}";
    }

    public function tapActivity(Activity $activity)
    {
        $activity->ip = \request()->ip();
        $activity->panel_id = auth()->user()->panel_id;
    }

    public function getStatusColorAttribute()
    {
        $colors = [
            'completed' => "#77b243",
            'cancelled' => "#f35151",
            'Canceled' => "#f35151",
            'processing' => "#afeeee",
            'inprogress' => "#ffe675",
            'partial' => "#17a2b8",
        ];
        return $colors[$this->status]??'#77B243';
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function provider()
    {
        return $this->belongsTo(SettingProvider::class, 'provider_id');
    }

    public function getCreatedAtAttribute($value)
    {
        $setting = request()->session()->get('timezone');
        if (!\Request::is('admin/*') && auth()->user()->timezone != null) {
            $setting = auth()->user()->timezone;
        }
        return  timezone($setting, $value); 
    }

    public function getCompletedAtAttribute($value)
    {
        $setting = request()->session()->get('timezone');
        if (!\Request::is('admin/*') && auth()->user()->timezone != null) {
            $setting = auth()->user()->timezone;
        }
        return  timezone($setting, $value); 
    }

    public function getOrderViewableTimeAttribute($value)
    {
        $setting = request()->session()->get('timezone');
        if (!\Request::is('admin/*') && auth()->user()->timezone != null) {
            $setting = auth()->user()->timezone;
        }
        return  timezone($setting, $value); 
    }

    public function getUpdatedAtAttribute($value)
    {
        $setting = request()->session()->get('timezone');
        if (!\Request::is('admin/*') && auth()->user()->timezone != null) {
            $setting = auth()->user()->timezone;
        }
        return  timezone($setting, $value); 
    }

    public function getChargesAttribute($value)
    {
        return  (float)currencyFormat(request()->session()->get('currency_format'), $value);
    }
}
