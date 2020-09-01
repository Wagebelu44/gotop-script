<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SettingFaq extends Model
{
    use SoftDeletes;

    protected $table = 'setting_faqs';

    protected $fillable = [
      'panel_id', 'question', 'answer', 'sort', 'status', 'created_by', 'updated_by', 'deleted_at'
    ];
}
