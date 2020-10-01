<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserReferralAmount extends Model
{
    use SoftDeletes;

    protected $table = 'user_referral_amounts';

    protected $fillable = [
        'panel_id', 'referral_id', 'user_id', 'amount'
    ];
}
