<?php

namespace App\Models\G;

use App\Models\SettingBonuse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GlobalPaymentMethod extends Model
{
    use SoftDeletes;

    protected $table = 'global_payment_methods';

    protected $fillable = [
        'uuid', 'name', 'fields', 'status'
    ];

    public function settingBonus() 
    {
        return $this->belongsTo(SettingBonuse::class);
    }
}
