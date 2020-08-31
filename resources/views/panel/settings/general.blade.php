@extends('layouts.panel')

@section('content')
<div class="row all-mt-30">
    @include('panel.settings.navbar')

    <div class="col-md-8">
        <div class="card panel-default">
            <div class="card-body">
                <form action="{{ route('admin.setting.generalUpdate') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="relative">

                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-10">
                                    <label class="control-label" for="logo">Logo</label><br/>
                                    <input type="file" id="logo" class="@error('logo') is-invalid @enderror" name="logo">
                                    <p class="help-block">200 x 80px recommended</p>

                                    @error('logo')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <img style="display: none; width: 200px" id="logoPreview" class="img-thumbnail" src="">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-10">
                                    <label class="control-label" for="favicon">Favicon</label><br/>
                                    <input type="file" class="@error('favicon') is-invalid @enderror" id="favicon" name="favicon">
                                    <p class="help-block">16 x 16px .png recommended</p>
                                    @error('favicon')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <img style="display: none; width: 50px" id="iconPreview" class="img-thumbnail" src="">
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group">
                            <label class="control-label" for="panel_name">Panel name</label>
                            <input type="text" id="panel_name" class="form-control" name="general[0][post_value]" value="" required>
                            <input type="hidden" name="general[0][key]" value="panel_name">
                            @error('panel_name')
                            <span class="invalid-feedback" role="alert">
                                <strong></strong>
                            </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="ticket_system">Ticket system</label>
                            <select class="form-control" name="general[4][post_value]" id="ticket_system">
                                <option>Ticket system</option>
                            </select>
                            <input type="hidden" name="general[4][key]" value="ticket_system">
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="tickets_per_user">Max pending tickets per user</label>
                            <select class="form-control" name="general[5][post_value]" id="tickets_per_user">
                                <option value="">Select Max pending tickets per user</option>
                            </select>
                            <input type="hidden" name="general[5][key]" value="tickets_per_user">
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="signup_page">Signup page</label>
                            <select class="form-control" name="general[6][post_value]" id="signup_page">
                                <option>Signup page</option>
                            </select>
                            <input type="hidden" name="general[6][key]" value="signup">
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="skype">Skype field</label>
                            <select class="form-control" name="general[7][post_value]" id="skype">
                                <option>Skype field</option>
                            </select>
                            <input type="hidden" name="general[7][key]" value="skype">
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="name_fields">Name fields</label>
                            <select class="form-control" name="general[8][post_value]" id="name_fields">
                                <option>Name fields</option>
                            </select>
                            <input type="hidden" name="general[8][key]" value="name_fields">
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="terms_checkbox">Terms checkbox</label>
                            <select class="form-control" name="general[9][post_value]" id="terms_checkbox">
                                <option>Terms checkbox</option>
                            </select>
                            <input type="hidden" name="general[9][key]" value="terms_checkbox">
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="forgot_password">Forgot password</label>
                            <select class="form-control" name="general[10][post_value]" id="forgot_password">
                                <option>Forgot password</option>
                            </select>
                            <input type="hidden" name="general[10][key]" value="forgot_password">
                        </div>
                    </div>
                    <hr>
                    <button type="submit" class="btn btn-primary" name="save-button">Save changes</button>
                    <a class="btn btn-default" href="">Cancel</a>

                </form>
                {{--<form id="delete-form-image" action="#" method="POST" style="display:none">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="type" id="type" value=""/>
                </form>--}}
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script>
        function readURL(input,type) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    if (type == 'icon') {
                        $('#iconPreview').attr('src', e.target.result);
                    } else {
                        $('#logoPreview').attr('src', e.target.result);
                    }

                };

                reader.readAsDataURL(input.files[0]); // convert to base64 string
            }
        }
        $("#logo").change(function() {
            readURL(this, 'logo');
            $('#logoPreview').show(200);
        });

        $("#favicon").change(function() {
            readURL(this, 'icon');
            $('#iconPreview').show(200);
        });

    </script>
@endsection
