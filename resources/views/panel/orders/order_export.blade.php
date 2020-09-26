@extends('layouts.panel')

@section('content')
    <!-- ============================================================== -->
    <!-- Start Page Content -->
    <!-- ============================================================== -->
    <!-- basic table -->
    <div class="row">
        <div class="col-12">
            <div class="material-card card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h4 class="card-title text-uppercase">Orders export</h4>
                        </div>
                        <div class="col-md-6">
                        </div>
                    </div>
                    <div class="material-card card">
                        <div class="card-body">
                            <form method="post" action="{{ route('admin.exported_orders.store') }}" novalidate>
                                <div class="modal bs-example-modal-lg" id="customizeColumns" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h4 class="modal-title" id="myLargeModalLabel">Customize columns</h4>
                                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Select the columns that you want to include in your file</p>
                                                <ul class="list-group customize-fields__list">
                                                    <li class="list-group-item">
                                                        <div class="checkbox">
                                                            <label>
                                                                <input class="column_item" type="checkbox" name="include_columns[]" value="id" checked=""> ID
                                                            </label>
                                                        </div>
                                                    </li>
                                                    <li class="list-group-item">
                                                        <div class="checkbox">
                                                            <label>
                                                                <input class="column_item" type="checkbox" name="include_columns[]" value="order_id" checked=""> External ID                                </label>
                                                        </div>
                                                    </li>
                                                    <li class="list-group-item">
                                                        <div class="checkbox">
                                                            <label>
                                                                <input class="column_item" type="checkbox" name="include_columns[]" value="user_username" checked=""> User                                </label>
                                                        </div>
                                                    </li>
                                                    <li class="list-group-item">
                                                        <div class="checkbox">
                                                            <label>
                                                                <input class="column_item" type="checkbox" name="include_columns[]" value="charges" checked=""> Charge                                </label>
                                                        </div>
                                                    </li>
                                                    {{--<li class="list-group-item">
                                                        <div class="checkbox">
                                                            <label>
                                                                <input class="column_item" type="checkbox" name="include_columns[]" value="cost" checked=""> Cost                                </label>
                                                        </div>
                                                    </li>--}}
                                                    <li class="list-group-item">
                                                        <div class="checkbox">
                                                            <label>
                                                                <input class="column_item" type="checkbox" name="include_columns[]" value="link" checked=""> Link                                </label>
                                                        </div>
                                                    </li>
                                                    <li class="list-group-item">
                                                        <div class="checkbox">
                                                            <label>
                                                                <input class="column_item" type="checkbox" name="include_columns[]" value="start_counter" checked=""> Start count                                </label>
                                                        </div>
                                                    </li>
                                                    <li class="list-group-item">
                                                        <div class="checkbox">
                                                            <label>
                                                                <input class="column_item" type="checkbox" name="include_columns[]" value="quantity" checked=""> Quantity                                </label>
                                                        </div>
                                                    </li>
                                                    <li class="list-group-item">
                                                        <div class="checkbox">
                                                            <label>
                                                                <input class="column_item" type="checkbox" name="include_columns[]" value="service_id" checked=""> Service ID                                </label>
                                                        </div>
                                                    </li>
                                                    <li class="list-group-item">
                                                        <div class="checkbox">
                                                            <label>
                                                                <input class="column_item" type="checkbox" name="include_columns[]" value="service_name" checked=""> Service                                </label>
                                                        </div>
                                                    </li>
                                                    <li class="list-group-item">
                                                        <div class="checkbox">
                                                            <label>
                                                                <input class="column_item" type="checkbox" name="include_columns[]" value="status" checked=""> Status                                </label>
                                                        </div>
                                                    </li>
                                                    <li class="list-group-item">
                                                        <div class="checkbox">
                                                            <label>
                                                                <input class="column_item" type="checkbox" name="include_columns[]" value="remains" checked=""> Remains                                </label>
                                                        </div>
                                                    </li>
                                                    <li class="list-group-item">
                                                        <div class="checkbox">
                                                            <label>
                                                                <input class="column_item" type="checkbox" name="include_columns[]" value="created_at" checked=""> Created                                </label>
                                                        </div>
                                                    </li>
                                                    <li class="list-group-item">
                                                        <div class="checkbox">
                                                            <label>
                                                                <input class="column_item" type="checkbox" name="include_columns[]" value="provider_domain" checked=""> Provider                                </label>
                                                        </div>
                                                    </li>
                                                    <li class="list-group-item">
                                                        <div class="checkbox">
                                                            <label>
                                                                <input class="column_item" type="checkbox" name="include_columns[]" value="mode" checked=""> Mode                                </label>
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="modal-footer">
                                                <div class="form-actions">
                                                    <button type="submit" class="btn btn-success" data-dismiss="modal"> <i class="fa fa-check"></i> Save</button>
                                                </div>
                                                <button type="button" class="btn btn-danger waves-effect text-left" data-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                        <!-- /.modal-content -->
                                    </div>
                                    <!-- /.modal-dialog -->
                                </div>

                                @csrf

                                <div class="row">
                                    <div class="col">
                                        <div class="form-group">
                                            <div class="controls">
                                                <input type="text" name="from" class="form-control  datepicker @error('from') is-invalid @enderror" placeholder="From" value="{{ old('from') }}" required data-validation-required-message="This field is required">

                                                @error('from')
                                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <div class="controls">
                                                <input type="text" name="to" class="form-control datepicker @error('to') is-invalid @enderror" placeholder="To" value="{{ old('to') }}" required data-validation-required-message="This field is required">

                                                @error('to')
                                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <div class="controls">
                                                <select name="user_ids[]" id="user_ids" class="form-control @error('user_ids') is-invalid @enderror" required data-validation-required-message="This field is required" multiple>
                                                    <option value="all" {{ old('user_ids') ? in_array('all', old('user_ids')) ? 'selected' : '' : 'selected' }}>All users</option>
                                                    @foreach($panel_users as $user)
                                                        <option value="{{ $user->id }}" {{ ( old('user_ids') ? in_array($user->id, old('user_ids')) : false ) ? 'selected' : '' }}>{{ $user->username }}</option>
                                                    @endforeach
                                                </select>

                                                @error('user_ids')
                                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <div class="controls">
                                                <select name="service_ids[]" id="service_ids" class="form-control select2 @error('service_ids') is-invalid @enderror" required data-validation-required-message="This field is required" multiple>
                                                    <option value="all" {{ old('service_ids') ? in_array('all', old('service_ids')) ? 'selected' : '' : 'selected' }}>All services</option>
                                                    @foreach($panel_services as $service)
                                                        <option value="{{ $service->id }}" {{ (  old('service_ids') ? in_array($service->id, old('service_ids')) : false ) ? 'selected' : '' }}>{{ $service->name }}</option>
                                                    @endforeach
                                                </select>

                                                @error('service_ids')
                                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <div class="controls">
                                                <select name="status[]" id="status" class="form-control @error('status') is-invalid @enderror" required data-validation-required-message="This field is required" multiple>
                                                    <option value="all" {{ old('status') ? in_array('all', old('status')) ? 'selected' : '' : 'selected' }}>All Statuses</option>
                                                    <option {{ ( old('status') ? in_array('pending', old('status')) : false ) ? 'selected' : '' }} value="pending">Pending</option>
                                                    <option {{ ( old('status') ? in_array('inprogress', old('status')) : false ) ? 'selected' : '' }} value="inprogress">In Progress</option>
                                                    <option {{ ( old('status') ? in_array('completed', old('status')) : false ) ? 'selected' : '' }} value="completed">Completed</option>
                                                    <option {{ ( old('status') ? in_array('partial', old('status')) : false ) ? 'selected' : '' }} value="partial">Partial</option>
                                                    <option {{ ( old('status') ? in_array('cancelled', old('status')) : false ) ? 'selected' : '' }} value="cancelled">Cancelled</option>
                                                    <option {{ ( old('status') ? in_array('processing', old('status')) : false ) ? 'selected' : '' }} value="processing">Processing</option>
                                                    <option {{ ( old('status') ? in_array('failed', old('status')) : false ) ? 'selected' : '' }} value="failed">Failed</option>
                                                    <option {{ ( old('status') ? in_array('error', old('status')) : false ) ? 'selected' : '' }} value="error">Error</option>
                                                </select>

                                                @error('status')
                                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <div class="controls">
                                                @php
                                                     $selected = '';
                                                    if (old('provider_ids'))
                                                    {
                                                        if (in_array('all', old('provider_ids'))) {
                                                            $selected = 'selected';
                                                        }
                                                    }
                                                    else
                                                    {
                                                        $selected = 'selected';
                                                    }
                                                @endphp
                                                <select name="provider_ids[]" id="provider_ids" class="form-control select2 @error('provider_ids') is-invalid @enderror" required data-validation-required-message="This field is required" multiple>
                                                    <option value="all" {{ $selected }}>All providers</option>
                                                    @foreach(\App\Models\SettingProvider::all() as $provider)
                                                        <option value="{{ $provider->id }}" {{ ( old('provider_ids') ? in_array($provider->id, old('provider_ids')) : false ) ? 'selected' : '' }}>{{ $provider->domain }}</option>
                                                    @endforeach
                                                </select>

                                                @error('provider_ids')
                                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <div class="controls">
                                                <select name="mode" class="form-control @error('mode') is-invalid @enderror" required data-validation-required-message="This field is required">
                                                    <option disabled selected>Choose mode</option>
                                                    <option value="all" selected>All</option>
                                                    <option {{ old('mode') == 'auto' ? 'selected' : '' }} value="inactive">Auto</option>
                                                    <option {{ old('mode') == 'manual' ? 'selected' : '' }} value="pending">Manual</option>
                                                </select>

                                                @error('mode')
                                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <div class="controls">
                                                <select name="format" class="form-control @error('format') is-invalid @enderror" required data-validation-required-message="This field is required">
                                                    <option selected>Choose Format</option>
                                                    <option {{ old('format') == 'xml' ? 'selected' : '' }} value="xml">XML</option>
                                                    <option {{ old('format') == 'json' ? 'selected' : '' }} value="json">JSON</option>
                                                    <option {{ old('format') == 'csv' ? 'selected' : '' }} value="csv">CSV</option>
                                                </select>

                                                @error('format')
                                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-actions">
                                            <button type="submit" class="btn btn-success"> <i class="fa fa-check"></i> Create file</button>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col">
                                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#customizeColumns">Customize columns</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="material-card card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="zero_config" class="table table-striped border table-hover">
                                    <thead>
                                    <tr>
                                        <th>From</th>
                                        <th>To</th>
                                        <th>Users</th>
                                        <th>Services</th>
                                        <th>Status</th>
                                        <th>Providers</th>
                                        <th>Modes</th>
                                        <th>Format</th>
                                        <th>Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($exported_order as $item)
                                    <tr>
                                        <td>{{ $item->from }}</td>
                                        <td>{{ $item->to }}</td>
                                        <td>{{ rtrim(implode(', ', array_map(function ($value) { return ucfirst($value); }, unserialize($item->user_ids)))) }}</td>
                                        <td>{{ rtrim(implode(', ', array_map(function ($value) { return ucfirst($value); }, unserialize($item->service_ids)))) }}</td>
                                        <td>{{ rtrim(implode(', ', array_map(function ($value) { return ucfirst($value); }, unserialize($item->status)))) }}</td>
                                        <td>{{ rtrim(implode(', ', array_map(function ($value) { return ucfirst($value); }, unserialize($item->provider_ids)))) }}</td>
                                        <td>{{ ucfirst($item->mode) }}</td>
                                        <td>{{ strtoupper($item->format) }}</td>
                                        <td>
                                            <form method="post" action="{{ route('admin.exported_orders.download', $item->id) }}">
                                                @csrf
                                                <button type="submit" class="btn btn-success">Download</button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>

        $(document).ready(function () {
            $('.datepicker').datepicker();
        });

       

        (function() {
            let status = $("#status");
            let user_ids = $("#user_ids");
            let service_ids = $("#service_ids");
            let provider_ids = $("#provider_ids");

            status.select2({
                width: '100%',
                allowClear: true,
                placeholder: 'Select status'
            });
            user_ids.select2({
                width: '100%',
                allowClear: true,
                placeholder: 'Select user'
            });
            service_ids.select2({
                width: '100%',
                allowClear: true,
                placeholder: 'Select services'
            });
            provider_ids.select2({
                width: '100%',
                allowClear: true,
                placeholder: 'Select providers'
            });

            status.on('select2:select', function (e) {
                toggleSelected (e, status);
            });
            user_ids.on('select2:select', function (e) {
                toggleSelected (e, user_ids);
            });
            service_ids.on('select2:select', function (e) {
                toggleSelected (e, service_ids);
            });
            provider_ids.on('select2:select', function (e) {
                toggleSelected (e, provider_ids);
            });
        })();

        function toggleSelected (e, element) {
            const data = e.params.data;

            // If selected all remove rest options else remove all
            if (data.id == 'all') {
                element.val(data.id).trigger('change');
            } else {
                const idToRemove = 'all';

                const values = element.val();
                if (values) {
                    const i = values.indexOf(idToRemove);
                    if (i >= 0) {
                        values.splice(i, 1);
                        element.val(values).change();
                    }
                }
            }
        }
    </script>
@endsection
