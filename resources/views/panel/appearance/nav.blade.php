<div class="col-md-2">
    <ul class="list-group">

        <a class="list-group-item {{ Request::segment(2) == 'pages' ? 'active' : '' }}" href="{{ route('admin.appearance.index') }}">Pages</a>

        <a class="list-group-item {{ Request::segment(2) == 'menu' ? 'active' : '' }}" href="{{ route('admin.menu.index') }}">Menu</a>

    </ul>

</div>
