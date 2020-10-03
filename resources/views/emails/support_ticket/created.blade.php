@component('mail::message')
# {{ ucfirst($s_ticket->subject) }}


<b>Username:</b> {{ ucfirst($s_ticket->sender->username) }}<br>
<b>Subject:</b> {{ ucfirst($s_ticket->subject) }}<br>
<b>Request:</b> {{ ucfirst($s_ticket->subject) }}<br>
<b>Description:</b> {{$s_ticket->description}}<br>

Thanks,<br>
{{ config('app.name') }}
@endcomponent
