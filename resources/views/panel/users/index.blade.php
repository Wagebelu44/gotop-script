@extends('layouts.panel')

@section('content')
    <div class="container-fluid padding-less  all-mt-30" id="user_panel_module">
        <div class="overlay-loader" v-if="loader">
            <div class="loader-holder">
                <img src="{{asset('loader.gif')}}" alt="">
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card padding-less  card-body">
                    <div class="__control_panel">
                        <div class="__left_control_panel">
                            @can('add user')
                                <button class="btn btn-outline-secondary" data-toggle="modal" data-target="#userModal" >Add User</button>
                            @endcan
                        </div>
                        <div class="__right_control_panel">
                            <form class="d-flex pull-right" id="search-form"  @submit.prevent="searchFilter">
                            <div>
                                @can('export user')
                                    <a class="btn btn-link" href="{{ route('admin.users.export') }}">Export @{{ filter.status }}</a>
                                @endcan
                            </div>
                                <div class="form-group mb-2 mr-0">
                                    <input type="text" name="keyword" class="form-control" v-model="filter.search" placeholder="Search User" value="">
                                </div>
                                <button type="submit" class="btn btn-default mb-2" style="border:1px solid #eeeff0;">
                                    <i class="fa fa-search" aria-hidden="true"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table dataTable" id="users">
                            <thead>
                                <tr v-if="selectedUsers.length>0">
                                    <th><input type="checkbox"  v-model="checkAlluser"></th>
                                    <th colspan="11">
                                        <span id="user-no"></span> users selected <span> @{{selectedUsers.length}} </span>
                                        <div class="btn-group">
                                            <button type="button" class="btn dropdown-toggle custom-dropdown-button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                Action
                                            </button>
                                            <div class="dropdown-menu">
                                                <form method="post" id="global-update" action="">
                                                    @csrf
                                                    @method('put')
                                                    <input type="hidden" name="type">
                                                </form>
                                                <form method="post" id="custom-rate-reset" action="">
                                                    @csrf
                                                    @method('put')
                                                </form>
                                                @can('change user status')
                                                    <a class="dropdown-item type-dropdown-item" @click="suspendAlluser" href="javascript:void(0)">Suspend all</a>
                                                @endcan
                                                <a class="dropdown-item type-dropdown-item" @click="activeAlluser" href="javascript:void(0)">Activate all</a>
                                                <a class="dropdown-item type-dropdown-item" @click="resetAlluserRate" href="javascript:void(0)">Reset custom rates</a>
                                                {{-- <a class="dropdown-item type-dropdown-item" href="javascript:void(0)">Copy rates from user</a> --}}
                                            </div>
                                        </div>
                                    </th>
                                </tr>
                            <tr v-else>
                                <th><input type="checkbox"  v-model="checkAlluser"></th>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Skype</th>
                                <th>Balance</th>
                                <th>Spent</th>
                                <th>
                                    <div class="input-group">
                                        <div class="input-group-append">
                                            <button class="btn  dropdown-toggle  custom-dropdown-button" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Status</button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item type-dropdown-item" @click="statusFilter('')" href="">All({{ $counts[0]->all_count??0 }})</a>
                                                <a class="dropdown-item type-dropdown-item" @click="statusFilter('Active')" href="javascript:void(0)">Active ({{ $counts[0]->active_count??0 }})</a>
                                                <a class="dropdown-item type-dropdown-item" @click="statusFilter('Deactivated')" href="javascript:void(0)">Suspended ({{ $counts[0]->deactive_count??0 }})</a>
                                                <a class="dropdown-item type-dropdown-item" @click="statusFilter('Pending')" href="javascript:void(0)">Unconfirmed ({{ $counts[0]->pending_count??0 }})</a>
                                            </div>
                                        </div>
                                    </div>
                                </th>
                                <th>Created</th>
                                <th>Last auth</th>
                                <th>Rates</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody id="tbody_ss" v-if="users.length>0">
                                <tr v-for="(user, index) in users">
                                    <td>
                                        <input type="checkbox"  v-model="selectedUsers" class="user_check" :value="user.id">
                                    </td>
                                    <td>@{{user.id}}</td>
                                    <td>@{{user.username}}</td>
                                    <td>@{{user.email}}</td>
                                    <td>@{{user.skype_name}}</td>
                                    <td>$@{{user.show_balance}}</td>
                                    <td>$@{{ user.spent??0 }}</td>
                                    <td>
                                        <span v-show="user.status == 'Active'">Active</span>
                                        <span v-show="user.status == 'Pending'">Unconfirmed</span>
                                        <span v-show="user.status == 'Deactivated'">Suspended</span>
                                    </td>
                                    <td>@{{user.created_at}}</td>
                                    <td>@{{user.last_login_at}}</td>
                                    @can('edit user custom rates')
                                        <td>
                                            <a href="javascript:void(0)" class="btn custom-dropdown-button" @click="customeRate(user.id)"
                                        title="Services custom rates">custom rates (<span v-if="user.services_list.length>0">@{{user.services_list.length}}</span> <span v-else>0</span>)</a>
                                        </td>
                                    @endcan
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn dropdown-toggle custom-dropdown-button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                Action
                                            </button>
                                            <div class="dropdown-menu">
                                                @can('edit user')
                                                    <a class="dropdown-item type-dropdown-item" href="javascript:void(0)" @click="editUser(user.id)">Edit user</a>
                                                @endcan
                                                <a class="dropdown-item type-dropdown-item" href="javascript:void(0)" @click="resetPassword(user.id)">Set password</a>
                                                  <a class="dropdown-item type-dropdown-item" @click="copyCustomRate(user.id)" href="javascript:void(0)">Copy rates</a>
                                                  <a class="dropdown-item type-dropdown-item" @click="userLoginLog(user.id)" href="javascript:void(0)">Sign-in History</a>
                                                @can('change user status')  
                                                <a class="dropdown-item type-dropdown-item" href="javascript:void(0)" @click="suspendUser(user.id)"> 
                                                    <span v-show="user.status==='Deactivated'">Active</span> 
                                                    <span v-show="user.status==='Active'">Suspend</span> 
                                                    <span v-show="user.status==='Pending'">Active</span> 
                                                    User
                                                </a>
                                                @endcan
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                            <div class="text-center mt-4" v-if="users.length === 0">No data available in table</div>
                        </table>
                    </div>
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <data-pagination v-if="pagination.last_page > 1" :pagination="pagination" :offset="5" @paginate="getUsers()"></data-pagination>
                        </div>
                    </div>
                    {{-- modal start from here --}}
                    <div class="modal bs-example-modal-lg" id="userModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                        <div class="modal-dialog __modal_dialog_custom">
                            <div class="modal-content">
                                <form  id="user-form" method="post" @submit.prevent="formFunc">
                                    @csrf
                                    <div class="modal-header">
                                    <h4 class="modal-title" id="userModalLabel">Add user  </h4>
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for=""> <strong>Email</strong> </label>
                                            <input type="email" name="email" v-model="formUser.email" class="form-control custom-form-control" placeholder="Email"  required />
                                        </div>
                                        <div class="form-group">
                                            <label for=""> <strong>Username</strong> </label>
                                            <input type="text" name="username" v-model="formUser.username" class="form-control custom-form-control" placeholder="Username" />
                                        </div>
                                        <div class="form-group">
                                            <label for=""> <strong>Skype Name</strong> </label>
                                            <input type="text" name="skype_name" v-model="formUser.skype_name" class="form-control custom-form-control" placeholder="Skype" />
                                        </div>
                                        <div class="form-group" >
                                            <label for=""> <strong>Password</strong> </label>
                                            <input type="password" name="password" v-model="formUser.password" class="form-control custom-form-control" placeholder="Password">
                                        </div>
                                        <div class="form-group">
                                            <div class="controls">
                                                <label for=""> <strong>Confirm Password</strong> </label>
                                                <input type="password" name="password_confirmation" v-model="formUser.password_confirmation" class="form-control custom-form-control " placeholder="Confirm Password">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="">Allowed payment methods</label><br>

                                            <label v-for="payment in global_payment_methods">
                                                <input type="checkbox" name="payment_methods[]" v-model="formUser.payment_methods" :value="payment.id"> @{{ payment.method_name }} &nbsp;&nbsp;&nbsp;&nbsp;
                                            </label>
                                        </div>
                                        <div class="form-group">
                                            <div class="custom-control custom-radio custom-control-inline mt-1">
                                                <input type="radio" name="status" class="custom-control-input" id="customControlValidation1" v-model="formUser.status"  value="Pending" required="" />
                                                <label class="custom-control-label" for="customControlValidation1">Unconfirmed</label>
                                            </div>
                                            <div class="custom-control custom-radio custom-control-inline mt-1">
                                                <input type="radio" name="status" class="custom-control-input" id="customControlValidation2"  v-model="formUser.status"  value="Active" required="" />
                                                <label class="custom-control-label" for="customControlValidation2">Active</label>
                                            </div>
                                            <div class="custom-control custom-radio custom-control-inline">
                                                <input type="radio" name="status" class="custom-control-input" id="customControlValidation3"  v-model="formUser.status"  value="Deactivated" required="" />
                                                <label class="custom-control-label" for="customControlValidation3">Suspended</label>
                                            </div>
                                        </div>
                                        <div v-if="validationErros.length>0" v-for="err in validationErros" class="alert alert-danger alert-dismissible fade show" role="alert">
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                                <span class="sr-only">Close</span>
                                            </button>
                                            <strong>@{{err.name}}</strong> @{{err.desc}}.
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary custom-button"> <i class="fa fa-check"></i> Save</button>
                                        <button type="button" class="btn btn-danger custom-button" data-dismiss="modal">Close</button>
                                    </div>
                                </form>
                            </div>
                            <!-- /.modal-content -->
                        </div>
                        <!-- /.modal-dialog -->
                    </div>
                    <div class="modal bs-example-modal-lg" id="passwordUpdateModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                        <div class="modal-dialog __modal_dialog_custom">
                            <div class="modal-content">
                                <form  id="password-update-form" method="post" @submit.prevent="updatePassword">
                                    @csrf
                                    <div class="modal-header">
                                        <h4 class="modal-title" id="myLargeModalLabel"> <strong>Update password</strong> </h4>
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="user_id" v-model="edit_user_id">
                                        <div class="form-group">
                                            <input type="password" name="password" class="form-control custom-form-control" placeholder="Password" required />
                                        </div>
                                        <div class="form-group">
                                            <input type="password" name="password_confirmation" class="form-control custom-form-control" placeholder="Confirm Password" required />
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary custom-button"> <i class="fa fa-check"></i> Change Password</button>
                                        <button type="button" class="btn btn-danger custom-button" data-dismiss="modal">Close</button>
                                    </div>
                                </form>
                                <div v-if="validationErros.length>0" v-for="err in validationErros" class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                        <span class="sr-only">Close</span>
                                    </button>
                                    <strong>@{{err.name}}</strong> @{{err.desc}}.
                                </div>
                            </div>
                            <!-- /.modal-content -->
                        </div>
                        <!-- /.modal-dialog -->
                    </div>
                    <div class="modal bs-example-modal-lg" id="customRateAddModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                        <div class="modal-dialog __modal_dialog_custom">
                            <div class="modal-content">
                                <form id="custom-rate-add-form" method="post" enctype="multipart/form-data">
                                    @csrf
                                    <div class="modal-header">
                                        <h4 class="modal-title" id="myLargeModalLabel">Edit custom rates(ID: @{{ current_user !== null? current_user.id:null }})</h4>
                                        <button type="button" class="close"  data-dismiss="modal" aria-hidden="true">×</button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <div class="dropdown">
                                                        <button class="btn btn-default custom-button dropdown-toggle" type="button"
                                                                id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                                                aria-expanded="false">
                                                                Choose service
                                                        </button>
                                                        <div class="dropdown-menu service-dropdown" aria-labelledby="dropdownMenuButton">
                                                            <div id="user_filter_type" v-for="(cs, ind) in categoryServices">
                                                                    <div
                                                                    class="dropdown-item type-dropdown-item"
                                                                    style="font-weight: 700; pointer-events: none">
                                                                        @{{cs.name}}
                                                                    </div>
                                                                    <div v-for="(service, i) in cs.services" class="dropdown-item type-dropdown-item"
                                                                    style="padding-left: 50px;" @click="addCustomRate(service)">
                                                                        <span style="padding: 2px; border: 1px solid rgba(0,0,0,0.7); font-size:10px; font-weight: 700; ">@{{service.id}}</span>
                                                                        <span>@{{ service.name }}</span>
                                                                        <span style="padding: 2px; border: 1px solid rgba(0,0,0,0.7); font-size:10px; font-weight: 700;">@{{service.price}}</span>
                                                                    </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="table-responsive">
                                                    <table id="zero_config" class="table">
                                                        <thead>
                                                        <tr>
                                                            <th>Service ID</th>
                                                            <th style="width: 30%">Name</th>
                                                            <th>Price</th>
                                                            <th style="width: 30%">Price update</th>
                                                            <th>Actions</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr v-for="(ser, ind) in userServices">
                                                                <td >@{{ser.service_id}}</td>
                                                                <td style="width: 30%">@{{ser.name}}</td>
                                                                <td >@{{ser.original_price}}</td>
                                                                <td style="width: 30%">
                                                                    <div class="input-group">
                                                                        <input step="any"
                                                                        type="number" name="price"
                                                                        @keyup="updateInput($event, ser.service_id)"
                                                                        class="form-control" placeholder="Price" :value="ser.price">
                                                                        <input type="hidden" name="percentage"  value="0">
                                                                        <div class="input-group-append">
                                                                            <span class="input-group-text" style="cursor: pointer">$</span>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <button type="button" @click="removeCustomRate(ser.service_id)"
                                                                    class="btn btn-danger"> <i class="fa fa-trash"></i> </button>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" @click="storeUserService" class="btn btn-primary custom-button save-button"> <i class="fa fa-check"></i> Save</button>
                                        <button type="button" @click="deleteAllUserService"> <i class="fa fa-trash"></i> Delete all</button>
                                        <button type="button" class="btn btn-danger custom-button"  data-dismiss="modal">Close</button>
                                    </div>
                                </form>
                                <form class="d-none" method="post" id="deleteCustomRates">
                                    @csrf
                                    @method('delete')
                                </form>
                                <form class="d-none" method="post" id="deleteCustomRate">
                                    @csrf
                                    @method('delete')
                                </form>
                            </div>
                            <!-- /.modal-content -->
                        </div>
                        <!-- /.modal-dialog -->
                    </div>

                    <div class="modal bs-example-modal-lg" id="copyCustomRateModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                        <div class="modal-dialog __modal_dialog_custom">
                            <div class="modal-content">
                                <form @submit.prevent="copyRatesSubmit()" id="copy-custom-rate-form" method="post">
                                    <div class="modal-header">
                                        <h4 class="modal-title" >Copy custom rates (ID: @{{ current_user !== null? current_user.id:null }}) </h4>
                                        <button type="button" class="close"  data-dismiss="modal" aria-hidden="true">×</button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="d-flex align-items-center w-100">
                                            <div class="form-group" style="flex-basis: 50%">
                                                <label for=""><strong>From User</strong></label>
                                                <select name="from_user" class="form-control custom-input" id="select2-payment-user">
                                                    <option disabled selected>Select User</option>
                                                    <option :value="u.id" v-for="(u, i) in custom_rated_users">@{{ u.username }} (@{{ u.services_list.length }}) </option>
                                                </select>
                                            </div>
                                            <div class="form-group" style="flex-basis: 10%">
                                                <div class="mt-4 ml-4"> <i class="fa fa-arrow-right" aria-hidden="true"></i> </div>
                                            </div>
                                            <div class="form-group" style="flex-basis: 40%">
                                                <label for=""><strong>To User</strong></label>
                                                <input type="hidden" name="to_user" :value="current_user !== null? current_user.id:null"> 
                                                <input type="text" class="form-control custom-input" readonly :value="current_user !== null? current_user.username:null">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-success theme-color custom-button">Copy Rates</button>
                                        <button type="button" class="btn btn-default custom-button"  data-dismiss="modal">Cancel</button>
                                    </div>
                                </form>
                            </div>
                            <!-- /.modal-content -->
                        </div>
                        <!-- /.modal-dialog -->
                    </div>
                    <div class="modal bs-example-modal-lg" id="user_login_log" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                        <div class="modal-dialog __modal_dialog_custom">
                            <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title" >Copy custom rates (ID: @{{ current_user !== null? current_user.id:null }}) </h4>
                                        <button type="button" class="close"  data-dismiss="modal" aria-hidden="true">×</button>
                                    </div>
                                    <div class="modal-body">
                                        {{-- <div class="form-group">
                                            <input type="search" class="form-control" placeholder="Search By IP" />
                                        </div> --}}
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th><strong>Date, Time</strong></th>
                                                        <th><strong>IP</strong></th>
                                                        <th><strong>Location</strong></th>
                                                    </tr>
                                                </thead>
                                                <tbody v-if="user_login_logs.length>0">
                                                    <tr v-for="l in user_login_logs">
                                                        <td>@{{ l.created_at }}</td>
                                                        <td>@{{ l.ip }}</td>
                                                        <td>@{{ l.location }}</td>
                                                    </tr>
                                                </tbody>
                                                <tbody v-else>
                                                    <tr>
                                                        <td colspan="3">No Records Founds</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default custom-button"  data-dismiss="modal">Close</button>
                                    </div>
                            </div>
                            <!-- /.modal-content -->
                        </div>
                        <!-- /.modal-dialog -->
                    </div>
                    {{-- currently not used --}}
                    <div class="modal bs-example-modal-lg" id="customRateUpdateModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                        <div class="modal-dialog __modal_dialog_custom">
                            <div class="modal-content">
                                <form id="custom-rate-update-form" method="post" novalidate>
                                    @csrf
                                    <div class="modal-header">
                                        <h4 class="modal-title" id="myLargeModalLabel">Copy custom rates</h4>
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group user-from">
                                                    <div class="controls">
                                                        <select id="from" name="from" class="form-control  custom-form-control @error('from') is-invalid @enderror" required data-validation-required-message="This field is required">
                                                            <option disabled selected>Choose from user</option>
                                                            <option value="">username</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-1"><i class="fa fa-arrow-right"></i></div>
                                            <div class="col-md-5">
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <input type="text" id="to" class="form-control custom-form-control" placeholder="To user" required data-validation-required-message="This field is required" readonly>
                                                        <input type="hidden" name="to" class="form-control" placeholder="To user" required data-validation-required-message="This field is required" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary custom-button"> <i class="fa fa-check"></i> Save</button>
                                        <button type="button" class="btn btn-danger custom-button" data-dismiss="modal">Close</button>
                                    </div>
                                </form>
                            </div>
                            <!-- /.modal-content -->
                        </div>
                        <!-- /.modal-dialog -->
                    </div>
                    <div class="modal bs-example-modal-lg" id="customRateBulkUpdateModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                        <div class="modal-dialog __modal_dialog_custom">
                            <div class="modal-content">
                                <form id="custom-rate-bulk-update" action="" method="post" novalidate>
                                    @csrf
                                    @method('put')
                                    <div class="modal-header">
                                        <h4 class="modal-title" id="myLargeModalLabel">Copy custom rates</h4>
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group user-from">
                                                    <select id="fromBulk" name="from" class="form-control  custom-form-control @error('from') is-invalid @enderror" required data-validation-required-message="This field is required">
                                                        <option disabled selected>Choose from user</option>
                                                        <option value="">username</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-1"><i class="fa fa-arrow-right"></i></div>
                                            <div class="col-md-5">
                                                <div class="form-group">
                                                    <textarea rows="10" disabled></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary custom-button"> <i class="fa fa-check"></i> Save</button>
                                        <button type="button" class="btn btn-danger custom-button" data-dismiss="modal">Close</button>
                                    </div>
                                </form>
                            </div>
                            <!-- /.modal-content -->
                        </div>
                        <!-- /.modal-dialog -->
                    </div>
                    <!-- /.modal -->
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
<script>
    let customRateduserUrl ="{{ route('admin.custom.rated.users.only') }}";
    let copyratestouser ="{{ route('admin.copy.custom.rates.users') }}";
    let deleteSingleService ="{{ route('admin.delete.user.single.service') }}";
</script>
<script src="{{asset('/panel-assets/vue-scripts/common/pagination.js')}}"></script>
<script src="{{asset('/panel-assets/vue-scripts/common/helper-mixin.js')}}"></script>
<script src="{{asset('/panel-assets/vue-scripts/user-vue.js?var=0.10')}}"></script>
<script>
    $('#userModal').on('hidden.bs.modal', function () {
        userModule.edit_user_id = null;
        userModule.formClear();
    });
    $('#passwordUpdateModal').on('hidden.bs.modal', function () {
        userModule.edit_user_id = null;
    });
    $('#customRateAddModal, #user_login_log').on('hidden.bs.modal', function () {
        userModule.current_user_id = null;
        userModule.current_user = null;
    });
    $('#copyCustomRateModal').on('hidden.bs.modal', function () {
        userModule.current_user = null;
        userModule.current_user_id = null;
        userModule.custom_rated_users = [];
    });
</script>
@endsection
