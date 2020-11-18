@extends('layouts.panel')

@section('content')
    <!-- ============================================================== -->
    <!-- Start Page Content -->
    <!-- ============================================================== -->
    <!-- basic table -->
    <style>
        .export-row {
            display: flex;
        }
        .export-col{
            display: table-cell;
            width: 100%;
            padding: 0 5px;
            vertical-align: middle;
            min-width: 115px;
            flex-basis: 151px;
        }
        .export-row .export-col:first-child {
            flex-basis: 210px;
            flex-shrink: 0;
            padding-left: 15px;
        }
    </style>
    <div class="container-fluid all-mt-30">
        <div class="row">
            <div class="col-12">
                <div class="card card-body custom-card-body">
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
                                                        <input class="column_item" type="checkbox" name="include_columns[]" value="order_id" checked=""> External ID
                                                    </label>
                                                </div>
                                            </li>
                                            <li class="list-group-item">
                                                <div class="checkbox">
                                                    <label>
                                                        <input class="column_item" type="checkbox" name="include_columns[]" value="user_username" checked=""> User
                                                    </label>
                                                </div>
                                            </li>
                                            <li class="list-group-item">
                                                <div class="checkbox">
                                                    <label>
                                                        <input class="column_item" type="checkbox" name="include_columns[]" value="charges" checked=""> Charge
                                                    </label>
                                                </div>
                                            </li>
                                            <li class="list-group-item">
                                                <div class="checkbox">
                                                    <label>
                                                        <input class="column_item" type="checkbox" name="include_columns[]" value="cost" checked=""> Cost 
                                                    </label>
                                                </div>
                                            </li>
                                            <li class="list-group-item">
                                                <div class="checkbox">
                                                    <label>
                                                        <input class="column_item" type="checkbox" name="include_columns[]" value="link" checked=""> Link
                                                    </label>
                                                </div>
                                            </li>
                                            <li class="list-group-item">
                                                <div class="checkbox">
                                                    <label>
                                                        <input class="column_item" type="checkbox" name="include_columns[]" value="start_counter" checked=""> Start count
                                                    </label>
                                                </div>
                                            </li>
                                            <li class="list-group-item">
                                                <div class="checkbox">
                                                    <label>
                                                        <input class="column_item" type="checkbox" name="include_columns[]" value="quantity" checked=""> Quantity
                                                    </label>
                                                </div>
                                            </li>
                                            <li class="list-group-item">
                                                <div class="checkbox">
                                                    <label>
                                                        <input class="column_item" type="checkbox" name="include_columns[]" value="service_id" checked=""> Service ID
                                                    </label>
                                                </div>
                                            </li>
                                            <li class="list-group-item">
                                                <div class="checkbox">
                                                    <label>
                                                        <input class="column_item" type="checkbox" name="include_columns[]" value="service_name" checked=""> Service
                                                    </label>
                                                </div>
                                            </li>
                                            <li class="list-group-item">
                                                <div class="checkbox">
                                                    <label>
                                                        <input class="column_item" type="checkbox" name="include_columns[]" value="status" checked=""> Status
                                                    </label>
                                                </div>
                                            </li>
                                            <li class="list-group-item">
                                                <div class="checkbox">
                                                    <label>
                                                        <input class="column_item" type="checkbox" name="include_columns[]" value="remains" checked=""> Remains
                                                    </label>
                                                </div>
                                            </li>
                                            <li class="list-group-item">
                                                <div class="checkbox">
                                                    <label>
                                                        <input class="column_item" type="checkbox" name="include_columns[]" value="created_at" checked=""> Created
                                                    </label>
                                                </div>
                                            </li>
                                            <li class="list-group-item">
                                                <div class="checkbox">
                                                    <label>
                                                        <input class="column_item" type="checkbox" name="include_columns[]" value="provider_domain" checked=""> Provider
                                                    </label>
                                                </div>
                                            </li>
                                            <li class="list-group-item">
                                                <div class="checkbox">
                                                    <label>
                                                        <input class="column_item" type="checkbox" name="include_columns[]" value="mode" checked=""> Mode
                                                    </label>
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
                        <div class="row export-row">
                            <div class="export-col">
                                <div class="form-group">
                                    <label for=""><strong>Date</strong></label>
                                    <input type="text" name="daterange" class="form-control custom-input" value="01/01/2018 - 01/15/2018" />
                                </div>
                                {{-- <div class="input-group input-daterange date">
                                    <input type="text" class="form-control" data-date-end-date="0d" value="2012-04-05">
                                    <div class="input-group-addon">to</div>
                                    <input type="text" class="form-control" data-date-end-date="0d" value="2012-04-19">
                                </div> --}}
                                {{-- <div class="form-group">
                                    <div class="controls">
                                        <input type="text" name="from" class="form-control  datepicker @error('from') is-invalid @enderror" placeholder="From" value="{{ old('from') }}" required data-validation-required-message="This field is required">
                                        @error('from')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div> --}}
                            </div>
                            <div class="export-col">
                                <div class="form-group">
                                    <label for=""><strong>Users</strong></label>
                                        <select 
                                        name="user_ids[]" 
                                        id="user_ids" 
                                        class="form-control custom-input with-ajax @error('user_ids') is-invalid @enderror" 
                                        required data-validation-required-message="This field is required" 
                                        title="All users" 
                                        data-live-search="true" 
                                        data-live-search-placeholder="Search"
                                        data-selected-text-format="count" 
                                        data-count-selected-text="All users" 
                                        data-container="body" 
                                        data-hide-disabled="true"
                                        data-selected-text-format="count>0"
                                        data-virtual-scroll="false"
                                        multiple>
                                        </select>
                                        @error('user_ids')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                </div>
                            </div>
                            <div class="export-col">
                                <div class="form-group">
                                    <label for=""><strong>Services</strong></label>
                                    <select name="service_ids[]" 
                                    id="service_ids" 
                                    class="form-control custom-input selectpicker @error('service_ids') is-invalid @enderror" 
                                    required data-validation-required-message="This field is required" 
                                    title="All services" 
                                    data-live-search="true" 
                                    data-live-search-placeholder="Search" 
                                    data-actions-box="true" 
                                    data-selected-text-format="count" 
                                    data-count-selected-text="All services" 
                                    data-container="body" 
                                    data-hide-disabled="true"
                                    data-selected-text-format="count>0"
                                    data-virtual-scroll="false"
                                    multiple>
                                        {{-- <option value="all" {{ old('service_ids') ? in_array('all', old('service_ids')) ? 'selected' : '' : 'selected' }}>All services</option> --}}
                                        @foreach($panel_services as $service)
                                            <option 
                                            value="{{ $service->id }}" {{ (  old('service_ids') ? in_array($service->id, old('service_ids')) : false ) ? 'selected' : '' }}
                                            data-content='<span style="padding: 2px; border: 1px solid rgba(0, 0, 0, 0.7); font-size: 10px; font-weight: 700;">{{ $service->id }}</span> <span>{{ $service->name }}</span>'>
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('service_ids')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="export-col">
                                <div class="form-group">
                                    <label for=""><strong>Status</strong></label>
                                    <select 
                                    name="status[]" 
                                    id="status" 
                                    title="All statuses" 
                                    data-selected-text-format="count>0"
                                    data-count-selected-text="Statuses ({0})"
                                    class="form-control custom-input selectpicker @error('status') is-invalid @enderror" 
                                    required data-validation-required-message="This field is required" 
                                    multiple
                                    >
                                        {{-- <option value="all" {{ old('status') ? in_array('all', old('status')) ? 'selected' : '' : 'selected' }}>All Statuses</option> --}}
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
                            <div class="export-col">
                                <div class="form-group">
                                    <label for=""><strong>Providers</strong></label>
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
                                        <select 
                                        name="provider_ids[]" 
                                        id="provider_ids" 
                                        class="form-control custom-input selectpicker @error('provider_ids') is-invalid @enderror" 
                                        required data-validation-required-message="This field is required" 
                                        title="All providers" 
                                        data-live-search="true" 
                                        data-live-search-placeholder="Search" 
                                        data-actions-box="true" 
                                        data-selected-text-format="count" 
                                        data-count-selected-text="All providers" 
                                        data-container="body" 
                                        data-hide-disabled="true" 
                                        data-virtual-scroll="false"
                                        multiple>
                                            {{-- <option value="all" {{ $selected }}>All providers</option> --}}
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
                            <div class="export-col">
                                <div class="form-group">
                                    <label for=""><strong>Mode</strong></label>
                                    <select name="mode" class="form-control custom-input @error('mode') is-invalid @enderror" required data-validation-required-message="This field is required">
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
                            <div class="export-col">
                                <div class="form-group">
                                    <label for=""><strong>Format</strong></label>
                                    <select name="format" class="form-control custom-input @error('format') is-invalid @enderror" required data-validation-required-message="This field is required">
                                        <option selected>Choose Format</option>
                                        <option {{ old('format') == 'xml' ? 'selected' : '' }} value="xml">XML</option>
                                        <option {{ old('format') == 'json' ? 'selected' : '' }} value="json">JSON</option>
                                        <option {{ old('format') == 'csv' ? 'selected' : 'selected' }} value="csv">CSV</option>
                                    </select>
                                    @error('format')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="export-col">
                                <button type="submit" style="position: relative;top: 29px;" class="btn btn-success theme-color custom-button"> <i class="fa fa-check"></i> Create file</button>
                            </div>
                        </div>
                        <button type="button" class="btn btn-link" data-toggle="modal" data-target="#customizeColumns">Customize columns</button>
                    </form>
                </div>
                <div class="card card-body custom-card-body">
                    <div class="table-responsive">
                        <table id="zero_config" class="table">
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
                                <td>
                                    @php
                                        $users_unserialized = unserialize($item->user_ids);
                                    @endphp
                                    @if ($users_unserialized != null)
                                        {{ rtrim(implode(', ', array_map(function ($value) { return ucfirst($value); }, unserialize($item->user_ids)))) }}
                                    @else
                                       All
                                    @endif
                                   
                                </td>
                                <td>
                                    @php
                                        $service_unserialized = unserialize($item->service_ids);
                                    @endphp
                                    @if ($service_unserialized!=null)
                                        {{ rtrim(implode(', ', array_map(function ($value) { return ucfirst($value); },  $service_unserialized))) }}
                                    @else
                                        All
                                    @endif
                                    
                                </td>
                                <td>{{ rtrim(implode(', ', array_map(function ($value) { return ucfirst($value); }, unserialize($item->status)))) }}</td>
                                <td>{{ rtrim(implode(', ', array_map(function ($value) { return ucfirst($value); }, unserialize($item->provider_ids)))) }}</td>
                                <td>{{ ucfirst($item->mode) }}</td>
                                <td>{{ strtoupper($item->format) }}</td>
                                <td>
                                    <form method="post" action="{{ route('admin.exported_orders.download', $item->id) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-success theme-color custom-button ">Download</button>
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
@endsection

@section('scripts')
    <script>

        $('input[name="daterange"]').daterangepicker({
            opens: 'left',
            startDate: moment().startOf('hour'),
            endDate: moment().startOf('hour').add(32, 'hour'),
        }, function(start, end, label) {
            console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
        });
        $(document).ready(function () {
            $('.datepicker').datepicker();
            $('.input-daterange input').each(function() {
                $(this).datepicker('clearDates');
            });

           

            var service_ids = $('#service_ids').selectpicker();
                service_ids.selectpicker('selectAll');
                $('#service_ids').on('change', function (e) {
                    pickerChange('#service_ids', 'Services');
                });

                var status = $('#status').selectpicker();
                    status.selectpicker('selectAll');
                    $('#status').on('change', function (e) {
                        pickerChange('#status', 'Statuses');
                    });

                var provider_ids = $('#provider_ids').selectpicker();
                    provider_ids.selectpicker('selectAll');
                    $('#provider_ids').on('change', function (e) {
                        pickerChange('#provider_ids', 'Providers');
                    });

                    var options = {
                        ajax: {
                            url: "{{ route('admin.search.user.keyword') }}",
                            type: "POST",
                            dataType: "json",
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: {
                                q: "@{{{q}}}"
                            }
                        },
                        locale: {
                            emptyTitle: "Select and Begin Typing"
                        },
                        log: 3,
                        preprocessData: function(data) {
                            var i,
                                l = data.length,
                                array = [];
                            if (l) {
                                for (i = 0; i < l; i++) {
                                    array.push(
                                        $.extend(true, data[i], {
                                            text: data[i].username,
                                            value: data[i].id,
                                        })
                                    );
                                }
                            }
                            return array;
                        }
                    };
                    var user = $('#user_ids').selectpicker().filter(".with-ajax").ajaxSelectPicker(options);
                    $('#user_ids').on('change', function (e) {
                        pickerChange('#user_ids', 'Users');
                    });

                    function pickerChange(id, txt) {
                        var count = $(id+' option').length;
                        var selected = $(id+' option:selected').length;

                        if (selected === count) {
                            // let input =   $('<input>', {
                            //     name: id+'_name',
                            //     type: 'hidden',
                            //     value: 'All '+id,
                            // });
                            // $(id).after(input);
                            $(id).selectpicker({ countSelectedText: 'All Selected'});
                        } else {
                            $(id).selectpicker({ countSelectedText: txt+' ({0})'});
                        }
                        $(id).selectpicker('render');
                    }

        });



       
        
       
        
      
    </script>
@endsection
