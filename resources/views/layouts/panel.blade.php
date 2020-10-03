<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('panel-assets/images/favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('panel-assets/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('panel-assets/css/weather-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('panel-assets/libs/bootstrap/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('panel-assets/libs/toastr/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('panel-assets/libs/sweetalert2/sweetalert2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('panel-assets/libs/summernote/summernote.css') }}">
    <link rel="stylesheet" href="{{ asset('panel-assets/libs/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('panel-assets/libs/b-datepicker/css/bootstrap-datepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('panel-assets/css/animate.css.min.css') }}">
    <link rel="stylesheet" href="{{ asset('panel-assets/css/style.min.css?var=0.1') }}">
    @yield('styles')
</head>

<body>
    <div class="preloader">
        <div class="lds-ripple">
            <div class="lds-pos"></div>
            <div class="lds-pos"></div>
        </div>
    </div>

    <div id="main-wrapper">
        <header class="topbar d-block d-md-none">
            <nav class="navbar top-navbar navbar-expand-md navbar-dark">
                <div class="navbar-header">
                    <a class="nav-toggler waves-effect waves-light d-block d-md-none"><i class="ti-menu ti-close"></i></a>
                    <a class="navbar-brand" href="{{ route('admin.panel.dashboard') }}">
                        <span class="logo-text">
                            <img src="{{ asset('panel-assets/images/logo.png') }}" alt="{{ config('app.name', 'Laravel') }}" class="dark-logo" />
                        </span>
                    </a>
                    <a class="topbartoggler d-block d-md-none"></a>
                </div>
            </nav>
        </header>

        <aside class="left-sidebar">
            <div class="scroll-sidebar">
                <nav class="sidebar-nav">
                    <ul id="sidebarnav">
                        <li class="sidebar-item {{ Request::routeIs('admin.users*') ? 'selected':'' }}">
                            <a class="sidebar-link {{ Request::routeIs('admin.users*') ? 'active':'' }}" href="{{ route('admin.users.index') }}">
                                <span class="hide-menu">Users</span>
                            </a>
                        </li>

                        <li class="sidebar-item {{ Request::routeIs('admin.orders*') ? 'selected':''}} {{  Request::routeIs('admin.exported_orders*') ? 'selected':'' }} ">
                            <a class="sidebar-link {{ Request::routeIs('admin.orders*') ? 'active':'' }}" href="{{ route('admin.orders.index') }}">
                                <span class="hide-menu">Orders</span>
                            </a>
                        </li>

                        <li class="sidebar-item {{ Request::routeIs('admin.subscriptions*') ? 'selected':''}} {{  Request::routeIs('admin.subscriptions*') ? 'selected':'' }} ">
                            <a class="sidebar-link {{ Request::routeIs('admin.subscriptions*') ? 'active':'' }}" href="{{ route('admin.subscriptions') }}">
                                <span class="hide-menu">Subscription</span>
                            </a>
                        </li>

                        <li class="sidebar-item {{ Request::routeIs('admin.drip-feed*') ? 'selected':'' }}">
                            <a class="sidebar-link {{ Request::routeIs('admin.drip-feed*') ? 'active':'' }}" href="{{ route('admin.drip-feed.index') }}">
                                <span class="hide-menu">Drip-Feed</span>
                            </a>
                        </li>

                        <li class="sidebar-item {{ Request::routeIs('admin.tasks*') ? 'selected':'' }}">
                            <a class="sidebar-link {{ Request::routeIs('admin.tasks*') ? 'active':'' }}" href="{{ route('admin.tasks.index') }}">
                                <span class="hide-menu">Tasks</span>
                            </a>
                        </li>

                        <li class="sidebar-item {{ Request::routeIs('admin.services*') ? 'selected':'' }}">
                            <a class="sidebar-link {{ Request::routeIs('admin.services*') ? 'active':'' }}" href="{{ route('admin.services.index') }}">
                                <span class="hide-menu">Services</span>
                            </a>
                        </li>

                        <li class="sidebar-item {{ Request::routeIs('admin.payments*') ? 'selected':'' }}">
                            <a class="sidebar-link {{ Request::routeIs('admin.payments*') ? 'active':'' }}" href="{{ route('admin.payments.index') }}">
                                <span class="hide-menu">Payments</span>
                            </a>
                        </li>

                        <li class="sidebar-item {{ Request::routeIs('admin.tickets*') ? 'selected':'' }}">
                            <a class="sidebar-link {{ Request::routeIs('admin.tickets*') ? 'active':'' }}" href="{{ route('admin.tickets.index') }}">
                                <span class="hide-menu mr-1">Tickets</span> 
                                    <span style="background: rgba(255,255,255,0%);">
                                      <span class="badge badge-danger d-block" id="unread_ticket_counter"
                                        style="background: #77b243;"> <b>0</b></span>
                                </span>
                                {{-- <span class="hide-menu">Tickets
                                    
                                    <span class="badge badge-danger">( 150 )</span> </span> --}}
                            </a>
                        </li>

                        {{--<li class="sidebar-item">
                            <a class="sidebar-link" href="javascript:void(0)"><span class="hide-menu">Affiliates</span></a>
                        </li>--}}

                        <li class="sidebar-item {{ Request::routeIs('admin.child-panels*') ? 'selected':'' }}">
                            <a class="sidebar-link {{ Request::routeIs('admin.child-panels*') ? 'active':'' }}" href="{{ route('admin.child-panels') }}">
                                <span class="hide-menu">Child panels</span>
                            </a>
                        </li>

                        <li class="sidebar-item {{ Request::routeIs('admin.reports*') ? 'selected':'' }}">
                            <a class="sidebar-link {{ Request::routeIs('admin.reports*') ? 'active':'' }}" href="{{ route('admin.reports.index') }}">
                                <span class="hide-menu">Reports</span>
                            </a>
                        </li>

                        <li class="sidebar-item {{ Request::routeIs('admin.newsfeed*') ? 'selected':'' }}">
                            <a class="sidebar-link {{ Request::routeIs('admin.newsfeed*') ? 'active':'' }}" href="{{ route('admin.newsfeed.index') }}">
                                <span class="hide-menu">News feed</span>
                            </a>
                        </li>

                        <li class="sidebar-item {{ Request::routeIs('admin.appearance*') ? 'selected':'' }}">
                            <a class="sidebar-link {{ Request::routeIs('admin.appearance*') ? 'active':'' }}" href="{{ route('admin.appearance.page.index') }}">
                                <span class="hide-menu">Appearance</span>
                            </a>
                        </li>

                        <li class="sidebar-item {{ Request::routeIs('admin.blog*') ? 'selected':'' }}">
                            <a class="sidebar-link {{ Request::routeIs('admin.blog*') ? 'active':'' }}" href="{{ route('admin.blog.index') }}">
                                <span class="hide-menu">Blog</span>
                            </a>
                        </li>

                        <li class="sidebar-item {{ Request::routeIs('admin.setting*') ? 'selected':'' }}">
                            <a class="sidebar-link {{ Request::routeIs('admin.setting*') ? 'active':'' }}" href="{{ route('admin.setting.general') }}">
                                <span class="hide-menu">Settings</span>
                            </a>
                        </li>

                        <li class="sidebar-item sidebar-item-right {{ Request::routeIs('admin.profile*') ? 'selected':'' }}">
                            <a class="sidebar-link {{ Request::routeIs('admin.profile*') ? 'active':'' }}" href="{{ route('admin.profile') }}">
                                <span class="hide-menu">Account</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><span class="hide-menu">Logout</span></a>
                            <form id="logout-form" action="{{ route('panel.logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

        <div class="page-wrapper">
            <div class="page-content container-fluid">
                @yield('content')
            </div>

            <footer class="footer text-center">
                All Rights Reserved by {{ config('app.name', 'Laravel') }}. Designed and Developed by <a href="#"></a>.
            </footer>
        </div>
    </div>

    <script src="{{ asset('panel-assets/libs/jquery.min.js') }}"></script>
    <script src="{{ asset('panel-assets/libs/popper.min.js') }}"></script>
    <script src="{{ asset('panel-assets/libs/bootstrap/bootstrap.min.js') }}"></script>
    {{-- <script src="{{ asset('panel-assets/libs/jquery-ui.min.js') }}"></script> --}}

    <!-- apps -->
    <script src="{{ asset('panel-assets/js/app.min.js') }}"></script>
    <script src="{{ asset('panel-assets/js/app.init.horizontal-fullwidth.js') }}"></script>
    <script src="{{ asset('panel-assets/js/app-style-switcher.js') }}"></script>

    <script src="{{ asset('panel-assets/libs/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('panel-assets/js/sidebarmenu.js') }}"></script>

    <script src="{{ asset('panel-assets/libs/toastr/toastr.min.js') }}"></script>
    <script src="{{ asset('panel-assets/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('panel-assets/libs/summernote/summernote.js') }}"></script>
    <script src="{{ asset('panel-assets/libs/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('panel-assets/libs/b-datepicker/js/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('panel-assets/js/custom.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>

    <script>
        @if (session('success'))
            toastr["success"]('{{ session('success') }}');
        @elseif (session('error'))
            toastr["error"]('{{ session('error') }}');
        @endif
        /* global instances */
        window.CSRF_TOKEN = '{{csrf_token()}}';
        window.base_url = "{{ url('/') }}";
        /* end of global instances */
      
        // update ticket info
        function getInfo()
        {
            fetch('{{ route("ticket.unread.count") }}')
            .then(res=>res.json())
            .then(res=>{
                $('#unread_ticket_counter b').html(res.data);
            });
        }
        getInfo();
        setInterval(()=>{
            getInfo();
        }, 1000);
    </script>
    @yield('scripts')
    
</body>
</html>
