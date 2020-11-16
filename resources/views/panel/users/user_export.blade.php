@extends('layouts.panel')
@section('content')
<!-- ============================================================== -->
<!-- Start Page Content -->
<!-- ============================================================== -->
<!-- basic table -->
<div class="container-fluid all-mt-30">
   <div class="row">
      <div class="col-12">
         <div class="card card-body custom-card-body">
            <form  id="users-export-form" method="post" action="{{ url('admin/exportedUser') }}" novalidate>
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
                              {{-- <li class="list-group-item">
                                 <div class="checkbox">
                                    <label>
                                    <input class="column_item" type="checkbox" name="include_columns[]" value="name" checked=""> Name
                                    </label>
                                 </div>
                              </li> --}}
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
               <div class="row">
                  <div class="col">
                     <div class="form-group">
                           <label for=""> <strong>From</strong> </label>
                           <input type="text" name="from"  data-date-end-date="0d" class="form-control custom-input datepicker @error('from') is-invalid @enderror" value="{{ old('from') }}" required data-validation-required-message="This field is required">
                           @error('from')
                           <span class="invalid-feedback" role="alert">
                           <strong>{{ $message }}</strong>
                           </span>
                           @enderror
                     </div>
                  </div>
                  <div class="col">
                     <div class="form-group">
                           <label for=""> <strong>To</strong> </label>
                           <input type="text" data-date-end-date="0d" name="to"  class="form-control custom-input datepicker @error('to') is-invalid @enderror" value="{{ old('to') }}" required data-validation-required-message="This field is required">
                           @error('to')
                           <span class="invalid-feedback" role="alert">
                              <strong>{{ $message }}</strong>
                           </span>
                           @enderror
                     </div>
                  </div>
                  <div class="col">
                     <div class="form-group">
                           <label for=""> <strong>Status</strong> </label>
                              <div class="dropdown custom-drop-down" input-name="status">
                              <button class="btn custom-input custom-drop-down-button btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton"  aria-haspopup="true" aria-expanded="false">
                                 <span class="button-text-holder">Status (<span class="count">0</span>)</span>
                              </button>
                              <div class="dropdown-menu"  aria-labelledby="dropdownMenuButton">
                                 <a class="dropdown-item"  onclick="itemAction('active', this)"><span>active</span>  <span class="check-mark"><i class="fas fa-check"></i></span> </a>
                                 <a class="dropdown-item"  onclick="itemAction('inactive', this)"><span>inactive</span> <span class="check-mark"><i class="fas fa-check"></i></span> </a>
                                 <a class="dropdown-item"  onclick="itemAction('pending', this)"><span>pending</span>  <span class="check-mark"><i class="fas fa-check"></i></span> </a>
                              </div>
                              </div>
                           @error('status')
                           <span class="invalid-feedback" role="alert">
                           <strong>{{ $message }}</strong>
                           </span>
                           @enderror
                     </div>
                  </div>
                  <div class="col">
                     <div class="form-group">
                        <label for=""> <strong>Format</strong> </label>
                           <select name="format" class="form-control custom-input @error('format') is-invalid @enderror" required data-validation-required-message="This field is required">
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
                  <div class="col">
                     <button type="submit" style="position: relative;top: 29px;" class="btn btn-success theme-color custom-button"> <i class="fa fa-check"></i> Create file</button>
                  </div>
               </div>
               <button type="button" class="btn btn-link" data-toggle="modal" data-target="#customizeColumns">Customize columns</button>
            </form>
         </div>
         <div class="card card-body custom-card-body">
            <div class="table-responsive">
               <table id="zero_config" class="table">
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
                              <button type="submit" class="btn btn-success theme-color custom-button">Download</button>
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
@endsection
@section('scripts')
<script>
   $(document).ready(function () {
        $('.datepicker').datepicker();
    });
   
    $(".custom-drop-down-button").click(function(e){
       $(".custom-drop-down > .dropdown-menu").toggleClass('show');
    })

    function itemAction(status, obj) {
         $(obj).find('.check-mark').toggleClass('show');
         let coun = $(".custom-drop-down").find('.count').text();
         let total_length = $(".custom-drop-down > .dropdown-menu > a").length;
         let parent_name_attribute = $(obj).closest('.custom-drop-down').attr('input-name')
         if (typeof parent_name_attribute !== undefined && parent_name_attribute !== false) {
            if ($(`.custom-drop-down > input[value=${status}]`).length === 0) {
                  $(".custom-drop-down").append(`<input type="hidden" name="${parent_name_attribute}[]" value="${status}">`);
               } else {
                  $(".custom-drop-down").find(`input[value=${status}]`).remove();
               }
         }
         if ($(obj).find('.check-mark').hasClass('show')) {
            let increament = parseInt(coun) + 1;
            if (increament === total_length) {
               $(".custom-drop-down").find('.button-text-holder').text(`All ${parent_name_attribute}`);
            } else {
               $(".custom-drop-down").find('.button-text-holder').html(`Status (<span class="count">0</span>)`);
               $(".custom-drop-down").find('.count').text(increament);
            }
         } else {
            if (coun === '') {
               $(".custom-drop-down").find('.button-text-holder').html(`Status (<span class="count">${total_length - 1}</span>)`);
            } else {
               let dec = parseInt(coun) - 1;
               $(".custom-drop-down").find('.count').text(dec);
            }
         }
    }

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