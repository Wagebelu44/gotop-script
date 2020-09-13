<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class Order extends Model
{
    use SoftDeletes, LogsActivity;

    protected $table = 'orders';

    protected $fillable = [
        'panel_id', 'user_id',  'order_id',  'service_id',  'drip_feed_id',  'category_id',  'provider_id',  'provider_order_id',  'provider_order_id',
        'charges',  'original_charges',  'unit_price',  'original_unit_price',  'link',  'start_counter',  'remains',  'quantity',  'auto_order_response',
        'custom_comments',  'mode',  'source',  'order_viewable_time',  'text_area_1',  'text_area_2',  'additional_inputs',  'refill_status',  'refill_order_status',
        'status', 'created_by', 'updated_by',
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

    public function user()
    {
        return $this->belongsTo('App\User');
    }


}
