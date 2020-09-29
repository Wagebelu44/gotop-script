<?php

namespace App\Models\G;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GlobalCurrencies extends Model
{
    use SoftDeletes;

    protected $table = 'global_currencies';

    protected $fillable = [
        'code', 'sign', 'name', 'status',
    ];
}
