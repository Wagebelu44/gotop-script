<?php

namespace App\Http\Controllers\Panel\Setting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(){
        return view('panel.settings.notifications');
    }
}
