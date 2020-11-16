<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServicePriceUser extends Model
{
    protected $table ='service_price_user';
    protected $fillable = ['panel_id', 'service_id', 'price', 'user_id'];
    protected $guarded = ['id'];
    public $timestamps = false;
}
