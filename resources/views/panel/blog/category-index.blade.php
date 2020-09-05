@extends('layouts.panel')

@section('content')
    @php
        $resource = 'admin.blog-category.';
    @endphp
    <div class="container all-mt-30">
        <div class="row">
            <!--navbar-->
            @include('panel.blog.nav')
            <!--navbar-->
            @if($page == 'index')
            <div class="col-md-8">
                <div class="card panel-default">
                    <div class="card-body">
                        <a class="btn btn-default m-b add-page" href="{{ route($resource.'create') }}">Add New Category</a>

                        <table class="table">
                            <thead>
                            <tr>
                                <th>SN</th>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(!empty($data))
                                @foreach($data as $key => $category)
                                <tr>
                                    <td>{{ $key+1 }}</td>
                                    <td>{{ $category->name }}</td>
                                    <td>{{ $category->status }}</td>
                                    <td class="p-r text-right">
                                        <a class="btn btn-default btn-xs" href="{{ route($resource.'edit', $category->id) }}">Edit</a>
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
                        <form method="post" action="{{ $page == 'edit' ? route($resource.'update', $data->id):route($resource.'store') }}">
                            @csrf
                            @if($page == 'edit')
                                @method('PUT')
                            @endif
                            <div class="form-group">
                                <label class="form-control-label">Category name</label>
                                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('question', isset($data) && $data->name ? $data->name:'' ) }}" placeholder="Category name">
                            </div>
                            <div class="form-group">
                                <label class="form-control-label">Status</label>
                                <select class="form-control @error('status') is-invalid @enderror" name="status" id="status">
                                    <option value="active" {{ isset($data) && $data->status == 'active' ? 'selected':'' }}>Active</option>
                                    <option value="inactive" {{ isset($data) && $data->status == 'inactive' ? 'selected':'' }}>Inactive</option>
                                </select>
                                @error('answer')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <hr>
                            <button type="submit" class="btn btn-primary" name="save-button">Save changes</button>
                            <a class="btn btn-default" href="{{ route($resource.'index') }}">Cancel</a>
                            @if($page == 'edit')
                                <a href="javascript: void(0)" onclick="document.getElementById('deleteCategory').submit();" class="btn btn-default waves-effect pull-right" ><i>Delete</i></a>
                            @endif
                        </form>
                        @if($page == 'edit')
                            <form id="deleteCategory" action="{{ route($resource.'destroy', $data->id)}}" method="post">
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
