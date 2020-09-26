<div class="col-md-3">
    <ul class="list-group">
        <a class="list-group-item {{ Request::segment(2) == 'newsfeed' ? 'active' : '' }}" href="{{ route('admin.newsfeed.index') }}">Newsfeed</a>
        <a class="list-group-item {{ Request::segment(2) == 'newsfeed-category' ? 'active' : '' }}" href="{{ route('admin.newsfeed-category.index') }}">Blog Categories</a>
    </ul>
</div>
