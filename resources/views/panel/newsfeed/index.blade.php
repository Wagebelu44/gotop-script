@extends('layouts.panel')

@section('content')
    @php
      $resource = 'admin.newsfeed.';
    @endphp

    <div class="container all-mt-30">
        <div class="row">
            <!--navbar-->
            @include('panel.newsfeed.nav')
            <!--navbar-->
            @if ($page == 'index')
            <div class="col-md-8">
                <div class="card panel-default">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div class="left-side">
                                <a class="btn btn-default m-b add-page" href="{{ route($resource.'create') }}">Add News feed</a>
                            </div>
                            <div class="right-side">
                                <form class="d-flex">
                                    <input type="search" name="search_text" class="form-control" placeholder="Search">
                                    <button type="submit" class="custom-button"> <i class="fa fa-search" aria-hidden="true"></i> </button>
                                </form>
                            </div>
                        </div>
                        <table class="table">
                            <thead>
                            <tr>
                                <th>SN</th>
                                <th>Title</th>
                                <th>Created</th>
                                <th>Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if (!empty($data))
                                @foreach($data as $key => $newfeed)
                                    <tr>
                                        <td>{{ $key+1 }}</td>
                                        <td>
                                            {{ $newfeed->title }}
                                        </td>
                                        <td>{{ $newfeed->created_at }}</td>
                                        <td>{{ $newfeed->status }}</td>
                                        <td class="p-r text-center">
                                            <a class="btn btn-default btn-xs" href="{{ route($resource.'edit', $newfeed->id) }}">Edit</a>
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
            @elseif ($page == 'create' || $page == 'edit')
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <form name="newsfeedForm" action="{{ $page == 'edit' ? route($resource.'update', $data->id):route($resource.'store') }}" method="post" enctype="multipart/form-data">
                                @csrf
                                @if ($page == 'edit')
                                    @method('PUT')
                                @endif
                                <div class="relative">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-10">
                                                <div class="form-group">
                                                    <label class="form-control-label">Post image</label><br/>
                                                    <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" id="image" accept="image/*" onchange="preview_image(event)">
                                                    <p class="help-block">400 x 400px recommended</p>
                                                </div>
                                                @error('image')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                                @enderror
                                            </div>
                                            <div class="col-md-2">
                                                <img style="width: 100px" id="preview" src="">
                                                @if (isset($data->image))
                                                    <img style="width: 200px" id="savedLogo" class="img-thumbnail" src="{{ asset('./storage/images/newsfeed/'.$data->image) }}">
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label" for="categories">News feed Category</label>
                                        <select class="select2 form-control is-public @error('cate') is-invalid @enderror" multiple="multiple" name="categories[]" id="categories" required>
                                            @if (!empty($categories))
                                                @foreach ($categories as $key => $category)
                                                    <option value="{{ $category->id }}"  {{ isset($data->getCategories) && (in_array($category->id, $data->getCategories))?'selected':'' }}>{{ $category->name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        @error('categories')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label" for="service_update">Service Update</label>
                                        <div class="setting-switch setting-switch-table">
                                            <label class="switch">
                                                <input type="checkbox" class="toggle-page-visibility" name="service_update" id="service_update" @if( isset($data->service_update) && $data->service_update =='Yes') checked @endif>
                                                <span class="slider round"></span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label" for="important_news">Important News</label>
                                        <div class="setting-switch setting-switch-table">
                                            <label class="switch">
                                                <input type="checkbox" class="toggle-page-visibility" name="important_news" id="important_news" @if(isset($data->important_news) && $data->important_news =='Yes') checked @endif>
                                                <span class="slider round"></span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label" for="post_title">Title</label>
                                        <input type="text" id="post_title" class="form-control default-page-name page-name @error('title') is-invalid @enderror" name="title" value="{{ old('title', isset($data) ? $data->title : '') }}" onchange="string_to_slug(this.value)" required>
                                        @error('title')
                                        <span role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label">Body</label>
                                        <textarea class="form-control summernote @error('newsfeed_content') is-invalid @enderror" name="newsfeed_content">
                                            {{ old('newsfeed_content', isset($data) ? $data->content : '') }}
                                        </textarea>
                                        @error('newsfeed_content')
                                        <span role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
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
                                <a class="btn btn-default" href="{{ route($resource.'index') }}">Cancel</a>
                                @if ($page == 'edit')
                                    <a href="javascript: void(0)" onclick="document.getElementById('deleteNewsfeed').submit();" class="btn btn-default waves-effect pull-right" ><i>Delete</i></a>
                                @endif
                            </form>

                            @if ($page == 'edit')
                                <form id="deleteNewsfeed" action="{{ route($resource.'destroy', $data->id)}}" method="post">
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
