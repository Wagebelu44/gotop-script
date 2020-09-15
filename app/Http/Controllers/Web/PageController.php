<?php

namespace App\Http\Controllers\Web;

use App\Models\Menu;
use App\Models\Page;
use App\Models\Order;
use App\Models\Service;
use App\Models\ThemePage;
use App\Models\SettingFaq;
use Illuminate\Http\Request;
use App\Models\DripFeedOrders;
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

        if ($url == 'faq') {
            $site['faqs'] = SettingFaq::where('panel_id', $panelId)->where('status', 'Active')->orderBy('sort', 'asc')->get();
        }


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
                $site['success'] = Session::get('success');
             }
        }
        elseif ( $url ==  'orders')
        {
            $input = $request->all();
            $order = Order::select('orders.*', 'services.name as service_name')
            ->where('orders.user_id', auth()->user()->id)
            ->where('orders.refill_status', 0)
            ->join('services', 'services.id', '=', 'orders.service_id');
            if(isset($input['status']))
            {
                $orders =  $order->where('orders.status', $input['status'])->orderBy('orders.id', 'DESC')->get()->toArray();
            }
            if(isset($input['query']))
            {
                $orders =  $order->where('orders.status', $input['query'])->orderBy('orders.id', 'DESC')->get()->toArray();
            }
            elseif (isset($input['user_search_keyword'])) {
                if ($input['status'] !='all') {
                    $order->where('orders.status', $input['status']);
                }
                $q_string = $input['user_search_keyword'];
                $orders =  $order->where(function($q) use($q_string) {
                        $q->where('orders.id', 'LIKE', '%' . $q_string . '%');
                        $q->orWhere('orders.link', 'LIKE', '%' . $q_string . '%');
                        $q->orWhere('orders.service_id', 'LIKE', '%' . $q_string . '%');
                        $q->orWhere('orders.status', 'LIKE', '%' . $q_string . '%');
                })->orderBy('orders.id', 'DESC')->get()->toArray();
            }
            else
            {
                $orders = $order->orderBy('orders.id', 'DESC')->get()->toArray();
            }
            $site['orderList'] = $orders;
            $site['url'] = $url;
            $site['status'] = $input['status']??'all';
        }
        elseif ( $url ==  'drip-feed')
        {
            $date = (new \DateTime())->format('Y-m-d H:i:s');
        $d_feeds = DripFeedOrders::select('drip_feed_orders.*','users.username as user_name', 'A.service_name', 'A.orders_link','A.service_quantity as service_quantity',  'B.runOrders as runOrders')
            ->join('users','users.id','=','drip_feed_orders.user_id')
            ->join(\DB::raw('(SELECT COUNT(orders.drip_feed_id) AS totalOrders, orders.drip_feed_id, GROUP_CONCAT(DISTINCT(orders.link)) AS orders_link,
            GROUP_CONCAT(DISTINCT(services.name)) AS service_name, GROUP_CONCAT(DISTINCT(orders.quantity)) AS service_quantity FROM orders INNER JOIN services
            ON services.id = orders.service_id GROUP BY orders.drip_feed_id) as A'), 'drip_feed_orders.id', '=', 'A.drip_feed_id') 
            ->leftJoin(\DB::raw("(SELECT drip_feed_id, COUNT(drip_feed_id) AS runOrders FROM orders
            WHERE order_viewable_time <='".$date."' GROUP BY drip_feed_id) AS B"), 'drip_feed_orders.id', '=', 'B.drip_feed_id');

        if (isset($request->status))
        {
            if($request->status != 'all')
            $d_feeds->where('drip_feed_orders.status',$request->status);
        }

            $drip_feeds = $d_feeds->OrderBy('id', 'DESC')->get()->toArray();
            $site['orderList'] = $drip_feeds;
            $site['url'] = $url;
            $site['status'] = $input['status']??'all';
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
