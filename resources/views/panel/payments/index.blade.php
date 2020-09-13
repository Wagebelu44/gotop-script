@extends('layouts.panel')

@section('content')
    <div class="container-fluid all-mt-30" id="payment_module">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row pb-3 pt-3">
                            <div class="col-md-6">
                                <button class="btn btn-outline-secondary" data-toggle="modal" data-target="#paymentAddModal">Add Payment</button>
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
                                        <select name="keyword" id="keyword" class="form-control">
                                            <option value="user">User</option>
                                            <option value="memo">Memo</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-default mb-2" style="border:1px solid #eeeff0;">
                                        <i class="fa fa-search" aria-hidden="true"></i>
                                    </button>
                                </form>
                            </div>
                            <form class="text-right" id="search-form"></form>
                        </div>
                        <div class="table-responsive">
                            <table class="table dataTable">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>User</th>
                                    <th>Amount</th>
                                    <th>
                                        <div class="input-group">
                                            <div class="input-group-append">
                                                <button class="btn  custom-dropdown-button dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Method</button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item type-dropdown-item" href="">All</a>
                                                        <a class="dropdown-item type-dropdown-item" href="javascript:void(0)"></a>
                                                    <a class="dropdown-item type-dropdown-item"
                                                       href="javascript:void(0)" > Bonus </a>
                                                </div>
                                            </div>
                                        </div>
                                    </th>
                                    <th>Memo</th>
                                    <th>Created</th>
                                    <th>Updated</th>
                                    <th>
                                        <div class="input-group">
                                            <div class="input-group-append">
                                                <button class="btn  custom-dropdown-button dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Mode</button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item type-dropdown-item" href="">All</a>
                                                    <a class="dropdown-item type-dropdown-item" href="javascript:void(0)" onclick="searchPayment('transaction_flag', 'admin_panel')">Manual</a>
                                                    <a class="dropdown-item type-dropdown-item" href="javascript:void(0)" onclick="searchPayment('transaction_flag', 'payment_gateway')">Auto</a>
                                                    <a class="dropdown-item type-dropdown-item" href="javascript:void(0)" onclick="searchPayment('transaction_flag', 'bonus_deposit')" > Bonus</a>
                                                </div>
                                            </div>
                                        </div>
                                    </th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(p, i) in payments">
                                        <td> @{{ p.id }} </td>
                                        <td>@{{ p.id }}</td>
                                        <td>@{{ p.id }}</td>
                                        <td>@{{ p.id }}</td>
                                        <td>@{{ p.id }}</td>
                                        <td>@{{ p.id }}</td>
                                        <td>@{{ p.id }}</td>
                                        <td>
                                            @{{ p.id }}
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <button type="button" class="btn  custom-dropdown-button dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    Action
                                                </button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item type-dropdown-item" href="javascript:void(0)">Details</a>
                                                    <a class="dropdown-item type-dropdown-item" href="javascript:void(0)" >Edit</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="modal bs-example-modal-lg" id="paymentAddModal" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                            <div class="modal-dialog __modal_dialog_custom">
                                <div class="modal-content">
                                    <form  id="payment-form" method="post" action="" enctype="multipart/form-data" novalidate>
                                        @csrf
                                        <div class="modal-header">
                                            <h4 class="modal-title" id="myLargeModalLabel">Add payment</h4>
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label for=""><strong>User</strong></label>
                                                {{-- <div class="ui-widget">
                                                  <input type="text" name="user_id" id="user_id_payment" class="form-control custom-form-control" placeholder="type user name" />
                                                </div> --}}
                                                <select name="user_id" class="form-control custom-form-control" required >
                                                    <option disabled selected>Choose user</option>
                                                <option :value="u.id" v-for="(u, i) in users">@{{ u.name }} </option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for=""><strong>Amount</strong></label>
                                                <input type="text" name="amount" class="form-control custom-form-control" placeholder="Amount" value="14" required >
                                            </div>
                                            <div class="form-group">
                                                <label for=""><strong>Payment Method</strong></label>
                                                <select name="reseller_payment_methods_setting_id" id="reseller_payment_methods_setting_id" class="form-control custom-form-control" required >
                                                    <option disabled selected>Choose payment method</option>
                                                <option :value="gp.id" v-for="(gp, i) in global_payments">@{{gp.name}}</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for=""><strong>Memo (optional)</strong></label>
                                                <input type="text" name="memo" class="form-control custom-form-control" placeholder="Memo" value="12" required >
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <div class="form-actions">
                                                <button type="button" onclick="payment_submt(this)" class="btn btn-primary custom-button"> <i class="fa fa-check"></i> Save</button>
                                            </div>
                                            <button type="button" class="btn btn-danger custom-button" data-dismiss="modal">Close</button>
                                        </div>
                                    </form>
                                </div>
                                <!-- /.modal-content -->
                            </div>
                            <!-- /.modal-dialog -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script src="{{asset('/panel-assets/vue-scripts/common/pagination.js')}}"></script>
<script src="{{asset('/panel-assets/vue-scripts/payment-vue.js')}}"></script>
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
    }, 5000);

    function service_type_modal()
    {
        $("#order_service_type_detail").modal('hide');
    }
</script>
@endsection
