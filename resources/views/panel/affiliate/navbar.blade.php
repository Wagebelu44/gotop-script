<div class="col-md-1 offset-1">
    <ul class="list-group">
        <a class="list-group-item {{ Request::segment(3) == 'affiliates' ? 'active' : '' }}" href="{{ route('admin.affiliates.index') }}">
            Affiliates
        </a>

        <a class="list-group-item {{ Request::segment(3) == 'referrals' ? 'active' : '' }}" href="{{ route('admin.affiliates.referrals') }}">
            Referrals
        </a>

        <a class="list-group-item {{ Request::segment(3) == 'payouts' ? 'active' : '' }}" href="{{ route('admin.affiliates.payouts') }}">
            Payouts
        </a>
    </ul>
</div>
