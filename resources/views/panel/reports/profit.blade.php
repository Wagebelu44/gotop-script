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
                    <div class="col-md-6">
                        @include('panel.reports.menu')
                    </div>
                    <div class="col-md-6">
                        <form id="search-form" method="get" novalidate>
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
                                            {{-- <select name="service_id[]" id="service_id" class="form-control" required data-validation-required-message="This field is required" multiple>
                                                <option value="all" {{ ( request()->query('service_id') && in_array('all', request()->query('service_id')) ) ? 'selected' : '' }}>All service</option>
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
                                            {{-- <select name="status[]" id="status" class="form-control" required data-validation-required-message="This field is required" multiple>
                                                <option value="all" {{ ( request()->query('status') && in_array('all', request()->query('status')) ) ? 'selected' : ''}}>All status</option>
                                                <option value="PENDING" {{ ( request()->query('status') ? in_array('PENDING', request()->query('status')) : false ) ?  'selected' : '' }}>Pending</option>
                                                <option value="INPROGRESS" {{ ( request()->query('status') ? in_array('INPROGRESS', request()->query('status')) : false ) ? 'selected' : '' }}>In progress</option>
                                                <option value="COMPLETED" {{ ( request()->query('status') ? in_array('COMPLETED', request()->query('status')) : false ) ? 'selected' : '' }}>Completed</option>
                                                <option value="PARTIAL" {{ ( request()->query('status') ? in_array('PARTIAL', request()->query('status')) : false ) ? 'selected' : '' }}>Partial</option>
                                                <option value="CANCELLED" {{ ( request()->query('status') ? in_array('CANCELLED', request()->query('status')) : false ) ? 'selected' : '' }}>Canceled</option>
                                                <option value="PROCESSING" {{ ( request()->query('status') ? in_array('PROCESSING', request()->query('status')) : false ) ? 'selected' : '' }}>Processing</option>
                                                <option value="REFUNDED" {{ ( request()->query('status') ? in_array('REFUNDED', request()->query('status')) : false ) ? 'selected' : '' }}>Refunded</option>
                                                <option value="REFILLING" {{ ( request()->query('status') ? in_array('REFILLING', request()->query('status')) : false ) ? 'selected' : '' }}>Refilling</option>
                                                <option value="CANCELLING" {{ ( request()->query('status') ? in_array('CANCELLING', request()->query('status')) : false ) ? 'selected' : '' }}>Cancelling</option>
                                            </select> --}}
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
                            @for ($i = 1; $i < 32; $i++)
                                <tr>
                                    <td>{{ $i }}</td>
                                    @for ($j = 1; $j < 13; $j++)
                                    <td>{{ isset($profits[$j][$i])?$profits[$j][$i]:'' }}</td>
                                        @php
                                            $monthData[$j][] = isset($profits[$j][$i])?$profits[$j][$i]:'';
                                        @endphp
                                    @endfor
                                </tr>
                            @endfor
                        </tbody>
                        <tfoot>
                        <tr>
                            <th>Total</th>
                            @foreach ($monthData as $mon)
                                <th>{{ array_sum($mon) }}</th>
                            @endforeach
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
        let status = $("#status");

        service_id.select2({
            width: '100%',
            allowClear: true,
            placeholder: 'Select service_id'
        });
        status.select2({
            width: '100%',
            allowClear: true,
            placeholder: 'Select status'
        });

        service_id.on('select2:select', function (e) {
            toggleSelected (e, service_id);
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

