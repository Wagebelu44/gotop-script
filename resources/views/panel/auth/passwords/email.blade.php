@extends('layouts.panel-auth')

@section('content')
<div class="logo">
    <span class="db"><img src="{{ asset('panel-assets/images/logo.png') }}" alt="logo" /></span>
    <h5 class="font-medium mb-3">Recover Password</h5>
    <span>Enter your Email and instructions will be sent to you!</span>
</div>

<div class="row">
    <div class="col-12">
        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @endif

        <form class="form-horizontal mt-3" method="POST" action="{{ route('panel.password.email') }}">
            @csrf

            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="ti-email"></i></span>
                </div>
                <input type="email" class="form-control form-control-lg @error('email') is-invalid @enderror" name="email" required value="{{ old('email') }}" placeholder="Email" autocomplete="email" autofocus>

                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group text-center">
                <div class="col-xs-12 pb-3">
                    <button class="btn btn-block btn-lg btn-info" type="submit">Send Password Reset Link</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
