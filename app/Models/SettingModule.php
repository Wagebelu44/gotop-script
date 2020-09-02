<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SettingModule extends Model
{
    use SoftDeletes;

    protected $table = 'setting_modules';

    protected $fillable = [
        'panel_id', 'amount', 'commission_rate', 'approve_payout', 'title', 'description', 'type', 'status', 'updated_by', 'created_by',
    ];
}
