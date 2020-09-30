<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserReferral extends Model
{
    use SoftDeletes;

    protected $table = 'user_referrals';

    protected $fillable = [
        'panel_id', 'referral_id', 'user_id', 'commission_rate', 'minimum_payout'
    ];
}
