<?php

namespace App\Http\Controllers\Web;

use App\Models\Menu;
use App\Models\Page;
use App\Models\Service;
use App\Models\ThemePage;
use Illuminate\Http\Request;
use App\Models\SettingGeneral;
use App\Models\ServiceCategory;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;

class PageController extends Controller
{
    public function index()
    {
        $panelId = session('panel');

        $menus = Menu::where('panel_id', $panelId)->orderBy('sort', 'asc')->get();
        $settingGeneral = SettingGeneral::where('panel_id', $panelId)->first();
        return view('web.home', compact('menus', 'settingGeneral'));
    }

    public function page(Request $request, $url)
    {
        $panelId = session('panel');

        $page = Page::where('panel_id', $panelId)->where('url', $url)->first();
        if (empty($page)) {
            return 1;
        }

        $setting = SettingGeneral::where('panel_id', $panelId)->first();

        $sql = Menu::with(['page' => function($q) {
            $q->select('id', 'url');
        }])
        ->select('menu_link_id', 'menu_name', 'external_link')
        ->where('panel_id', $panelId)
        ->where('status', 'Active')
        ->orderBy('sort', 'ASC');
        if (Auth::check()) {
            $sql->where('menu_link_type', 'No');
        } else {
            $sql->where('menu_link_type', 'Yes');
        }
        $menus = $sql->get();

        $site['auth'] = (Auth::check()) ? true : false;
        $site['menuActive'] = (Auth::check()) ? true : false;
        $site['title'] = 'ASDF';
        $site['logo'] = asset('storage/images/setting/'.$setting->logo);
        $site['favicon'] = asset('storage/images/setting/'.$setting->favicon);
        $site['csrf_field'] = csrf_field();
        $site['styles'] = [
            asset('assets/css/bootstrap.css'),
            asset('assets/css/style.css'),
        ];
        $site['scripts'] = [
            asset('assets/js/jquery.js'),
            asset('assets/js/bootstrap.js'),
            asset('assets/js/custom.js'),
        ];


        $layout = ThemePage::where('panel_id', $panelId)->where('name', 'layout.twig')->first();
        $themePage = ThemePage::where('panel_id', $panelId)->where('page_id', $page->id)->first();

        if ($url == 'sign-in') 
        {
            $site['url'] = route('login');
           
            $site['validation_error'] = 0;
            if (Session::has('errors')) {
                $error = Session::get('errors');
                $site['errors'] = $error->all();
                $site['validation_error'] = $error->count();
            }
        }
        elseif ( $url ==  'services')
        {
            $service = ServiceCategory::with(['services'=> function($q){
                $q->where('panel_id', auth()->user()->panel_id);
                $q->where('status', 'active');
                $q->orderBy('id', 'ASC');
            }])
            ->where('status', 'active')
            ->where('panel_id', auth()->user()->panel_id)
            ->orderBy('id', 'ASC');
            $site['service_count'] = $service->count();
            $site['service_lists'] = $service->get()->toArray();
        }
        elseif ( $url ==  'mass-order')
        {
             $site['url'] = route('massOrder.store');
             if (Session::has('error')) {
                $site['error'] = Session::get('error');
             }
             if (Session::has('success')) {
                $site['success'] = Session::get('error');
             }
        }

        $loader1 = new \Twig\Loader\ArrayLoader([
            'base.html' => str_replace('{{ content }}', '{% block content %}{% endblock %}', $layout->content),
        ]);
        $loader2 = new \Twig\Loader\ArrayLoader([
            'index.html' => '{% extends "base.html" %}{% block content %}'.$themePage->content.'{% endblock %}',
            'base.html'  => 'Will never be loaded',
        ]);

        $loader = new \Twig\Loader\ChainLoader([$loader1, $loader2]);
        $twig = new \Twig\Environment($loader);
        
        return $twig->render('index.html', ['content' => $page->content, 'page' => $page->toArray(), 'site' => $site, 'menus' => $menus->toArray()]);
    }
}
