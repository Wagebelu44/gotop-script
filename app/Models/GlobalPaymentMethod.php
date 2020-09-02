<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GlobalPaymentMethod extends Model
{
    use SoftDeletes;

    protected $table = 'global_payment_methods';

    protected $fillable = [
        'name', 'fields', 'status'
    ];
}
