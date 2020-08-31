<div class="col-md-2">
    <ul class="list-group">

        <a class="list-group-item {{ Request::segment(2) == 'blog' ? 'active' : '' }}" href="#">Blog</a>

        <a class="list-group-item {{ Request::segment(2) == 'blog-category' ? 'active' : '' }}" href="#">Blog Categories</a>
        <a class="list-group-item {{ Request::segment(2) == 'blog-slider' ? 'active' : '' }}" href="#">Blog Slider</a>

    </ul>

</div>
