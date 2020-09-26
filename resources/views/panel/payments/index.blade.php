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
                                <button class="btn btn-outline-secondary" data-toggle="modal" data-target="#redeemPointModal">Redeem Point</button>
                            </div>
                            <div class="col-md-6">
                                <form class="d-flex pull-right" method="get" action="">
                                    <div>
                                        <a href="{{ route('admin.payments.export') }}" class="btn btn-link">Export</a>
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
                                    <td>@{{ p.username }}</td>
                                    <td>@{{ p.amount }}</td>
                                    <td>@{{ p.payment_method_name }}</td>
                                    <td>@{{ p.memo }}</td>
                                    <td>@{{ p.created_at }}</td>
                                    <td>@{{ p.updated_at }}</td>
                                    <td>
                                        <span v-if="p.transaction_flag == 'admin_panel'">Manual</span>
                                        <span v-else-if="p.transaction_flag == 'payment_gateway'">Auto</span>
                                        <span v-else-if="p.transaction_flag == 'bonus_deposit'">Bonus</span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn  custom-dropdown-button dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                Action
                                            </button>
                                            <div class="dropdown-menu">
                                                {{-- <a class="dropdown-item type-dropdown-item" href="javascript:void(0)">Details</a> --}}
                                                <a class="dropdown-item type-dropdown-item" href="javascript:void(0)" @click="editPayment(p.id)" >Edit</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="modal bs-example-modal-lg" id="paymentAddModal" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                            <div class="modal-dialog __modal_dialog_custom">
                                <div class="modal-content">
                                    <form  id="payment-form" @submit.prevent="savePayment" method="post" action="" enctype="multipart/form-data" novalidate>
                                        @csrf
                                        <div class="modal-header">
                                            <h4 class="modal-title" id="myLargeModalLabel">Add payment</h4>
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label for=""><strong>User @{{ payment_obj.user_id }}</strong></label>
                                                {{-- <div class="ui-widget">
                                                  <input type="text" name="user_id" id="user_id_payment" class="form-control custom-form-control" placeholder="type user name" />
                                                </div> --}}
                                                <select name="user_id" v-model='payment_obj.user_id' class="form-control custom-form-control" id="select2-payment-user" required>
                                                    <option disabled selected>Choose user</option>
                                                <option :value="u.id" v-for="(u, i) in users">@{{ u.username }} </option>
                                                </select>
                                                <span class="text-danger" v-if="errorFilter('user_id')!==''"> @{{ errorFilter('user_id') }} </span>
                                            </div>
                                            <div class="form-group">
                                                <label for=""><strong>Amount</strong></label>
                                                <input type="text" name="amount" v-model='payment_obj.amount' class="form-control custom-form-control" placeholder="Amount"  required >
                                                <span class="text-danger" v-if="errorFilter('amount')!==''"> @{{ errorFilter('amount') }} </span>
                                            </div>
                                            <div class="form-group">
                                                <label for=""><strong>Payment Method</strong></label>
                                                <select name="reseller_payment_methods_setting_id" v-model='payment_obj.reseller_payment_methods_setting_id' id="reseller_payment_methods_setting_id" class="form-control custom-form-control" required >
                                                    <option disabled selected>Choose payment method</option>
                                                <option :value="gp.id" v-for="(gp, i) in global_payments">@{{gp.method_name}}</option>
                                                </select>
                                                <span class="text-danger" v-if="errorFilter('reseller_payment_methods_setting_id')!==''"> @{{ errorFilter('reseller_payment_methods_setting_id') }} </span>
                                            </div>
                                            <div class="form-group">
                                                <label for=""><strong>Memo (optional)</strong></label>
                                                <input type="text" v-model='payment_obj.memo' name="memo" class="form-control custom-form-control" placeholder="Memo"  required >
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <div class="form-actions">
                                                <button type="submit"  class="btn btn-primary custom-button"> <i class="fa fa-check"></i> Save</button>
                                            </div>
                                            <button type="button" class="btn btn-danger custom-button" data-dismiss="modal">Close</button>
                                        </div>
                                    </form>
                                </div>
                                <!-- /.modal-content -->
                            </div>
                            <!-- /.modal-dialog -->
                        </div>

                        <div class="modal bs-example-modal-lg" id="redeemPointModal" role="dialog" aria-labelledby="redeemPointModal" aria-hidden="true">
                            <div class="modal-dialog __modal_dialog_custom">
                                <div class="modal-content">
                                    <form method="post" action="{{ route('admin.redeem.store') }}">
                                        @csrf
                                        <div class="modal-header">
                                            <h4 class="modal-title">Redeem Point</h4>
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label for=""><strong>User</strong></label>
                                                <select name="user_id" class="form-control custom-form-control" id="select2-redeem-user" required>
                                                    <option disabled>Choose user</option>
                                                    <option :value="user.id" v-for="(user, index) in users">@{{ user.username }} </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <div class="form-actions">
                                                <button type="submit"  class="btn btn-primary custom-button"> <i class="fa fa-check"></i> Save</button>
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
<script src="{{asset('/panel-assets/vue-scripts/payment-vue.js?var=0.3')}}"></script>
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

    $('#paymentAddModal').on('hidden.bs.modal', function () {
        paymentModule.payment_edit_id = null;
        paymentModule.payment_edit = false;
        document.getElementById('payment-form').reset();
    });
</script>
@endsection
