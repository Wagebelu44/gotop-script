<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>

	<link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" />
    <link rel="stylesheet" type="text/css" href="{{ asset('web-assets/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('web-assets/css/main.css') }}" />
</head>
<body>
    <div id="app">
        <a id="button"><i class="fas fa-chevron-up fa-2x mt-2 text-white"></i></a>

        <header id="top">
            <nav class="navbar navbar-expand-lg navbar-light fixed-top">
                <div class="container">
                    <a class="navbar-brand" href="{{ route('home') }}">
                        <img src="{{ asset('web-assets/img/logo.png') }}" width="200" alt="{{ config('app.name', 'Laravel') }}">
                    </a>
                </div>
            </nav>
        </header>

        <main>
            @yield('content')
        </main>

        <footer class="bg-white pt-2 pb-2" style="position: fixed; bottom: 0; width: 100%;">
            <div class="container-md">
                <div class="row">
                    <div class="col-md-6">
                        <span class="navbar-text text-dark">© Copyright Idea house. All Rights Reserved.</span>
                    </div>
                    <div class="col-md-6">
                        <ul class="list-inline mb-0 float-md-right">
                            <li class="list-inline-item">
                                <a class="nav-link text-dark" href="http://gopanelshop.com/privacy-policy">Privacy Policy <span class="sr-only">(current)</span></a>
                            </li>
                            <li class="list-inline-item">
                                <a class="nav-link text-dark" href="http://gopanelshop.com/terms-and-service">Terms & Service</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <script src="{{ asset('web-assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('web-assets/js/popper.min.js') }}"></script>
    <script src="{{ asset('web-assets/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('web-assets/js/main.js') }}"></script>
</body>
</html>
