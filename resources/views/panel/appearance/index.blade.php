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
                                            <td class="p-l">{{ $info->page_name }}</td>
                                            <td>
                                                <div class="setting-switch setting-switch-table">
                                                    <label class="switch">
                                                        <input type="checkbox" class="toggle-page-visibility" name="page_status" id="page_status" value="{{ $info->status }}"  {{ $info->status == 'active' ? 'Checked' : '' }}>
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
                                        <label class="control-label" for="page_name">Page name</label>
                                        <input type="text" id="page_name" class="form-control" name="page_name" value="{{ old('page_name', isset($data) ? $data->page_name : '') }}">
                                        @error('page_name')
                                        <span role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label" for="page_Content">Content</label>
                                        <textarea name="page_content" id="page_content" class="form-control summernote">
                                            {{ old('page_content', isset($data) ? $data->content : '') }}
                                        </textarea>
                                        @error('page_Content')
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
                                        <input type="text" id="page_url" class="form-control" name="page_url" value="{{ old('page_url', isset($data) ? $data->url : '') }}">
                                        @error('page_url')
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

                                        <div class="seo-preview">
                                            <div class="seo-preview__title edit-seo__title"></div>
                                            <div class="seo-preview__description edit-seo__meta"></div>
                                        </div>

                                    </div>

                                    <div class="appearance-seo__block-collapse collapse in" id="collapse-languages-seo" aria-expanded="true">

                                        <div class="form-group">
                                            <label class="control-label" for="seo_title">Page title</label>
                                            <input type="text" id="seo_title" class="form-control" name="seo_title" value="{{ old('seo_title', isset($data) ? $data->page_title : '') }}">
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label" for="seo_keywords">Meta-keywords</label>
                                            <input id="seo_keywords" class="form-control" name="seo_keywords" value="{{ old('seo_keywords', isset($data) ? $data->meta_keyword : '') }}">
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label" for="seo_description">Meta-description</label>
                                            <textarea id="seo_description" class="form-control" name="seo_description" rows="5">{{ old('seo_description', isset($data) ? $data->meta_description : '') }}</textarea>
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
