@extends('layouts.panel')

@section('styles')
    <style>
        .row-disable {background: #e2e2e2;color: #c5c5c5;}
    </style>
@endsection

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
                            @if(!empty($paymentMethodList))
                                @foreach($paymentMethodList as $key => $value)
                                <tr class="{{ $value->visibility == 'enabled' ? '':'row-disable' }}">
                                    <td>
                                        <div class="settings-menu-drag">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Drag-Handle</title><path d="M7 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm6-8c1.104 0 2-.896 2-2s-.896-2-2-2-2 .896-2 2 .896 2 2 2zm0 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2z"></path></svg>
                                        </div>
                                        {{ $value->name }}
                                    </td>
                                    <td>{{ $value->method_name }}</td>
                                    <td>{{ $value->minimum }}</td>
                                    <td>{{ $value->maximum }}</td>
                                    <td>{{ $value->new_user_status == 'active' ? 'Allowed' : 'Not Allowed' }}</td>
                                    <td class="text-center">
                                        <div class="setting-switch setting-switch-table">
                                            <label class="switch">
                                                <input type="checkbox" value="{{ $value->visibility }}" class="toggle-page-visibility" {{ $value->visibility == 'enabled' ? 'checked' : '' }}  name="page_status" onclick="isActiveInactive({{ $key }},{{ $value->id }})" id="page_status_{{ $key }}">
                                                <span class="slider round"></span>
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <a class="btn btn-default btn-xs" href="javascript:void(0)" onclick="editPaymentMethod('{{ $value->id }}')" >Edit</a>
                                    </td>
                                </tr>
                                @endforeach
                            @endif
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
                <form class="form-material" id="addPayment" method="post" action="{{ route('admin.setting.payment.store') }}">
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
                                @if (!empty($globalPaymentList))
                                    @foreach ($globalPaymentList as $key => $value)
                                        <option value="{{ $value->id }}">{{ $value->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            @error('payment_method')
                               <strong class="text-danger">{{ $message }}</strong>
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
        </div>
    </div>

    <!--Payment Edit modal-->
    <div class="modal fade in" id="cmsPaymentMethodEditPopUp" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <form class="form-material" id="moduleEditForm" method="post" action="{{ route('admin.setting.payment.paymentUpdate') }}">
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
                                <option value="">Select New Users</option>
                            </select>
                        </div>
                        <hr>
                        <div id="payment_parameters">
                        </div>
                        <input type="hidden" id="id" name="payment_id" value=""/>
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
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        function isActiveInactive(sl, id) {
            var status_value = '';
            let status = $('#page_status_'+sl).val();
            $.ajax({
                type: "POST",
                dataType: "json",
                url: "{{ route('admin.setting.payment.updateStatus') }}",
                data: {'status': status, 'id': id, "_token": "{{ csrf_token() }}"},
                success: function (response) {
                    if(response.status === 200){
                        location.reload();
                    }
                }
            });
        }

        function editPaymentMethod(id) {
            $.ajax({
                type: "POST",
                dataType: "json",
                url: "{{ route('admin.setting.payment.paymentEdit') }}",
                data: {'id': id, "_token": "{{ csrf_token() }}"},
                success: function (response) {
                    var newUser = '<option value="">Select New Users</opton>';
                    if (response.payment_method.new_user_status === 'Active') {
                        newUser += '<option value="Active" selected>Allowed</option>'+
                            '<option value="Deactivated">Not Allowed</option>';
                    } else {
                        newUser += '<option value="Active">Allowed</option>'+
                            '<option value="Deactivated" selected>Not Allowed</option>';
                    }
                    var parametersHTMl = '';
                    for (var i = 0; i < response.payment_details.length; i++) {
                        var parameter_value = response.payment_details[i].value != null ? response.payment_details[i].value : '';
                        parametersHTMl +='<input type="hidden" name="payment_details[payment]['+i+'][form_label]" value="'+response.payment_details[i].form_label+'">';
                        parametersHTMl +='<input type="hidden" name="payment_details[payment]['+i+'][key]" value="'+response.payment_details[i].key+'">';
                        parametersHTMl +='<div class="form-group form-group__languages">'+
                            '<label class="control-label" for="input_'+i+'">'+response.payment_details[i].form_label+'</label>'+
                            '<input type="text" name="payment_details[payment]['+i+'][value]" id="input_'+i+'" class="form-control" value="'+parameter_value+'" required/>'+
                            '</div>';
                    }
                    $('#payment_parameters').html(parametersHTMl);
                    $('#new_users').html(newUser);
                    $('#ModalEditLabel').html(response.payment_method.method_name+' (ID: '+response.payment_method.id+')');
                    $('#method_name').val(response.payment_method.method_name);
                    $('#minimum').val(response.payment_method.minimum);
                    $('#maximum').val(response.payment_method.maximum);
                    $('#id').val(response.payment_method.id);
                    $('#global_methods_id').val(response.payment_method.global_payment_method_id);
                    $('#cmsPaymentMethodEditPopUp').modal("show");
                }
            });
        }
    </script>
@endsection
