<div class="col-md-3">
    <ul class="list-group">
        <a class="list-group-item {{ Request::segment(2) == 'newsfeed' ? 'active' : '' }}" href="{{ route('admin.newsfeed.index') }}">News Feed</a>
        <a class="list-group-item {{ Request::segment(2) == 'newsfeed-category' ? 'active' : '' }}" href="{{ route('admin.newsfeed-category.index') }}">News Categories</a>
    </ul>
</div>
