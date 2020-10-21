@extends('layouts.panel')

@section('content')
    @php
        $resource = 'admin.setting.provider.';
    @endphp

    <div class="row all-mt-30">
        @include('panel.settings.navbar')

        <div class="col-md-8">
            <div class="card panel-default">
                <div class="card-body">
                    @if($setting->panel_type == 'Main')
                        <button type="button" class="btn btn-default m-b add-page"  data-toggle="modal" data-target="#cmsPaymentMethodAddPopUp">Add Provider</button>
                    @endif

                    <table class="table">
                        <thead>
                            <tr>
                                <th class="p-l">Provider</th>
                                <th>Balance</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody class="payment-method-list ui-sortable-handle">
                        <tbody class="payment-method-list ui-sortable-handle">
                            @foreach($data as $provider)
                            <tr>
                                <td class="p-l">{{ $provider->domain }}</td>
                                <td class="p-l"> 
                                    @if ($provider->balance !=null)
                                        @if (isset($provider->balance['balance']))
                                            ${{$provider->balance['balance']}}
                                        @endif
                                    @endif
                                </td>
                                <td class="p-r text-center">
                                    <button data-url="{{ route($resource.'edit', $provider->id) }}" data-id="{!! $provider->id !!}" class="edit btn btn-default m-t-20">
                                        Edit
                                    </button>
                                </td>
                            </tr>
                            @endforeach
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
                <form class="form-material" id="moduleEditForm" method="post" action="{{ route($resource.'store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h4 class="modal-title" id="ModalLabel">Add Provider</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    </div>

                    <div class="modal-body" id="modalBody">
                        <div class="form-group form-group__languages">
                            <label class="control-label" for="domain">Domain </label>
                            <input type="text" class="form-control" name="domain" id="domain" required>
                            @error('domain')
                            <span role="alert">
                                <strong>{{ $message }}</strong>
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

                        <div class="form-group">
                            <label class="control-label" for="status">Status</label>
                            <select class="form-control" name="status" id="status">
                                <option value="Active">Active</option>
                                <option value="Deactivated">Deactivated</option>
                            </select>
                            @error('status')
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
        </div>
    </div>
    <!--End:Create Modal-->

    <div class="modal fade in" id="cmsPaymentMethodEditPopUp" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="ModalEditLabel">Update Provider</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <form class="form-material" id="editFormPro" method="post" action="">
                    @method('PUT')
                    @csrf
                    <div class="modal-body" id="modalBody">

                        <div class="form-group">
                            <label class="control-label" for="edit_domain">Domain </label>
                            <input type="text" class="form-control" name="domain" id="edit_domain">
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="edit_url">URL </label>
                            <input type="text" class="form-control" name="url" id="edit_url">
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="edit_api_key">API Key </label>
                            <input type="text" class="form-control" name="api_key" id="edit_api_key">
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="edit_status">Status</label>
                            <select class="form-control" name="status" id="edit_status">
                                <option value="Active">Active</option>
                                <option value="Deactivated">Deactivated</option>
                            </select>
                        </div>
                    </div>
                </form>
                <div class="modal-footer">
                    <div class="col-md-10 submit-update-section">
                        <div class="form-actions pull-right">
                            <form id="deleteFormPro" method="post" action="">
                                @method('DELETE')
                                @csrf
                                <button type="button" onclick="document.getElementById('editFormPro').submit();" class="btn btn-primary"> <i class="fa fa-check"></i> Update Provider</button>
                                <button type="button" class="btn btn-danger waves-effect text-left" data-dismiss="modal" >Cancel</button>
                                
                                @if($setting->panel_type == 'Main')
                                    <button type="submit" onclick="return confirm('Are you sure...?')" class="btn btn-secondary waves-effect text-right">Delete</button>
                                @endif
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    $(document).on('click', '.edit', function (e) {
        e.preventDefault();
        let url = $(this).attr('data-url');
        let id = $(this).attr('data-id');
        $.ajax({
            url:url,
            type:"GET",
            dataType:"JSON",
            success(response) {
                if (response.status === 'success'){
                    $('#cmsPaymentMethodEditPopUp').modal('show');
                    $("#edit_domain").val(response.data.domain);
                    $("#edit_url").val(response.data.api_url);
                    $("#edit_api_key").val(response.data.api_key);
                    $("#edit_status").val(response.data.status);
                    let updateUrl = "{{ url('admin/setting/provider') }}/"+id;
                    $('#editFormPro').attr('action', updateUrl);
                    $('#deleteFormPro').attr('action', updateUrl);
                }

            }
        })
    })
</script>
@endsection
