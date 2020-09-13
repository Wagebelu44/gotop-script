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
    <link rel="stylesheet" href="{{ asset('panel-assets/libs/bootstrap/bootstrap.min.css') }}">
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


    <div class="auth-wrapper d-flex no-block justify-content-center align-items-center" style="background:url({{ asset('panel-assets/images/login-register-bg.jpg') }}) no-repeat center center;">
        <div class="auth-box on-sidebar">
            @yield('content')
        </div>
    </div>

    <script src="{{ asset('panel-assets/libs/jquery.min.js') }}"></script>
    <script src="{{ asset('panel-assets/libs/popper.min.js') }}"></script>
    <script src="{{ asset('panel-assets/libs/bootstrap/bootstrap.min.js') }}"></script>

    <!-- apps -->
    <script src="{{ asset('panel-assets/js/app.min.js') }}"></script>
    <script src="{{ asset('panel-assets/js/app.init.horizontal-fullwidth.js') }}"></script>
    <script src="{{ asset('panel-assets/js/app-style-switcher.js') }}"></script>

    <script src="{{ asset('panel-assets/libs/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('panel-assets/js/sidebarmenu.js') }}"></script>
    <script src="{{ asset('panel-assets/js/custom.min.js') }}"></script>
    @yield('scripts')
</html>
