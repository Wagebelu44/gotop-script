@extends('layouts.panel')

@section('content')
    @php
    $resource = 'admin.setting.faq.';
    @endphp

    <div class="row all-mt-30">
        @include('panel.settings.navbar')

        @if($page == 'index')
        <div class="col-md-8">
            <a href="{{ route($resource.'create') }}" class="btn btn-sm btn-primary">Add Faq</a>
            <div class="card panel-default" style="margin-top: 10px;">
                <div class="card-body">
                    <div class="col-md-12">
                        <table class="setting-table">
                            <tbody id="tablecontents">
                            @if(!empty($data))
                                @foreach($data as $key => $faq)
                                <tr class="row1" data-id="{{ $faq->id }}" style="border-bottom: 1px solid lightgray">
                                    <td>
                                        <div class="settings-menu-drag">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Drag-Handle</title><path d="M7 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm6-8c1.104 0 2-.896 2-2s-.896-2-2-2-2 .896-2 2 .896 2 2 2zm0 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2z"></path></svg>
                                        </div>
                                        {{ $faq->question }}
                                    </td>
                                    <td style="width: 10%" class="text-center">{{ $faq->status }}</td>
                                    <td style="width: 10%">
                                        <a href="{{ route($resource.'edit', $faq->id) }}" class="btn btn-default btn-xs" style="margin-left: 10px;">Edit</a>
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

        @elseif($page == 'create' || $page == 'edit')
            <div class="col-md-8">
                <a href="{{ route($resource.'index') }}" class="btn btn-sm btn-primary">Back</a>
                <div class="card panel-default" style="margin-top: 10px;">
                    <div class="card-body">
                        <form action="{{ $page == 'edit' ? route($resource.'update', $data->id):route($resource.'store') }}" method="post">
                            @csrf
                            @if($page == 'edit')
                                @method('PUT')
                            @endif

                            <div class="form-group">
                                <label class="control-label" for="question">Question</label>
                                <textarea class="form-control @error('question') is-invalid @enderror" name="question">{{ old('question', isset($data) && $data->question ? $data->question:'' ) }}</textarea>
                                @error('question')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label class="control-label" for="answer">Answer</label>
                                <textarea class="form-control summernote @error('answer') is-invalid @enderror" name="answer">{{ old('answer', isset($data) && $data->answer ? $data->answer:'' ) }}</textarea>
                                @error('answer')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label class="control-label" for="status">Status</label>
                                <select class="form-control @error('status') is-invalid @enderror" name="status" id="status">
                                    <option value="active" {{ isset($data) && $data->status == 'active' ? 'selected':'' }}>Active</option>
                                    <option value="inactive" {{ isset($data) && $data->status == 'inactive' ? 'selected':'' }}>Inactive</option>
                                </select>
                                @error('answer')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <hr>
                            <button type="submit" class="btn btn-primary" name="save-button">Save changes</button>
                            <a class="btn btn-default" href="">Cancel</a>
                            @if($page == 'edit')
                                <a href="javascript: void(0)" onclick="document.getElementById('deleteFaq').submit();" class="btn btn-default waves-effect pull-right" ><i>Delete</i></a>
                            @endif
                        </form>
                        @if($page == 'edit')
                        <form id="deleteFaq" action="{{ route($resource.'destroy', $data->id)}}" method="post">
                            @csrf
                            @method('DELETE')
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@section('scripts')
    <script type="text/javascript">
        @if($page == 'index')
            $(function () {
            $( "#tablecontents" ).sortable({
                items: "tr",
                cursor: 'move',
                opacity: 0.6,
                update: function() {
                    sendOrderToServer();
                }
            });

            function sendOrderToServer() {
                var order = [];

                $('tr.row1').each(function(index,element) {
                    order.push({
                        id: $(this).attr('data-id'),
                        position: index+1,
                    });
                });
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: "{{ route($resource.'sortable') }}",
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
        @endif
    </script>
@endsection
