@extends('layouts.panel')

@section('style')
    <style>
        .label-font {
            font-weight: bold;
            font-size: 20px;
        }
    </style>
@endsection
@section('content')
    <div class="container-fluid">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-md-3"></div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <form method="post" action="{{ route('admin.password.update') }}" >
                                @csrf
                                @method('put')
                                <div class="form-group">
                                    <label class="label-font">Current Password</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" name="password">
                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label class="label-font">New Password</label>
                                    <input type="password" class="form-control @error('new_password') is-invalid @enderror" name="new_password">
                                    @error('new_password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label class="label-font">Confirm Password</label>
                                    <input type="password" class="form-control" name="new_password_confirmation">
                                </div>
                                <div class="button-group">
                                    <button class="btn btn-sm btn-primary" type="submit">Change password</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-3"></div>
            </div>
        </div>
    </div>
@endsection
