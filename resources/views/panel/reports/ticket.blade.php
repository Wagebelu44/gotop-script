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
                    <div class="col-md-8">
                        @include('panel.reports.menu')
                    </div>
                    <div class="col-md-3 text-right">
                        <a class="btn btn-primary" style="{{ !request()->query('status') || request()->query('status') == 1 ? 'background-color: #707CB8' : '' }}" href="{{ url('admin/reports/tickets') }}?year={{ request()->query('year') ?? date('Y') }}&status=1">New tickets</a>
                        <a class="btn btn-primary" style="{{ request()->query('status') == 4 ? 'background-color: #707CB8' : '' }}" href="{{ url('admin/reports/tickets') }}?year={{ request()->query('year') ?? date('Y') }}&status=4">User messages</a>
                        <a class="btn btn-primary" style="{{ request()->query('status') == 3 ? 'background-color: #707CB8' : '' }}" href="{{ url('admin/reports/tickets') }}?year={{ request()->query('year') ?? date('Y') }}&status=3">Staff replies</a>
                    </div>
                    <div class="col-md-1">
                        <div class="input-group">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{ request()->query('year') ?? date('Y') }}</button>
                                <div class="dropdown-menu" style="max-height: 200px; overflow-y: scroll">
                                    @for($i = 2020; $i <= date('Y'); $i++)
                                        <a class="dropdown-item" href="{{ url('admin/reports/tickets') }}?year={{ $i }}&status={{ request()->query('status') ?? 1 }}">{{ $i }}</a>
                                    @endfor
                                </div>
                            </div>
                        </div>
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
                        @foreach($tickets as $ticket)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $ticket[1] }}</td>
                            <td>{{ $ticket[2] }}</td>
                            <td>{{ $ticket[3] }}</td>
                            <td>{{ $ticket[4] }}</td>
                            <td>{{ $ticket[5] }}</td>
                            <td>{{ $ticket[6] }}</td>
                            <td>{{ $ticket[7] }}</td>
                            <td>{{ $ticket[8] }}</td>
                            <td>{{ $ticket[9] }}</td>
                            <td>{{ $ticket[10] }}</td>
                            <td>{{ $ticket[11] }}</td>
                            <td>{{ $ticket[12] }}</td>
                        </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                            <th>Total</th>
                            <th>{{ $tickets->sum('1') }}</th>
                            <th>{{ $tickets->sum('2') }}</th>
                            <th>{{ $tickets->sum('3') }}</th>
                            <th>{{ $tickets->sum('4') }}</th>
                            <th>{{ $tickets->sum('5') }}</th>
                            <th>{{ $tickets->sum('6') }}</th>
                            <th>{{ $tickets->sum('7') }}</th>
                            <th>{{ $tickets->sum('8') }}</th>
                            <th>{{ $tickets->sum('9') }}</th>
                            <th>{{ $tickets->sum('10') }}</th>
                            <th>{{ $tickets->sum('11') }}</th>
                            <th>{{ $tickets->sum('12') }}</th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
