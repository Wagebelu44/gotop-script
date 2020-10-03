@component('mail::message')
# {{ ucfirst($ticket['subject']) }}

<b>Username:</b> {{ ucfirst($ticket['username'] ) }}<br>
<b>Subject:</b> {{ ucfirst($ticket['subject'] ) }}<br>
<b>Request:</b> {{ ucfirst($ticket['subject'] ) }}<br>
<b>Description:</b> {{$ticket['description'] }}<br>


Thanks,<br>
{{ config('app.name') }}
@endcomponent
