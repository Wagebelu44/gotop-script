<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserReferralVisit extends Model
{
    use SoftDeletes;

    protected $table = 'user_referral_visits';

    protected $fillable = [
        'panel_id', 'referral_id', 'visitor_ip'
    ];
}
