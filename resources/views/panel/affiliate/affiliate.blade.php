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
                            <th>Affiliate</th>
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
                        @if(!empty($data))
                            @foreach($data as $key => $child)
                            <tr>
                                <td>{{ $child->domain }}</td>
                                <td>{{ $child->user->name }}</td>
                                <td>{{ $child->status }}</td>
                                <td><span class="nowrap">{{ $child->created_at }}</span></td>
                                <td><span class="nowrap"></span></td>
                                <td class="td-caret">
                                    @if($child->status == 'Pending')
                                    <div class="btn-group">
                                        <button type="button" class="btn dropdown-toggle custom-dropdown-button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            Action
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item type-dropdown-item" href="{{ route('admin.child-panels.cancelRefund', $child->id) }}">Cancel and refund</a>
                                        </div>
                                    </div>
                                    @endif
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
