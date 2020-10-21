<?php

namespace App\Models;

use App\Models\Order;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;

class SettingProvider extends Model
{

    use SoftDeletes, LogsActivity;

    protected $table = 'setting_providers';

    protected $fillable = [
        'panel_id', 'domain', 'api_url', 'api_key', 'status', 'created_by', 'updated_by',
    ];

    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;
    protected static $logName = 'Provider'; //custom_log_name_for_this_model

    public function getDescriptionForEvent(string $eventName): string
    {
        return self::$logName. " {$eventName}";
    }

    public function tapActivity(Activity $activity)
    {
        $activity->ip = \request()->ip();
        $activity->panel_id = auth()->user()->panel_id;
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'provider_id');
    }

    protected $appends = ['balance'];

    public function getBalanceAttribute()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->api_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, 
                http_build_query(array(
                    'key' =>$this->api_key,
                    'action' => 'balance',
                    )));
        // Receive server response ...
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        curl_close ($ch);
        return json_decode($server_output, true);
    }
}
