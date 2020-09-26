<?php

namespace App\Http\Controllers\Web;

use App\Models\AccountStatus;
use App\Models\Blog;
use App\Models\BlogSlider;
use App\Models\Menu;
use App\Models\Newsfeed;
use App\Models\Page;
use App\Models\Order;
use App\Models\Redeem;
use App\Models\Ticket;
use App\Models\Service;
use App\Models\ThemePage;
use App\Models\SettingFaq;
use Illuminate\Http\Request;
use App\Models\DripFeedOrders;
use App\Models\SettingGeneral;
use App\Models\ServiceCategory;
use App\Http\Controllers\Controller;
use App\Models\NewsfeedCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;

class PageController extends Controller
{
    public function index(Request $request)
    {
        return $this->page($request, 'sign-in');
    }

    public function page(Request $request, $url)
    {
        $panelId = session('panel');
        $page = Page::with(['menu' => function($q) use($panelId) {
            $q->select('menu_link_id', 'menu_link_type');
            $q->where('panel_id', $panelId);
        }])->where('panel_id', $panelId)->where('url', $url)->first();

        // If page not found...
        if (empty($page)) {
            abort(404);
        }

        // If without authentication hit authentic url...
        if ($page->menu->menu_link_type == 'No' && Auth::check() == false) {
            abort(404);
        }

        //Menu data fetch...
        $sql = Menu::with(['page' => function($q) use($panelId) {
            $q->select('id', 'url');
            $q->where('panel_id', $panelId);
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
        foreach ($menus as $menu) {
            if ($menu['page']['url'] == $url) {
                $menu['page']['active'] = true;
            }
        }

        //Panel Setting row fetch...
        $setting = SettingGeneral::where('panel_id', $panelId)->first();

        $page['meta_title'] = ($page['meta_title'] != null)? $page['meta_title'] : $setting->panel_name;

        $site['panel_name'] = $setting->panel_name;
        $site['site_url'] = url('/');
        $site['auth'] = (Auth::check()) ? Auth::user() : false;
        $site['logout_url'] = route('logout');
        $site['logo'] = asset('storage/images/setting/'.$setting->logo);
        $site['favicon'] = asset('storage/images/setting/'.$setting->favicon);
        $site['notifigIcon'] = asset('assets/img/notifig.svg');
        $site['horizontal_menu'] = (Auth::check()) ? $setting->horizontal_menu : 'Yes';
        $site['csrf_field'] = csrf_field();
        $site['styles'] = [
            asset('assets/css/bootstrap.css'),
            asset('assets/css/fontawesome.css'),
            asset('assets/css/site-modal.css'),
            asset('assets/css/style.css'),
        ];
        $site['scripts'] = [
            ['code' => '
                window.CSRF_TOKEN = "'.csrf_token().';
                window.base_url = "'.url('/').'";'],
            ['src' => asset('assets/js/jquery.js')],
            ['src' => asset('assets/js/bootstrap.js')],
            ['src' => asset('assets/js/vue.js')],
            ['src' => asset('assets/js/custom.js')],
        ];

        if ($page->default_url == 'sign-in') {
            $site['url'] = route('login');

            $site['validation_error'] = 0;
            if (Session::has('errors')) {
                $error = Session::get('errors');
                $site['errors'] = $error->all();
                $site['validation_error'] = $error->count();
            }
        } elseif ($page->default_url == 'sign-up') {
            $site['url'] = route('register');
            $site['validation_error'] = 0;

            if (Session::has('errors')) {
                $error = Session::get('errors');
                $site['errors'] = $error->all();
                $site['validation_error'] = $error->count();
            }
        } elseif ($page->default_url == 'blog') {
            $site['url'] = url('/blog');
            $site['postsImgUrl'] = asset('./storage/images/blog/');
            if ($request->details > 0) {
                $site['post'] = Blog::where('panel_id', $panelId)->where('id', $request->details)->first();
            } else {
                $site['posts'] = Blog::where('panel_id', $panelId)->orderBy('id', 'desc')->paginate(15);
            }
            $site['postSliders']  = BlogSlider::where('panel_id', $panelId)->orderBy('id', 'desc')->get();
        } elseif ($page->default_url == 'services') {
            $service = ServiceCategory::with(['services'=> function($q) use($panelId) {
                $q->where('panel_id', $panelId);
                $q->where('status', 'active');
                $q->orderBy('id', 'ASC');
            }])
            ->where('status', 'active')
            ->where('panel_id', $panelId)
            ->orderBy('id', 'ASC');

           if (auth()->check()) {
             $user_service_prices = auth()->user()->servicesList()->get();
             $categories = $service->get();
                $cate = [];
                if (count($user_service_prices) > 0) {
                    foreach ($user_service_prices as $user_price) {
                        foreach ($categories as $key => &$category) {
                            $cate[$key] =  $category->toArray();
                            foreach ($category->services as $k => &$cs) {
                                $cate[$key]['services'][$k] =  $cs->toArray();
                                if ($cs->id == $user_price->id) {
                                    $cate[$key]['services'][$k]['price'] =  $user_price->pivot->price;
                                    $cs->price  = $user_price->pivot->price;
                                }
                            }
                        }

                    }
                    $categories = json_decode(json_encode($cate));
                }

                $site['service_count'] = count($cate);
                $site['service_lists'] = $cate;
            } else {
                $site['service_count'] = $service->count();
                $site['service_lists'] = $service->get()->toArray();
            }
        } elseif ($page->default_url == 'orders') {
            $input = $request->all();
            $order = Order::select('orders.*', 'services.name as service_name')
            ->where('orders.user_id', auth()->user()->id)
            ->where('orders.refill_status', 0)
            ->join('services', 'services.id', '=', 'orders.service_id');

            if (isset($input['status'])) {
                $orders =  $order->where('orders.status', $input['status'])->orderBy('orders.id', 'DESC')->get()->toArray();
            }
            if (isset($input['query'])) {
                $orders =  $order->where('orders.status', $input['query'])->orderBy('orders.id', 'DESC')->get()->toArray();
            } elseif (isset($input['user_search_keyword'])) {
                if ($input['status'] !='all') {
                    $order->where('orders.status', $input['status']);
                }
                $qString = $input['user_search_keyword'];

                $orders =  $order->where(function($q) use($qString) {
                    $q->where('orders.id', 'LIKE', '%' . $qString . '%');
                    $q->orWhere('orders.link', 'LIKE', '%' . $qString . '%');
                    $q->orWhere('orders.service_id', 'LIKE', '%' . $qString . '%');
                    $q->orWhere('orders.status', 'LIKE', '%' . $qString . '%');
                })->orderBy('orders.id', 'DESC')->get()->toArray();
            } else {
                $orders = $order->orderBy('orders.id', 'DESC')->get()->toArray();
            }

            $site['orderList'] = $orders;
            $site['url'] = $url;
            $site['status'] = $input['status'] ?? 'all';
        } elseif ($page->default_url ==  'drip-feed') {
            $date = (new \DateTime())->format('Y-m-d H:i:s');
            $d_feeds = DripFeedOrders::select('drip_feed_orders.*','users.username as user_name', 'A.service_name', 'A.orders_link','A.service_quantity as service_quantity',  'B.runOrders as runOrders')
            ->join('users','users.id','=','drip_feed_orders.user_id')
            ->join(\DB::raw('(SELECT COUNT(orders.drip_feed_id) AS totalOrders, orders.drip_feed_id, GROUP_CONCAT(DISTINCT(orders.link)) AS orders_link,
            GROUP_CONCAT(DISTINCT(services.name)) AS service_name, GROUP_CONCAT(DISTINCT(orders.quantity)) AS service_quantity FROM orders INNER JOIN services
            ON services.id = orders.service_id GROUP BY orders.drip_feed_id) as A'), 'drip_feed_orders.id', '=', 'A.drip_feed_id')
            ->leftJoin(\DB::raw("(SELECT drip_feed_id, COUNT(drip_feed_id) AS runOrders FROM orders
            WHERE order_viewable_time <='".$date."' GROUP BY drip_feed_id) AS B"), 'drip_feed_orders.id', '=', 'B.drip_feed_id');

            if (isset($request->status)) {
                if ($request->status != 'all') {
                    $d_feeds->where('drip_feed_orders.status', $request->status);
                }
            }

            $drip_feeds = $d_feeds->OrderBy('id', 'DESC')->get()->toArray();
            $site['dripFeedOrderList'] = $drip_feeds;
            $site['url'] = $url;
            $site['status'] = $input['status']??'all';
        } elseif ($page->default_url == 'tickets') {
            $site['url'] = route('ticket.store');

            $site['scripts'][] = ['src' => asset('user-assets/vue-scripts/ticket-vue.js')];

            if (Session::has('error')) {
                $site['error'] = Session::get('error');
            }

            if (Session::has('success')) {
                $site['success'] = Session::get('success');
            }

            $ticketLists = Ticket::where('user_id', auth()->user()->id)->get()->toArray();
            $site['ticketLists'] = $ticketLists;
        } elseif ($page->default_url == 'new-order' || $page->default_url == 'mass-order') {
            $site['single_order_url'] = route('make.single.order');
            $site['mass_order_url'] = route('massOrder.store');

            $site['scripts'][] = ['src' => asset('user-assets/vue-scripts/single-order.js')];

            if (Session::has('error')) {
                $site['error'] = Session::get('error');
            }

            if (Session::has('success')) {
                $site['success'] = Session::get('success');
            }

            $site['validation_error'] = 0;

            if (Session::has('errors')) {
                $error = Session::get('errors');
                $site['errors'] = $error->all();
                $site['validation_error'] = $error->count();
            }

            $ticketLists = Ticket::where('user_id', auth()->user()->id)->get()->toArray();
            $site['ticketLists'] = $ticketLists;
            $site['setting'] = $setting;

            $site['total_order']  = Order::where('panel_id', $panelId)->count();
            $totalSpent = Order::where('panel_id', $panelId)->where('user_id', Auth::user()->id)->sum('charges');
            $site['total_spent']  = numberFormat($totalSpent, 2);
            $accountStatusData = AccountStatus::where('panel_id', $panelId)->orderBy('id', 'desc')->get()->toArray();
            $accountStatuses = [];
            $statusPosition = [
                'spent_amount' => 0,
                'point' => 0,
            ];
            foreach ($accountStatusData as $accStatus){
                $accountStatuses [] = [
                    'name' => $accStatus['name'],
                    'minimum_spent_amount' => $accStatus['minimum_spent_amount'],
                    'point' => $accStatus['point'],
                    'statusKeys' => json_decode($accStatus['status_keys'], true),
                    'pointKeys' => json_decode($accStatus['point_keys'], true),
                ];
                if (($accStatus['minimum_spent_amount'] <= $totalSpent) && ($accStatus['minimum_spent_amount'] > $statusPosition['spent_amount'])) {
                    $statusPosition = [
                        'name' => $accStatus['name'],
                        'point' => $accStatus['point'],
                        'spent_amount' => $accStatus['minimum_spent_amount'],
                    ];
                }
            }
            $site['accountStatuses'] = $accountStatuses;
            $site['accountStatusKeys'] = accountStatusKeys();
            $site['accountPointKeys'] = accountPointKeys();
            $site['statusPosition']  = $statusPosition;
            $redeemSpent = Redeem::where('panel_id', $panelId)->where('user_id', Auth::user()->id)->sum('spent_amount');
            $site['redeem_point']  = round($totalSpent-$redeemSpent);
            $site['redeem_amount']  = numberFormat((($site['redeem_point']*$statusPosition['point'])/100));
        } elseif ($page->default_url == 'api') {
            $site['url'] = url('/');
            $site['api_key'] = auth()->user()->api_key;
        } elseif ($page->default_url == 'add-funds') {
            $site['url'] = url('/');
            $site['bitcoin'] = asset('assets/img/bit-icon.png');
            $site['FreeReviewCopy'] = asset('assets/img/FreeReviewCopy.png');
            $site['payoneer1'] = asset('assets/img/payoneer1.png');
            $site['pp-icon'] = asset('assets/img/pp-icon.png');
            $site['skrill2'] = asset('assets/img/skrill2.png');
            $site['visa1'] = asset('assets/img/visa1.png');
            $site['payop'] = asset('assets/img/payop.png');
            $site['pay_pal_store'] = url('/payment/add-funds/paypal');
            $site['bit_coin_store'] = url('/payment/add-funds/bitcoin');
            $site['pay_op_store'] = route('payment.payOp');
            if (Session::has('success')) {
                $site['success'] = Session::get('success');
            }
            if (Session::has('error')) {
                $site['error'] = Session::get('error');
            }
        } elseif ($page->default_url == 'faq') {
            $site['faqs'] = SettingFaq::where('panel_id', $panelId)->where('status', 'Active')->orderBy('sort', 'asc')->get();
        }

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

        return $twig->render('index.html', ['content' => $page->content, 'page' => $page->toArray(), 'site' => $site, 'menus' => $menus->toArray()]);
    }

    public function newsfeedApi(Request $request)
    {
        $panelId = session('panel');
        $categories = NewsfeedCategory::where('panel_id', $panelId)->where('status', 'Active')->orderBy('name', 'ASC')->get();
        $news = Newsfeed::with(['getCategories'])->where('panel_id', $panelId)->where('status', 'Active')->orderBy('id', 'DESC')->get();
        return view('web.newsfeed', compact('categories', 'news'));
    }

}
