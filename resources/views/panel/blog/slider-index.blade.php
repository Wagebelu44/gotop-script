@extends('layouts.panel')

@section('content')
    @php
        $resource = 'admin.blog-slider.';
    @endphp
    <div class="container all-mt-30">
        <div class="row">
            <!--navbar-->
            @include('panel.blog.nav')
            <!--navbar-->
            @if ($page == 'index')
            <div class="col-md-8">
                <div class="card panel-default">
                    <div class="card-body">
                        <a class="btn btn-default m-b add-page" href="{{ route($resource.'create') }}">Add New Slider</a>

                        <table class="table">
                            <thead>
                            <tr>
                                <th>SN</th>
                                <th>Title & Image</th>
                                <th>Read More</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if (!empty($data))
                                @foreach($data as $key => $slider)
                                <tr>
                                    <td>{{ $key+1 }}</td>
                                    <td>
                                        {{ $slider->title }}
                                    </td>
                                    <td>{{ $slider->read_more }}</td>
                                    <td>{{ $slider->status }}</td>
                                    <td class="p-r text-right">
                                        <a class="btn btn-default btn-xs" href="{{ route($resource.'edit', $slider->id) }}">Edit</a>
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
                        <form method="post" action="{{ $page == 'edit' ? route($resource.'update', $data->id):route($resource.'store') }}" enctype="multipart/form-data">
                            @csrf
                            @if ($page == 'edit')
                                @method('PUT')
                            @endif
                            <div class="row">
                                <div class="col-md-10">
                                    <div class="form-group">
                                        <label class="form-control-label">Slider image</label><br/>
                                        <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" id="image" accept="image/*" onchange="preview_image(event)">
                                        <span>1880 x 600px recommended</span>
                                    </div>
                                    @error('image')
                                    <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <img style="width: 200px" id="preview" class="img-thumbnail" src="">
                                    @if (isset($data->image))
                                        <img style="width: 200px" id="savedLogo" class="img-thumbnail" src="{{ asset('./storage/images/blog-slider/'.$data->image) }}">
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-control-label">Title</label>
                                <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', isset($data) && $data->title ? $data->title:'' ) }}" placeholder="Slider title">
                                @error('title')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label class="form-control-label">Read More(Blog page Url)</label>
                                <input type="text" name="read_more" id="read_more" class="form-control @error('read_more') is-invalid @enderror" value="{{ old('read_more', isset($data) && $data->read_more ? $data->read_more:'' ) }}" placeholder="Read more">
                                @error('read_more')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label class="form-control-label">Status</label>
                                <select class="form-control @error('status') is-invalid @enderror" name="status" id="status">
                                    <option value="Active" {{ isset($data) && $data->status == 'Active' ? 'selected':'' }}>Active</option>
                                    <option value="Deactivated" {{ isset($data) && $data->status == 'Deactivated' ? 'selected':'' }}>Deactivated</option>
                                </select>
                                @error('status')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <hr>
                            <button type="submit" class="btn btn-primary" name="save-button">Save changes</button>
                            <a class="btn btn-default" href="{{ route($resource.'index') }}">Cancel</a>
                            @if ($page == 'edit')
                                <a href="javascript: void(0)" onclick="document.getElementById('deleteSlider').submit();" class="btn btn-default waves-effect pull-right" ><i>Delete</i></a>
                            @endif
                        </form>
                        @if ($page == 'edit')
                            <form id="deleteSlider" action="{{ route($resource.'destroy', $data->id)}}" method="post">
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
