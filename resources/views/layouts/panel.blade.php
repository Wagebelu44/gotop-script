<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('panel-assets/images/favicon.png') }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="stylesheet" href="{{ asset('panel-assets/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('panel-assets/css/weather-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('panel-assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('panel-assets/css/animate.css.min.css') }}">
    <link rel="stylesheet" href="{{ asset('panel-assets/css/style.min.css') }}">
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
                    <a class="navbar-brand" href="{{ route('panel.dashboard') }}">
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
                        <li class="sidebar-item">
                            <a class="sidebar-link" href="javascript:void(0)"><span class="hide-menu">Dashboards </span></a>
                            <ul class="collapse first-level">
                                <li class="sidebar-item"><a href="#" class="sidebar-link"><span class="hide-menu"> Dashboard 1</span></a></li>
                                <li class="sidebar-item"><a href="#" class="sidebar-link"><span class="hide-menu"> Dashboard 2</span></a></li>
                            </ul>
                        </li>

                        <li class="sidebar-item">
                            <a class="sidebar-link" href="#"><span class="hide-menu">Widgets</span></a>
                        </li>

                        <li class="sidebar-item sidebar-item-right">
                            <a class="sidebar-link" href="#"><span class="hide-menu">Account</span></a>
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
    <script src="{{ asset('panel-assets/libs/bootstrap.min.js') }}"></script>

    <!-- apps -->
    <script src="{{ asset('panel-assets/js/app.min.js') }}"></script>
    <script src="{{ asset('panel-assets/js/app.init.horizontal-fullwidth.js') }}"></script>
    <script src="{{ asset('panel-assets/js/app-style-switcher.js') }}"></script>

    <script src="{{ asset('panel-assets/libs/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('panel-assets/js/sidebarmenu.js') }}"></script>
    <script src="{{ asset('panel-assets/js/custom.min.js') }}"></script>
    @yield('scripts')
</html>
