<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class SettingFaq extends Model
{
    use SoftDeletes, LogsActivity;

    protected $table = 'setting_faqs';

    protected $fillable = [
      'panel_id', 'question', 'answer', 'sort', 'status', 'created_by', 'updated_by', 'deleted_at'
    ];

    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;
    protected static $logName = 'faq'; //custom_log_name_for_this_model

    public function getDescriptionForEvent(string $eventName): string
    {
        return self::$logName. " {$eventName}";
    }
}
