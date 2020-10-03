@extends('layouts.panel')

@section('content')
<div class="row all-mt-30">
    @include('panel.affiliate.navbar')

    <div class="col-md-9">
        <div class="card panel-default">
            <div class="card-body">
                <div class="row pb-3 pt-3">
                    <div class="col-md-6">
                        <h3>Payouts</h3>
                    </div>
                    <div class="col-md-6">
                        <form class="d-flex pull-right" method="get" action="{{ route('admin.affiliates.payouts') }}">
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
                                <th>Affiliate</th>
                                <th>Payout amount</th>
                                <th scope="col">Created</th>
                                <th scope="col">Updated</th>
                                <th scope="col">Status</th>
                                <th scope="col">Mode</th>
                                <th scope="col"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(!empty($payouts))
                                @foreach($payouts as $aff)
                                <tr>
                                    <td>{{ $aff->id }}</td>
                                    <td>{{ $aff->referral->username }}</td>
                                    <td>{{ $aff->amount }}</td>
                                    <td>{{ $aff->created_at }}</td>
                                    <td>{{ $aff->updated_at }}</td>
                                    <td>{{ $aff->mode }}</td>
                                    <td>{{ $aff->status }}</td>
                                    <td class="td-caret">
                                        <div class="btn-group">
                                            <button type="button" class="btn dropdown-toggle custom-dropdown-button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                Action
                                            </button>
                                            <div class="dropdown-menu">
                                                @if($aff->status == 'Canceled')
                                                    <a class="dropdown-item type-dropdown-item" href="#" onclick="affiliateStatus({{ $aff->id }}, 'Approved')">Approve Payout</a>
                                                @elseif($aff->status == 'Approved')
                                                    <a class="dropdown-item type-dropdown-item" href="#" onclick="affiliateStatus({{ $aff->id }}, 'Canceled')">Cancel Payout</a>
                                                @else
                                                    <a class="dropdown-item type-dropdown-item" href="#" onclick="affiliateStatus({{ $aff->id }}, 'Approved')">Approve Payout</a>
                                                    <a class="dropdown-item type-dropdown-item" href="#" onclick="affiliateStatus({{ $aff->id }}, 'Canceled')">Cancel Payout</a>
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

                {{ $payouts->appends(Request::except('page'))->links() }}
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
                url: "{{ route('admin.affiliates.payout-status') }}",
                data: { 'id': id, 'status': status },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function( res ) {
                    if (res.status) {
                        toastr["success"](res.message);
                        setTimeout(function(){ window.location.reload(); }, 2000);
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
