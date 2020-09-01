<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SettingProvider extends Model
{

    use SoftDeletes;

    protected $table = 'setting_providers';

    protected $fillable = [
        'panel_id', 'domain', 'api_url', 'api_key', 'status', 'created_by', 'updated_by', 'deleted_at'
    ];
}
