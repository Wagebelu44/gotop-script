@extends('layouts.panel')

@section('content')
    <div class="row">
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
                            <tr>
                                <td class="p-l">1</td>
                                <td>100%</td>
                                <td>PayOP</td>
                                <td>100.00</td>
                                <td>Enabled</td>
                                <td class="p-r text-right">
                                    <a class="btn btn-default btn-xs" href="javascript:void(0)" data-toggle="modal" data-target="#cmsBonusesEditPopUp">Edit</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade in" id="cmsBonusesPopUp" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <form class="form-material" id="menuForm" method="post" action="" enctype="multipart/form-data">
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
                                <select class="form-control" id="payment_method_id" name="payment_method_id" aria-required="true">
                                    <option value="">Select payment method</option>
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
                                    <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Disabled</option>
                                    <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>Enabled</option>
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
                            <button type="submit" class="btn btn-success"> <i class="fa fa-check"></i> Save Change</button>
                        </div>
                        <button type="button" class="btn btn-danger waves-effect text-left" data-dismiss="modal">Close</button>
                    </div>

                </form>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!--End:Create Modal-->

    <!--Start:Edit Modal-->
    <div class="modal fade in" id="cmsBonusesEditPopUp" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myEditModalLabel">Update bonus</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <div class="modal-body">
                        <div class="form-group form-group__languages">
                            <label class="control-label" for="edit_bonus_amount">Bonus amount</label>
                            <div class="input-group">
                                <input type="number" id="edit_bonus_amount" class="form-control" name="edit_bonus_amount" min="0.01" step="0.01" max="100" value="" aria-required="true">
                                <div class="input-group-addon">%</div>
                            </div>
                            <span role="alert">
                                <strong id="error_edit_bonus_amount"></strong>
                            </span>
                        </div>
                        <div class="form-group">
                            <label for="menu_link" class="control-label" for="edit_payment_method_id">For method</label>
                            <select class="form-control" id="edit_payment_method_id" name="edit_payment_method_id" aria-required="true">
                                <option value="">Select payment method</option>
                            </select>
                            <span role="alert">
                                <strong id="error_edit_payment_method_id"></strong>
                            </span>
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="edit_deposit_from">Deposit from</label>
                            <input type="number" class="form-control" name="edit_deposit_from" id="edit_deposit_from" min="0.01" step="0.01" max="100" value="" aria-required="true">
                            <span role="alert">
                                <strong id="error_edit_deposit_from"></strong>
                            </span>
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="status">Status</label>
                            <select class="form-control" name="edit_status" id="edit_status" aria-required="true">
                                <option value="0">Disabled</option>
                                <option value="1">Enabled</option>
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
                        <a href="javascript:void(0)" class="btn btn-success"> <i class="fa fa-check"></i> Save Change</a>
                    </div>
                    <button type="button" class="btn btn-danger waves-effect text-left" data-dismiss="modal">Close</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!--End:Edit Modal-->
@endsection
