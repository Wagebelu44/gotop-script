@component('mail::message')
    # {{ $notification->subject }}

    @php echo str_replace('{{ ticket.url }}', '', $notification->body) @endphp

    @component('mail::button', ['url' => route('admin.tickets.show', $ticket->id)])
        View message
    @endcomponent

    Thanks,<br>
    {{ config('app.name') }}
@endcomponent
