<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserReferral extends Model
{
    use SoftDeletes;

    protected $table = 'user_referrals';

    protected $fillable = [
        'panel_id', 'referral_id', 'user_id', 'commission_rate', 'minimum_payout', 'status'
    ];

    public function referral()
    {
        return $this->belongsTo(User::class, 'referral_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
