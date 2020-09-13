@extends('layouts.panel')

@section('content')
    @php
        $resource = 'admin.appearance.file.';
    @endphp
    <div class="container all-mt-30">
        <div class="row">
            @include('panel.appearance.nav')
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <a data-toggle="modal" data-target="#fileModal" class="btn btn-default m-b add-page">Add File</a>

                        <table class="table">
                            <thead>
                            <tr>
                                <th width="40%" class="p-l">File</th>
                                <th width="40%">Url</th>
                                <th width="10%">Size</th>
                                <th width="10%"></th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(!empty($data))
                                @foreach($data as $file)
                                <tr>
                                    <td>
                                        <img class="img-thumbnail" src="{{ $file->url }}" width="80">
                                        {{ $file->name }}
                                    </td>
                                    <td><label class="d-inline-block">{{ $file->url }} <i class="fa fa-copy"></i></label></td>
                                    <td>{{ $file->size }}</td>
                                    <td>
                                        <form action="{{ route($resource.'destroy', $file->id) }}" method="post">
                                            @csrf
                                            @method('DELETE')
                                            <button  type="submit" class="btn btn-default btn-xs">Delete</button>
                                        </form>
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
        <!--Menu Create Modal-->
        <div class="modal fade in" id="fileModal" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="myLargeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <form class="form-material" id="fileForm" method="post" action="{{ route($resource.'store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-header">
                            <h4 class="modal-title" id="myLargeModalLabel">Add File</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        </div>
                        <div class="modal-body">
                            <div class="modal-body">
                                <div class="form-group form-group__languages">
                                    <label class="control-label" for="files">Select your file</label>
                                    <input type="file" id="files" class="form-control translations default-menu-name" name="files[]" accept="image/*" multiple required>
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
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
    </div>
@endsection
@section('scripts')

@endsection
