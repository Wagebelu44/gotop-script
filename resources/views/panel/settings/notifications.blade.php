@extends('layouts.panel')

@section('content')
    <div class="row">
        @include('panel.settings.navbar')

        <div class="col-md-8">
            <div class="card panel-default">
                <div class="card-body">
                    <div class="settings-emails-block">
                        <div class="settings-emails-block-title">
                            Users notifications
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
                                            <div class="settings-emails-row-name">Welcome</div>
                                            <div class="settings-emails-row-description">
                                                Sent to new users when their account is created.
                                            </div>
                                        </td>
                                        <td>Enabled</td>
                                        <td class="settings-emails-td-actions">
                                            <a href=""  class="btn btn-xs btn-default edit-module">
                                                Edit
                                            </a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="settings-emails-block">
                        <div class="settings-emails-block-title">
                            Staff notifications
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
                                            <div class="settings-emails-row-name">Payment received</div>
                                            <div class="settings-emails-row-description">
                                                Sent to staff when a user adds funds automatically.
                                            </div>
                                        </td>
                                        <td>Disabled</td>
                                        <td class="settings-emails-td-actions">
                                            <a href="" class="btn btn-xs btn-default edit-module">
                                                Edit
                                            </a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="settings-emails-block">
                        <div class="settings-emails-block-title">
                            Staff Email
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
                                            <div class="settings-emails-row-name">thesocialmediagrowthh@gmail.com</div>
                                        </td>
                                        <td>10 notifications</td>
                                        <td class="settings-emails-td-actions">
                                            <a href="javascript:void(0)" class="btn btn-xs btn-default edit-module">
                                                Edit
                                            </a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="settings-emails-block-acitons">
                        <a class="btn btn-default" href="javascript:void();" data-toggle="modal" data-target="#cmsStaffEmailModal">
                            Add email
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--Start Create modal-->
    <div class="modal fade in" id="cmsStaffEmailModal" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <form class="form-material" id="moduleEditForm" method="post" action="" enctype="multipart/form-data">
                    @csrf

                    <div class="modal-header">
                        <h4 class="modal-title" id="ModalLabel">Add email</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    </div>

                    <div class="modal-body" id="modalBody">

                        <div class="form-group">
                            <label class="control-label" for="email"><b>Email</b></label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" class="form-control" />
                            @error('email')
                            <span role="alert">
                                    <strong></strong>
                                </span>
                            @enderror
                        </div>

                        <div class="settings-emails-list">
                            <div class="settings-emails-list-title">
                                Notifications
                            </div>
                            <div class="settings-emails-list-body">
                                <div class="settings-emails-list-row">
                                    <div class="settings-emails-list-row-title">Payment received</div>
                                    <div class="settings-emails-list-row-action">
                                        <div class="setting-switch switch-custom-table">
                                            <label class="switch">
                                                <input type="checkbox" class="switch-input" name="payment_received" id="payment_received" @if (old('payment_received')) checked @endif>
                                                <span class="slider round"></span>
                                            </label>
                                        </div>

                                    </div>
                                </div>

                                <div class="settings-emails-list-row">
                                    <div class="settings-emails-list-row-title">New manual orders</div>
                                    <div class="settings-emails-list-row-action">
                                        <div class="setting-switch setting-switch-table">
                                            <label class="switch">
                                                <input type="checkbox" class="switch-input" name="new_manual_orders" id="new_manual_orders"  @if (old('new_manual_orders')) checked @endif>
                                                <span class="slider round"></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="settings-emails-list-row">
                                    <div class="settings-emails-list-row-title">Fail orders</div>
                                    <div class="settings-emails-list-row-action">
                                        <div class="setting-switch setting-switch-table">
                                            <label class="switch">
                                                <input type="checkbox" class="switch-input" name="fail_orders" id="fail_orders" @if (old('fail_orders')) checked @endif>
                                                <span class="slider round"></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="settings-emails-list-row">
                                    <div class="settings-emails-list-row-title">New messages</div>
                                    <div class="settings-emails-list-row-action">
                                        <div class="setting-switch setting-switch-table">
                                            <label class="switch">
                                                <input type="checkbox" class="switch-input" name="new_messages" id="new_messages" @if (old('new_messages')) checked @endif>
                                                <span class="slider round"></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="settings-emails-list-row">
                                    <div class="settings-emails-list-row-title">New manual payout</div>
                                    <div class="settings-emails-list-row-action">
                                        <div class="setting-switch setting-switch-table">
                                            <label class="switch">
                                                <input type="checkbox" class="switch-input" name="new_manual_payout" id="new_manual_payout" @if (old('new_manual_payout')) checked @endif>
                                                <span class="slider round"></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <input type="hidden" name="add_email_popup" value="1"/>
                    </div>

                    <div class="modal-footer">
                        <div class="col-md-6 submit-update-section">
                            <div class="form-actions">
                                <button type="submit" class="btn btn-success"> <i class="fa fa-check"></i> Save Changes</button>
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
