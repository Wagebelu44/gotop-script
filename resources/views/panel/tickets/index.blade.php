@extends('layouts.panel')

@section('content')
    @php
        $colors = [
               'pending' => [
                   'bg'=> "#F68331",
                   'cl'=> "#000",
               ] ,
               'closed' =>[
                   'bg'=> "#ff0000",
                   'cl'=> "#000",
               ],
               'answered' => [
                   'bg'=> "#008000",
                   'cl'=> "#000",
                ],
           ];
    @endphp

    <div class="container-fluid all-mt-30">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="__control_panel">
                            <div class="__left_control_panel">
                                <button class="btn btn-outline-secondary" data-toggle="modal" data-target="#ticketAddModal">Add Ticket</button>
                            </div>
                            <div class="__right_control_panel">
                                <form class="d-flex pull-right" id="search-form" method="get" action="">
                                    <div class="form-group mb-2 mr-0">
                                        <input type="text" name="keyword" class="form-control" placeholder="Search User" value="">
                                    </div>
                                    <button type="submit" class="btn btn-default mb-2" style="border:1px solid #eeeff0;"> <i class="fa fa-search" aria-hidden="true"></i> </button>
                                </form>
                            </div>
                        </div>
                        <table class="table" id="tickets">
                            <thead>
                            <tr>
                                <th><input type="checkbox" name="select_all" onchange="checkAllTicket()"></th>
                                <th colspan="9" style="display: none">
                                    <span id="user-no"></span> tickets selected
                                    <div class="btn-group">
                                        <button type="button" class="btn custom-dropdown-button dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            Action
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item type-dropdown-item" href="javascript:void(0)"
                                                onclick="bulkStatusChange('closed')">Mark as Closed</a>
                                            <a class="dropdown-item type-dropdown-item" href="javascript:void(0)"
                                                onclick="bulkStatusChange('pending')">Mark as Pending</a>
                                            <a class="dropdown-item type-dropdown-item" href="javascript:void(0)"
                                                onclick="bulkStatusChange('answered')">Mark as Answered</a>
                                            <a class="dropdown-item type-dropdown-item" href="javascript:void(0)"
                                                onclick="bulkStatusChange('delete')">Delete</a>
                                        </div>
                                    </div>
                                </th>

                                <th>ID</th>
                                <th>Subject</th>
                                <th>Request</th>
                                <th style="width: 30%">Description</th>
                                <th width="15%">Client name</th>
                                <th width="15%">
                                    <div class="input-group">
                                        <div class="input-group-append">
                                            <button class="btn custom-dropdown-button dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Status</button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item type-dropdown-item" href="{{ route('admin.tickets.index') }}">All</a>
                                                <a class="dropdown-item type-dropdown-item" href="javascript:void(0)" onclick="searchTickets('status', 'pending')">Pending</a>
                                                <a class="dropdown-item type-dropdown-item" href="javascript:void(0)" onclick="searchTickets('status', 'answered')">Answered</a>
                                                <a class="dropdown-item type-dropdown-item" href="javascript:void(0)" onclick="searchTickets('status', 'closed')">Resolved</a>
                                            </div>
                                            <form class="text-right" id="search-form">
                                            </form>
                                        </div>
                                    </div>
                                </th>
                                <th>Created at</th>
                                <th>Last update</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($tickets as $key => $ticket)
                                <tr>
                                    <td><input type="checkbox" name="tickets[]" value="{{ $ticket->id }}" class="ticket_check" onchange="checkTicket()" {{ $ticket->status == 2 ? 'disabled' : '' }}></td>
                                    <td>{{ $ticket->id }}</td>
                                    <td>{{ ucfirst($ticket->subject) }}</td>
                                    <td>{{ ucfirst($ticket->payment_type) }}</td>
                                    <td style="width: 30%">
                                        {!! substr(strip_tags($ticket->description), $limit = 200) !!}
                                        @if (strlen(strip_tags($ticket->description)) > 200)
                                            <a href="{{ route('admin.tickets.show', $ticket->id) }}"
                                                class="btn btn-link">Read More</a>
                                        @endif
                                    </td>
                                    <td>{{ $ticket->user->username }}</td>
                                    <td style="width: 10%">
                                        <p style="background-color: {{isset($colors[$ticket->status])?$colors[$ticket->status]['bg']:"#fff"}}; color:#fff; padding: 0px 5px; text-align:center;"> {{ucfirst($ticket->status)}} </p>
                                    </td>
                                    <td width="20%">{{ date('M d, Y h:iA', strtotime($ticket->created_at)) }}</td>
                                    <td width="20%">{{ date('M d, Y h:iA', strtotime($ticket->updated_at)) }}</td>
                                    <td>
                                        <div class="d-flex">
                                            <div class="dropdown show goTopDropdown">
                                                <a class="btn btn-default dropdown-toggle custom-dropdown-button" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fa fa-cog"></i>
                                                </a>
                                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                                    <li class="dropdown-submenu" style="cursor: pointer"><a class="dropdown-item dropdown-toggle type-dropdown-item">Change status</a>
                                                        <ul class="dropdown-menu">
                                                            <li><a href="{{route('admin.tickets.status.change', ['status' => 'pending', 'ticket_id' => $ticket->id ])}}" class="dropdown-item type-dropdown-item">Pending</a></li>
                                                            <li><a  href="{{route('admin.tickets.status.change', ['status' => 'answered', 'ticket_id' => $ticket->id ])}}" class="dropdown-item type-dropdown-item">Answered</a></li>
                                                            <li><a  href="{{route('admin.tickets.status.change', ['status' => 'closed', 'ticket_id' => $ticket->id ])}}" class="dropdown-item type-dropdown-item">Closed</a></li>
                                                        </ul>
                                                    </li>
                                                </ul>
                                            </div>
                                            <a href="{{ route('admin.tickets.show', $ticket->id) }}" class="btn custom-dropdown-button"><i class="fa fa-eye"></i> </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        {{ $tickets->links() }}
                        <!--Ticket add modal-->
                        <div class="modal bs-example-modal-lg" id="ticketAddModal" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                            <div class="modal-dialog  __modal_dialog_custom">
                                <div class="modal-content">
                                    <form  method="post" id="support_admin_ticket" action="{{ route('admin.tickets.store') }}" enctype="multipart/form-data" novalidate>
                                        @csrf
                                        <div class="modal-header">
                                            <h4 class="modal-title" id="myLargeModalLabel">Add ticket</h4>
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label for=""> <strong>Subject</strong></label>
                                                        <input type="text" name="subject" class="form-control custom-form-control">
                                                        @error('subject')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong></strong>
                                                        </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label for=""> <strong>User</strong></label>
                                                        <select name="user_id[]"
                                                                class="form-control custom-form-control select2 @error('user_id') is-invalid @enderror"
                                                                required data-validation-required-message="This field is required"
                                                                multiple="multiple">
                                                                @foreach($users as $key => $user)
                                                                 <option value="{{ $user->id }}">{{ $user->username }}</option>
                                                                @endforeach
                                                        </select>
                                                        @error('user_id')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <div class="controls">
                                                            <textarea style="height: 200px" name="message" class="form-control summernote @error('message') is-invalid @enderror" placeholder="Message" required data-validation-required-message="This field is required">{{ old('message') }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <div class="form-actions">
                                                <button type="submit" class="btn btn-sm btn-primary custom-button"> <i class="fa fa-check"></i> Save</button>
                                            </div>
                                            <button type="button" class="btn  btn-sm btn-danger custom-button" data-dismiss="modal">Close</button>
                                        </div>
                                    </form>
                                </div>
                                <!-- /.modal-content -->
                            </div>
                            <!-- /.modal-dialog -->
                        </div>
                        <!-- /.modal -->
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        function checkAllTicket() {
            if (event.target.checked) {
                $('.ticket_check').prop('checked', true);
                $('#tickets thead > tr > th:not(:first-child)').hide();
                $('#tickets thead > tr').children().eq(1).show();
                $('#user-no').text($('.ticket_check').length);
            } else {
                $('.ticket_check').prop('checked', false);
                $('#tickets thead > tr > th').show();
                $('#tickets thead > tr').children().eq(1).hide();
            }
        }

        function checkTicket() {
            let count = 0;

            $('.ticket_check').each(function () {
                if (this.checked) {
                    count += 1;
                }
            });

            if (count) {
                $('input[name=select_all]').prop('checked', true);
                $('#tickets thead > tr > th:not(:first-child)').hide();
                $('#tickets thead > tr').children().eq(1).show();
                $('#user-no').text(count);
            } else {
                $('input[name=select_all]').prop('checked', false);
                $('#tickets thead > tr > th').show();
                $('#tickets thead > tr').children().eq(1).hide();
            }
        }

        function getCheckedIds()
        {
            let allids = [];
            let cbox = document.querySelectorAll('.ticket_check');
            for (let index = 0; index < cbox.length; index++) {
                if (cbox[index].checked) {
                    allids.push(cbox[index].value);
                }
            }
            return allids;
        }

        function bulkDelete() {
            alert('Are you sure you want to delete this ticket?')
                .then((result) => {
                if (result.value) {
                    $('#bulk-delete').append($('.ticket_check')).submit();
                }
            });
        }


        function bulkStatusChange(status) {

            const myform = new FormData();
            myform.append('status', status);
            myform.append('ids', getCheckedIds());
            fetch('{{route('admin.tickets.status.changes')}}',
                {
                    headers:
                        {
                            "Accept": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        },
                    credentials: "same-origin",
                    method: "POST",
                    body: myform,
                })
                .then(res => {
                    if (!res.ok) {
                        throw res.json();
                    }
                    return res.json();
                })
                .then(response => {
                    alert('Successfully changes status')
                    setTimeout(()=>{
                        window.location.reload();
                    }, 1000);

                }).catch(error => {
                console.log(error)
                setTimeout(()=>{
                    window.location.reload();
                }, 1000);
            });

            console.log(allids);
            //$('#bulk-close').submit();
        }

        function submit_ticket(obj)
        {
            obj.setAttribute('disabled', 'disabled');
            document.querySelector('#support_admin_ticket').submit();
        }

        function searchTickets(column, value) {
            let form = $('#search-form');
            form.prepend(`<input type="hidden" name="columns[${column}]" value="${value}">`);
            form.submit();
        }

        setTimeout(function () {
            $('.dropdown-menu a.dropdown-toggle').on('click', function(e) {
                if (!$(this).next().hasClass('show')) {
                    $(this).parents('.dropdown-menu').first().find('.show').removeClass("show");
                }
                var $subMenu = $(this).next(".dropdown-menu");
                $subMenu.toggleClass('show');


                $(this).parents('li.nav-item.dropdown.show').on('hidden.bs.dropdown', function(e) {
                    $('.dropdown-submenu .show').removeClass("show");
                });

                return false;
            });
        }, 5000);
    </script>
@endsection
