<div class="col-md-2">
    <ul class="list-group">
        <a class="list-group-item {{ Request::segment(2) == 'appearance' ? 'active' : '' }}" href="{{ route('admin.appearance.index') }}">Pages</a>
        <a class="list-group-item {{ Request::segment(2) == 'blog' ? 'active' : '' }}" href="{{ route('admin.blog.index') }}">Blog</a>
        <a class="list-group-item {{ Request::segment(2) == 'menu' ? 'active' : '' }}" href="{{ route('admin.menu.index') }}">Menu</a>
        <a class="list-group-item {{ Request::segment(2) == 'theme' ? 'active' : '' }}" href="{{ route('admin.theme.index') }}">Themes</a>
        <a class="list-group-item {{ Request::segment(2) == 'file' ? 'active' : '' }}" href="">Files</a>
    </ul>
</div>
