@extends('layouts.panel')

@section('content')
<div class="row all-mt-30">
    @include('panel.settings.navbar')

    <div class="col-md-8">
        <div class="card panel-default">
            <div class="card-body">
                <form action="{{ route('admin.setting.generalUpdate') }}" method="post" enctype="multipart/form-data">
                    @csrf
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
                                    @if (isset($general->logo))
                                        <img style="width: 200px" id="savedLogo" class="img-thumbnail" src="{{ asset('./storage/images/setting/'.$general->logo) }}">
                                    @endif
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
                                    @if (isset($general->favicon))
                                        <img style="width: 50px" id="savedFavicon" class="img-thumbnail" src="{{ asset('./storage/images/setting/'.$general->favicon) }}">
                                    @endif
                                </div>
                            </div>
                        </div>
                        <hr>

                        <div class="form-group">
                            <label class="control-label" for="panel_name">Panel Name</label>
                            <input type="text" class="form-control" value="{{ old('panel_name', isset($general) && $general->panel_name ? $general->panel_name:'') }}" name="panel_name">
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="timezone">Timezone</label>
                            <select class="form-control" name="timezone" id="timezone">
                                @foreach(getTimezone() as $key => $timezone)
                                    <option value="{{ $key }}" {{ isset($general) && $general->timezone == $key ? 'selected':'' }} >{{ $timezone }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="currency_format">Currency Format</label>
                            <select class="form-control" name="currency_format" id="currency_format">
                                @foreach(getCurrencyFormat() as $currency)
                                <option value="{{ $currency }}" {{ isset($general) && $general->currency_format == $currency ? 'selected':'' }}>{{ $currency }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="rates_rounding">Rates Rounding</label>
                            <select class="form-control" name="rates_rounding" id="rates_rounding">
                                @foreach(getRateFormat() as $rate)
                                <option value="{{ $rate }}" {{ isset($general) && $general->rates_rounding == $rate ? 'selected':'' }}>{{ $rate }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="ticket_system">Ticket system</label>
                            <select class="form-control" name="ticket_system" id="ticket_system">
                                <option value="1" {{ isset($general) && $general->ticket_system == 1 ? 'selected':'' }}>Enabled</option>
                                <option value="0" {{ isset($general) && $general->ticket_system == 0 ? 'selected':'' }}>Disabled</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="tickets_per_user">Max pending tickets per user</label>
                            <select class="form-control" name="tickets_per_user" id="tickets_per_user">
                                @foreach(getTicketPerUser() as $ticketUser)
                                <option value="{{ $ticketUser }}" {{ isset($general) && $general->tickets_per_user == $ticketUser ? 'selected':'' }}>{{ $ticketUser }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="signup_page">Signup Page</label>
                            <select class="form-control" name="signup_page" id="signup_page">
                                <option value="1" {{ isset($general) && $general->signup_page == 1 ? 'selected':'' }}>Enabled</option>
                                <option value="0" {{ isset($general) && $general->signup_page == 0 ? 'selected':'' }}>Disabled</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="email_confirmation">Email Confirmation</label>
                            <select class="form-control" name="email_confirmation" id="email_confirmation">
                                <option value="1" {{ isset($general) && $general->email_confirmation == 1 ? 'selected':'' }}>Enabled</option>
                                <option value="0" {{ isset($general) && $general->email_confirmation == 0 ? 'selected':'' }}>Disabled</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="skype_field">Skype field</label>
                            <select class="form-control" name="skype_field" id="skype_field">
                                <option value="1" {{ isset($general) && $general->skype_field == 1 ? 'selected':'' }}>Enabled</option>
                                <option value="0" {{ isset($general) && $general->skype_field == 0 ? 'selected':'' }}>Disabled</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="name_fields">Name fields</label>
                            <select class="form-control" name="name_fields" id="name_fields">
                                <option value="1" {{ isset($general) && $general->name_fields == 1 ? 'selected':'' }}>Enabled</option>
                                <option value="0" {{ isset($general) && $general->name_fields == 0 ? 'selected':'' }}>Disabled</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="terms_checkbox">Terms checkbox</label>
                            <select class="form-control" name="terms_checkbox" id="terms_checkbox">
                                <option value="1" {{ isset($general) && $general->terms_checkbox == 1 ? 'selected':'' }}>Enabled</option>
                                <option value="0" {{ isset($general) && $general->terms_checkbox == 0 ? 'selected':'' }}>Disabled</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="reset_password">Reset Password</label>
                            <select class="form-control" name="reset_password" id="reset_password">
                                <option value="1" {{ isset($general) && $general->reset_password == 1 ? 'selected':'' }}>Enabled</option>
                                <option value="0" {{ isset($general) && $general->reset_password == 0 ? 'selected':'' }}>Disabled</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="average_time">Average Time</label>
                            <select class="form-control" name="average_time" id="average_time">
                                <option value="1" {{ isset($general) && $general->average_time == 1 ? 'selected':'' }}>Enabled</option>
                                <option value="0" {{ isset($general) && $general->average_time == 0 ? 'selected':'' }}>Disabled</option>
                            </select>
                        </div>

                        <div class="form-group row">
                            <div class="col-6">
                                <label class="control-label" for="newsfeed">News feed</label>
                                <div class="setting-switch setting-switch-table">
                                    <label class="switch">
                                        <input type="checkbox" class="toggle-page-visibility" name="newsfeed" id="newsfeed" {{ isset($general) && $general->newsfeed == 'Yes' ? 'checked' : '' }}>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>

                            <div class="col-6">
                                <label class="control-label" for="newsfeed_align">Average Time</label>
                                <select class="form-control" name="newsfeed_align" id="newsfeed_align">
                                    <option value="Left" {{ isset($general) && $general->newsfeed_align == 1 ? 'selected':'' }}>Left</option>
                                    <option value="Right" {{ isset($general) && $general->newsfeed_align == 0 ? 'selected':'' }}>Right</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="drip_feed_interval">Minimum drip-feed interval </label>
                            <input type="text" class="form-control" value="{{ old('drip_feed_interval', isset($general) && $general->drip_feed_interval ? $general->drip_feed_interval:'') }}" name="drip_feed_interval">
                        </div>

                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label" for="horizontal_menu">Horizontal Menu</label>
                                    <div class="setting-switch setting-switch-table">
                                        <label class="switch">
                                            <input type="checkbox" class="toggle-page-visibility" name="horizontal_menu" id="horizontal_menu" {{ isset($general) && $general->horizontal_menu == 'Yes' ? 'checked' : '' }}>
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label" for="total_order">Total order</label>
                                    <div class="setting-switch setting-switch-table">
                                        <label class="switch">
                                            <input type="checkbox" class="toggle-page-visibility" name="total_order" id="total_order" {{ isset($general) && $general->total_order == 'Yes' ? 'checked' : '' }}>
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label" for="total_spent">Total spent</label>
                                    <div class="setting-switch setting-switch-table">
                                        <label class="switch">
                                            <input type="checkbox" class="toggle-page-visibility" name="total_spent" id="total_spent" {{ isset($general) && $general->total_spent == 'Yes' ? 'checked' : '' }}>
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label" for="account_status">Account status</label>
                                    <div class="setting-switch setting-switch-table">
                                        <label class="switch">
                                            <input type="checkbox" class="toggle-page-visibility" name="account_status" id="account_status" {{ isset($general) && $general->account_status == 'Yes' ? 'checked' : '' }}>
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label" for="point">Point</label>
                                    <div class="setting-switch setting-switch-table">
                                        <label class="switch">
                                            <input type="checkbox" class="toggle-page-visibility" name="point" id="point" {{ isset($general) && $general->point == 'Yes' ? 'checked' : '' }}>
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label" for="redeem">Redeem</label>
                                    <div class="setting-switch setting-switch-table">
                                        <label class="switch">
                                            <input type="checkbox" class="toggle-page-visibility" name="redeem" id="redeem" {{ isset($general) && $general->redeem == 'Yes' ? 'checked' : '' }}>
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <hr>
                    <button type="submit" class="btn btn-primary" name="save-button">Save changes</button>
                    <a class="btn btn-default" href="">Cancel</a>
                </form>

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
            $('#savedLogo').hide();
            $('#logoPreview').show(200);
        });

        $("#favicon").change(function() {
            readURL(this, 'icon');
            $('#savedFavicon').hide();
            $('#iconPreview').show(200);
        });

    </script>
@endsection
