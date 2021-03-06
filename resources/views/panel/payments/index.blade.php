@extends('layouts.panel')

@section('content')
    <div class="container-fluid all-mt-30" id="payment_module">
        <div class="overlay-loader" v-if="loader">
            <div class="loader-holder">
                <img src="{{asset('loader.gif')}}" alt="">
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row pb-3 pt-3">
                            <div class="col-md-6">
                                @can('add payment')
                                    <button class="btn btn-outline-secondary" data-toggle="modal" data-target="#paymentAddModal">Add Payment</button>
                                @endcan
                                @if(isset($setting) && $setting->redeem === 'Yes')
                                <button class="btn btn-outline-secondary" data-toggle="modal" data-target="#redeemPointModal">Redeem Point</button>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <form class="d-flex pull-right" method="get" @submit.prevent="filterType">
                                    <div>
                                        @can('export payment')
                                            <a href="{{ route('admin.payments.export') }}" class="btn btn-link">Export</a>
                                        @endcan
                                    </div>
                                    <div class="form-group mb-2 mr-0">
                                        <input type="search" name="search" v-model="filter.filter_type.data" class="form-control" placeholder="search...">
                                    </div>
                                    <div class="form-group mb-2 ml-0">
                                        <select name="keyword" id="keyword"  class="form-control">
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
                                                    <a class="dropdown-item type-dropdown-item" @click='filterPaymentMethod(g.id)'  v-for="(g, i) in global_payments" href="javascript:void(0)"> @{{ g.method_name }} (@{{g.totalPayment??0}}) </a>
                                                    <a class="dropdown-item type-dropdown-item" href="javascript:void(0)" > Bonus (0) </a>
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
                                                <a class="dropdown-item type-dropdown-item" href="javascript:void(0)" @click="searchPayment('admin_panel')">Manual</a>
                                                <a class="dropdown-item type-dropdown-item" href="javascript:void(0)" @click="searchPayment('payment_gateway')">Auto</a>
                                                <a class="dropdown-item type-dropdown-item" href="javascript:void(0)" @click="searchPayment('bonus_deposit')" > Bonus</a>
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
                                                @can('edit payment')
                                                    <a class="dropdown-item type-dropdown-item" href="javascript:void(0)" @click="editPayment(p.id)" >Edit</a>
                                                @endcan
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
                                            <h4 class="modal-title" id="myLargeModalLabel"> <span v-if='!payment_edit'>Add</span><span v-else>Edit</span>  payment</h4>
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label for=""><strong>User</strong></label>
                                                <select name="user_id" class="form-control custom-form-control" id="select2-payment-user" required>
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
                                                <select name="global_payment_method_id" v-model='payment_obj.global_payment_method_id' id="global_payment_method_id" class="form-control custom-form-control" required >
                                                    <option disabled selected>Choose payment method</option>
                                                <option :value="gp.id" v-for="(gp, i) in global_payments">@{{gp.method_name}}</option>
                                                </select>
                                                <span class="text-danger" v-if="errorFilter('global_payment_method_id')!==''"> @{{ errorFilter('global_payment_method_id') }} </span>
                                            </div>
                                            <div class="form-group">
                                                <label for=""><strong>Memo (optional)</strong></label>
                                                <input type="text" v-model='payment_obj.memo' name="memo" class="form-control custom-form-control" placeholder="Memo"  required >
                                            </div>
                                            <div class="alert alert-danger alert-dismissible fade show" v-if="errors.common" role="alert">
                                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                    <span class="sr-only">Close</span>
                                                </button>
                                                <strong>Warning!</strong> @{{ errors.common }}.
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
<script src="{{asset('/panel-assets/vue-scripts/common/helper-mixin.js?var=0.3')}}"></script>
<script src="{{asset('/panel-assets/vue-scripts/payment-vue.js?var=0.5')}}"></script>
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
        paymentModule.errors.payment = [];
        paymentModule.errors.common  = null;
        document.getElementById('payment-form').reset();
    });
</script>
@endsection
