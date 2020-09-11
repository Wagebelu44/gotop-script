@extends('layouts.panel')

@section('content')
    @php
        $resource = 'admin.theme.';
    @endphp
    <div class="container all-mt-30">
        <div class="row el-element-overlay">
            @include('panel.appearance.nav')
            @foreach($themes as $theme)
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <div class="el-card-item">
                            <div class="el-card-avatar el-overlay-1"> <img src="{{ asset($theme->snapshot) }}" alt="user">
                                @if($theme->status != 'Active')
                                <div class="el-overlay">
                                    <ul class="list-style-none el-info">
                                        <li class="el-item"><a class="btn default btn-outline el-link" href="javascript:void(0);" onclick="activate('{{ route($resource.'active', $theme->id) }}')">Activate</a></li>
                                    </ul>
                                </div>
                                @endif
                            </div>
                            <div class="d-flex no-block align-items-center">
                                <div class="ml-3">
                                    <h4 class="mb-0">{{ $theme->name }}</h4>
                                    <span class="text-muted">{{ $theme->status }}</span>
                                </div>
                                <div class="ml-auto mr-3">
                                    <a href="{{ route($resource.'show', $theme->id) }}" class="btn btn-dark btn-circle" style="padding:10px;"><i class="fa fa-edit"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
@endsection

@section('scripts')
<script>
function activate(url) {
    Swal.fire({
        title: 'Are you sure?',
        text: "Active this theme for your panel.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, active it!'
    }).then((result) => {
        if (result.value) {
            var statusForm = `<form method="POST" action="`+url+`" id="statusForm">
                <input name="_token" type="hidden" value="`+$('meta[name="csrf-token"]').attr('content')+`">
                <button type="submit">Active</button>
            </form>`;
            $(document.body).append(statusForm);
            $('#statusForm').submit();
        }
    })
}
</script>
