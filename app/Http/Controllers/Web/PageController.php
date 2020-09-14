<?php

namespace App\Http\Controllers\Web;

use App\Models\Menu;
use App\Models\SettingGeneral;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\ThemePage;

class PageController extends Controller
{
    public function index()
    {
        $panelId = session('panel');

        $menus = Menu::where('panel_id', $panelId)->orderBy('sort', 'asc')->get();
        $settingGeneral = SettingGeneral::where('panel_id', $panelId)->first();
        return view('web.home', compact('menus', 'settingGeneral'));
    }

    public function page($url)
    {
        $panelId = session('panel');

        $page = Page::where('panel_id', $panelId)->where('url', $url)->first();
        if (empty($page)) {
            return 1;
        }
        $setting = SettingGeneral::where('panel_id', $panelId)->first();

        $site['title'] = 'ASDF';
        $site['logo'] = asset('storage/images/setting/'.$setting->logo);
        $site['favicon'] = asset('storage/images/setting/'.$setting->favicon);
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


        $loader1 = new \Twig\Loader\ArrayLoader([
            'base.html' => str_replace('{{ content }}', '{% block content %}{% endblock %}', $layout->content),
        ]);
        $loader2 = new \Twig\Loader\ArrayLoader([
            'index.html' => '{% extends "base.html" %}{% block content %}'.$themePage->content.'{% endblock %}',
            'base.html'  => 'Will never be loaded',
        ]);

        $loader = new \Twig\Loader\ChainLoader([$loader1, $loader2]);
        $twig = new \Twig\Environment($loader);
        
        return $twig->render('index.html', ['content' => $page->content, 'page' => $page->toArray(), 'site' => $site]);
    }
}
