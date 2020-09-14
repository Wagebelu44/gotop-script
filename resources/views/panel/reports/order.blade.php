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
                <div class="row mb-4">
                    <div class="col-md-3">
                        @include('panel.reports.menu')
                    </div>
                    <div class="col-md-3 text-right">
                        <a class="btn btn-primary" style="{{ !request()->query('show') || request()->query('show') == 'count' ? 'background-color: #707CB8' : '' }}" href="javascript:void(0)" onclick="$('#search-form').find('input[name=show]').val('count');$('#search-form').submit()">Total orders</a>
                        <a class="btn btn-primary" style="{{ request()->query('show') == 'charge' ? 'background-color: #707CB8' : '' }}" href="javascript:void(0)" onclick="$('#search-form').find('input[name=show]').val('charge');$('#search-form').submit()">Total charges</a>
                        <a class="btn btn-primary" style="{{ request()->query('show') == 'quantity' ? 'background-color: #707CB8' : '' }}" href="javascript:void(0)" onclick="$('#search-form').find('input[name=show]').val('quantity');$('#search-form').submit()">Total quantity</a>
                    </div>
                    <div class="col-md-6">
                        <form id="search-form" method="get" novalidate>

                            <input type="hidden" name="show" value="{{ request()->query('show') }}">

                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <div class="controls">
                                            <select name="year" class="form-control" required data-validation-required-message="This field is required">
                                                @for($i = 2020; $i <= date('Y'); $i++)
                                                <option {{ $i == request()->query('year') ? 'selected' : '' }} value="{{ $i }}">{{ $i }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <div class="controls">
                                            
                                            {{-- <select name="user_ids[]" id="user_ids" class="form-control" required data-validation-required-message="This field is required" multiple>
                                                <option value="all" {{ ( request()->query('user_ids') && in_array('all', request()->query('user_ids')) ) ? 'selected' : '' }}>All users</option>
                                                @foreach(auth()->guard('reseller')->user()->users as $user)
                                                    <option value="{{ $user->id }}" {{ ( request()->query('user_ids') ? in_array($user->id, request()->query('user_ids')) : false) ? 'selected' : '' }}>{{ $user->name }}</option>
                                                @endforeach
                                            </select> --}}
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <div class="controls">
                                            {{-- <select name="service_id[]" id="service_id" class="form-control" required data-validation-required-message="This field is required" multiple>
                                                <option value="all" {{ ( request()->query('service_id') && in_array('all', request()->query('service_id')) ) ? 'selected' : ''  }}>All service</option>
                                                @foreach(auth()->guard('reseller')->user()->services as $service)
                                                    <option value="{{ $service->id }}" {{ ( request()->query('service_id') ? in_array($service->id, request()->query('service_id')) : false ) ? 'selected' : '' }}>{{ $service->name }}</option>
                                                @endforeach
                                            </select> --}}
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <div class="controls">
                                            <select name="status[]" id="status" class="form-control" required data-validation-required-message="This field is required" multiple>
                                                <option value="all" {{ ( request()->query('status') && in_array('all', request()->query('status')) ) ? 'selected' : '' }}>All status</option>
                                                <option value="PENDING" {{ ( request()->query('status') ? in_array('PENDING', request()->query('status')) : false ) ? 'selected' : '' }}>Pending</option>
                                                <option value="INPROGRESS" {{ ( request()->query('status') ? in_array('INPROGRESS', request()->query('status')) : false ) ? 'selected' : '' }}>In progress</option>
                                                <option value="COMPLETED" {{ ( request()->query('status') ? in_array('COMPLETED', request()->query('status')) : false ) ? 'selected' : '' }}>Completed</option>
                                                <option value="PARTIAL" {{ ( request()->query('status') ? in_array('PARTIAL', request()->query('status')) : false ) ? 'selected' : '' }}>Partial</option>
                                                <option value="CANCELLED" {{ ( request()->query('status') ? in_array('CANCELLED', request()->query('status')) : false ) ? 'selected' : '' }}>Canceled</option>
                                                <option value="PROCESSING" {{ ( request()->query('status') ? in_array('PROCESSING', request()->query('status')) : false )? 'selected' : '' }}>Processing</option>
                                                <option value="REFUNDED" {{ ( request()->query('status') ? in_array('REFUNDED', request()->query('status')) : false ) ? 'selected' : '' }}>Refunded</option>
                                                <option value="REFILLING" {{ ( request()->query('status') ? in_array('REFILLING', request()->query('status')) : false ) ? 'selected' : '' }}>Refilling</option>
                                                <option value="CANCELLING" {{ ( request()->query('status') ? in_array('CANCELLING', request()->query('status')) : false ) ? 'selected' : '' }}>Cancelling</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-actions">
                                        <button type="submit" class="btn btn-success"> <i class="fa fa-check"></i> Submit</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped border table-hover">
                        <thead>
                        <tr>
                            <th></th>
                            <th>January</th>
                            <th>February</th>
                            <th>March</th>
                            <th>April</th>
                            <th>May</th>
                            <th>June</th>
                            <th>July</th>
                            <th>August</th>
                            <th>September</th>
                            <th>October</th>
                            <th>November</th>
                            <th>December</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($orders as $order)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $order[1] }}</td>
                            <td>{{ $order[2] }}</td>
                            <td>{{ $order[3] }}</td>
                            <td>{{ $order[4] }}</td>
                            <td>{{ $order[5] }}</td>
                            <td>{{ $order[6] }}</td>
                            <td>{{ $order[7] }}</td>
                            <td>{{ $order[8] }}</td>
                            <td>{{ $order[9] }}</td>
                            <td>{{ $order[10] }}</td>
                            <td>{{ $order[11] }}</td>
                            <td>{{ $order[12] }}</td>
                        </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                            <th>Total</th>
                            <th>{{ $orders->sum('1') }}</th>
                            <th>{{ $orders->sum('2') }}</th>
                            <th>{{ $orders->sum('3') }}</th>
                            <th>{{ $orders->sum('4') }}</th>
                            <th>{{ $orders->sum('5') }}</th>
                            <th>{{ $orders->sum('6') }}</th>
                            <th>{{ $orders->sum('7') }}</th>
                            <th>{{ $orders->sum('8') }}</th>
                            <th>{{ $orders->sum('9') }}</th>
                            <th>{{ $orders->sum('10') }}</th>
                            <th>{{ $orders->sum('11') }}</th>
                            <th>{{ $orders->sum('12') }}</th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
    <script>
        let service_id = $("#service_id");
        let user_ids = $("#user_ids");
        let status = $("#status");

        service_id.select2({
            width: '100%',
            allowClear: true,
            placeholder: 'Select service_id'
        });
        user_ids.select2({
            width: '100%',
            allowClear: true,
            placeholder: 'Select user'
        });
        status.select2({
            width: '100%',
            allowClear: true,
            placeholder: 'Select status'
        });

        service_id.on('select2:select', function (e) {
            toggleSelected (e, service_id);
        });
        user_ids.on('select2:select', function (e) {
            toggleSelected (e, user_ids);
        });
        status.on('select2:select', function (e) {
            toggleSelected (e, status);
        });

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

