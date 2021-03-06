@extends('layouts.panel')

@section('content')
<style>
    .dropdown-submenu {
        position: relative;
    }

    .dropdown-submenu a::after {
        transform: rotate(90deg);
        position: absolute;
        left: 0;
        top: 10px;
    }
    .dropdown-submenu.top-one a::after {
        position: absolute;
        right: 0px;
        top: 15px;
        width: 10px; 
        height: 10px;
        transform: translate(120px, -7px) rotate(-90deg);
    }

    .dropdown-submenu .dropdown-menu {
        top: 0;
        left: -100%;
        margin-left: .1rem;
        margin-right: .1rem;
    }
    .unseenOrder{
        border: 1px solid green !important;
        background: #0080000f;
    }
</style>
    <div class="all-mt-30" id="order_module">
        <div class="card-body padding-less">
            <div class="row pt-3 pb-3">
                <div class="col-md-8">
                    <ul class="list-group d-flex flex-row reseller_order_filter_lists">
                        <li class="list-group-item script-active">
                            <button type="button" @click="filterStatus('all')"  class="btn btn-link">All</button>
                        </li>
                        <li class="list-group-item">
                            <button type="button" @click="filterStatus('AWAITING')" class="btn btn-link d-flex"> 
                                <span>Awaiting </span> 
                                {{-- <span class="badge badge-danger ml-1">0</span> --}}
                            </button>
                        </li>
                        <li class="list-group-item">
                            <button type="button" @click="filterStatus('PENDING')" class="btn btn-link">Pending</button>
                        </li>
                        <li class="list-group-item">
                            <button type="button" @click="filterStatus('INPROGRESS')" class="btn btn-link">In&nbsp;Progress</button>
                        </li>
                        <li class="list-group-item">
                            <button type="button" @click="filterStatus('COMPLETED')" class="btn btn-link">Completed</button>
                        </li>
                        <li class="list-group-item">
                            <button type="button" @click="filterStatus('PARTIAL')" class="btn btn-link">Partial</button>
                        </li>
                        <li class="list-group-item">
                            <button type="button" @click="filterStatus('cancelled')" class="btn btn-link">Cancelled</button>
                        </li>
                        <li class="list-group-item">
                            <button type="button" @click="filterStatus('PROCESSING')" class="btn btn-link">Processing</button>
                        </li>
                        <li class="list-group-item">
                            <button type="button" @click="filterStatus('FAILED')" class="btn btn-link d-flex"> <span>Failed </span> <span class="badge badge-danger ml-1">{{ $failOrdercount }}</span> </button>
                        </li>
                        <li class="list-group-item ">
                            <button type="button" @click="filterStatus('ERROR')" class="btn btn-link">Error</button>
                        </li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <div class="d-flex pull-right">
                        <div>
                            @can('export order')
                            <a href="{{ route('admin.exported_orders.index') }}" class="btn btn-link">Export</a>
                        @endcan
                        </div>
                        <div class="form-group mb-2 mr-0">
                            <input type="search" name="search" v-model="filter.filter_type.data" class="form-control" placeholder="Search..." value="">
                        </div>
                        <input type="hidden" name="query_service" value="">
                        <div class="form-group mb-2 ml-0">
                            <select name="filter_type" v-model="filter.filter_type.type" class="form-control">
                                <option value="order_id">Order ID</option>
                                <option value="link">Link</option>
                                <option value="username">Username</option>
                                <option value="service_id">Service ID</option>
                                @can('see order external id')
                                    <option value="null">External ID</option>
                                @endcan
                                <option value="null">Provider</option>
                            </select>
                        </div>
                        <button type="button" @click="filterType" class="btn btn-default mb-2" style="border:1px solid #eeeff0;"> <i class="fa fa-search" aria-hidden="true"></i> </button>
                    </div>
                </div>
            </div>
            <div class="overlay-loader" v-if="loader">
                <div class="loader-holder">
                    <img src="{{asset('loader.gif')}}" alt="">
                </div>
            </div>
            @include('panel.orders.table')
            <div class="row">
                <div class="col-md-12 text-center">
                    <data-pagination v-if="pagination.last_page > 1" :pagination="pagination" :offset="5" @paginate="getOrders()"></data-pagination>
                </div>
            </div>
        </div>

        <div class="modal fade" id="orderEdit-modal" tabindex="-1" role="dialog"
        aria-labelledby="orderEdit-modalTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered __modal_dialog_custom" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Update Order</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="post"
                            id="formDescription"  >
                            <input type="hidden" name="id" >
                            <div class="row">
                                <div class="col-md-12" id="link_id" v-if="visiblelink">
                                    <div class="form-group">
                                        <label for="link">Link</label>
                                        <input type="url" class="form-control" name="link">
                                    </div>
                                </div>
                                <div class="col-md-12" id="start_count_id" v-if="visibleStartCount">
                                    <div class="form-group">
                                        <label for="start_counter">Set Start Count</label>
                                        <input type="number" class="form-control" name="start_counter" >
                                    </div>
                                </div>
                                <div class="col-md-12" id="remain_id" v-if="visibleRemain">
                                    <div class="form-group">
                                        <label for="remains">Remain</label>
                                        <input type="number" class="form-control" name="remains" >
                                    </div>
                                </div>
                                <div class="col-md-12" id="partial_id" v-if="visiblePartical">
                                    <div class="form-group">
                                        <label for="partial">Partials</label>
                                        <input type="number" class="form-control" name="partial" >
                                    </div>
                                </div>
                            </div>
                            <div class="form-actions">
                                <button type="button" @click="update_service"  class="btn btn-success"><i class="fa fa-check"></i> update</button>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" id="mi-modal">
            <div class="modal-dialog modal-sm modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel">Confirm</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" @click="yes()">Yes</button>
                        <button type="button" class="btn btn-primary"  @click="no()">No</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" id="order_service_type_detail">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        Order Detail 
                    </div>
                    <div class="modal-body">
                        <p id="order-modal-detail" v-if="single_order" style="white-space: break-spaces;">
                            <span v-html="single_order.auto_order_response"></span>
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
@endsection
@section('scripts')
<script src="{{asset('/panel-assets/vue-scripts/common/pagination.js')}}"></script>
<script src="{{asset('/panel-assets/vue-scripts/common/helper-mixin.js')}}"></script>
<script>
    let adminSeenRoute  = '{{ route("admin.order-seen") }}';
    let resendOrderRoute  = '{{ route("admin.resend.order") }}';
</script>
<script src="{{asset('/panel-assets/vue-scripts/order-vue.js?var=0.10')}}"></script>
<script>
    setTimeout(function () {
    $('.dropdown-menu a.dropdown-toggle').on('click', function(e) {
       if (!$(this).next().hasClass('show')) {
           $(this).parents('.dropdown-menu').first().find('.show').removeClass("show");
       }
       //alert('dafs');
       var $subMenu = $(this).next(".dropdown-menu");
       $subMenu.toggleClass('show');


       $(this).parents('li.nav-item.dropdown.show').on('hidden.bs.dropdown', function(e) {
           $('.dropdown-submenu .show').removeClass("show");
       });

       return false;
   });
    }, 2000);

    

    function service_type_modal()
    {
        $("#order_service_type_detail").modal('hide');
    }
    $("#order_service_type_detail").on('hidden.bs.modal', function() {
        console.log('asdfasd');
        orderModule.single_order = null;
    })

    $('#orderEdit-modal').on('hidden.bs.modal', function () {
        orderModule.visiblelink = false;
        orderModule.visibleStartCount = false;
        orderModule.visiblePartical = false;
        orderModule.visibleRemain = false;
    });
</script>
@endsection
