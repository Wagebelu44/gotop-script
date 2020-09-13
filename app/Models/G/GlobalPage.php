<?php

namespace App\Models\G;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GlobalPage extends Model
{
    use SoftDeletes;

    protected $table = 'global_pages';

    protected $fillable = [
        'name', 'url', 'content', 'meta_title', 'meta_keyword', 'meta_description', 'is_public', 'is_editable', 'status',
    ];
}
