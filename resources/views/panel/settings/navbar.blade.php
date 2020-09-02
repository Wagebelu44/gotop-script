<div class="col-md-2 offset-1">
    <ul class="list-group">
        <a class="list-group-item {{ Request::segment(3) == 'general' ? 'active' : '' }}" href="{{ route('admin.setting.general') }}">
            General
        </a>

        <a class="list-group-item {{ Request::segment(3) == 'faq' ? 'active' : '' }}" href="{{ route('admin.setting.faq.index') }}">
            FAQ
        </a>

        <a class="list-group-item {{ Request::segment(3) == 'provider' ? 'active' : '' }}" href="{{ route('admin.setting.provider.index') }}">
            Providers
        </a>

        <a class="list-group-item {{ Request::segment(3) == 'payment' ? 'active' : '' }}" href="{{ route('admin.setting.payment.index') }}">
            Payments
        </a>

        <a class="list-group-item {{ Request::segment(3) == 'module' ? 'active' : '' }}" href="{{ route('admin.setting.module') }}">
            Modules
        </a>

        <a class="list-group-item  {{ Request::segment(3) == 'notification' ? 'active' : '' }}" href="{{ route('admin.setting.notification') }}">
            Notifications
        </a>

        <a class="list-group-item  {{ Request::segment(3) == 'bonuses' ? 'active' : '' }}" href="{{ route('admin.setting.bonuses.index') }}">
            Bonuses
        </a>
    </ul>
</div>
