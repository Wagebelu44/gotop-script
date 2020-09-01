@extends('layouts.panel')

@section('content')
    <div class="row all-mt-30">
        @include('panel.settings.navbar')

        <div class="col-md-8">
            <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#cmsPaymentMethodAddPopUp">Add Payments</button>
            <div class="card panel-default" style="margin-top: 10px; border: 1px solid lightgray; border-radius: 5px;">
                <div class="col-md-12">
                    <div class="card-body">
                        <table class="table" style="border: 1px solid lightgray; border-radius: 5px;">
                            <thead>
                            <tr style="background-color: whitesmoke">
                                <th class="p-l">Method</th>
                                <th>Name</th>
                                <th>Min</th>
                                <th>Max</th>
                                <th>New users</th>
                                <th>Visibility</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>
                                    <div class="settings-menu-drag">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Drag-Handle</title><path d="M7 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm6-8c1.104 0 2-.896 2-2s-.896-2-2-2-2 .896-2 2 .896 2 2 2zm0 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2z"></path></svg>
                                    </div>
                                    Bank/Other
                                </td>
                                <td>Bank/Other</td>
                                <td>10.00</td>
                                <td>20.00</td>
                                <td>Allowed</td>
                                <td class="text-center">
                                    <div class="setting-switch setting-switch-table">
                                        <label class="switch">
                                            <input type="checkbox" class="toggle-page-visibility" name="page_status" id="page_status">
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </td>
                                <td>
                                    <a class="btn btn-default btn-xs" href="javascript:void(0)" data-toggle="modal" data-target="#cmsPaymentMethodEditPopUp">Edit</a>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--Start:Edit Modal-->
    <div class="modal fade in" id="cmsPaymentMethodAddPopUp" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <form class="form-material" id="moduleEditForm" method="post" action="" enctype="multipart/form-data">
                    @csrf

                    <div class="modal-header">
                        <h4 class="modal-title" id="ModalLabel">Add payment</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    </div>

                    <div class="modal-body" id="modalBody">
                        <div class="form-group form-group__languages">
                            <label class="control-label" for="payment_method">Payment method</label>
                            <select class="form-control" name="payment_method" id="payment_method" required>
                                <option value="">Select Payment Method</option>

                            </select>
                            @error('payment_method')
                            <span role="alert">

                                </span>
                            @enderror
                        </div>

                    </div>

                    <div class="modal-footer">
                        <div class="col-md-12 submit-update-section">
                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary"> <i class="fa fa-check"></i> Add Method</button>
                                <button type="button" class="btn btn-danger waves-effect text-left" data-dismiss="modal" >Cancel</button>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

    <!--Payment Edit modal-->
    <div class="modal fade in" id="cmsPaymentMethodEditPopUp" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <form class="form-material" id="moduleEditForm" method="post" action="" enctype="multipart/form-data">
                    @csrf

                    <div class="modal-header">
                        <h4 class="modal-title" id="ModalEditLabel">Add payment</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    </div>

                    <div class="modal-body" id="modalBody">

                        <div class="form-group form-group__languages">
                            <label class="control-label" for="payment_method">Method name</label>
                            <input type="text" name="method_name" id="method_name" class="form-control" value="" required/>
                        </div>

                        <div class="form-group form-group__languages">
                            <label class="control-label" for="minimum">Minimal payment</label>
                            <input type="number" name="minimum" id="minimum" class="form-control" value="" min="0" max="1000" step="0.01" required/>
                        </div>

                        <div class="form-group form-group__languages">
                            <label class="control-label" for="maximum">Maximal payment</label>
                            <input type="number" name="maximum" id="maximum" class="form-control" value="" min="0" max="1000" step="0.01" required/>
                        </div>

                        <div class="form-group form-group__languages">
                            <label class="control-label" for="New users">New users</label>
                            <select class="form-control" name="new_users" id="new_users" required>
                                <option value="">Selete New Users</option>
                            </select>
                        </div>
                        <hr>
                        <div id="payment_parameters">
                        </div>
                        <input type="hidden" id="id" name="id" value=""/>
                        <input type="hidden" id="global_methods_id" name="global_methods_id" value=""/>
                    </div>
                    <div class="modal-footer">
                        <div class="col-md-12 submit-update-section">
                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary"> <i class="fa fa-check"></i> Add Method</button>
                                <button type="button" class="btn btn-danger waves-effect text-left" data-dismiss="modal" >Cancel</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
@endsection
