<?php

namespace App\Models\G;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GlobalTheme extends Model
{
    use SoftDeletes;

    protected $table = 'global_themes';

    protected $fillable = [
        'name', 'location', 'snapshot', 'status',
    ];
}
