@extends('layouts.panel')

@section('content')
    <div class="container-fluid all-mt-30" id="user_panel_module">
        <div class="row">
            <div class="col-12">
                <div class="material-card card">
                    <div class="card-body">
                        <div class="__control_panel">
                            <div class="__left_control_panel">
                                <button class="btn btn-outline-secondary" data-toggle="modal" data-target="#userModal" >Add User</button>
                            </div>
                            <div class="__right_control_panel">
                                <form class="d-flex pull-right" id="search-form" method="get" action="">
                                    <div><a class="btn btn-link" href="">Export</a></div>
                                    <input type="hidden" name="order_by" value="">
                                    <input type="hidden" name="sort_by" value="">
                                    <div class="form-group mb-2 mr-0">
                                        <input type="text" name="keyword" class="form-control" placeholder="Search User" value="">
                                    </div>
                                    <button type="submit" class="btn btn-default mb-2" style="border:1px solid #eeeff0;"> <i class="fa fa-search" aria-hidden="true"></i> </button>
                                </form>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table dataTable" id="users">
                                <thead>
                                <tr>
                                    <th><input type="checkbox" name="select_all"></th>
                                    <th colspan="11" style="display: none">
                                        <span id="user-no"></span> users selected
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
                                                <a class="dropdown-item type-dropdown-item" href="javascript:void(0)">Suspend all</a>
                                                <a class="dropdown-item type-dropdown-item" href="javascript:void(0)">Activate all</a>
                                                <a class="dropdown-item type-dropdown-item" href="javascript:void(0)">Reset custom rates</a>
                                                <a class="dropdown-item type-dropdown-item" href="javascript:void(0)">Copy rates from user</a>
                                            </div>
                                        </div>
                                    </th>
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Whatsapp</th>
                                    <th>Balance</th>
                                    <th>Spent</th>
                                    <th>
                                        <div class="input-group">
                                            <div class="input-group-append">
                                                <button class="btn  dropdown-toggle  custom-dropdown-button" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Status</button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item type-dropdown-item" href="">All</a>
                                                    <a class="dropdown-item type-dropdown-item" href="javascript:void(0)">Active</a>
                                                    <a class="dropdown-item type-dropdown-item" href="javascript:void(0)">Inactive</a>
                                                </div>
                                            </div>
                                        </div>
                                    </th>
                                    <th>
                                        Created
                                    </th>
                                    <th>
                                        Last auth
                                    </th>
                                    <th>
                                        Rates
                                    </th>
                                    <th>
                                        Actions
                                    </th>
                                </tr>
                                </thead>
                                <tbody id="tbody_ss">
                                <tr>
                                    <td>
                                        <input type="checkbox" name="users[]" value="" class="user_check">
                                    </td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td>
                                        <a href="javascript:void(0)" class="btn custom-dropdown-button" title="Services custom rates">custom rates 2</a>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn dropdown-toggle custom-dropdown-button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                Action
                                            </button>
                                            <div class="dropdown-menu">
                                                <form class="d-none" action="" method="post" id="suspend-user">
                                                    @csrf
                                                    @method('patch')
                                                </form>
                                                <a class="dropdown-item type-dropdown-item" href="javascript:void(0)">Edit user</a>
                                                <a class="dropdown-item type-dropdown-item" href="javascript:void(0)">Set password</a>
                                                <a class="dropdown-item type-dropdown-item" href="javascript:void(0)">Copy rates</a>
                                                <a class="dropdown-item type-dropdown-item" href="javascript:void(0)">User</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>


                        <div class="row mt-4">
                            <div class="col-md-6">
                                <data-pagination v-if="pagination.last_page > 1" :pagination="pagination" :offset="5" @paginate="getUsers()"></data-pagination>
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
                        <div class="text-center mt-4">No data available in table</div>


                        {{-- modal start from here --}}
                        <div class="modal bs-example-modal-lg" id="userModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                            <div class="modal-dialog __modal_dialog_custom">
                                <div class="modal-content">
                                    <form  id="user-form" method="post" action="" enctype="multipart/form-data">
                                        @csrf
                                        <div class="modal-header">
                                            <h4 class="modal-title" id="userModalLabel">Add user</h4>
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <input type="email" name="email" class="form-control custom-form-control @error('email') is-invalid @enderror" placeholder="Email" value="{{ old('email') }}" required data-validation-required-message="This field is required">
                                            </div>
                                            <div class="form-group">
                                                <input type="text" name="username" class="form-control custom-form-control @error('username') is-invalid @enderror" placeholder="Username" value="{{ old('username') }}">
                                            </div>
                                            <div class="form-group">
                                                <input type="text" name="skype_name" class="form-control custom-form-control @error('skype_name') is-invalid @enderror" placeholder="Skype" value="{{ old('skype_name') }}">
                                            </div>
                                            <div class="form-group">
                                                <input type="password" name="password" class="form-control custom-form-control @error('password') is-invalid @enderror" placeholder="Password">
                                            </div>
                                            <div class="form-group">
                                                <div class="controls">
                                                    <input type="password" name="password_confirmation" class="form-control custom-form-control " placeholder="Confirm Password">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="">Allowed payment methods</label><br>
                                                <input type="checkbox" name="payment_methods[]" value="" checked> Allowed payment methods
                                            </div>
                                            <div class="form-group">
                                                <div class="custom-control custom-radio custom-control-inline mt-1">
                                                    <input type="radio" name="status" class="custom-control-input" id="customControlValidation1" value="pending" required="" {{ old('status') == 'pending' ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="customControlValidation1">Pending</label>
                                                </div>
                                                <div class="custom-control custom-radio custom-control-inline mt-1">
                                                    <input type="radio" name="status" class="custom-control-input" id="customControlValidation2" value="active" required="" {{ old('status') == 'active' ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="customControlValidation2">Active</label>
                                                </div>
                                                <div class="custom-control custom-radio custom-control-inline">
                                                    <input type="radio" name="status" class="custom-control-input" id="customControlValidation3" value="inactive" required="" {{ old('status') == 'inactive' ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="customControlValidation3">Inactive</label>
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
                        <div class="modal bs-example-modal-lg" id="passwordUpdateModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                            <div class="modal-dialog __modal_dialog_custom">
                                <div class="modal-content">
                                    <form  id="password-update-form" method="post" enctype="multipart/form-data" novalidate>
                                        @csrf
                                        @method('put')
                                        <div class="modal-header">
                                            <h4 class="modal-title" id="myLargeModalLabel"> <strong>Update password</strong> </h4>
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <input type="password" name="password" class="form-control custom-form-control @error('password') is-invalid @enderror" placeholder="Password" required data-validation-required-message="This field is required">
                                            </div>
                                            <div class="form-group">
                                                <input type="password" name="password_confirmation" class="form-control custom-form-control" placeholder="Confirm Password" required data-validation-required-message="This field is required">
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
                        <div class="modal bs-example-modal-lg" id="customRateAddModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                            <div class="modal-dialog __modal_dialog_custom">
                                <div class="modal-content">
                                    <form id="custom-rate-add-form" method="post" enctype="multipart/form-data">
                                        @csrf
                                        <div class="modal-header">
                                            <h4 class="modal-title" id="myLargeModalLabel">Edit custom rates</h4>
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
                                                                <div id="user_filter_type">
                                                                    <div
                                                                        class="dropdown-item type-dropdown-item"
                                                                        style="font-weight: 700; pointer-events: none">
                                                                    </div>
                                                                    <div class="dropdown-item type-dropdown-item"
                                                                         style="padding-left: 50px;">
                                                                        <span style="padding: 2px; border: 1px solid rgba(0,0,0,0.7); font-size:10px; font-weight: 700; ">11</span>
                                                                        <span>dfdfdf</span>
                                                                        <span style="padding: 2px; border: 1px solid rgba(0,0,0,0.7); font-size:10px; font-weight: 700;">222</span>
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
                                                                <th style="width: 50%">Name</th>
                                                                <th>Price</th>
                                                                <th>Price update</th>
                                                                <th>Actions</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>

                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="save" class="btn btn-primary custom-button save-button"> <i class="fa fa-check"></i> Save</button>
                                            <button type="button"> <i class="fa fa-trash"></i> Delete all</button>
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
    </div>
@endsection
@section('scripts')
 <script>
     Vue.component('data-pagination', {
        props:['pagination', 'offset'],
        data: function () {
            return {
                count: 0
            }
        },
        methods: {
        isCurrentPage(page)
        {
            return this.pagination.current_page === page
        },
        changePage(page) 
        {
            if (page > this.pagination.last_page) {
                page = this.pagination.last_page;
            }
            this.pagination.current_page = page;
            this.$emit('paginate');
        }
    },
    computed: {
        pages() {
            let pages = []
            let from = this.pagination.current_page - Math.floor(this.offset / 2)
            if (from < 1) {
                from = 1
            }
            let to = from + this.offset -1
            if (to > this.pagination.last_page) {
                to = this.pagination.last_page
            }
            while (from <= to) {
                pages.push(from)
                from++
            }
            return pages
        }
    },
    template: ` <nav aria-label="...">
                        <ul class="pagination justify-content-center">
                        <li class="page-item" :class="{ disabled: pagination.current_page <= 1 }">
                            <a class="page-link" @click.prevent="changePage(1)"  >First page</a>
                        </li>
                        <li class="page-item" :class="{ disabled: pagination.current_page <= 1 }">
                            <a class="page-link" @click.prevent="changePage(pagination.current_page - 1)">Previous</a>
                        </li>

                        <li class="page-item" v-for="page in pages"  :key="page" :class="isCurrentPage(page) ? 'active' : ''">
                            <a class="page-link" @click.prevent="changePage(page)">@{{ page }}
                            <span v-if="isCurrentPage(page)" class="sr-only">(current)</span>
                            </a>
                        </li>

                        <li class="page-item" :class="{ disabled: pagination.current_page >= pagination.last_page }">
                            <a class="page-link" @click.prevent="changePage(pagination.current_page + 1)">Next</a>
                        </li>
                        <li class="page-item" :class="{ disabled: pagination.current_page >= pagination.last_page }">
                            <a class="page-link" @click.prevent="changePage(pagination.last_page)">Last page</a>
                        </li>
                        </ul>
                    </nav>`
    });
 </script>
<script src="{{asset('/panel-assets/vue-scripts/user-vue.js')}}"></script>
@endsection
