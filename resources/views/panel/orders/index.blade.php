@extends('layouts.panel')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="material-card card">
                    <div class="card-body">
                        <div class="row pt-3 pb-3">
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
                                            <input type="hidden" name="status" value="PENDING">
                                            <button type="submit" class="btn btn-link">Pending</button>
                                        </form>
                                    </li>
                                    <li class="list-group-item">
                                        <form action="" method="GET">
                                            <input type="hidden" name="status" value="INPROGRESS">
                                            <button type="submit" class="btn btn-link">In&nbsp;Progress</button>
                                        </form>
                                    </li>
                                    <li class="list-group-item">
                                        <form action="" method="GET">
                                            <input type="hidden" name="status" value="COMPLETED">
                                            <button type="submit" class="btn btn-link">Completed</button>
                                        </form>
                                    </li>
                                    <li class="list-group-item">
                                        <form action="" method="GET">
                                            <input type="hidden" name="status" value="PARTIAL">
                                            <button type="submit" class="btn btn-link">Partial</button>
                                        </form>
                                    </li>
                                    <li class="list-group-item">
                                        <form action="" method="GET">
                                            <input type="hidden" name="status" value="cancelled">
                                            <button type="submit" class="btn btn-link">Cancelled</button>
                                        </form>
                                    </li>
                                    <li class="list-group-item">
                                        <form action="" method="GET">
                                            <input type="hidden" name="status" value="PROCESSING">
                                            <button type="submit" class="btn btn-link">Processing</button>
                                        </form>
                                    </li>
                                    <li class="list-group-item">
                                        <form action="" method="GET">
                                            <input type="hidden" name="status" value="FAILED">
                                            <button type="submit" class="btn btn-link">Failed <span class="badge badge-danger"></span> </button>
                                        </form>
                                    </li>
                                    <li class="list-group-item ">
                                        <form action="" method="GET">
                                            <input type="hidden" name="status" value="ERROR">
                                            <button type="submit" class="btn btn-link">Error</button>
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
                                        <input type="search" name="search" class="form-control" placeholder="Search..." value="">
                                    </div>
                                    <input type="hidden" name="query_service" value="">
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
                        </div><div>
                    </div>
                        @include('panel.orders.table')
                        <div class="row">
                            <div class="col-md-6">

                            </div>
                            <div class="col-md-6 text-right">
                                <span>Record per page</span>
                                <form action="" id="show_per_page" method="get" class="d-inline">
                                    <select name="page_size" id="page_size">
                                        <option value="100">100</option>
                                        <option value="200">200</option>
                                        <option value="500">500</option>
                                        <option value="1000">1000</option>
                                    </select>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
