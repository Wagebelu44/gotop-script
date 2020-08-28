<?php

namespace App\Http\Controllers\Panel\Setting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    public function index(){
        return view('panel.settings.modules');
    }
}
