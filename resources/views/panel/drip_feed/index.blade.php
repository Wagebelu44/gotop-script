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
                                        <button type="button" @click="filterStatus('all')" class="btn btn-link">All</button>
                                    </li>
                                    <li class="list-group-item">
                                        <button type="button"  @click="filterStatus('ACTIVE')" class="btn btn-link">Active</button>
                                    </li>
                                    <li class="list-group-item">
                                        <button type="button"  @click="filterStatus('COMPLETED')" class="btn btn-link">COMPLETED</button>
                                    </li>
                                    <li class="list-group-item">
                                        <button type="button"  @click="filterStatus('CANCELLED')" class="btn btn-link">CANCELLED</button>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <form class="d-flex pull-right" method="get" action="">
                                    <div>
                                  {{--       <a href="" class="btn btn-link">Export</a> --}}
                                    </div>
                                    <div class="form-group mb-2 mr-0">
                                        <input type="search" name="search" v-model="filter.filter_type.data" class="form-control" placeholder="search...">
                                    </div>
                                    <div class="form-group mb-2 ml-0">
                                        <select name="filter_type" id="filter_type"  v-model="filter.filter_type.type" class="form-control">
                                            <option value="order_id">Order ID</option>
                                            <option value="link">Link</option>
                                            <option value="username">Username</option>
                                            <option value="service_id">Service ID</option>
                                            <option value="null">External ID</option>
                                            <option value="null">Provider</option>
                                        </select>
                                    </div>
                                    <button type="button" @click="filterType" class="btn btn-default mb-2" style="border:1px solid #eeeff0;"> <i class="fa fa-search" aria-hidden="true"></i> </button>
                                </form>
                            </div>
                        </div>
                        <div class="overlay-loader" v-if="loader">
                            <div class="loader-holder">
                                <img src="{{asset('loader.gif')}}" alt="">
                            </div>
                        </div>
                        <div class="table-responsive">
                            @include('panel.drip_feed.table')

                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <data-pagination v-if="pagination.last_page > 1" :pagination="pagination" :offset="5" @paginate="getDripFeedOrders()"></data-pagination>
                                </div>
                            </div>
                        </div>
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
                        <button type="button" class="btn btn-default" @click="yes" >Yes</button>
                        <button type="button" class="btn btn-primary"  @click="no">No</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
<script src="{{asset('/panel-assets/vue-scripts/common/pagination.js')}}"></script>
<script src="{{asset('/panel-assets/vue-scripts/drip-feed-vue.js?var=0.1')}}"></script>
<script>
    
    console.log('hello world');

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
    }, 5000);
</script>
@endsection
