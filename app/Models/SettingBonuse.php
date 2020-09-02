<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SettingBonuse extends Model
{
    use SoftDeletes;

    protected $table = 'setting_bonuses';

    protected $fillable = [
      'panel_id', 'global_payment_method_id', 'bonus_amount', 'deposit_from', 'status', 'created_by', 'updated_by'
    ];

    public function globalPaymentMethod() {
        return $this->belongsTo(GlobalPaymentMethod::class, 'global_payment_method_id', 'id');
    }
}
