@extends('layouts.panel')
@section('content')
<!-- ============================================================== -->
<!-- Start Page Content -->
<!-- ============================================================== -->
<!-- basic table -->
<div class="row">
   <div class="col-12">
      <div class="material-card card">
         <div class="card-body">
            <div class="row">
               <div class="col-md-6">
                  <h4 class="card-title text-uppercase">Users export</h4>
               </div>
               <div class="col-md-6">
               </div>
            </div>
            <div class="material-card card">
               <div class="card-body">
                  <form class="form-material" id="users-export-form" method="post" action="{{ url('admin/exportedUser') }}" novalidate>
                    @csrf
                     <div class="modal bs-example-modal-lg" id="customizeColumns" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                           <div class="modal-content">
                              <div class="modal-header">
                                 <h4 class="modal-title" id="myLargeModalLabel">Customize columns</h4>
                                 <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                              </div>
                              <div class="modal-body">
                                 <p>Select the columns that you want to include in your file</p>
                                 <ul class="list-group customize-fields__list">
                                    <li class="list-group-item">
                                       <div class="checkbox">
                                          <label>
                                          <input class="column_item" type="checkbox" name="include_columns[]" value="id" checked=""> ID
                                          </label>
                                       </div>
                                    </li>
                                    <li class="list-group-item">
                                       <div class="checkbox">
                                          <label>
                                          <input class="column_item" type="checkbox" name="include_columns[]" value="username" checked=""> Username
                                          </label>
                                       </div>
                                    </li>
                                    <li class="list-group-item">
                                       <div class="checkbox">
                                          <label>
                                          <input class="column_item" type="checkbox" name="include_columns[]" value="email" checked=""> Email
                                          </label>
                                       </div>
                                    </li>
                                    <li class="list-group-item">
                                       <div class="checkbox">
                                          <label>
                                          <input class="column_item" type="checkbox" name="include_columns[]" value="name" checked=""> Name
                                          </label>
                                       </div>
                                    </li>
                                    <li class="list-group-item">
                                       <div class="checkbox">
                                          <label>
                                          <input class="column_item" type="checkbox" name="include_columns[]" value="skype_name" checked=""> Skype
                                          </label>
                                       </div>
                                    </li>
                                    <li class="list-group-item">
                                       <div class="checkbox">
                                          <label>
                                          <input class="column_item" type="checkbox" name="include_columns[]" value="balance" checked=""> Balance
                                          </label>
                                       </div>
                                    </li>
                                    <li class="list-group-item">
                                       <div class="checkbox">
                                          <label>
                                          <input class="column_item" type="checkbox" name="include_columns[]" value="spent" checked=""> Spent
                                          </label>
                                       </div>
                                    </li>
                                    <li class="list-group-item">
                                       <div class="checkbox">
                                          <label>
                                          <input class="column_item" type="checkbox" name="include_columns[]" value="status" checked=""> Status
                                          </label>
                                       </div>
                                    </li>
                                    <li class="list-group-item">
                                       <div class="checkbox">
                                          <label>
                                          <input class="column_item" type="checkbox" name="include_columns[]" value="created_at" checked=""> Created
                                          </label>
                                       </div>
                                    </li>
                                    <li class="list-group-item">
                                       <div class="checkbox">
                                          <label>
                                          <input class="column_item" type="checkbox" name="include_columns[]" value="last_login_at" checked=""> Last auth                                </label>
                                       </div>
                                    </li>
                                 </ul>
                              </div>
                              <div class="modal-footer">
                                 <div class="form-actions">
                                    <button type="submit" class="btn btn-success" data-dismiss="modal"> <i class="fa fa-check"></i> Save</button>
                                 </div>
                                 <button type="button" class="btn btn-danger waves-effect text-left" data-dismiss="modal">Close</button>
                              </div>
                           </div>
                           <!-- /.modal-content -->
                        </div>
                        <!-- /.modal-dialog -->
                     </div>
                     @csrf
                     <div class="row">
                        <div class="col">
                           <div class="form-group">
                              <div class="controls">
                                 <input type="text" name="from"  class="form-control datepicker @error('from') is-invalid @enderror" placeholder="From" value="{{ old('from') }}" required data-validation-required-message="This field is required">
                                 @error('from')
                                 <span class="invalid-feedback" role="alert">
                                 <strong>{{ $message }}</strong>
                                 </span>
                                 @enderror
                              </div>
                           </div>
                        </div>
                        <div class="col">
                           <div class="form-group">
                              <div class="controls">
                                 <input type="text" name="to"  class="form-control datepicker @error('to') is-invalid @enderror" placeholder="To" value="{{ old('to') }}" required data-validation-required-message="This field is required">
                                 @error('to')
                                 <span class="invalid-feedback" role="alert">
                                 <strong>{{ $message }}</strong>
                                 </span>
                                 @enderror
                              </div>
                           </div>
                        </div>
                        <div class="col">
                           <div class="form-group">
                              <div class="controls">
                                 <select name="status[]" class="form-control select2 @error('status') is-invalid @enderror" required data-validation-required-message="This field is required" multiple>
                                    <option value="all" selected>All statuses</option>
                                    <option {{ old('status') == 'active' ? 'selected' : '' }} value="active">Active</option>
                                    <option {{ old('status') == 'inactive' ? 'selected' : '' }} value="inactive">Suspended</option>
                                    <option {{ old('status') == 'pending' ? 'selected' : '' }} value="pending">Unconfirmed</option>
                                 </select>
                                 @error('status')
                                 <span class="invalid-feedback" role="alert">
                                 <strong>{{ $message }}</strong>
                                 </span>
                                 @enderror
                              </div>
                           </div>
                        </div>
                        <div class="col">
                           <div class="form-group">
                              <div class="controls">
                                 <select name="format" class="form-control @error('format') is-invalid @enderror" required data-validation-required-message="This field is required">
                                    <option selected>Choose Format</option>
                                    <option {{ old('format') == 'xml' ? 'selected' : '' }} value="xml">XML</option>
                                    <option {{ old('format') == 'json' ? 'selected' : '' }} value="json">JSON</option>
                                    <option {{ old('format') == 'csv' ? 'selected' : '' }} value="csv">CSV</option>
                                 </select>
                                 @error('format')
                                 <span class="invalid-feedback" role="alert">
                                 <strong>{{ $message }}</strong>
                                 </span>
                                 @enderror
                              </div>
                           </div>
                        </div>
                        <div class="col">
                           <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#customizeColumns">Customize columns</button>
                        </div>
                        <div class="col">
                           <div class="form-actions">
                              <button type="submit" class="btn btn-success"> <i class="fa fa-check"></i> Create file</button>
                           </div>
                        </div>
                     </div>
                  </form>
               </div>
            </div>
            <div class="material-card card">
               <div class="card-body">
                  <div class="table-responsive">
                     <table id="zero_config" class="table table-striped border table-hover">
                        <thead>
                           <tr>
                              <th>From</th>
                              <th>To</th>
                              <th>Status</th>
                              <th>Format</th>
                              <th>Actions</th>
                           </tr>
                        </thead>
                        <tbody>
                           @foreach($exported_users as $item)
                           <tr>
                              <td>{{ $item->from }}</td>
                              <td>{{ $item->to }}</td>
                              <td>{{ rtrim(implode(', ', array_map(function ($value) { return ucfirst($value); }, unserialize($item->status)))) }}</td>
                              <td>{{ strtoupper($item->format) }}</td>
                              <td>
                                 <form method="post" action="{{ route('admin.users.exported_user.download', $item->id) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-success">Download</button>
                                 </form>
                              </td>
                           </tr>
                           @endforeach
                        </tbody>
                     </table>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
@endsection
@section('scripts')
<script>
   $(document).ready(function () {
        $('.datepicker').datepicker();
    });
   
    let $select2 = $(".select2");
   
    $select2.select2({
        width: '100%',
        allowClear: true,
        placeholder: 'Select status'
    });
   
    $select2.on('select2:select', function (e) {
        const data = e.params.data;
   
        // If selected all remove rest options else remove all
        if (data.id == 'all') {
            $select2.val(data.id).trigger('change');
        } else {
            const idToRemove = 'all';
   
            const values = $select2.val();
            if (values) {
                const i = values.indexOf(idToRemove);
                if (i >= 0) {
                    values.splice(i, 1);
                    $select2.val(values).change();
                }
            }
        }
    });
</script>
@endsection