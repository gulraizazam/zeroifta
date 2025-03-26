@extends('layouts.new_main')
@section('content')

<div class="dashbord-inner">
    <!-- Section 1 -->
    <div class="profileForm-area mb-4">
        <div class="sec1-style">
        <form method="post" action="{{route('driver.update',$driver->id)}}" enctype="multipart/form-data">
            @csrf
            <div class="row pt-3">
                <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-2">
                    <div class="dash-input mb-3">
                        <label class="input-lables pb-2" for="exampleFormControlInput1" class="pb-1">{{__('messages.First Name')}}</label>
                        <input type="text" required class="form-control login-input" id="exampleFormControlInput1" placeholder="{{__('messages.First Name')}}" name="first_name" value="{{$driver->first_name}}"/>
                    </div>
                    @error('first_name')
                            <span class="invalid-feedback" role="alert" style="display: block;">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-2">
                    <div class="dash-input mb-3">
                        <label class="input-lables pb-2" for="exampleFormControlInput1" class="pb-1">{{__('messages.Last Name')}}</label>
                        <input type="text" required class="form-control login-input" id="exampleFormControlInput1" placeholder="{{__('messages.Last Name')}}" name="last_name" value="{{$driver->last_name}}"/>
                    </div>
                    @error('last_name')
                            <span class="invalid-feedback" role="alert" style="display: block;">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-2">
                    <div class="dash-input mb-3">
                        <label class="input-lables pb-2" for="exampleFormControlInput1" class="pb-1">{{__('messages.Driver ID')}}</label>
                        <input type="text" required class="form-control login-input" id="exampleFormControlInput1" placeholder="{{__('messages.Driver ID')}}" name="driver_id" value="{{$driver->driver_id}}"/>
                    </div>
                    @error('driver_id')
                            <span class="invalid-feedback" role="alert" style="display: block;">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-2">
                    <div class="dash-input mb-3">
                        <label class="input-lables pb-2" for="exampleFormControlInput1" class="pb-1">{{__('messages.Phone')}}</label>
                        <input type="text" required class="form-control login-input" id="exampleFormControlInput1" placeholder="{{__('messages.Phone')}}" name="phone" value="{{$driver->phone}}" />
                    </div>
                    @error('phone')
                            <span class="invalid-feedback" role="alert" style="display: block;">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-2">
                    <div class="dash-input mb-3">
                        <label class="input-lables pb-2" for="exampleFormControlInput1" class="pb-1">{{__('messages.License State')}}</label>
                        <select name="license_state" id="state" class="form-control login-input" required>
                            <option value="">-- Select a State --</option>
                            <option value="AL" {{$driver->license_state == 'AL' ? 'selected' : ''}}>Alabama</option>
                            <option value="AK" {{$driver->license_state == 'AK' ? 'selected' : ''}}>Alaska</option>
                            <option value="AZ" {{$driver->license_state == 'AZ' ? 'selected' : ''}}>Arizona</option>
                            <option value="AR" {{$driver->license_state == 'AR' ? 'selected' : ''}}>Arkansas</option>
                            <option value="CA" {{$driver->license_state == 'CA' ? 'selected' : ''}}>California</option>
                            <option value="CO" {{$driver->license_state == 'CO' ? 'selected' : ''}}>Colorado</option>
                            <option value="CT" {{$driver->license_state == 'CT' ? 'selected' : ''}}>Connecticut</option>
                            <option value="DE" {{$driver->license_state == 'DE' ? 'selected' : ''}}>Delaware</option>
                            <option value="FL" {{$driver->license_state == 'FL' ? 'selected' : ''}}>Florida</option>
                            <option value="GA" {{$driver->license_state == 'GA' ? 'selected' : ''}}>Georgia</option>
                            <option value="HI" {{$driver->license_state == 'HI' ? 'selected' : ''}}>Hawaii</option>
                            <option value="ID" {{$driver->license_state == 'ID' ? 'selected' : ''}}>Idaho</option>
                            <option value="IL" {{$driver->license_state == 'IL' ? 'selected' : ''}}>Illinois</option>
                            <option value="IN" {{$driver->license_state == 'IN' ? 'selected' : ''}}>Indiana</option>
                            <option value="IA" {{$driver->license_state == 'IA' ? 'selected' : ''}}>Iowa</option>
                            <option value="KS" {{$driver->license_state == 'KS' ? 'selected' : ''}}>Kansas</option>
                            <option value="KY" {{$driver->license_state == 'KY' ? 'selected' : ''}}>Kentucky</option>
                            <option value="LA" {{$driver->license_state == 'LA' ? 'selected' : ''}}>Louisiana</option>
                            <option value="ME" {{$driver->license_state == 'ME' ? 'selected' : ''}}>Maine</option>
                            <option value="MD" {{$driver->license_state == 'MD' ? 'selected' : ''}}>Maryland</option>
                            <option value="MA" {{$driver->license_state == 'MA' ? 'selected' : ''}}>Massachusetts</option>
                            <option value="MI" {{$driver->license_state == 'MI' ? 'selected' : ''}}>Michigan</option>
                            <option value="MN" {{$driver->license_state == 'MN' ? 'selected' : ''}}>Minnesota</option>
                            <option value="MS" {{$driver->license_state == 'MS' ? 'selected' : ''}}>Mississippi</option>
                            <option value="MO" {{$driver->license_state == 'MO' ? 'selected' : ''}}>Missouri</option>
                            <option value="MT" {{$driver->license_state == 'MT' ? 'selected' : ''}}>Montana</option>
                            <option value="NE" {{$driver->license_state == 'NE' ? 'selected' : ''}}>Nebraska</option>
                            <option value="NV" {{$driver->license_state == 'NV' ? 'selected' : ''}}>Nevada</option>
                            <option value="NH" {{$driver->license_state == 'NH' ? 'selected' : ''}}>New Hampshire</option>
                            <option value="NJ" {{$driver->license_state == 'NJ' ? 'selected' : ''}}>New Jersey</option>
                            <option value="NM" {{$driver->license_state == 'NM' ? 'selected' : ''}}>New Mexico</option>
                            <option value="NY" {{$driver->license_state == 'NY' ? 'selected' : ''}}>New York</option>
                            <option value="NC" {{$driver->license_state == 'NC' ? 'selected' : ''}}>North Carolina</option>
                            <option value="ND" {{$driver->license_state == 'ND' ? 'selected' : ''}}>North Dakota</option>
                            <option value="OH" {{$driver->license_state == 'OH' ? 'selected' : ''}}>Ohio</option>
                            <option value="OK" {{$driver->license_state == 'OK' ? 'selected' : ''}}>Oklahoma</option>
                            <option value="OR" {{$driver->license_state == 'OR' ? 'selected' : ''}}>Oregon</option>
                            <option value="PA" {{$driver->license_state == 'PA' ? 'selected' : ''}}>Pennsylvania</option>
                            <option value="RI" {{$driver->license_state == 'RI' ? 'selected' : ''}}>Rhode Island</option>
                            <option value="SC" {{$driver->license_state == 'SC' ? 'selected' : ''}}>South Carolina</option>
                            <option value="SD" {{$driver->license_state == 'SD' ? 'selected' : ''}}>South Dakota</option>
                            <option value="TN" {{$driver->license_state == 'TN' ? 'selected' : ''}}>Tennessee</option>
                            <option value="TX" {{$driver->license_state == 'TX' ? 'selected' : ''}}>Texas</option>
                            <option value="UT" {{$driver->license_state == 'UT' ? 'selected' : ''}}>Utah</option>
                            <option value="VT" {{$driver->license_state == 'VT' ? 'selected' : ''}}>Vermont</option>
                            <option value="VA" {{$driver->license_state == 'VA' ? 'selected' : ''}}>Virginia</option>
                            <option value="WA" {{$driver->license_state == 'WA' ? 'selected' : ''}}>Washington</option>
                            <option value="WV" {{$driver->license_state == 'WV' ? 'selected' : ''}}>West Virginia</option>
                            <option value="WI" {{$driver->license_state == 'WI' ? 'selected' : ''}}>Wisconsin</option>
                            <option value="WY" {{$driver->license_state == 'WY' ? 'selected' : ''}}>Wyoming</option>
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
                        <label class="input-lables pb-2" for="exampleFormControlInput1" class="pb-1">{{__('messages.License Number')}}</label>
                        <input type="text" required class="form-control login-input" id="exampleFormControlInput1" placeholder="{{__('messages.License Number')}}" name="license_number" value="{{$driver->license_number}}" />
                    </div>
                    @error('license_number')
                            <span class="invalid-feedback" role="alert" style="display: block;">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-2">
                    <div class="dash-input mb-3">
                        <label class="input-lables pb-2" for="exampleFormControlInput1" class="pb-1">{{__('messages.License Start Date')}}</label>
                        <input type="date" required class="form-control login-input" id="exampleFormControlInput1" placeholder="{{__('messages.License Start Date')}}" name="license_start_date" value="{{$driver->license_start_date}}" />
                    </div>
                    @error('license_start_date')
                            <span class="invalid-feedback" role="alert" style="display: block;">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-2">
                    <div class="dash-input mb-3">
                        <label class="input-lables pb-2" for="exampleFormControlInput1" class="pb-1">{{__('messages.Email')}}</label>
                        <input type="email" required class="form-control login-input" id="exampleFormControlInput1" placeholder="{{__('messages.Email')}}" name="email" value="{{$driver->email}}" />
                    </div>
                    @error('email')
                            <span class="invalid-feedback" role="alert" style="display: block;">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-2">
                    <div class="dash-input mb-3">
                        <label class="input-lables pb-2" for="exampleFormControlInput1" class="pb-1">{{__('messages.Username')}}</label>
                        <input type="text" required class="form-control login-input" id="exampleFormControlInput1" placeholder="{{__('messages.Username')}}" name="username" value="{{$driver->username}}" />
                    </div>
                    @error('username')
                            <span class="invalid-feedback" role="alert" style="display: block;">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-2">
                    <div class="dash-input mb-3">
                        <label class="input-lables pb-2" for="exampleFormControlInput1" class="pb-1">{{__('messages.Profile Picture')}}</label>
                        <input type="file"  class="form-control login-input choose-file-input" id="exampleFormControlInput1"  name="profile_picture"  />
                    </div>
                    @error('profile_picture')
                            <span class="invalid-feedback" role="alert" style="display: block;">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                </div>



            </div>
            <div class="buttons mt-5">
                <a href="{{route('drivers.all')}}" class="cancelBtn">{{__('messages.Cancel')}}</a>
                <button type="submit"  class="mainBtn">{{__('messages.Submit')}}</a>
            </div>
        </div>
    </div>
</div>
@endsection
