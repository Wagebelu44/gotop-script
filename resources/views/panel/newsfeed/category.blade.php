@extends('layouts.panel')

@section('content')
    @php
        $resource = 'admin.newsfeed-category.';
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
                                <a class="btn btn-default m-b add-page" href="{{ route($resource.'create') }}">Add New Category</a>
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
                                <th>Name</th>
                                <th>Color</th>
                                <th>Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if (!empty($data))
                                @foreach($data as $key => $category)
                                <tr>
                                    <td>{{ $key+1 }}</td>
                                    <td>{{ $category->name }}</td>
                                    <td><span class="badge" style="background-color: {{ $category->color }}">&nbsp;&nbsp;&nbsp;</span></td>
                                    <td>{{ $category->status }}</td>
                                    <td class="p-r text-center">
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
            @elseif ($page == 'create' || $page == 'edit')
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <form method="post" action="{{ $page == 'edit' ? route($resource.'update', $data->id):route($resource.'store') }}">
                            @csrf
                            @if ($page == 'edit')
                                @method('PUT')
                            @endif
                            <div class="form-group">
                                <label class="form-control-label">Category name</label>
                                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', isset($data) && $data->name ? $data->name:'' ) }}" placeholder="Category name">
                                @error('name')
                                    <span>{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label class="form-control-label">Color</label>
                                <input type="color" name="color" id="color" class="form-control @error('color') is-invalid @enderror" value="{{ old('color', isset($data) && $data->color ? $data->color:'' ) }}" placeholder="Category name">
                            </div>

                            <div class="form-group">
                                <label class="form-control-label">Status</label>
                                <select class="form-control @error('status') is-invalid @enderror" name="status" id="status">
                                    <option value="Active" {{ isset($data) && $data->status == 'Active' ? 'selected':'' }}>Active</option>
                                    <option value="Deactivated" {{ isset($data) && $data->status == 'Deactivated' ? 'selected':'' }}>Deactivated</option>
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
                            @if ($page == 'edit')
                                <a href="javascript: void(0)" onclick="document.getElementById('deleteCategory').submit();" class="btn btn-default waves-effect pull-right" ><i>Delete</i></a>
                            @endif
                        </form>
                        @if ($page == 'edit')
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
