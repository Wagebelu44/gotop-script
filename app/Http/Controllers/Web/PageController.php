<?php

namespace App\Http\Controllers\Web;

use App\Models\Menu;
use App\Models\SettingGeneral;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ThemePage;

class PageController extends Controller
{
    public function panelNotFound()
    {
        $msg = session('panelErr');
        return view('panel-not-found', compact('msg'));
    }

    public function index()
    {
        $menus = Menu::where('panel_id', 1)->orderBy('sort', 'asc')->get();
        $settingGeneral = SettingGeneral::where('panel_id', 1)->first();
        return view('web.home', compact('menus', 'settingGeneral'));
    }

    public function page($url)
    {
        $layout = ThemePage::where('panel_id', 1)->where('name', 'layout.twig')->first();
        $page = ThemePage::with('page')->where('panel_id', 1)->where('name', 'account.twig')->first();

        $loader1 = new \Twig\Loader\ArrayLoader([
            'base.html' => str_replace('{{ content }}', '{% block content %}{% endblock %}', $layout->content),
        ]);
        $loader2 = new \Twig\Loader\ArrayLoader([
            'index.html' => '{% extends "base.html" %}{% block content %}'.$page->content.'{% endblock %}',
            'base.html'  => 'Will never be loaded',
        ]);

        $loader = new \Twig\Loader\ChainLoader([$loader1, $loader2]);
        $twig = new \Twig\Environment($loader);

        return $twig->render('index.html', ['content' => $page->page->content]);
    }
}
