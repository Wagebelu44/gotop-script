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
                        @for ($i = 1; $i < 32; $i++)
                        <tr>
                            <td>{{ $i }}</td>
                            @for ($j = 1; $j < 13; $j++)
                            <td>{{ isset($tickets[$j][$i])?$tickets[$j][$i]:0 }}</td>
                                @php
                                    $monthData[$j][] = isset($tickets[$j][$i])?$tickets[$j][$i]:0;
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
