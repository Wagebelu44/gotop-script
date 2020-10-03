@extends('layouts.panel')

@section('content')
<div class="row all-mt-30">
    @include('panel.affiliate.navbar')

    <div class="col-md-9">
        <div class="card panel-default">
            <div class="card-body">
                <div class="row pb-3 pt-3">
                    <div class="col-md-6">
                        <h3>Referrals</h3>
                    </div>
                    <div class="col-md-6">
                        <form class="d-flex pull-right" method="get" action="{{ route('admin.affiliates.referrals') }}">
                            <div class="form-group mb-2 mr-0">
                                <input type="search" name="q" value="{{ Request::get('q') }}" class="form-control" placeholder="search...">
                            </div>

                            <button type="submit" class="btn btn-default mb-2" style="border:1px solid #eeeff0;"> <i class="fa fa-search" aria-hidden="true"></i> </button>
                        </form>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">User ID</th>
                                <th>Username</th>
                                <th>Referral</th>
                                <th>Payments</th>
                                <th>Commission</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(!empty($referrals))
                                @foreach($referrals as $ref)
                                <tr>
                                    <td>{{ $ref->user_id }}</td>
                                    <td>{{ $ref->user->username }}</td>
                                    <td>{{ $ref->referral->username }}</td>
                                    <td>${{ $ref->payments }}</td>
                                    <td>${{ $ref->commissions }}</td>
                                </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>

                {{ $referrals->appends(Request::except('page'))->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
