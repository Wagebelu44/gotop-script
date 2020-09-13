@extends('layouts.panel')

@section('content')
    @php
        $resource = 'admin.appearance.';
    @endphp
    <div class="container all-mt-30">
        <div class="row">
            @include('panel.appearance.nav')

            @if($page == 'index')
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <a class="btn btn-default m-b add-page" href="{{ route($resource.'create') }}">Add page</a>

                            <table class="table">
                                <thead>
                                <tr>
                                    <th class="p-l">SN</th>
                                    <th width="35%" class="p-l">Name</th>
                                    <th>Visibility</th>
                                    <th>Public</th>
                                    <th>Last modified</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if(!empty($data))
                                    @foreach($data as $key => $info)
                                        <tr class="@if($info->non_editable == 'yes') disable-keystroke @endif">
                                            <td class="p-l">{{ $key+1 }}</td>
                                            <td class="p-l">{{ $info->name }}</td>
                                            <td>
                                                <div class="setting-switch setting-switch-table">
                                                    <label class="switch">
                                                        <input type="checkbox" class="toggle-page-visibility" onclick="isActiveInactive({{ $info->id }})" name="status" id="status{{$info->id}}" value="{{ $info->status }}"  {{ $info->status == 'Active' ? 'checked' : '' }}>
                                                        <span class="slider round"></span>
                                                    </label>
                                                </div>
                                            </td>
                                            <td>{{ $info->public }}</td>
                                            <td>{{ $info->created_at }}</td>
                                            <td class="p-r text-right">
                                                <a class="btn btn-default btn-xs" href="{{ route($resource.'edit', $info->id) }}">Edit</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            @elseif($page == 'create' || $page == 'edit')
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <form name="blogForm" action="{{ $page == 'edit' ? route($resource.'update', $data->id):route($resource.'store') }}" method="post" enctype="multipart/form-data">
                                @csrf

                                @if($page == 'edit')
                                    @method('PUT')
                                @endif

                                <div class="relative">

                                    <div class="form-group">
                                        <label class="control-label" for="name">Page name</label>
                                        <input type="text" id="name" class="form-control" name="name" value="{{ old('name', isset($data) ? $data->name : '') }}">
                                        @error('name')
                                        <span role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label" for="content">Content</label>
                                        <textarea name="page_content" id="content" class="form-control summernote">
                                            {{ old('content', isset($data) ? $data->content : '') }}
                                        </textarea>
                                        @error('content')
                                        <span role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>

                                </div>
                                <hr>

                                <div class="form-group">
                                    <label class="control-label" for="createpageform-url">URL</label>
                                    <div class="input-group">
                                        <span class="input-group-addon" for="url">{{ URL::to('/') }}</span>
                                        <input type="text" id="url" class="form-control" name="url" value="{{ old('url', isset($data) ? $data->url : '') }}">
                                        @error('url')
                                        <span role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label" for="is_public">Public</label>
                                    <select class="form-control" name="is_public" id="is_public">
                                        <option value="no" {{ old('is_public', isset($data) ? $data->public : '') == 'no' ? 'selected' : '' }}>No</option>
                                        <option value="yes" {{ old('is_public', isset($data) ? $data->public : '') == 'yes' ? 'selected' : '' }}>Yes</option>
                                    </select>
                                </div>

                                <div class="bg-grey" id="seo_container">

                                    <div class="appearance-seo__block">

                                        <div class="appearance-seo__block-title">Search engine listing preview</div>

                                        <div class="seo-preview" style="margin-top: 30px;">
                                            <div class="seo-preview__title edit-seo__title"></div>
                                            <div class="seo-preview-url">{{ URL::to('/') }}<span class="edit-seo__url"></span></div>
                                            <div class="seo-preview__description edit-seo__meta"></div>
                                        </div>

                                    </div>

                                    <div class="appearance-seo__block-collapse collapse in" id="collapse-languages-seo" aria-expanded="true">

                                        <div class="form-group" style="margin-top: 20px;">
                                            <label class="control-label" for=meta_title">Page title</label>
                                            <input type="text" id="meta_title" class="form-control" name="meta_title" value="{{ old('meta_title', isset($data) ? $data->meta_title : '') }}">
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label" for="meta_keywords">Meta-keywords</label>
                                            <input id="meta_keywords" class="form-control" name="meta_keywords" value="{{ old('meta_keywords', isset($data) ? $data->meta_keyword : '') }}">
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label" for="meta_description">Meta-description</label>
                                            <textarea id="meta_description" class="form-control" name="meta_description" rows="5">{{ old('meta_description', isset($data) ? $data->meta_description : '') }}</textarea>
                                        </div>

                                    </div>
                                </div>
                                <hr>
                                <button type="submit" class="btn btn-primary" name="save-button">Save changes</button>
                                <a class="btn btn-default" href="{{ route($resource.'index') }}">Cancel</a>

                            </form>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function isActiveInactive(pageId) {
            var status_value = '';
            let status = $('#status'+pageId).val();
            $.ajax({
                type: "POST",
                dataType: "json",
                url: "{{ route($resource.'updateStatus') }}",
                data: {'status': status, 'id': pageId, "_token": "{{ csrf_token() }}"},
                success: function (response) {
                    if (response.status === 'success'){
                        if (status === 'Active') {
                            status_value = 'Deactivated';
                        } else if (status === 'Deactivated') {
                            status_value = 'Active';
                        }
                        $('#status'+pageId).val(status_value);
                        toastr["success"](response.message);
                    }else{
                        toastr["error"]("Something went wrong !! Please try again !!");
                    }
                }
            });
        }
    </script>
@endsection
