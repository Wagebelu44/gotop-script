@extends('layouts.panel')

@section('content')
    <div class="container all-mt-30">
        <div class="row">
            <!--navbar-->
            @include('panel.blog.nav')
            <!--navbar-->
            @if($page == 'index')
            <div class="col-md-8">
                <div class="card panel-default">
                    <div class="card-body">
                        <a class="btn btn-default m-b add-page" href="{{ route('admin.blog.create') }}">Add New Blog</a>

                        <table class="table">
                            <thead>
                            <tr>
                                <th>SN</th>
                                <th>Page Title</th>
                                <th>Created</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(!empty($data))
                                @foreach($data as $key => $blog)
                                    <tr>
                                        <td>{{ $key+1 }}</td>
                                        <td>
                                            {{ $blog->title }}
                                        </td>
                                        <td>{{ $blog->created_at }}</td>
                                        <td>{{ $blog->status }}</td>
                                        <td class="p-r text-right">
                                            <a class="btn btn-default btn-xs" href="{{ route('admin.blog.edit', $blog->id) }}">Edit</a>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td>No Data found.</td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @elseif($page == 'create' || $page == 'edit')
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <form name="blogForm" action="{{ $page == 'edit' ? route('admin.blog.update', $data->id):route('admin.blog.store') }}" method="post" enctype="multipart/form-data">
                                @csrf
                                @if($page == 'edit')
                                    @method('PUT')
                                @endif
                                <div class="relative">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-10">
                                                <div class="form-group">
                                                    <label class="form-control-label">Post image</label><br/>
                                                    <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" id="image" accept="image/*" onchange="preview_image(event)">
                                                    <p class="help-block">800 x 450px recommended</p>
                                                </div>
                                                @error('image')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                                @enderror
                                            </div>
                                            <div class="col-md-2">
                                                <img style="width: 200px" id="preview" src="">
                                                @if(isset($data->image))
                                                    <img style="width: 200px" id="savedLogo" class="img-thumbnail" src="{{ asset('./storage/images/blog-post/'.$data->image) }}">
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label" for="blog_category">Blog Category</label>
                                        <select class="form-control is-public @error('category_id') is-invalid @enderror" name="category_id" id="category_id">
                                            <option value="">Select Category</option>
                                            @if (!$get_blog_category->isEmpty())
                                                @foreach ($get_blog_category as $key => $category)
                                                    <option value="{{ $category->id }}"  {{ old('category_id', isset($data) ? $data->category_id : $category->id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        @error('category_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label" for="post_title">Post title</label>
                                        <input type="text" id="post_title" class="form-control default-page-name page-name @error('title') is-invalid @enderror" name="title" value="{{ old('title', isset($data) ? $data->title : '') }}" onchange="string_to_slug(this.value)">
                                        @error('title')
                                        <span role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label" for="post_content">Body</label>
                                        <textarea class="form-control summernote @error('blog_content') is-invalid @enderror" name="blog_content">
                                            {{ old('blog_content', isset($data) ? $data->content : '') }}
                                        </textarea>
                                        @error('post_content')
                                        <span role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                                <hr>

                                <div class="form-group">
                                    <label class="control-label" for="createpageform-url">Type</label>
                                    <div class="input-group">
                                        <label class="radio-inline">
                                            <input type="radio" value="trending_blog" name="type" id="blog_type" {{ old('type', isset($data) ? $data->type : '') == 'trending_blog' ? 'checked' : '' }} required>Trending
                                        </label>
                                        <label class="radio-inline radio-ml">
                                            <input type="radio" value="popular_blog" name="type" id="blog_type" {{ old('type', isset($data) ? $data->type : '') == 'popular_blog' ? 'checked' : '' }} required>Popular
                                        </label>
                                        <label class="radio-inline radio-ml">
                                            <input type="radio" value="blog" name="type" id="blog_type" {{ old('type', isset($data) ? $data->type : '') == 'blog' ? 'checked' : '' }} required>None
                                        </label>
                                    </div>
                                    @error('type')
                                    <span role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label class="control-label" for="is_visibility">Status</label>
                                    <select class="form-control is-public" name="status" id="status" required>
                                        <option value="active" {{ old('status', isset($data) ? $data->status : '') == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('status', isset($data) ? $data->status : '') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    @error('status')
                                    <span role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                                <hr>
                                <button type="submit" class="btn btn-primary" name="save-button">Save changes</button>
                                <a class="btn btn-default" href="{{ route('admin.blog.index') }}">Cancel</a>
                                @if($page == 'edit')
                                    <a href="javascript: void(0)" onclick="document.getElementById('deleteBlog').submit();" class="btn btn-default waves-effect pull-right" ><i>Delete</i></a>
                                @endif
                            </form>

                            @if($page == 'edit')
                                <form id="deleteBlog" action="{{ route('admin.blog.destroy', $data->id)}}" method="post">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        function preview_image(event)
        {
            var reader = new FileReader();
            reader.onload = function()
            {
                var output = document.getElementById('preview');
                output.src = reader.result;
            }
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
@endsection
