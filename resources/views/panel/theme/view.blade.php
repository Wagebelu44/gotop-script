@extends('layouts.panel')

@section('content')
    @php
        $resource = 'admin.theme.';
    @endphp
    <div class="container all-mt-30">

        <div class="row">
            <div class="col-md-12">
                @if(!empty($page))
                    <form method="POST" action="{{ route($resource.'update', $page->id) }}">
                @else
                    <form>
                @endif
                    @csrf
                    <input type="hidden" name="_method" value="PUT">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-sm-8">{{ $theme->name }} Theme</div>
                                <div class="col-sm-4 text-right">
                                    <a href="javascript:void(0)" class="btn btn-xs btn-default" id="full-screen">
                                        <span class="glyphicon glyphicon-fullscreen"></span> Full screen
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-3">
                                    <ul class="list-style-none" style="max-height:500px; overflow:auto;">
                                        @foreach($pages as $k => $gr)
                                        <li>
                                            <a href="javascript:void(0)" onclick="hideShow({{ $k }})"><i class="fa fa-folder text-info"></i> {{ strtoupper($gr->group) }}</a>
                                            <ul class="list-style-none" id="hideShow{{ $k }}" style="padding-left: 15px;">
                                                @foreach($gr->groupPages as $pg)
                                                <li data-toggle="tooltip" data-placement="top" title="{{ $pg->updated_at }}">
                                                    <a href="{{ route($resource.'edit', $theme->id) }}?page={{ $pg->name }}">
                                                        <i class="fa fa-file{{ ($pg->updated_at != null)?'-alt':'' }} text-info"></i> {{ $pg->name }}
                                                    </a>
                                                </li>
                                                @endforeach
                                            </ul>
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="col-sm-9">
                                    @if(!empty($page))
                                    <div class="alert alert-success">{{ strtoupper($page->group).'/'.$page->name }}</div>
                                    <textarea id="content" name="content">{{ $page->content }}</textarea>
                                    @else
                                        <div class="alert alert-success">Pick a file from the left sidebar to start editing.</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-right">
                            @if(!empty($page))
                                @if($page->updated_at != null)
                                    <a type="reset" class="btn btn-default" href="javascript:void(0);" onclick="reset('{{ route($resource.'reset', $page->id) }}')">Reset</a>
                                @endif
                                <button type="submit" class="btn btn-primary">Save</button>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@if(!empty($page))
    @section('styles')
        <link href="{{ asset('panel-assets/libs/codemirror/codemirror.css') }}" rel="stylesheet">
        <link href="{{ asset('panel-assets/libs/codemirror/addon/display/fullscreen.css') }}" rel="stylesheet">
    @endsection
@endif

@section('scripts')
    @if(!empty($page))
    <script src="{{ asset('panel-assets/libs/codemirror/codemirror.js') }}"></script>
    <script src="{{ asset('panel-assets/libs/codemirror/mode/css/css.js') }}"></script>
    <script src="{{ asset('panel-assets/libs/codemirror/addon/display/fullscreen.js') }}"></script>
    <script>
        $(function () {
            var editor = CodeMirror.fromTextArea(document.getElementById("content"), {
                lineNumbers: true,
                lineWrapping: true,
                viewportMargin: Infinity,
                extraKeys: {
                    "F11": function(cm) {
                        cm.setOption("fullScreen", !cm.getOption("fullScreen"));
                    },
                    "Esc": function(cm) {
                        if (cm.getOption("fullScreen")) cm.setOption("fullScreen", false);
                    }
                }
            });

            $("#full-screen").click(function() {
                editor.setOption("fullScreen", true);
            });
        });

        function reset(url) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, reset it!'
            }).then((result) => {
                if (result.value) {
                    var resetForm = `<form method="POST" action="`+url+`" id="resetForm">
                        <input name="_token" type="hidden" value="`+$('meta[name="csrf-token"]').attr('content')+`">
                        <button type="submit">Reset</button>
                    </form>`;
                    $(document.body).append(resetForm);
                    $('#resetForm').submit();
                }
            })
        }
    </script>
    @endif

    <script>
        function hideShow(key) {
            $('#hideShow'+key).slideToggle('slow');
        }
    </script>
@endsection
