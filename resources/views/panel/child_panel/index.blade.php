@extends('layouts.panel')

@section('content')
    <div class="container-fluid all-mt-30" id="drip_feed_module">
        <div class="row">
            <div class="col-12">
                <div class="material-card card">
                    <div class="card-body">
                        <div class="row pb-3 pt-3">
                            <div class="col-md-6">
                                <ul class="list-group d-flex flex-row reseller_order_filter_lists">
                                    <li class="list-group-item">
                                        <a href="{{ route('admin.child-panels') }}" type="button" class="btn btn-link {{ $status == 'All' ? 'active':'' }}">All</a>
                                    </li>
                                    <li class="list-group-item">
                                        <a href="{{ route('admin.child-panels', ['status' => 'Pending']) }}" type="button" class=" btn btn-link {{ $status == 'Pending' ? 'active':'' }}">Pending</a>
                                    </li>
                                    <li class="list-group-item">
                                        <a href="{{ route('admin.child-panels', ['status' => 'Active']) }}" type="button" class="btn btn-link {{ $status == 'Active' ? 'active':'' }}">Active</a>
                                    </li>
                                    <li class="list-group-item">
                                        <a href="{{ route('admin.child-panels', ['status' => 'Frozen']) }}" type="button"  class="btn btn-link {{ $status == 'Frozen' ? 'active':'' }}">Frozen</a>
                                    </li>
                                    <li class="list-group-item">
                                        <a href="{{ route('admin.child-panels', ['status' => 'Terminated']) }}" type="button"  class="btn btn-link {{ $status == 'Terminated' ? 'active':'' }}">Terminated</a>
                                    </li>
                                    <li class="list-group-item">
                                        <a href="{{ route('admin.child-panels', ['status' => 'Canceled']) }}" type="button"  class="btn btn-link {{ $status == 'Canceled' ? 'active':'' }}">Canceled</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <form class="d-flex pull-right" method="get" action="{{ route('admin.child-panels') }}">
                                    <div class="form-group mb-2 mr-0">
                                        <input type="search" name="search" class="form-control" placeholder="search...">
                                    </div>

                                    <button type="submit" class="btn btn-default mb-2" style="border:1px solid #eeeff0;"> <i class="fa fa-search" aria-hidden="true"></i> </button>
                                </form>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th scope="col">Domain</th>
                                    <th>User</th>
                                    <th>Status</th>
                                    <th scope="col">Created</th>
                                    <th scope="col">Expiry</th>
                                    <th scope="col">Action</th>
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

    </div>
@endsection
@section('scripts')

@endsection
