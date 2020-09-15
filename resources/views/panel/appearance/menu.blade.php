@extends('layouts.panel')

@section('content')
    <div class="container all-mt-30">
        <div class="row">
            @include('panel.appearance.nav')
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="settings-menu-title">Public</div>
                                <div class="settings-menu-description">Shown for any visitors</div>
                            </div>
                            <div class="col-md-8">
                                <table class="table table-bordered">
                                    <tbody class="tablecontents" id="public_menu">
                                    @if(!empty($menus))
                                        @foreach($menus as $key => $public_menu)
                                            @if ($public_menu->menu_link_type == 'Yes')
                                                <tr class="row1" data-id="{{ $public_menu->id }}">
                                                    <td>
                                                        <div class="settings-menu-drag">
                                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Drag-Handle</title><path d="M7 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm6-8c1.104 0 2-.896 2-2s-.896-2-2-2-2 .896-2 2 .896 2 2 2zm0 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2z"></path></svg>
                                                        </div>
                                                        <a href="{{ url($public_menu->page->url) }}" target="_blank">{{ $public_menu->menu_name }}</a>
                                                    </td>
                                                    <td class="p-r text-right">
                                                        <button data-url="{{ route('admin.appearance.menu.edit', $public_menu->id) }}" data-id="{!! $public_menu->id !!}" class="edit btn btn-default m-t-20">
                                                            Edit
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="settings-menu-title">Signed</div>
                                <div class="settings-menu-description">Available for signed in users</div>
                            </div>
                            <div class="col-md-8">
                                <a href="javascript:void(0)" onclick="addMenuItem(1)" class="btn btn-default m-b add-modal-menu">Add menu item</a>
                                <table class="table table-bordered" style="margin-top: 10px;">
                                    <tbody class="tablecontents" id="signed_menu">
                                    @if(!empty($menus))
                                        @foreach($menus as $key => $menu)
                                            @if ($menu->menu_link_type == 'No')
                                                <tr class="row1" data-id="{{ $menu->id }}">
                                                    <td>
                                                        <div class="settings-menu-drag">
                                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Drag-Handle</title><path d="M7 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm6-8c1.104 0 2-.896 2-2s-.896-2-2-2-2 .896-2 2 .896 2 2 2zm0 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2z"></path></svg>
                                                        </div>
                                                        <a href="{{ url($menu->page->url) }}" target="_blank">{{ $menu->menu_name }}</a>
                                                    </td>
                                                    <td class="p-r text-right">
                                                        <button data-url="{{ route('admin.appearance.menu.edit', $menu->id) }}" data-id="{!! $menu->id !!}" class="edit btn btn-default m-t-20">
                                                            Edit
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    @endif
                                    </tbody>
                                </table>
                                <a href="javascript:void(0)" onclick="addMenuItem(2)" class="btn btn-default m-b add-modal-menu">Add menu item</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--Menu Create Modal-->
        <div class="modal fade in" id="menuPagePopUp" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="myLargeModalLabel" aria-hidden="true" onsubmit="return addPageLink()">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <form class="form-material" id="menuForm" method="post" action="{{ route('admin.appearance.menu.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="modal-header">
                            <h4 class="modal-title" id="myLargeModalLabel">Add menu item</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        </div>
                        <div class="modal-body">

                            <div class="modal-body">
                                <div class="form-group form-group__languages">
                                    <label class="control-label" for="menu_name">Name</label>
                                    <input type="text" id="menu_name" class="form-control translations default-menu-name" name="menu_name" data-lang="en" value="{{ old('menu_name') }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="menu_link" class="control-label">Page link</label>
                                    <select class="form-control" id="menu_link" name="menu_link" required onclick="menuPageName(this.value)">
                                        <option value="">Select page</option>
                                        @if (!empty($pages))
                                            @foreach ($pages as $key => $value)
                                                <option value="{{ $value->id }}" id="menu_{{ $value->id }}">{{ $value->name }}</option>
                                            @endforeach
                                        @endif
                                        <option value="0" id="menu_0">External link</option>
                                    </select>
                                </div>

                                <div class="form-group" id="external_link" style="display: none">
                                    <label class="control-label" for="external_url">Page URL <span class="badge badge-secondary">English</span></label>
                                    <input type="text" id="external_url" class="form-control translations default-menu-name" name="external_url" data-lang="en" value="{{ old('external_url') }}">
                                </div>

                            </div>
                        </div>
                        <input type="hidden" name="menu_type" id="menu_type" value="{{ old('menu_type') }}">
                        <div class="modal-footer">
                            <div class="form-actions">
                                <button type="submit" class="btn btn-success"> <i class="fa fa-check"></i> Save</button>
                            </div>
                            <button type="button" class="btn btn-danger waves-effect text-left" data-dismiss="modal">Close</button>
                        </div>

                    </form>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!--Update Menu Modal-->
        <div class="modal fade in" id="menuEditPopUp" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="myLargeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <form class="form-material" id="menuEditForm" method="post" action="" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="modal-header">
                            <h4 class="modal-title" id="myLargeModalLabel">Update menu item</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        </div>
                        <div class="modal-body">
                            <div class="modal-body">

                                <div class="form-group form-group__languages">

                                    <label class="control-label" for="menu_name">Name</label>
                                    <input type="text" id="menu_name_edit" class="form-control translations default-menu-name" name="menu_name_edit" data-lang="en" value="" data-validation-required-message="This field is required" required>
                                </div>

                                <div class="form-group">
                                    <label for="menu_link" class="control-label">Page link</label>
                                    <select class="form-control" id="menu_link_edit" name="menu_link_edit" onclick="editMenuPageName(this.value)" required data-validation-required-message="This field is required">
                                        <option value="">Select page</option>
                                        @if (!empty($pages))
                                            @foreach ($pages as $key => $value)
                                                <option value="{{ $value->id }}" id="menu_{{ $value->id }}">{{ $value->name }}</option>
                                            @endforeach
                                        @endif
                                        <option value="0" id="menu_0">External link</option>
                                    </select>
                                </div>

                                <div class="form-group" id="edit_external_link" style="display: none">
                                    <label class="control-label" for="edit_external_url">Page URL <span class="badge">English</span></label>
                                    <input type="text" id="edit_external_url" class="form-control translations default-menu-name" name="edit_external_url" data-lang="en" value="">
                                </div>

                            </div>
                        </div>

                        <input type="hidden" value="" name="menu_type" id="menu_type">
                        <div class="modal-footer">
                            <div class="col-md-6 submit-update-section">
                                <div class="form-actions">
                                    <button type="submit" class="btn btn-success"> <i class="fa fa-check"></i> Update</button>
                                    <button type="button" class="btn btn-danger waves-effect text-left" data-dismiss="modal">Close</button>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-actions">
                                    <input type="hidden" id="edit_id" value=""/>
                                    <form id="deleteFormPro" method="post" action="">
                                        @method('DELETE')
                                        @csrf
                                        <button type="submit" onclick="return confirm('Are you sure...?')" class="btn btn-secondary waves-effect text-right float-right">Delete</button>
                                    </form>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
    </div>
@endsection
@section('scripts')
    <script>

        $(function () {
            $( ".tablecontents" ).sortable({
                items: "tr",
                cursor: 'move',
                opacity: 0.6,
                update: function() {
                    sendOrderToServer();
                }
            });

            function sendOrderToServer() {
                let order = [];
                console.log(order)
                $('tr.row1').each(function(index,element) {
                    order.push({
                        id: $(this).attr('data-id'),
                        position: index+1,
                    });
                });
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: "{{ route('admin.appearance.menu.sortable') }}",
                    data: {
                        order:order,
                        _token: '{{csrf_token()}}'
                    },
                    success: function(response) {
                        if(response.status === 'success'){
                            toastr["success"](response.message);
                        }else{
                            toastr["error"](response.message);
                        }

                    }
                });

            }
        });

        function addMenuItem(type) {
            $('#menu_type').val(type);
            $('#menuPagePopUp').modal("show");
        }
        function menuPageName(value){

            if (value == '0') {
                $('#external_link').show();
            } else {
                $('#external_link').hide();
            }
        }
        function editMenuPageName(value){
            if (value == 0) {
                $('#edit_external_link').show();
            } else {
                $('#edit_external_link').hide();
            }
        }

        function addPageLink(){
            var externalLink = $('#menu_link').val();
            var external_url = $('#external_url').val();
            if (externalLink == 0) {
                if (external_url == "") {
                    $('#external_url').attr('style','border: 1px solid red');
                    return false;
                }
            }
        }

        $(document).on('click', '.edit', function (e) {
            e.preventDefault();
            let url = $(this).attr('data-url');
            let id = $(this).attr('data-id');
            //console.log(url + id)
            $.ajax({
                url:url,
                type:"GET",
                dataType:"JSON",
                success(response) {
                    if (response.status === 'success'){
                        $('#menuEditPopUp').modal('show');
                        $('#menu_name_edit').val(response.data.menu_name);
                        $('#menu_link_edit').val(response.data.menu_link_id);
                        let selectedExtLink = '';
                        let extUrlLink = '';
                        if(response.data.menu_link_id === 0){
                            $('#edit_external_url').val(response.data.external_link);
                            $('#edit_external_link').show();
                        }else {
                            $('#edit_external_link').hide();
                        }
                        let updateUrl = "{{ url('admin/menu') }}/"+id;
                        $('#menuEditForm').attr('action', updateUrl);
                        $('#deleteFormPro').attr('action', updateUrl);
                    }

                }
            })
        })
    </script>
@endsection
