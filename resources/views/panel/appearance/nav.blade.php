<div class="col-md-2">
    <ul class="list-group">
        <a class="list-group-item {{ Request::segment(3) == 'page' ? 'active' : '' }}" href="{{ route('admin.appearance.page.index') }}">Pages</a>
        <a class="list-group-item {{ Request::segment(3) == 'blog' ? 'active' : '' }}" href="{{ route('admin.blog.index') }}">Blog</a>
        <a class="list-group-item {{ Request::segment(3) == 'menu' ? 'active' : '' }}" href="{{ route('admin.appearance.menu.index') }}">Menu</a>
        <a class="list-group-item {{ Request::segment(2) == 'theme' ? 'active' : '' }}" href="{{ route('admin.theme.index') }}">Themes</a>
        <a class="list-group-item {{ Request::segment(3) == 'file' ? 'active' : '' }}" href="{{ route('admin.appearance.file.index') }}">Files</a>
    </ul>
</div>
