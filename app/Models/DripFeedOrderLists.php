<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class DripFeedOrderLists extends Model
{
    use SoftDeletes, LogsActivity;
    protected $table = 'drip_feed_order_lists';
    protected $fillable = ['id','order_id','status charges','original_charges','unit_price','original_unit_price','link','start_counter','remains','quantity','user_id','service_id','category_id','provider_id','provider_order_id','custom_comments','mode','source','drip_feed_id','order_viewable_time','text_area_1','text_area_2','additional_inputs','refill_status','refill_order_status','order_posted','order_table_id','panel_id','created_at','updated_at','deleted_at'];

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
}
