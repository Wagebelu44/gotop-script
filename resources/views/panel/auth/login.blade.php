@extends('layouts.panel-auth')

@section('content')
<div class="logo">
    <span class="db"><img src="{{ asset('panel-assets/images/logo.png') }}" alt="logo" /></span>
    <h5 class="font-medium mb-3">Sign In to Admin</h5>
</div>

<div class="row">
    <div class="col-12">
        @if (session('error'))
            <div class="alert alert-warning">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
                <h3 class="text-warning"><i class="fa fa-exclamation-triangle"></i> Warning</h3> {{ Session::get('error') }}
            </div>
        @endif
        <form class="form-horizontal mt-3" method="POST" action="{{ route('panel.login.action') }}">
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

            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="ti-pencil"></i></span>
                </div>
                <input type="password" class="form-control form-control-lg @error('password') is-invalid @enderror" name="password" required placeholder="Password" autocomplete="current-password">

                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group row">
                <div class="col-md-12">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label class="custom-control-label" for="remember">Remember me</label>

                        @if (Route::has('panel.password.request'))
                            <a class="text-dark float-right" href="{{ route('panel.password.request') }}">
                                <i class="fa fa-lock mr-1"></i> Forgot password?
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="form-group text-center">
                <div class="col-xs-12 pb-3">
                    <button class="btn btn-block btn-lg btn-info" type="submit">Log In</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
