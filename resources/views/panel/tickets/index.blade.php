@extends('layouts.panel')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="__control_panel">
                            <div class="__left_control_panel">
                                <button class="btn btn-outline-secondary" data-toggle="modal" data-target="#ticketAddModal">Add Ticket</button>
                            </div>
                            <div class="__right_control_panel">
                                <form class="d-flex pull-right" id="search-form" method="get" action="">
                                    <div class="form-group mb-2 mr-0">
                                        <input type="text" name="keyword" class="form-control" placeholder="Search User" value="">
                                    </div>
                                    <button type="submit" class="btn btn-default mb-2" style="border:1px solid #eeeff0;"> <i class="fa fa-search" aria-hidden="true"></i> </button>
                                </form>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table" id="tickets">
                                <thead>
                                <tr>
                                    <th><input type="checkbox" name="select_all" onchange="checkAllTicket()"></th>
                                    <th colspan="7" style="display: none">
                                        <span id="user-no"></span> tickets selected
                                        <div class="btn-group">
                                            <button type="button" class="btn custom-dropdown-button dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                Action
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item type-dropdown-item" href="javascript:void(0)"
                                                   onclick="bulkStatusChange('closed')">Mark as Closed</a>
                                                <a class="dropdown-item type-dropdown-item" href="javascript:void(0)"
                                                   onclick="bulkStatusChange('pending')">Mark as Pending</a>
                                                <a class="dropdown-item type-dropdown-item" href="javascript:void(0)"
                                                   onclick="bulkStatusChange('answered')">Mark as Answered</a>
                                                <a class="dropdown-item type-dropdown-item" href="javascript:void(0)"
                                                   onclick="bulkStatusChange('delete')">Delete</a>
                                            </div>
                                        </div>
                                    </th>
                                    <th>ID</th>
                                    <th>Subject</th>
                                    <th>Request</th>
                                    <th style="width: 30%">Description</th>
                                    <th>Client name</th>
                                    <th>
                                        <div class="input-group">
                                            <div class="input-group-append">
                                                <button class="btn custom-dropdown-button dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Status</button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item type-dropdown-item" href="">All</a>
                                                    <a class="dropdown-item type-dropdown-item" href="javascript:void(0)" onclick="searchTickets('status', 'pending')">Pending</a>
                                                    <a class="dropdown-item type-dropdown-item" href="javascript:void(0)" onclick="searchTickets('status', 'answered')">Answered</a>
                                                    <a class="dropdown-item type-dropdown-item" href="javascript:void(0)" onclick="searchTickets('status', 'closed')">Resolved</a>
                                                </div>
                                                <form class="text-right" id="search-form">
                                                </form>
                                            </div>
                                        </div>
                                    </th>
                                    <th>Created at</th>
                                    <th>Last update</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td><input type="checkbox" name="tickets[]" value="" class="ticket_check"></td>
                                    <td>112</td>
                                    <td>Bangla</td>
                                    <td>Bank</td>
                                    <td style="width: 30%">
                                        Description Description
                                        <a href="" class="btn btn-link">Read More</a>
                                    </td>
                                    <td>username</td>
                                    <td>
                                        <p style="color:#fff; padding: 0px 5px; text-align:center;">style </p>
                                    </td>
                                    <td>10:20 PM</td>
                                    <td>10:20 PM</td>
                                    <td>
                                        <div class="d-flex">
                                            <div class="dropdown show goTopDropdown">
                                                <a class="btn btn-default dropdown-toggle custom-dropdown-button" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fa fa-cog"></i>
                                                </a>
                                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                                    <li class="dropdown-submenu"><a class="dropdown-item dropdown-toggle type-dropdown-item" href="#">Change status</a>
                                                        <ul class="dropdown-menu">
                                                            <li><a href="" class="dropdown-item type-dropdown-item">Pending</a></li>
                                                            <li><a  href="" class="dropdown-item type-dropdown-item">Answered</a></li>
                                                            <li><a  href="" class="dropdown-item type-dropdown-item">Closed</a></li>
                                                        </ul>
                                                    </li>
                                                </ul>
                                            </div>
                                            <a href="" class="btn custom-dropdown-button"><i class="fa fa-eye"></i> </a>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <table class="table table-bordered table-hover">
                                    <tr class="text-center">
                                        <td> < </td>
                                        <td style="background-color: #2cabe3; color: white">1</td>
                                        <td>2</td>
                                        <td>3</td>
                                        <td>4</td>
                                        <td>5</td>
                                        <td>6</td>
                                        <td>7</td>
                                        <td>...</td>
                                        <td>9</td>
                                        <td>10</td>
                                        <td> > </td>
                                    </tr>
                                </table>
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
                        <div class="modal bs-example-modal-lg" id="ticketAddModal" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                            <div class="modal-dialog  __modal_dialog_custom">
                                <div class="modal-content">
                                    <form  method="post" id="support_admin_ticket" action="" enctype="multipart/form-data" novalidate>
                                        @csrf
                                        <div class="modal-header">
                                            <h4 class="modal-title" id="myLargeModalLabel">Add ticket</h4>
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label for=""> <strong>Subject</strong></label>
                                                        <input type="text" name="subject" class="form-control custom-form-control">
                                                        @error('subject')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong></strong>
                                                        </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label for=""> <strong>User</strong></label>
                                                        <select name="user_id[]"
                                                                class="form-control custom-form-control select2 @error('user_id') is-invalid @enderror"
                                                                required data-validation-required-message="This field is required"
                                                                multiple="multiple">
                                                            <option value="">username</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <div class="controls">
                                                            <textarea style="height: 200px" name="message" class="form-control summernote @error('message') is-invalid @enderror" placeholder="Message" required data-validation-required-message="This field is required">{{ old('message') }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <div class="form-actions">
                                                <button type="button"  onclick="submit_ticket(this)" class="btn btn-sm btn-primary custom-button"> <i class="fa fa-check"></i> Save</button>
                                            </div>
                                            <button type="button" class="btn  btn-sm btn-danger custom-button" data-dismiss="modal">Close</button>
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
