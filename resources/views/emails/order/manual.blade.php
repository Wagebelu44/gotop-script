@component('mail::message')
@php $body =  $notification->body; @endphp
{!! $body !!}

Thanks,<br>
{{ config('app.name') }}
@endcomponent
