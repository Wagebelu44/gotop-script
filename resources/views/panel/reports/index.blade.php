@extends('layouts.panel')

@section('content')
    <div class="container-fluid all-mt-30">
        <div class="row">
            <div class="col-12">
                <div class="material-card card">
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-5">
                                @include('panel.reports.menu')
                            </div>
                            <div class="col-md-2 text-right">
                                <a class="btn btn-primary" style="{{ !request()->query('show') || request()->query('show') == 'amount' ? 'background-color: #707CB8' : '' }}" href="javascript:void(0)">Total amount</a>
                                <a class="btn btn-primary" style="{{ request()->query('show') == 'count' ? 'background-color: #707CB8' : '' }}" href="javascript:void(0)">Total count</a>
                            </div>
                            <div class="col-md-5">
                                <form id="search-form" method="get" novalidate>

                                    <input type="hidden" name="show" value="">

                                    <div class="row">
                                        <div class="col">
                                            <div class="form-group">
                                                <div class="controls">
                                                    <select name="year" class="form-control" required data-validation-required-message="This field is required">
                                                        <option value="all">Year</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-group">
                                                <div class="controls">
                                                    <select name="user_ids[]" id="user_ids" class="form-control" required data-validation-required-message="This field is required">
                                                        <option value="all">All users</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-group">
                                                <div class="controls">
                                                    <select name="payment_method_id[]" id="payment_method_id" class="form-control" required data-validation-required-message="This field is required">
                                                        <option value="all">All method</option>
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
                                    @foreach($payments as $payment)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>${{ $payment[1] }}</td>
                                        <td>${{ $payment[2] }}</td>
                                        <td>${{ $payment[3] }}</td>
                                        <td>${{ $payment[4] }}</td>
                                        <td>${{ $payment[5] }}</td>
                                        <td>${{ $payment[6] }}</td>
                                        <td>${{ $payment[7] }}</td>
                                        <td>${{ $payment[8] }}</td>
                                        <td>${{ $payment[9] }}</td>
                                        <td>${{ $payment[10] }}</td>
                                        <td>${{ $payment[11] }}</td>
                                        <td>${{ $payment[12] }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>Total</th>
                                        <th>${{ $payments->sum('1') }}</th>
                                        <th>${{ $payments->sum('2') }}</th>
                                        <th>${{ $payments->sum('3') }}</th>
                                        <th>${{ $payments->sum('4') }}</th>
                                        <th>${{ $payments->sum('5') }}</th>
                                        <th>${{ $payments->sum('6') }}</th>
                                        <th>${{ $payments->sum('7') }}</th>
                                        <th>${{ $payments->sum('8') }}</th>
                                        <th>${{ $payments->sum('9') }}</th>
                                        <th>${{ $payments->sum('10') }}</th>
                                        <th>${{ $payments->sum('11') }}</th>
                                        <th>${{ $payments->sum('12') }}</th>
                                    </tr>
                                    </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
