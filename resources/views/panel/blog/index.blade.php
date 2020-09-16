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
                                                <img style="width: 100px" id="preview" src="">
                                                @if(isset($data->image))
                                                    <img style="width: 200px" id="savedLogo" class="img-thumbnail" src="{{ asset('./storage/images/blog/'.$data->image) }}">
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label" for="blog_category">Blog Category</label>
                                        <select class="form-control is-public @error('category_id') is-invalid @enderror" name="category_id" id="category_id">
                                            <option value="">Select Category</option>
                                            @if (!empty($categories))
                                                @foreach ($categories as $key => $category)
                                                    <option value="{{ $category->id }}"  {{ isset($data) && $data->category_id == $category->id ? 'selected':''  }}>{{ $category->name }}</option>
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
                                        <label class="control-label" for="post_title">Title</label>
                                        <input type="text" id="post_title" class="form-control default-page-name page-name @error('title') is-invalid @enderror" name="title" value="{{ old('title', isset($data) ? $data->title : '') }}" onchange="string_to_slug(this.value)">
                                        @error('title')
                                        <span role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label">Body</label>
                                        <textarea class="form-control summernote @error('blog_content') is-invalid @enderror" name="blog_content">
                                            {{ old('blog_content', isset($data) ? $data->content : '') }}
                                        </textarea>
                                        @error('blog_content')
                                        <span role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="appearance-seo__block-collapse collapse in" id="collapse-languages-seo" aria-expanded="true">

                                    <div class="form-group" style="margin-top: 20px;">
                                        <label class="control-label" for=meta_title">Page title</label>
                                        <input type="text" id="meta_title" class="form-control" name="meta_title" value="{{ old('meta_title', isset($data) ? $data->meta_title : '') }}">
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label" for="meta_keyword">Meta-keywords</label>
                                        <input id="meta_keyword" class="form-control" name="meta_keyword" value="{{ old('meta_keyword', isset($data) ? $data->meta_keyword : '') }}">
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label" for="meta_description">Meta-description</label>
                                        <textarea id="meta_description" class="form-control" name="meta_description" rows="5">{{ old('meta_description', isset($data) ? $data->meta_description : '') }}</textarea>
                                    </div>

                                </div>

                                <hr>

                                <div class="form-group">
                                    <label class="control-label" for="createpageform-url">Type</label>
                                    <div class="input-group">
                                        <label class="radio-inline">
                                            <input type="radio" value="Trending" name="type" id="blog_type1" {{ old('type', isset($data) ? $data->type : '') == 'Trending' ? 'checked' : '' }} required> Trending
                                        </label>
                                        <label class="radio-inline radio-ml">
                                            <input type="radio" value="Popular" name="type" id="blog_type2" {{ old('type', isset($data) ? $data->type : '') == 'Popular' ? 'checked' : '' }} required> Popular
                                        </label>
                                        <label class="radio-inline radio-ml">
                                            <input type="radio" value="Blog" name="type" id="blog_type3" {{ old('type', isset($data) ? $data->type : '') == 'Blog' ? 'checked' : '' }} required> None
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
                                        <option value="Active" {{ old('status', isset($data) ? $data->status : '') == 'Active' ? 'selected' : '' }}>Active</option>
                                        <option value="Deactivated" {{ old('status', isset($data) ? $data->status : '') == 'Deactivated' ? 'selected' : '' }}>Deactivated</option>
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
