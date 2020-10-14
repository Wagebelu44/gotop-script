<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\Blog;
use App\Models\Menu;
use App\Models\Page;
use App\Models\Order;
use App\Models\Redeem;
use App\Models\SettingModule;
use App\Models\Ticket;
use App\Models\Newsfeed;
use App\Models\ThemePage;
use App\Models\BlogSlider;
use App\Models\SettingFaq;
use App\Models\Transaction;
use App\Models\UserChildPanel;
use App\Models\AccountStatus;
use App\Models\DripFeedOrders;
use App\Models\SettingGeneral;
use App\Models\ServiceCategory;
use App\Models\NewsfeedCategory;
use App\Models\G\GlobalCurrencies;
use App\Models\UserReferral;
use App\Models\UserReferralAmount;
use App\Models\UserReferralPayout;
use App\Models\UserReferralVisit;

class PageController extends Controller
{
    public function index(Request $request)
    {
        return $this->page($request, 'sign-in');
    }

    public function page(Request $request, $url, $param = null)
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
            return $this->page($request, 'sign-in');
        }

        // If after authentication hit un authentic url...
        // if ($page->menu->menu_link_type == 'Yes' && Auth::check() == true) {
        //     return redirect('new-order');
        // }

        //Menu data fetch...
        $sql = Menu::with(['page' => function($q) use($panelId) {
            $q->select('id', 'url');
            $q->where('panel_id', $panelId);
        }])
        ->select('menu_link_id', 'menu_name', 'external_link')
        ->where('panel_id', $panelId)
        ->where('page_in_menu', 'Yes')
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
        if (isset($setting->logo) && file_exists('storage/images/setting/'.$setting->logo)) {
            $site['logo'] = '<img style="width: 100px;" src="'.asset('storage/images/setting/'.$setting->logo).'">';
        } elseif ($setting->panel_name != null) {
            $site['logo'] = $setting->panel_name;
        } else {
            $site['logo'] = null;
        }

        if (isset($setting->favicon) && file_exists('storage/images/setting/'.$setting->favicon)) {
            $site['favicon'] = asset('storage/images/setting/'.$setting->favicon);
        } else {
            $site['favicon'] = null;
        }
        if ($setting->ticket_system == 0) {
            $menus = $menus->filter(function($q){
                return $q->menu_name !='Tickets';
            });
        }
        $site['panel_name'] = $setting->panel_name;
        $site['newsfeed'] = $setting->newsfeed;
        $site['newsfeed_align'] = $setting->newsfeed_align;
        $site['site_url'] = url('/');
        $site['auth'] = (Auth::check()) ? Auth::user() : false;
        $site['logout_url'] = route('logout');

        $site['notifigIcon'] = asset('assets/img/notify.png');
        $site['horizontal_menu'] = (Auth::check()) ? $setting->horizontal_menu : 'Yes';
        $site['csrf_field'] = csrf_field();
        $site['styles'] = [
            asset('assets/css/bootstrap.css'),
            asset('assets/css/fontawesome.css'),
            asset('assets/css/site-modal.css'),
            asset('assets/css/style.css?var=0.2'),
        ];
        $site['scripts'] = [
            ['code' => '
                window.CSRF_TOKEN = "'.csrf_token().'";
                window.base_url = "'.url('/').'";'],
            ['src' => asset('assets/js/jquery.js')],
            ['src' => asset('assets/js/bootstrap.js')],
            ['src' => asset('assets/js/vue.js')],
            ['src' => asset('assets/js/custom.js')],
        ];

        if ($page->default_url == 'sign-in') {
            if (Auth::check()) {
                return redirect('new-order');
            }

            $site['url'] = route('login');
            $site['sign_up'] = ($setting->signup_page == 1) ? true : false;
            $site['reset_password'] = ($setting->reset_password == 1) ? true : false;
            $site['sign_up_url'] = url('/sign-up');
            $site['reset_password_url'] = url('/password-reset');

            $site['validation_error'] = 0;
            if (Session::has('errors')) {
                $error = Session::get('errors');
                $site['errors'] = $error->all();
                $site['validation_error'] = $error->count();
            }
        } elseif ($page->default_url == 'sign-up') {
            if (Auth::check()) {
                return redirect('new-order');
            }
            
            $site['url'] = route('register');
            $site['sign_in_url'] = url('/sign-in');
            $site['reset_password_url'] = url('/password-reset');
            $site['terms_url'] = url('/terms');
            
            $site['signup_page'] = ($setting->signup_page == 1) ? true : false;
            $site['name_fields'] = ($setting->name_fields == 1) ? true : false;
            $site['skype_field'] = ($setting->skype_field == 1) ? true : false;
            $site['terms_checkbox'] = ($setting->terms_checkbox == 1) ? true : false;

            $site['validation_error'] = 0;
            if (Session::has('errors')) {
                $error = Session::get('errors');
                $site['errors'] = $error->all();
                $site['validation_error'] = $error->count();
            }
        } elseif ($page->default_url == 'email-verify') {
            $site['url'] = route('verification.resend');

            $site['email_status'] = session('resent');

            $site['validation_error'] = 0;
            if (Session::has('errors')) {
                $error = Session::get('errors');
                $site['errors'] = $error->all();
                $site['validation_error'] = $error->count();
            }
        } elseif ($page->default_url == 'password-reset') {
            if (Auth::check()) {
                return redirect('new-order');
            }
            
            $site['url'] = route('password.email');
            $site['sign_up'] = ($setting->signup_page == 1) ? true : false;
            $site['sign_up_url'] = url('/sign-up');
            $site['sign_in_url'] = url('/sign-in');

            $site['reset_status'] = session('status');

            $site['validation_error'] = 0;
            if (Session::has('errors')) {
                $error = Session::get('errors');
                $site['errors'] = $error->all();
                $site['validation_error'] = $error->count();
            }
        } elseif ($page->default_url == 'password-set') {
            if (Auth::check()) {
                return redirect('new-order');
            }
            
            $site['url'] = route('password.update');
            $site['token'] = $param;
            
            $site['reset_status'] = session('status');

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
                $q->select('services.*', 'A.avg_time');
                $q->where('panel_id', $panelId);
                $q->where('status', 'active');
                $q->orderBy('id', 'ASC');
                $q->leftJoin(\DB::raw('(SELECT service_id, AVG(duration) as avg_time FROM orders GROUP BY service_id) AS A'), 'A.service_id', '=', 'services.id');
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
                else
                {
                    $categories = $categories->toArray();
                }

                $site['service_count'] = count($categories);
                $site['service_lists'] = $categories;
            } else {
              
                $site['service_count'] = $service->count();
                $site['service_lists'] = $service->get()->toArray();
            }
        } elseif ($page->default_url == 'orders') {
            $order = Order::select('orders.*', 'services.name as service_name', 'services.refill_status')
            ->where('orders.user_id', auth()->user()->id)
            ->where('orders.refill_status', 0)
            ->join('services', 'services.id', '=', 'orders.service_id');

            if (isset($request->status) && $request->status != 'all') {
                $order->where('orders.status', $request->status);
            }
            
            if (isset($request->search)) {
                $order->where(function($q) use($request) {
                    $q->where('orders.id', 'LIKE', '%' . $request->search . '%');
                    $q->orWhere('orders.link', 'LIKE', '%' . $request->search . '%');
                    $q->orWhere('orders.service_id', 'LIKE', '%' . $request->search . '%');
                    $q->orWhere('orders.status', 'LIKE', '%' . $request->search . '%');
                });
            }
            $orders = $order->orderBy('orders.id', 'DESC')->paginate(50);

            $site['orders'] = $orders;
            $site['url'] = $url;
            if (Session::has('error')) {
                $site['error'] = Session::get('error');
            }

            if (Session::has('success')) {
                $site['success'] = Session::get('success');
            }
            $site['refill_url'] = route('order.changeRefillStatus');
            $site['status'] = $request->status ?? 'all';
        } elseif ($page->default_url == 'subscriptions') {
            $order = Order::select('orders.*', 'services.name as service_name')
            ->where('orders.user_id', auth()->user()->id)
            ->where('orders.refill_status', 0)
            ->join('services', 'services.id', '=', 'orders.service_id')
            ->where('service_type', 'Subscriptions');

            if (isset($request->status) && $request->status != 'all') {
                $order->where('orders.status', $request->status);
            }

            if (isset($request->search)) {
                $order->where(function($q) use($request) {
                    $q->where('orders.id', 'LIKE', '%' . $request->search . '%');
                    $q->orWhere('orders.link', 'LIKE', '%' . $request->search . '%');
                    $q->orWhere('orders.service_id', 'LIKE', '%' . $request->search . '%');
                    $q->orWhere('orders.status', 'LIKE', '%' . $request->search . '%');
                });
            }
            $orders = $order->orderBy('orders.id', 'DESC')->paginate(50);

            $site['subscriptions'] = $orders;
            $site['url'] = $url;
            $site['status'] = $request->status ?? 'all';
        } elseif ($page->default_url == 'drip-feed') {
            $date = (new \DateTime())->format('Y-m-d H:i:s');
            $sql = DripFeedOrders::select('drip_feed_orders.*','users.username as user_name', 'A.service_name', 'A.orders_link','A.service_quantity as service_quantity',  'B.runOrders as runOrders')
            ->join('users','users.id','=','drip_feed_orders.user_id')
            ->join(\DB::raw('(SELECT COUNT(orders.drip_feed_id) AS totalOrders, orders.drip_feed_id, GROUP_CONCAT(DISTINCT(orders.link)) AS orders_link,
            GROUP_CONCAT(DISTINCT(services.name)) AS service_name, GROUP_CONCAT(DISTINCT(orders.quantity)) AS service_quantity FROM orders INNER JOIN services
            ON services.id = orders.service_id GROUP BY orders.drip_feed_id) as A'), 'drip_feed_orders.id', '=', 'A.drip_feed_id')
            ->leftJoin(\DB::raw("(SELECT drip_feed_id, COUNT(drip_feed_id) AS runOrders FROM orders
            WHERE order_viewable_time <='".$date."' GROUP BY drip_feed_id) AS B"), 'drip_feed_orders.id', '=', 'B.drip_feed_id');

            if (isset($request->status) && $request->status != 'all') {
                $sql->where('drip_feed_orders.status', $request->status);
            }

            $drip_feeds = $sql->orderBy('id', 'DESC')->paginate(50)->toArray();
            $site['dripfeeds'] = $drip_feeds;
            $site['url'] = $url;
            $site['status'] = $request->status ?? 'all';
        } elseif ($page->default_url == 'tickets') {
            $site['ticket_page'] = ($setting->ticket_system == 1) ? true : false;
            $site['url'] = route('ticket.store');
            $site['base_url'] = url('/tickets');
            $site['single-ticket'] = null;

            $site['scripts'][] = ['src' => asset('user-assets/vue-scripts/ticket-vue.js')];

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
            if (isset($request->id) && !empty($request->id)) {
                $site['comment-store'] = route('ticket.comment.store');
                $site['single-ticket'] = Ticket::with('comments')->where('id', $request->id)->first();
            }
        } elseif ($page->default_url == 'new-order' || $page->default_url == 'mass-order') {
            $site['single_order_url'] = route('make.single.order');
            $site['mass_order_url'] = route('massOrder.store');

            $site['scripts'][] = ['src' => asset('user-assets/vue-scripts/single-order.js?var=1.0')];

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
                $accountStatuses[] = [
                    'name' => $accStatus['name'],
                    'minimum_spent_amount' => $accStatus['minimum_spent_amount'],
                    'point' => $accStatus['point'],
                    'statusKeys' => json_decode($accStatus['status_keys'], true),
                    'pointKeys' => json_decode($accStatus['point_keys'], true),
                ];
                if (($accStatus['minimum_spent_amount'] >= $totalSpent)) {
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
            $site['important_newses'] = Newsfeed::where('important_news', 'Yes')->where('status', 'Active')->where('panel_id', auth()->user()->panel_id)->orderBy('created_at', 'DESC')->get()->toArray();
            $site['service_updates'] = Newsfeed::where('service_update', 'Yes')->where('status', 'Active')->where('panel_id', auth()->user()->panel_id)->orderBy('created_at', 'DESC')->get()->toArray();
            if (isset($request->order_id) && !empty($request->order_id)) {
                $site['submitted_order'] = Order::with('service')->where('id', $request->order_id)->first()->toArray();
            }
        } elseif ($page->default_url == 'api') {
            $site['url'] = url('/');
            $site['api_key'] = auth()->user()->api_key;
            $site['serviceApi'] = apiServiceJson();
            $site['orderResponse'] = apiOrderResponse();
            $site['multiOrderResponse'] = apiMultiOrderResponse();
            $site['userBalance'] = apiUserBalance();
            $site['apiAddOrder'] = apiAddOrder();
        } elseif ($page->default_url == 'add-funds') {
            $site['url'] = url('/');
            $site['pay_pal_store'] = route('payment.paypal.store');
            $site['bit_coin_store'] = route('payment.bitcoin.store');
            $site['pay_op_store'] = route('payment.payop.store');
            $site['user_payment_route'] = route('payment.make');

            $site['validation_error'] = 0;
            if (Session::has('errors')) {
                $error = Session::get('errors');
                $site['errors'] = $error->all();
                $site['validation_error'] = $error->count();
            }
            $site['user_payment_methods'] = auth()->user()->paymentMethods()
            ->select('user_payment_methods.*', 'payment_methods.method_name', 'payment_methods.global_payment_method_id')
                ->join('payment_methods', function($q) use($panelId) {
                    $q->on('payment_methods.id', '=', 'user_payment_methods.payment_id');
                    $q->where('visibility', 'enabled');
                    $q->where('payment_methods.panel_id', $panelId);
                })->get()->toArray();
            $site['transactions']  = Transaction::where(function($q){
                $q->where('transaction_flag', 'payment_gateway');
                $q->orWhere('transaction_flag', 'admin_panel');
            })
            ->where('status', 'done')
            ->where('amount', '>' , 0)
            ->where('user_id', auth()->user()->id)->orderBy('id', 'DESC')->latest()->take(10)->get()->toArray();
            if (Session::has('success')) {
                $site['success'] = Session::get('success');
            }
            if (Session::has('error')) {
                $site['error'] = Session::get('error');
            }
        } elseif ($page->default_url == 'faq') {
            $site['faqs'] = SettingFaq::where('panel_id', $panelId)->where('status', 'Active')->orderBy('sort', 'asc')->get();
        } elseif ($page->default_url == 'child-panels') {
            $site['child_selling_amount'] = SettingModule::select('amount')->where('panel_id', $panelId)->where('type', 'child_panels')->first();
            $site['panel_store'] = route('child-panel.store');
            $site['token'] = csrf_field();

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
            $site['currencies'] = GlobalCurrencies::where('status', 'Active')->get();
            $site['panelsList'] =  UserChildPanel::where('panel_id', $panelId)->where('user_id', Auth::user()->id)->orderBy('id', 'desc')->get();
        } elseif ($page->default_url == 'affiliates') {
            $aff = SettingModule::where('panel_id', $panelId)->where('type', 'affiliate')->first();
            $site['ref_link'] = route('referral.link', Auth::user()->referral_key);
            $site['commission_rate'] = (!empty($aff)) ? round($aff->commission_rate) : '0';
            $site['minimum_payout'] = (!empty($aff)) ? $aff->amount : '0';
            $site['url'] = route('request-payout');

            $site['affiliates']['total_visits'] = UserReferralVisit::where('panel_id', $panelId)->where('referral_id', Auth::user()->id)->count();

            $site['affiliates']['unpaid_referrals'] = UserReferral::where('user_referrals.panel_id', $panelId)
            ->where('user_referrals.referral_id', Auth::user()->id)
            ->leftJoin(DB::raw("(SELECT user_id FROM transactions WHERE transaction_flag='payment_gateway' AND status='done' GROUP BY user_id) AS A"), 'user_referrals.user_id', '=', 'A.user_id')
            ->whereNull('A.user_id')
            ->count('user_referrals.user_id');

            $site['affiliates']['paid_referrals'] = UserReferral::where('user_referrals.panel_id', $panelId)
            ->where('user_referrals.referral_id', Auth::user()->id)
            ->join(DB::raw("(SELECT user_id FROM transactions WHERE transaction_flag='payment_gateway' AND status='done' GROUP BY user_id) AS A"), 'user_referrals.user_id', '=', 'A.user_id')
            ->count('user_referrals.user_id');

            $site['affiliates']['total_earnings'] = UserReferralAmount::where('panel_id', $panelId)->where('referral_id', Auth::user()->id)->sum('amount');

            $site['affiliates']['total_payouts'] = UserReferralPayout::where('panel_id', $panelId)->where('referral_id', Auth::user()->id)->sum('amount');

            $site['affiliates']['unpaid_earnings'] = ($site['affiliates']['total_earnings']-$site['affiliates']['total_payouts']);
            
            $site['affiliates']['conversion_rate'] = ($site['affiliates']['total_visits'] > 0)?numberFormat(($site['affiliates']['total_visits'] * 100) / $site['affiliates']['paid_referrals']):0;

            $site['affiliates']['request_payout'] = ($site['affiliates']['unpaid_earnings'] >= $site['minimum_payout']) ? true : false;

            $site['affiliates']['payouts'] = UserReferralPayout::where('panel_id', $panelId)->where('referral_id', Auth::user()->id)->paginate(10);
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
        $sql = Newsfeed::with(['getCategories.category'])->where('panel_id', $panelId)
        ->where('status', 'Active')
        ->where('important_news', 'No')
        ->where('service_update', 'No')
        ->orderBy('id', 'DESC');
        if ($request->category != null) {
            $sql->whereHas('getCategories', function ($q) use($request) {
                $q->where('category_id', '=', $request->category);
            });
        }

        $news = $sql->paginate(3);
        return view('web.newsfeed', compact('categories', 'news'));
    }
}
