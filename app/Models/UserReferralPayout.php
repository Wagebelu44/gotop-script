<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserReferralPayout extends Model
{
    use SoftDeletes;

    protected $table = 'user_referral_payouts';

    protected $fillable = [
        'panel_id', 'referral_id', 'amount', 'date', 'mode', 'status'
    ];

    public function referral()
    {
        return $this->belongsTo(User::class, 'referral_id', 'id');
    }
}
