<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Go2Fans</title>
    @if(isset($settingGeneral->favicon))
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('./storage/images/setting/'.$settingGeneral->favicon) }}">
    @endif
    <!-- Bootstrap core CSS -->
    <link href="{{ asset('assets/css/bootstrap.css') }}" rel="stylesheet">
    <link href=" {{ asset('assets/css/style.css') }}" rel="stylesheet">
</head>
<body>

<div class="wrapper">
    <!-- Sidebar  -->
    @if(isset($settingGeneral) && $settingGeneral->horizontal_menu == 'No')
    <nav class="navbar-light bg-light shadow-sm" id="sidebar">
        <div class="sidebar-header">
            @if(isset($settingGeneral->logo))
                <img style="width: 100px;" src="{{ asset('./storage/images/setting/'.$settingGeneral->logo) }}">
            @else
                Your Logo
            @endif
        </div>
        <ul class="list-unstyled components">
            @guest
                @if(!empty($menus))
                    @foreach($menus as $menu)
                        @if($menu->menu_link_type == 'Yes')
                            <li class="nav-item menu-border">
                                <a class="nav-link" href="#">{{ $menu->menu_name }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @else
                @if(!empty($menus))
                    @foreach($menus as $menu)
                        @if($menu->menu_link_type == 'No')
                            <li class="nav-item menu-border">
                                <a class="nav-link" href="#">{{ $menu->menu_name }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endguest
        </ul>
    </nav>
    @endif
    <div id="content">
        <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
            @if(isset($settingGeneral) && $settingGeneral->horizontal_menu == 'Yes')
                @if(isset($settingGeneral->logo))
                    <img style="width: 100px;" src="{{ asset('./storage/images/setting/'.$settingGeneral->logo) }}">
                @else
                    Your Logo
                @endif
            @endif
            @if(isset($settingGeneral) && $settingGeneral->horizontal_menu == 'No')
                <button id="sidebarCollapse" class="navbar-toggler" type="button" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
            @endif
                
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                @if(isset($settingGeneral) && $settingGeneral->horizontal_menu == 'Yes')
                <ul class="navbar-nav">
                    @guest
                        @if(!empty($menus))
                            @foreach($menus as $menu)
                                @if($menu->menu_link_type == 'Yes')
                                    <li class="nav-item">
                                        <a class="nav-link" href="#">{{ $menu->menu_name }}</a>
                                    </li>
                                @endif
                            @endforeach
                        @endif
                    @else
                        @if(!empty($menus))
                            @foreach($menus as $menu)
                                @if($menu->menu_link_type == 'No')
                                    <li class="nav-item">
                                        <a class="nav-link" href="#">{{ $menu->menu_name }}</a>
                                    </li>
                                @endif
                            @endforeach
                        @endif
                    @endguest
                </ul>
                @endif

                <ul class="navbar-nav ml-auto">
                    <!-- Authentication Links -->
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                        </li>
                        @if (Route::has('register'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                            </li>
                        @endif
                    @else
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ Auth::user()->name }}
                            </a>

                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                   onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                                    {{ __('Logout') }}
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    @endguest
                </ul>
            </div>
        </nav>
        <!-- Main Content -->
        <div class="container">
            <div class="px-3 py-3 pt-md-5 pb-md-4 mx-auto text-center">
                <h1 class="display-4">Main Content</h1>
                <p class="lead">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Dicta error exercitationem iste nulla numquam pariatur possimus repellat. Dolore dolores eveniet fuga inventore libero magni modi officia, pariatur reiciendis sed, voluptatem!</p>
            </div>
        </div>
        <!--End Main Content -->


    </div>
</div>

<script src="{{ asset('assets/js/jquery.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap.js') }}"></script>

<script type="text/javascript">
    $(document).ready(function () {
        $('#sidebarCollapse').on('click', function () {
            $('#sidebar').toggleClass('active');
        });
    });
</script>
</body>
</html>

