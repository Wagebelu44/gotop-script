<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NewsfeedRelation extends Model
{
    use SoftDeletes;

    protected $table = 'newsfeed_relations';

    protected $fillable = [
        'newsfeed_id', 'category_id',
    ];

    public function category()
    {
        return $this->belongsTo(NewsfeedCategory::class, 'category_id', 'id');
    }
}
