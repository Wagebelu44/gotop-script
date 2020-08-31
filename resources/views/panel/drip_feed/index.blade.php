@extends('layouts.panel')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="material-card card">
                    <div class="card-body">
                        <div class="row pb-3 pt-3">
                            <div class="col-md-6">
                                <ul class="list-group d-flex flex-row reseller_order_filter_lists">
                                    <li class="list-group-item">
                                        <form action="" method="GET">
                                            <input type="hidden" name="status" value="all">
                                            <button type="submit" class="btn btn-link">All</button>
                                        </form>
                                    </li>
                                    <li class="list-group-item">
                                        <form action="" method="GET">
                                            <input type="hidden" name="status" value="ACTIVE">
                                            <button type="submit" class="btn btn-link">Active</button>
                                        </form>
                                    </li>
                                    <li class="list-group-item">
                                        <form action="" method="GET">
                                            <input type="hidden" name="status" value="COMPLETED">
                                            <button type="submit" class="btn btn-link">COMPLETED</button>
                                        </form>
                                    </li>
                                    <li class="list-group-item">
                                        <form action="" method="GET">
                                            <input type="hidden" name="status" value="CANCELLED">
                                            <button type="submit" class="btn btn-link">CANCELLED</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <form class="d-flex pull-right" method="get" action="">
                                    <div>
                                        <a href="" class="btn btn-link">Export</a>
                                    </div>
                                    <div class="form-group mb-2 mr-0">
                                        <input type="search" name="search" class="form-control" placeholder="search...">
                                    </div>
                                    <div class="form-group mb-2 ml-0">
                                        <select name="filter_type" id="filter_type" class="form-control">
                                            <option value="order_id">Order ID</option>
                                            <option value="link">Link</option>
                                            <option value="username">Username</option>
                                            <option value="service_id">Service ID</option>
                                            <option value="null">External ID</option>
                                            <option value="null">Provider</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-default mb-2" style="border:1px solid #eeeff0;"> <i class="fa fa-search" aria-hidden="true"></i> </button>
                                </form>
                            </div>
                        </div>
                        <div class="table-responsive">
                            @include('panel.drip_feed.table')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
