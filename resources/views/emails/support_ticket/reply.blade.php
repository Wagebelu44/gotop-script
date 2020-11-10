

@component('mail::layout')
{{-- Header --}}
@slot('header')
@endslot

{!! $body !!}

{{-- Footer --}}
@slot('footer')
@endslot
@endcomponent