@extends('layouts.panel')

@section('content')
<div class="row all-mt-30">
    @include('panel.affiliate.navbar')

    <div class="col-md-9">
        <div class="card panel-default">
            <div class="card-body">
                <div class="row pb-3 pt-3">
                    <div class="col-md-6">
                        <h3>Affiliates</h3>
                    </div>
                    <div class="col-md-6">
                        <form class="d-flex pull-right" method="get" action="{{ route('admin.affiliates.index') }}">
                            <div class="form-group mb-2 mr-0">
                                <input type="search" name="q" value="{{ Request::get('q') }}" class="form-control" placeholder="search...">
                            </div>

                            <button type="submit" class="btn btn-default mb-2" style="border:1px solid #eeeff0;"> <i class="fa fa-search" aria-hidden="true"></i> </button>
                        </form>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">User ID</th>
                                <th>Username</th>
                                <th>Status</th>
                                <th scope="col">Total visits</th>
                                <th scope="col">Unpaid referrals</th>
                                <th scope="col">Paid referrals</th>
                                <th scope="col">Conversion rate</th>
                                <th scope="col">Total earnings</th>
                                <th scope="col">Unpaid earnings</th>
                                <th scope="col"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(!empty($affiliates))
                                @foreach($affiliates as $aff)
                                <tr {!! ($aff->affiliate_status == 'Active') ? '' : 'class=deactive' !!}>
                                    <td>{{ $aff->id }}</td>
                                    <td>{{ $aff->username }}</td>
                                    <td>{{ $aff->affiliate_status }}</td>
                                    <td>{{ $aff->total_visits }}</td>
                                    <td>{{ $aff->unpaid_referrals }}</td>
                                    <td>{{ $aff->paid_referrals }}</td>
                                    <td>{{ $aff->conversion_rate }}</td>
                                    <td>{{ $aff->total_earnings }}</td>
                                    <td>{{ $aff->unpaid_earnings }}</td>
                                    <td class="td-caret">
                                        <div class="btn-group">
                                            <button type="button" class="btn dropdown-toggle custom-dropdown-button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                Action
                                            </button>
                                            <div class="dropdown-menu">
                                                @if($aff->affiliate_status == 'Active')
                                                    <a class="dropdown-item type-dropdown-item" href="#" onclick="affiliateStatus({{ $aff->id }}, 'Deactivated')">Deactive Affiliate</a>
                                                @else
                                                    <a class="dropdown-item type-dropdown-item" href="#" onclick="affiliateStatus({{ $aff->id }}, 'Active')">Active Affiliate</a>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>

                {{ $affiliates->appends(Request::except('page'))->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function affiliateStatus(id, status) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You want be "+status+" this user!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, '+status+' it!'
    }).then((result) => {
        if (result.value) {
            $.ajax({
                type: "POST",
                url: "{{ route('admin.affiliates.status') }}",
                data: { 'user_id': id, 'affiliate_status': status },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function( res ) {
                    if (res.status) {
                        window.location.reload();
                    } else {
                        toastr["error"](res.errors);                        
                    }
                }
            });
        }
    });
}
</script>
@endsection
