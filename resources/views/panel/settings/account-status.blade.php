@extends('layouts.panel')

@section('content')
    @php
        $resource = 'admin.setting.account-status.';
    @endphp
    <div class="row all-mt-30">
        @include('panel.settings.navbar')

        <div class="col-md-8">
            <div class="card panel-default">
                <div class="card-body">
                    <a class="btn btn-default m-b add-page" href="javascript:void(0)" data-toggle="modal" data-target="#cmsBonusesPopUp">Add bonus</a>

                    <table class="table">
                        <thead>
                        <tr>
                            <th width="45%" class="p-l">ID</th>
                            <th>Bonus</th>
                            <th>Method</th>
                            <th>From</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


@endsection

@section('scripts')
    <script>

        $(document).on('click', '.edit', function (e) {
            e.preventDefault();
            let url = $(this).attr('data-url');
            let id = $(this).attr('data-id');
            $.ajax({
                url:url,
                type:"GET",
                dataType:"JSON",
                success(response) {

                    if (response.status === 'success'){
                        $('#cmsBonusesEditPopUp').modal('show');
                        $("#edit_bonus_amount").val(response.data.bonus_amount);
                        $("#edit_payment_method_id").val(response.data.global_payment_method_id);
                        $("#edit_deposit_from").val(response.data.deposit_from);
                        document.forms['editFormBonus'].elements['status'].value = response.data.status;
                        let updateUrl = "{{ url('admin/setting/bonuses') }}/"+id;
                        $('#editFormBonus').attr('action', updateUrl);
                    }

                }
            })
        })
    </script>
@endsection
