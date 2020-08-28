@extends('layouts.panel')

@section('content')
    <div class="row">
        @include('panel.settings.navbar')

        <div class="col-md-8">
            <div class="card panel-default">
                <div class="card-body">
                    <button type="button" class="btn btn-default m-b add-page"  data-toggle="modal" data-target="#cmsPaymentMethodAddPopUp">Add Provider</button>
                    <table class="table">
                        <thead>
                        <tr>
                            <th class="p-l">Provider</th>
                            <th>Balance</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody class="payment-method-list ui-sortable-handle">
                            <tr id="">
                                <td class="p-l">developer-test</td>
                                <td>200</td>
                                <td class="p-r text-right">
                                    <a class="btn btn-default btn-xs" href="javascript:void(0)" data-toggle="modal" data-target="#cmsPaymentMethodEditPopUp">Edit</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!--Start:Create Modal-->
    <div class="modal fade in" id="cmsPaymentMethodAddPopUp" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form class="form-material" id="moduleEditForm" method="post" action="" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h4 class="modal-title" id="ModalLabel">Add Provider</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    </div>

                    <div class="modal-body" id="modalBody">
                        <div class="form-group form-group__languages">
                            <label class="control-label" for="domain">Domain </label>
                            <input type="text" class="form-control" name="domain" id="domain">
                            @error('domain')
                            <span role="alert">
                                    <strong></strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group form-group__languages">
                            <label class="control-label" for="url">URL </label>
                            <input type="text" class="form-control" name="url" id="url">
                            @error('url')
                            <span role="alert">
                                    <strong></strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group form-group__languages">
                            <label class="control-label" for="api_key">API Key </label>
                            <input type="text" class="form-control" name="api_key" id="api_key">
                            @error('api_key')
                            <span role="alert">
                                    <strong></strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="col-md-12 submit-update-section">
                            <div class="form-actions pull-right">
                                <button type="submit" class="btn btn-primary"> <i class="fa fa-check"></i> Add Provider</button>
                                <button type="button" class="btn btn-danger waves-effect text-left" data-dismiss="modal">Cancel</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!--End:Create Modal-->
    <div class="modal fade in" id="cmsPaymentMethodEditPopUp" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <form class="form-material" id="moduleEditForm" method="post" action="">
                    @method('put')
                    @csrf
                    <div class="modal-header">
                        <h4 class="modal-title" id="ModalEditLabel">Update Provider</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    </div>

                    <div class="modal-body" id="modalBody">
                        <div class="form-group form-group__languages">
                            <label class="control-label" for="edit_domain">Domain </label>
                            <input type="text" class="form-control" name="edit_domain" id="edit_domain">
                        </div>
                        <div class="form-group form-group__languages">
                            <label class="control-label" for="edit_url">URL </label>
                            <input type="text" class="form-control" name="edit_url" id="edit_url">
                        </div>
                        <div class="form-group form-group__languages">
                            <label class="control-label" for="edit_api_key">API Key </label>
                            <input type="text" class="form-control" name="edit_api_key" id="edit_api_key">
                        </div>
                        <input type="hidden" name="provider_domain_id" id="provider_domain_id">
                    </div>
                    <div class="modal-footer">
                        <div class="col-md-10 submit-update-section">
                            <div class="form-actions pull-right">
                                <button type="submit" class="btn btn-primary"> <i class="fa fa-check"></i> Update Provider</button>
                                <button type="button" class="btn btn-danger waves-effect text-left" data-dismiss="modal" >Cancel</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
    </div>
@endsection
