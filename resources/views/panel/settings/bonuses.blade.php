@extends('layouts.panel')

@section('content')
    @php
        $resource = 'admin.setting.bonuses.';
    @endphp
    <div class="row all-mt-30">
        @include('panel.settings.navbar')

        <div class="col-md-8">
            <div class="card panel-default">
                <div class="card-body">
                    <a class="btn btn-default m-b add-page" href="javascript:void(0)" data-toggle="modal" data-target="#cmsBonusesPopUp">Add bonus</a>

                    <table class="table">
                        <thead>
                        <tr>
                            <th width="45%" class="p-l">ID</th>
                            <th>Bonus</th>
                            <th>Method</th>
                            <th>From</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($bonuses as $key => $bonuse)
                            <tr>
                                <td class="p-l">{{ $key+1 }}</td>
                                <td>
                                    @if (isset($bonuse->bonus_amount))
                                        {{ $bonuse->bonus_amount }}%
                                    @endif
                                </td>
                                <td>
                                    @if (isset($bonuse->globalPaymentMethod->name))
                                        {{ $bonuse->globalPaymentMethod->name }}
                                    @endif
                                </td>
                                <td>
                                    @if ( $bonuse->deposit_from )
                                        {{ $bonuse->deposit_from }}
                                    @endif
                                </td>
                                <td>
                                    @if ( $bonuse->status === 'Active' )
                                        Enabled
                                    @else
                                        Disabled
                                    @endif
                                </td>
                                <td class="p-r text-right">
                                    <button data-url="{{ route($resource.'edit', $bonuse->id) }}" data-id="{!! $bonuse->id !!}" class="edit btn btn-default m-t-20">
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

    <div class="modal fade in" id="cmsBonusesPopUp" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <form class="form-material" id="menuForm" method="post" action="{{ route($resource.'store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h4 class="modal-title" id="myLargeModalLabel">Add bonus</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    </div>
                    <div class="modal-body">
                        <div class="modal-body">
                            <div class="form-group form-group__languages">
                                <label class="control-label" for="bonus_amount">Bonus amount</label>
                                <div class="input-group">
                                    <input type="number" id="bonus_amount" class="form-control" name="bonus_amount" min="0.01" step="0.01" max="100" value="{{ old('bonus_amount') }}" aria-required="true">
                                    <div class="input-group-addon">%</div>
                                </div>
                                @error('bonus_amount')
                                <span role="alert">
                                        <strong></strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="menu_link" class="control-label" for="payment_method_id">For method</label>
                                <select class="form-control" id="payment_method_id" name="global_payment_method_id" aria-required="true">
                                    <option value="">Select payment method</option>
                                    @foreach($methodsName as $method)
                                        <option value="{{ $method->id }}">{{ $method->name }}</option>
                                    @endforeach
                                </select>
                                @error('payment_method_id')
                                <span role="alert">
                                        <strong></strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label class="control-label" for="deposit_from">Deposit from</label>
                                <input type="number" class="form-control" name="deposit_from" id="deposit_from" min="0.01" step="0.01" max="100" value="{{ old('deposit_from') }}"  aria-required="true">
                                @error('deposit_from')
                                <span role="alert">
                                        <strong></strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label class="control-label" for="status">Status</label>
                                <select class="form-control" name="status" id="status" aria-required="true">
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
                    </div>

                    <div class="modal-footer">
                        <div class="form-actions">
                            <button type="submit" class="btn btn-success"> <i class="fa fa-check"></i> Save</button>
                        </div>
                        <button type="button" class="btn btn-danger waves-effect text-left" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!--End:Create Modal-->

    <!--Start:Edit Modal-->
    <div class="modal fade in" id="cmsBonusesEditPopUp" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <form class="form-material" id="editFormBonus" method="post" action="">
                    @method('put')
                    @csrf
                    <div class="modal-header">
                        <h4 class="modal-title" id="myEditModalLabel">Update bonus</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    </div>
                    <div class="modal-body">
                        <div class="modal-body">
                            <div class="form-group form-group__languages">
                                <label class="control-label" for="edit_bonus_amount">Bonus amount</label>
                                <div class="input-group">
                                    <input type="number" id="edit_bonus_amount" class="form-control" name="bonus_amount" min="0.01" step="0.01" max="100" value="" aria-required="true">
                                    <div class="input-group-addon">%</div>
                                </div>
                                <span role="alert">
                                <strong id="error_edit_bonus_amount"></strong>
                            </span>
                            </div>
                            <div class="form-group">
                                <label for="menu_link" class="control-label" for="edit_payment_method_id">For method</label>
                                <select class="form-control" id="edit_payment_method_id" name="global_payment_method_id" aria-required="true">
                                    @foreach($methodsName as $method)
                                        <option value="{{ $method->id }}">{{ $method->name }}</option>
                                    @endforeach
                                </select>
                                <span role="alert">
                                <strong id="error_edit_payment_method_id"></strong>
                            </span>
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="edit_deposit_from">Deposit from</label>
                                <input type="number" class="form-control" name="deposit_from" id="edit_deposit_from" min="0.01" step="0.01" max="100" value="" aria-required="true">
                                <span role="alert">
                                <strong id="error_edit_deposit_from"></strong>
                            </span>
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="status">Status</label>
                                <select class="form-control" name="status" id="edit_status" aria-required="true">
                                    <option value="Active">Active</option>
                                    <option value="Deactivated">Deactivated</option>
                                </select>
                                <span role="alert">
                                <strong id="error_status"></strong>
                            </span>
                            </div>
                        </div>
                        <input type="hidden" id="edit_bonus_id"/>
                    </div>
                    <div class="modal-footer">
                        <div class="form-actions">
                            <button type="button" onclick="document.getElementById('editFormBonus').submit();" class="btn btn-primary"> <i class="fa fa-check"></i> Update</button>
                        </div>
                        <button type="button" class="btn btn-danger waves-effect text-left" data-dismiss="modal">Close</button>
                    </div>
                </form>
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
                        $('#cmsBonusesEditPopUp').modal('show');
                        $("#edit_bonus_amount").val(response.data.bonus_amount);
                        $("#edit_payment_method_id").val(response.data.global_payment_method_id);
                        $("#edit_deposit_from").val(response.data.deposit_from);
                        document.forms['editFormBonus'].elements['status'].value = response.data.status;
                        let updateUrl = "{{ url('admin/setting/bonuses') }}/"+id;
                        $('#editFormBonus').attr('action', updateUrl);
                    }

                }
            })
        })
    </script>
@endsection
