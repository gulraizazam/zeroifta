@extends('layouts.new_main')
@section('content')
<div class="dashbord-inner">
    <!-- Section 1 -->
    <div class="profileForm-area mb-4">
        <div class="sec1-style">
        <form method="post" action="{{ route('driver.store') }}" enctype="multipart/form-data" id="driverForm">
            @csrf
            <div class="row pt-3">
                <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-2">
                    <div class="dash-input mb-3">
                        <label class="input-lables pb-2" for="first_name">{{ __('messages.First Name') }}</label>
                        <input type="text" required class="form-control login-input" placeholder="{{ __('messages.First Name') }}" id="first_name" name="first_name" value="{{ old('first_name') }}"/>
                    </div>
                    @error('first_name')
                            <span class="invalid-feedback" role="alert" style="display: block;">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-2">
                    <div class="dash-input mb-3">
                        <label class="input-lables pb-2" for="last_name">{{ __('messages.Last Name') }}</label>
                        <input type="text" required class="form-control login-input" placeholder="{{ __('messages.Last Name') }}" id="last_name" name="last_name" value="{{ old('last_name') }}"/>
                    </div>
                    @error('last_name')
                            <span class="invalid-feedback" role="alert" style="display: block;">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-2">
                    <div class="dash-input mb-3">
                        <label class="input-lables pb-2" for="driver_id">{{ __('messages.Driver ID') }}</label>
                        <input type="text" required class="form-control login-input" placeholder="{{ __('messages.Driver ID') }}" id="driver_id" name="driver_id" value="{{ old('driver_id') }}"/>
                    </div>
                    @error('driver_id')
                            <span class="invalid-feedback" role="alert" style="display: block;">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-2">
                    <div class="dash-input mb-3">
                        <label class="input-lables pb-2" for="phone_number">{{ __('messages.Phone') }}</label>
                        <div class="d-flex">
                            <select name="country_code" id="country_code" class="form-control login-input me-2" style="width: 120px;">
                            <option value="+1">USA (+1)</option>
                            <option value="+1">Canada (+1)</option>
                            </select>
                            <input type="text" required class="form-control login-input" placeholder="{{ __('messages.Phone') }}" id="phone_number" name="phone_number" value="{{ old('phone_number') }}" />
                        </div>
                        <input type="hidden" name="phone" id="full_phone" value="{{ old('phone') }}">
                    </div>
                    @error('phone')
                            <span class="invalid-feedback" role="alert" style="display: block;">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-2">
                    <div class="dash-input mb-3">
                        <label class="input-lables pb-2" for="license_state">{{ __('messages.License State') }}</label>
                        <select name="license_state" id="license_state" class="form-control login-input" required>
                            <option value="">-- Select a State --</option>
                            <option value="AL">Alabama</option>
                            <option value="AK">Alaska</option>
                            <option value="AZ">Arizona</option>
                            <option value="AR">Arkansas</option>
                            <option value="CA">California</option>
                            <option value="CO">Colorado</option>
                            <option value="CT">Connecticut</option>
                            <option value="DE">Delaware</option>
                            <option value="FL">Florida</option>
                            <option value="GA">Georgia</option>
                            <option value="HI">Hawaii</option>
                            <option value="ID">Idaho</option>
                            <option value="IL">Illinois</option>
                            <option value="IN">Indiana</option>
                            <option value="IA">Iowa</option>
                            <option value="KS">Kansas</option>
                            <option value="KY">Kentucky</option>
                            <option value="LA">Louisiana</option>
                            <option value="ME">Maine</option>
                            <option value="MD">Maryland</option>
                            <option value="MA">Massachusetts</option>
                            <option value="MI">Michigan</option>
                            <option value="MN">Minnesota</option>
                            <option value="MS">Mississippi</option>
                            <option value="MO">Missouri</option>
                            <option value="MT">Montana</option>
                            <option value="NE">Nebraska</option>
                            <option value="NV">Nevada</option>
                            <option value="NH">New Hampshire</option>
                            <option value="NJ">New Jersey</option>
                            <option value="NM">New Mexico</option>
                            <option value="NY">New York</option>
                            <option value="NC">North Carolina</option>
                            <option value="ND">North Dakota</option>
                            <option value="OH">Ohio</option>
                            <option value="OK">Oklahoma</option>
                            <option value="OR">Oregon</option>
                            <option value="PA">Pennsylvania</option>
                            <option value="RI">Rhode Island</option>
                            <option value="SC">South Carolina</option>
                            <option value="SD">South Dakota</option>
                            <option value="TN">Tennessee</option>
                            <option value="TX">Texas</option>
                            <option value="UT">Utah</option>
                            <option value="VT">Vermont</option>
                            <option value="VA">Virginia</option>
                            <option value="WA">Washington</option>
                            <option value="WV">West Virginia</option>
                            <option value="WI">Wisconsin</option>
                            <option value="WY">Wyoming</option>
                        </select>
                    </div>
                    @error('license_state')
                            <span class="invalid-feedback" role="alert" style="display: block;">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-2">
                    <div class="dash-input mb-3">
                        <label class="input-lables pb-2" for="license_number">{{ __('messages.License Number') }}</label>
                        <input type="text" required class="form-control login-input" placeholder="{{ __('messages.License Number') }}" id="license_number" name="license_number" value="{{ old('license_number') }}" />
                    </div>
                    @error('license_number')
                            <span class="invalid-feedback" role="alert" style="display: block;">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-2">
                    <div class="dash-input mb-3">
                        <label class="input-lables pb-2" for="licenseStartDate">{{ __('messages.License Start Date') }}</label>
                        <input type="date" required class="form-control login-input" placeholder="{{ __('messages.License Start Date') }}" id="licenseStartDate" name="license_start_date" value="{{ old('license_start_date') }}" />
                    </div>
                    @error('license_start_date')
                            <span class="invalid-feedback" role="alert" style="display: block;">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-2">
                    <div class="dash-input mb-3">
                        <label class="input-lables pb-2" for="email">{{ __('messages.Email') }}</label>
                        <input type="email" required class="form-control login-input" placeholder="{{ __('messages.Email') }}" id="email" name="email" value="{{ old('email') }}" />
                    </div>
                    @error('email')
                            <span class="invalid-feedback" role="alert" style="display: block;">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-2">
                    <div class="dash-input mb-3">
                        <label class="input-lables pb-2" for="username">{{ __('messages.Username') }}</label>
                        <input type="text" required class="form-control login-input" placeholder="{{ __('messages.Username') }}" id="username" name="username" value="{{ old('username') }}" />
                    </div>
                    @error('username')
                            <span class="invalid-feedback" role="alert" style="display: block;">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-2">
                    <div class="dash-input mb-3">
                        <label class="input-lables pb-2" for="profile_picture">{{ __('messages.Profile Picture') }}</label>
                        <input type="file" class="form-control login-input choose-file-input" id="profile_picture" name="profile_picture" />
                    </div>
                    @error('profile_picture')
                            <span class="invalid-feedback" role="alert" style="display: block;">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-2">
                    <div class="dash-input mb-3 position-relative">
                        <label class="input-lables pb-2" for="password">{{ __('messages.Password') }}</label>
                        <input type="password" required class="form-control login-input" placeholder="{{ __('messages.Password') }}" id="password" name="password" value="{{ old('password') }}"/>
                        <div class="show-pass1 position-absolute" style="right: 10px; top: 65%; transform: translateY(-50%); cursor: pointer;" onclick="togglePasswordVisibility('password', 'show-icon1', 'hide-icon1')">
                            <span id="show-icon1" style="display: inline;">
                                <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="#a5a9ad">
                                    <path d="m644-428-58-58q9-47-27-88t-93-32l-58-58q17-8 34.5-12t37.5-4q75 0 127.5 52.5T660-500q0 20-4 37.5T644-428Zm128 126-58-56q38-29 67.5-63.5T832-500q-50-101-143.5-160.5T480-720q-29 0-57 4t-55 12l-62-62q41-17 84-25.5t90-8.5q151 0 269 83.5T920-500q-23 59-60.5 109.5T772-302Zm20 246L624-222q-35 11-70.5 16.5T480-200q-151 0-269-83.5T40-500q21-53 53-98.5t73-81.5L56-792l56-56 736 736-56 56ZM222-624q-29 26-53 57t-41 67q50 101 143.5 160.5T480-280q20 0 39-2.5t39-5.5l-36-38q-11 3-21 4.5t-21 1.5q-75 0-127.5-52.5T300-500q0-11 1.5-21t4.5-21l-84-82Zm319 93Zm-151 75Z"/>
                                </svg>
                            </span>
                            <span id="hide-icon1" style="display: none;">
                                <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="#a5a9ad">
                                    <path d="M480-320q75 0 127.5-52.5T660-500q0-75-52.5-127.5T480-680q-75 0-127.5 52.5T300-500q0 75 52.5 127.5T480-320Zm0-72q-45 0-76.5-31.5T372-500q0-45 31.5-76.5T480-608q45 0 76.5 31.5T588-500q0 45-31.5 76.5T480-392Zm0 192q-146 0-266-81.5T40-500q54-137 174-218.5T480-800q146 0 266 81.5T920-500q-54 137-174 218.5T480-200Zm0-300Zm0 220q113 0 207.5-59.5T832-500q-50-101-144.5-160.5T480-720q-113 0-207.5 59.5T128-500q50 101 144.5 160.5T480-280Z"/>
                                </svg>
                            </span>
                        </div>
                    </div>
                    @error('password')
                        <span class="invalid-feedback" role="alert" style="display: block;">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-2">
                    <div class="dash-input mb-3" style="position: relative;">
                        <label class="input-lables pb-2" for="confirm_password">{{ __('messages.Confirm Password') }}</label>
                        <input type="password" required class="form-control login-input" placeholder="{{ __('messages.Confirm Password') }}" id="confirm_password" name="password_confirmation" value="{{ old('password_confirmation') }}" />
                        <div class="show-pass1" style="position: absolute; right: 10px; top: 65%; transform: translateY(-50%); cursor: pointer;" onclick="togglePasswordVisibility('confirm_password', 'show-icon2', 'hide-icon2')">
                            <span id="show-icon2" style="display: inline;">
                                <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="#a5a9ad">
                                    <path d="m644-428-58-58q9-47-27-88t-93-32l-58-58q17-8 34.5-12t37.5-4q75 0 127.5 52.5T660-500q0 20-4 37.5T644-428Zm128 126-58-56q38-29 67.5-63.5T832-500q-50-101-143.5-160.5T480-720q-29 0-57 4t-55 12l-62-62q41-17 84-25.5t90-8.5q151 0 269 83.5T920-500q-23 59-60.5 109.5T772-302Zm20 246L624-222q-35 11-70.5 16.5T480-200q-151 0-269-83.5T40-500q21-53 53-98.5t73-81.5L56-792l56-56 736 736-56 56ZM222-624q-29 26-53 57t-41 67q50 101 143.5 160.5T480-280q20 0 39-2.5t39-5.5l-36-38q-11 3-21 4.5t-21 1.5q-75 0-127.5-52.5T300-500q0-11 1.5-21t4.5-21l-84-82Zm319 93Zm-151 75Z"/>
                                </svg>
                            </span>
                            <span id="hide-icon2" style="display: none;">
                                <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="#a5a9ad">
                                    <path d="M480-320q75 0 127.5-52.5T660-500q0-75-52.5-127.5T480-680q-75 0-127.5 52.5T300-500q0 75 52.5 127.5T480-320Zm0-72q-45 0-76.5-31.5T372-500q0-45 31.5-76.5T480-608q45 0 76.5 31.5T588-500q0 45-31.5 76.5T480-392Zm0 192q-146 0-266-81.5T40-500q54-137 174-218.5T480-800q146 0 266 81.5T920-500q-54 137-174 218.5T480-200Zm0-300Zm0 220q113 0 207.5-59.5T832-500q-50-101-144.5-160.5T480-720q-113 0-207.5 59.5T128-500q50 101 144.5 160.5T480-280Z"/>
                                </svg>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="buttons mt-5">
                <a href="{{ route('drivers.all') }}" class="cancelBtn">{{ __('messages.Cancel') }}</a>
                <button type="submit" class="mainBtn">{{ __('messages.Submit') }}</button>
            </div>
        </form>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
    // Set the max attribute to today's date using JavaScript
    document.getElementById('licenseStartDate').setAttribute('max', new Date().toISOString().split('T')[0]);

    function togglePasswordVisibility(inputId, showIconId, hideIconId) {
        const inputField = document.getElementById(inputId);
        const showIcon = document.getElementById(showIconId);
        const hideIcon = document.getElementById(hideIconId);

        if (inputField.type === "password") {
            inputField.type = "text";
            showIcon.style.display = "none";
            hideIcon.style.display = "inline";
        } else {
            inputField.type = "password";
            showIcon.style.display = "inline";
            hideIcon.style.display = "none";
        }
    }

    // Combine country code and phone number before form submission
    document.getElementById('driverForm').addEventListener('submit', function(e) {
        var countryCode = document.getElementById('country_code').value;
        var phoneNumber = document.getElementById('phone_number').value;
        document.getElementById('full_phone').value = countryCode + phoneNumber;
    });

    // Update full phone whenever country code or phone number changes
    document.getElementById('country_code').addEventListener('change', updateFullPhone);
    document.getElementById('phone_number').addEventListener('input', updateFullPhone);

    function updateFullPhone() {
        var countryCode = document.getElementById('country_code').value;
        var phoneNumber = document.getElementById('phone_number').value;
        document.getElementById('full_phone').value = countryCode + phoneNumber;
    }
</script>
@endsection
