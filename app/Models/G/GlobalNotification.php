<?php

namespace App\Models\G;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GlobalNotification extends Model
{
    use SoftDeletes;

    protected $table = 'global_notifications';

    protected $fillable = [
        'type', 'title', 'description', 'subject', 'body', 'status',
    ];
}
