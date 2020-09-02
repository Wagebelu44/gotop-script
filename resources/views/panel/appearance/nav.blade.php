<div class="col-md-2">
    <ul class="list-group">

        <a class="list-group-item {{ Request::segment(2) == 'pages' ? 'active' : '' }}" href="#">Pages</a>

        <a class="list-group-item {{ Request::segment(2) == 'menu' ? 'active' : '' }}" href="#">Menu</a>

    </ul>

</div>
