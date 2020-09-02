@extends('layouts.panel')

@section('content')
    <div class="row all-mt-30">
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
                                        <a href="javascript:void(0)" class="btn btn-xs btn-default edit-module pull-right" onclick="getModuleData('affiliate')" data-title="Affiliate system" data-module="1">
                                            Edit
                                        </a>
                                    </td>
                                </tr>
                                <tr class="settings-emails-row">
                                    <td>
                                        <div class="settings-emails-row-name">Child panels selling</div>
                                        <div class="settings-emails-row-description">
                                            <span>
                                                A panel with limited features that can have only your panel as a service provider. Users can order child panels on your panel.
                                            </span>
                                        </div>
                                    </td>
                                    <td class="settings-emails-td-actions">
                                        <a href="javascript:void(0)" class="btn btn-xs btn-default edit-module pull-right" onclick="getModuleData('child_panels')" data-title="Affiliate system" data-module="1">
                                            Edit
                                        </a>
                                    </td>
                                </tr>
                                <tr class="settings-emails-row">
                                    <td>
                                        <div class="settings-emails-row-name">Free balance</div>
                                        <div class="settings-emails-row-description">
                                            <span>
                                                Set up a one-time free balance amount for new panel users after signing up.
                                            </span>
                                        </div>
                                    </td>
                                    <td class="settings-emails-td-actions">
                                        <a href="javascript:void(0)" class="btn btn-xs btn-default edit-module pull-right" onclick="getModuleData('free_balance')" data-title="Affiliate system" data-module="1">
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

    <!--Edit Affiliate modal-->
    <div class="modal fade in" id="cmsModuleEditAffiliatePopUp" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <form class="form-material" id="affiliateModuleEditForm" method="post" action="{{ route('admin.setting.module.update') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h4 class="modal-title" id="ModalLabel">Affiliate system</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    </div>
                    <div class="modal-body" id="modalBody">
                        <input type="hidden" id="affiliate" name="type" value="affiliate"/>
                        <div class="form-group">
                            <label class="form-control-label">Commission rate, %</label>
                            <input type="number" id="commission_rate" class="form-control" name="commission_rate" required>
                        </div>
                        <div class="form-group">
                            <label class="form-control-label">Minimum payout</label>
                            <input type="number" id="amount" class="form-control" name="amount" required>
                        </div>
                        <div class="form-group">
                            <label class="form-control-label">Approve payouts</label>
                            <select class="form-control" name="approve_payout" id="approve_payout" aria-required="true">
                                <option value="auto">Auto</option>
                                <option value="manual">Manual</option>
                            </select>
                        </div>
                    </div>
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
        </div>
    </div>

    <!--Edit Child panel modal-->
    <div class="modal fade in" id="cmsModuleEditChildPopUp" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <form class="form-material" id="childModuleEditForm" method="post" action="{{ route('admin.setting.module.update') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h4 class="modal-title" id="ModalLabel">Child panels selling</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    </div>
                    <div class="modal-body" id="modalBody">
                        <input type="hidden" id="child_panels" name="type" value="child_panels"/>
                        <div class="form-group">
                            <label class="form-control-label">Price per month</label>
                            <input type="number" id="month_amount" class="form-control" name="amount" required>
                        </div>
                    </div>
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
        </div>
    </div>

    <!---Free balance modal--->
    <div class="modal fade in" id="cmsModuleEditFreeBalancePopUp" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <form class="form-material" id="freeBalanceModuleEditForm" method="post" action="{{ route('admin.setting.module.update') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="modal-header">
                        <h4 class="modal-title" id="ModalLabel">Free Balance</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    </div>
                    <div class="modal-body" id="modalBody">
                        <input type="hidden" id="free_balance" name="type" value="free_balance"/>
                        <div class="form-group">
                            <label class="form-control-label">Amount</label>
                            <input type="number" id="free_amount" class="form-control" name="amount" required>
                        </div>
                    </div>
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
        </div>
    </div>
@endsection

@section('scripts')
<script>
    function getModuleData(type) {
        $.ajax({
            type: "POST",
            dataType: "json",
            url: 'module-edit',
            data: {'type': type, "_token": "{{ csrf_token() }}"},
            success(response) {
                console.log(response)
                if (type == 'affiliate') {
                    $('#cmsModuleEditAffiliatePopUp').modal('show')
                    $('#commission_rate').val(response.data.commission_rate);
                    $('#amount').val(response.data.amount);
                    $('#approve_payout').val(response.data.approve_payout);
                }
                if (type == 'child_panels') {
                    $('#cmsModuleEditChildPopUp').modal('show')
                    $('#month_amount').val(response.data.amount);
                }
                if (type == 'free_balance') {
                    $('#cmsModuleEditFreeBalancePopUp').modal('show')
                    $('#free_amount').val(response.data.amount);
                }
            }
        })
    }
</script>
@endsection
