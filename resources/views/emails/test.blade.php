@component('mail::message')
# Test Mail

{{ $settingNotification->body }}

@component('mail::button', ['url' => ''])
Button Text
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
