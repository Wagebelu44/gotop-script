@extends('layouts.panel')

@section('content')

<a class="dropdown-item" href="{{ route('panel.logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
    {{ __('Logout') }}
</a>

<form id="logout-form" action="{{ route('panel.logout') }}" method="POST" style="display: none;">
    @csrf
</form>
@endsection
