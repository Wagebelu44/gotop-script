@extends('layouts.panel')

@section('content')
<div class="row all-mt-30">
    @include('panel.affiliate.navbar')

    <div class="col-md-9">
        <div class="card panel-default">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th scope="col">User ID</th>
                            <th>Username</th>
                            <th>Status</th>
                            <th scope="col">Total visits</th>
                            <th scope="col">Unpaid referrals</th>
                            <th scope="col">Paid referrals</th>
                            <th scope="col">Conversion rate</th>
                            <th scope="col">Total earnings</th>
                            <th scope="col">Unpaid earnings</th>
                            <th scope="col"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tbody>
                        @if(!empty($affiliates))
                            @foreach($affiliates as $aff)
                            <tr>
                                <td>{{ $aff->id }}</td>
                                <td>{{ $aff->username }}</td>
                                <td>{{ $aff->affiliate_status }}</td>
                                <td>{{ $aff->total_visits }}</td>
                                <td>{{ $aff->unpaid_referrals }}</td>
                                <td>{{ $aff->paid_referrals }}</td>
                                <td>{{ $aff->conversion_rate }}</td>
                                <td>{{ $aff->total_earnings }}</td>
                                <td>{{ $aff->unpaid_earnings }}</td>
                                <td class="td-caret">
                                    <div class="btn-group">
                                        <button type="button" class="btn dropdown-toggle custom-dropdown-button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            Action
                                        </button>
                                        <div class="dropdown-menu">
                                            @if($aff->affiliate_status == 'Active')
                                                <a class="dropdown-item type-dropdown-item" href="#">Deactive Affiliate</a>
                                            @else
                                                <a class="dropdown-item type-dropdown-item" href="#">Active Affiliate</a>
                                            @endif
                                        </div>
                                    </div>
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
</div>
@endsection
