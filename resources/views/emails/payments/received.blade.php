@component('mail::message')
# {{ $notification->subject }}

@php $body = str_replace(['{{ payment.id }}', 
'{{ payment.admin_url }}', 
'{{ payment.user }}',
'{{ payment.payment_method }}',
'{{ payment.amount }}',
], 
[
    $transaction->id, 
'<a href="#">{{ url("/") }}</a>', 
$transaction->user->username,
$transaction->resellerPaymentMethodsSetting->method_name,
$transaction->amount,
]
, $notification->body); @endphp
{!! $body !!}

@component('mail::button', ['url' => '#'])
View payment
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent

