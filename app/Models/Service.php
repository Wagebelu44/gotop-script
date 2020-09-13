<?php

namespace App\Models;

use App\Models\ProviderService;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use SoftDeletes, LogsActivity;

    protected $table = 'services';
    protected $fillable = [
        'id', 'panel_id', 'category_id', 'sort', 'name', 'mode', 'drip_feed_status', 'refill_status', 'link_duplicates', 'service_type', 'crown', 'price', 'increment', 'auto_overflow', 'min_quantity', 'max_quantity', 'provider_id', 'provider_service_id', 'provider_sync_status', 'short_description', 'description', 'icon', 'service_average_time', 'subscription_type', 'is_user', 'status',
    ];

    public function provider()
    {
        return $this->hasOne(ProviderService::class, 'service_id', 'id');
    }
    
    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;
    protected static $logName = 'Service'; //custom_log_name_for_this_model

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
