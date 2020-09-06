<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\PanelAdminResetPasswordNotification;
use Spatie\Permission\Traits\HasRoles;
use DateTimeInterface;

class PanelAdmin extends Authenticatable
{
    use Notifiable, HasRoles;

    protected $guard_name = 'admin';
    protected $dateFormat = 'Y-m-d H:i:s';

    protected $fillable = [
        'uuid', 'panel_id', 'name', 'email', 'password', 'role', 'status',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    //Send password reset notification
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new PanelAdminResetPasswordNotification($token));
    }
}
