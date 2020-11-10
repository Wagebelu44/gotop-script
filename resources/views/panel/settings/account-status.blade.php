
@extends('layouts.panel')

@section('content')
    @php
        $resource = 'admin.setting.account-status.';
    @endphp

    <div class="row all-mt-30">
        @include('panel.settings.navbar')

        @if ($page == 'index')
            <div class="col-md-8">
                <a href="{{ route($resource.'create') }}" class="btn btn-sm btn-primary">Add Status</a>
                <div class="card panel-default" style="margin-top: 10px;">
                    <div class="card-body">
                        <div class="col-md-12">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>SL</th>
                                    <th>Name</th>
                                    <th>Maximum spent amount</th>
                                    <th>Point</th>
                                    <th width="5%"></th>
                                </tr>
                                </thead>
                                <tbody>
                                @if (!empty($data))
                                    @foreach($data as $key => $accStatus)
                                    <tr>
                                        <td>{{ ++$key }}</td>
                                        <td>{{ $accStatus->name }}</td>
                                        <td>{{ $accStatus->minimum_spent_amount }}</td>
                                        <td>{{ $accStatus->point }}</td>
                                        <td>
                                            <a href="{{ route($resource.'edit', $accStatus->id) }}" class="edit btn btn-default m-t-20">
                                                Edit
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        @elseif ($page == 'create' || $page == 'edit')
            <div class="col-md-8">
                <a href="{{ route($resource.'index') }}" class="btn btn-sm btn-primary">Back</a>
                <div class="card panel-default" style="margin-top: 10px;">
                    <div class="card-body">
                        <form action="{{ $page == 'edit' ? route($resource.'update', $data->id):route($resource.'store') }}" method="post">
                            @csrf
                            @if ($page == 'edit')
                                @method('PUT')
                            @endif

                            <div class="form-group">
                                <label class="control-label" for="name">Name</label>
                                <input class="form-control @error('name') is-invalid @enderror" value="{{ old('name', isset($data) && $data->name ? $data->name:'' ) }}" name="name">
                                @error('name')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label class="control-label" for="minimum_spent_amount">Maximum spent amount</label>
                                <input type="number" step="0.01" min="0" class="form-control @error('minimum_spent_amount') is-invalid @enderror" value="{{ old('minimum_spent_amount', isset($data) && $data->minimum_spent_amount ? $data->minimum_spent_amount:'' ) }}" name="minimum_spent_amount">
                                @error('minimum_spent_amount')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label class="control-label" for="point">Point (Per 100 Point = Amount)</label>
                                <input type="number" step="0.01" min="0" class="form-control @error('point') is-invalid @enderror" value="{{ old('point', isset($data) && $data->point ? $data->point:'' ) }}" name="point">
                                @error('point')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-12">
                                    <label class="control-label" for="point">Account status keys</label>
                                </div>
                                @foreach(accountStatusKeys() as $key => $accStatusData)
                                <div class="col-md-6 mt-2">
                                    <div class="form-check">
                                        <input class="form-check-input" name="status_keys[]" type="checkbox" value="{{ $accStatusData }}" id="status_keys{{ $key }}" style="margin-top: 2px" {{ isset($statusKeys) && in_array($accStatusData, $statusKeys)?'checked':'' }}>
                                        <label class="form-check-label" for="status_keys{{ $key }}">
                                            {{ $accStatusData }}
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-12">
                                    <label class="control-label" for="point">Account point keys</label>
                                </div>
                                @foreach(accountPointKeys() as $pk => $accPointData)
                                    <div class="col-md-6 mt-2">
                                        <div class="form-check">
                                            <input class="form-check-input" name="point_keys[]" type="checkbox" value="{{ $accPointData }}" id="point_keys{{ $pk }}" style="margin-top: 2px" {{ isset($pointKeys) && in_array($accPointData, $pointKeys)?'checked':'' }}>
                                            <label class="form-check-label" for="point_keys{{ $pk }}">
                                                {{ $accPointData }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <hr>
                            <button type="submit" class="btn btn-primary" name="save-button">Save changes</button>
                            <a class="btn btn-default" href="{{ route($resource.'index')}}">Cancel</a>
                            @if ($page == 'edit')
                                <a href="javascript: void(0)" onclick="document.getElementById('deleteStatus').submit();" class="btn btn-default waves-effect pull-right" ><i>Delete</i></a>
                            @endif
                        </form>
                        @if ($page == 'edit')
                            <form id="deleteStatus" action="{{ route($resource.'destroy', $data->id)}}" method="post">
                                @csrf
                                @method('DELETE')
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
