@extends('layouts.panel')

@section('content')
    <div class="row">
        @include('panel.settings.navbar')

        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <div class="settings-emails-block">
                        <div class="settings-emails-block-title">
                            Active
                        </div>
                        <div class="settings-emails-block-body">
                            <table class="module-table">
                                <thead>
                                <tr>
                                    <th class="settings-emails-th-name"></th>
                                    <th class="settings-emails-th-actions"></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr class="settings-emails-row">
                                    <td>
                                        <div class="settings-emails-row-name">Affiliate system</div>
                                        <div class="settings-emails-row-description">
                                            <span>
                                                Existing users (affiliates) invite new users (referrals) and get commissions from all their payments. Affiliates may request payouts when they save the minimum payout.
                                            </span>
                                        </div>
                                    </td>
                                    <td class="settings-emails-td-actions">
                                        <a href="javascript:void(0)" class="btn btn-xs btn-default edit-module pull-right" data-toggle="modal" data-target="#cmsModuleEditPopUp" data-title="Affiliate system" data-module="1">
                                            Edit
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--Edit modal-->
    <div class="modal fade in" id="cmsModuleEditPopUp" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <form class="form-material" id="moduleEditForm" method="post" action="#" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="modal-header">
                        <h4 class="modal-title" id="ModalLabel">Update menu item</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    </div>
                    <div class="modal-body" id="modalBody"></div>
                    <div class="modal-footer">
                        <div class="col-md-6 submit-update-section">
                            <div class="form-actions">
                                <button type="submit" class="btn btn-success"> <i class="fa fa-check"></i> Update</button>
                                <button type="button" class="btn btn-danger waves-effect text-left" data-dismiss="modal" >Close</button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-actions" id="deactive-btn" style="display: none">
                                <input type="hidden" id="edit_id" value=""/>
                                <a class="btn btn-default waves-effect text-left" data-toggle="confirmation" data-original-title="" title="">Deactivate</a>
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
