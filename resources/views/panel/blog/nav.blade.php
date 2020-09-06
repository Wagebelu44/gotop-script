<div class="col-md-2">
    <ul class="list-group">

        <a class="list-group-item {{ Request::segment(2) == 'blog' ? 'active' : '' }}" href="{{ route('admin.blog.index') }}">Blog</a>

        <a class="list-group-item {{ Request::segment(2) == 'blog-category' ? 'active' : '' }}" href="{{ route('admin.blog-category.index') }}">Blog Categories</a>
        <a class="list-group-item {{ Request::segment(2) == 'blog-slider' ? 'active' : '' }}" href="{{ route('admin.blog-slider.index') }}">Blog Slider</a>

    </ul>

</div>
