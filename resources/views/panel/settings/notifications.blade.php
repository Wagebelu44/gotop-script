@extends('layouts.panel')

@section('content')
    @php
        $resource = 'admin.setting.notification.';
    @endphp


    <div class="row all-mt-30">
        @include('panel.settings.navbar')

        <div class="col-md-8">
            <div class="card panel-default">
                @if($page == 'index')
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
                                @foreach($data as $userNotification)
                                    @if ($userNotification->type == 1)
                                    <tr class="settings-emails-row">
                                        <td>
                                            <div class="settings-emails-row-name">{{ $userNotification->title }}</div>
                                            <div class="settings-emails-row-description">
                                                {{ $userNotification->description }}
                                            </div>
                                        </td>
                                        <td>{{ $userNotification->status == 'active' ? 'Enabled' : 'Disabled' }}</td>
                                        <td class="settings-emails-td-actions">
                                            <a href="{{ route($resource.'edit', $userNotification->id) }}"  class="btn btn-xs btn-default edit-module">
                                                Edit
                                            </a>
                                        </td>
                                    </tr>
                                    @endif
                                @endforeach
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
                                @foreach($data as $staffNotification)
                                    @if ($staffNotification->type == 2)
                                        <tr class="settings-emails-row">
                                            <td>
                                                <div class="settings-emails-row-name">{{ $staffNotification->title }}</div>
                                                <div class="settings-emails-row-description">
                                                    {{ $staffNotification->description }}
                                                </div>
                                            </td>
                                            <td>{{ $staffNotification->status == 'active' ? 'Enabled' : 'Disabled' }}</td>
                                            <td class="settings-emails-td-actions">
                                                <a href="{{ route($resource.'edit', $staffNotification->id) }}"  class="btn btn-xs btn-default edit-module">
                                                    Edit
                                                </a>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
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
                        <a class="btn btn-default" href="javascript:void(0);" data-toggle="modal" data-target="#cmsStaffEmailModal">
                            Add email
                        </a>
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
                    </div>
                </div>
                @elseif($page == 'edit')
                <div class="card-body">
                    <form action="{{ route($resource.'update', $data->id) }}" method="post" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="relative">
                            <div class="form-group">
                                <label class="control-label" for="subject">Subject</label>
                                <input type="text" id="subject" class="form-control" name="subject" value="{{ old('subject', isset($data) ? $data->subject : '') }}" required>
                                @error('subject')
                                <span role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label class="control-label" for="status">Status</label>
                                <select class="form-control" name="status" id="status" required>
                                    <option value="active" {{ old('status', isset($data) ? $data->status : '') == 'active' ? 'selected' : '' }}>Enabled</option>
                                    <option value="inactive" {{ old('status', isset($data) ? $data->status : '') == 'inactive' ? 'selected' : '' }}>Disabled</option>
                                </select>
                                @error('status')
                                <span role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label class="control-label" for="body">Body</label>
                                <textarea id="body" class="form-control" name="body" rows="5" required>{{ old('body', isset($data) ? $data->body : '') }}</textarea>
                            </div>

                            <div class="btn-group" role="group">
                                <a href="#" id="btn-test" class="btn btn-default">
                                    Send test
                                </a>
                                <a href="#" id="btn-reset" class="btn btn-default disabled">
                                    Reset
                                </a>
                            </div>

                        </div>

                        <hr>
                        <button type="submit" class="btn btn-primary" name="save-button">Save changes</button>
                        <a class="btn btn-default" href="{{ route($resource.'index') }}">Cancel</a>
                    </form>
                </div>
                @endif

            </div>
        </div>
    </div>
@endsection
