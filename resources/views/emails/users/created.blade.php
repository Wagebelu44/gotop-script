@component('mail::message')
# {{ $notification->subject }}

@php $body = str_replace(array('{{ user.username }}', '{{ panel.url }}'), array($user->username, '<a href="' . route('login') . '">' . route('login') . '</a>'), $notification->body); @endphp
{!! nl2br($body) !!}

@component('mail::button', ['url' => '#'])
Sign in
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
