@extends('layouts.new_main')
@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
    .toggle-password {
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #6c757d;
    }
</style>
<div class="dashbord-inner">
    <!-- Section 1 -->
    <div class="profileForm-area mb-4">
    <form method="post" action="{{route('companies.update',$company->id)}}">
    @csrf
        <div class="sec1-style">
            <div class="row pt-3">
                <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-2">
                    <div class="dash-input mb-3">
                        <label class="input-lables pb-2" for="exampleFormControlInput1" class="pb-1">{{__('messages.Name')}}</label>
                        <input type="text" class="form-control login-input" id="exampleFormControlInput1" placeholder="{{__('messages.Name')}}"  name="name"  value="{{$company->name}}"/>
                    </div>
                    @error('name')
                            <span class="invalid-feedback" role="alert" style="display: block;">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-2">
                    <div class="dash-input mb-3">
                        <label class="input-lables pb-2" for="exampleFormControlInput1" class="pb-1">{{__('messages.Email Address')}}</label>
                        <input type="email" class="form-control login-input" id="exampleFormControlInput1" placeholder="{{__('messages.Email Address')}}" name="email"  value="{{$company->email}}"/>
                    </div>
                    @error('email')
                                <span class="invalid-feedback" role="alert" style="display: block;">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                </div>


                <div class="col-lg-12 col-md-12 col-sm-12 col-12 mb-2">
                    <div class="dash-input mb-3">
                        <label class="input-lables pb-2" for="exampleFormControlInput1" class="pb-1">{{__('messages.Phone')}}</label>
                        <input type="text" class="form-control login-input" id="exampleFormControlInput1" name="phone" placeholder="{{__('messages.Phone')}}" value="{{$company->phone}}" />
                    </div>
                    @error('phone')
                                <span class="invalid-feedback" role="alert" style="display: block;">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-2">
                    <div class="dash-input mb-3">
                        <label class="input-lables pb-2" for="exampleFormControlInput1" class="pb-1">{{__('messages.MC')}}</label>
                        <input type="text" class="form-control login-input" id="exampleFormControlInput1" placeholder="{{__('messages.MC')}}" name="mc" value="{{$company->mc}}" required/>
                    </div>
                    @error('mc')
                                <span class="invalid-feedback" role="alert" style="display: block;">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-2">
                    <div class="dash-input mb-3">
                        <label class="input-lables pb-2" for="exampleFormControlInput1" class="pb-1">{{__('messages.DOT')}}</label>
                        <input type="text" class="form-control login-input dis-input" id="exampleFormControlInput1" name="dot" placeholder="{{__('messages.DOT')}}" value="{{$company->dot}}" required/>
                    </div>
                    @error('dot')
                                <span class="invalid-feedback" role="alert" style="display: block;">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-2">
                    <div class="dash-input mb-3">
                        <label class="input-lables pb-2" for="exampleFormControlInput1" class="pb-1">{{__('messages.City')}}</label>
                        <input type="text" class="form-control login-input" id="exampleFormControlInput1" name="city" placeholder="{{__('messages.City')}}" value="{{$company->city}}"/>
                    </div>
                    @error('city')
                                <span class="invalid-feedback" role="alert" style="display: block;">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-2">
                    <div class="dash-input mb-3">
                        <label class="input-lables pb-2" for="exampleFormControlInput1" class="pb-1">{{__('messages.State')}}</label>
                        <select name="state" class="form-control login-input" name="state">
                      <option value="">Select state</option>
                      <option value="Alabama" {{$company->state=="Alabama" ? 'selected':''}}> Alabama</option>
                      <option value="Arizona" {{$company->state=="Arizona" ? 'selected':''}}>Arizona</option>
                      <option value="Arkansas" {{$company->state=="Arkansas" ? 'selected':''}}>Arkansas</option>
                      <option value="California" {{$company->state=="California" ? 'selected':''}}>California</option>
                      <option value="Colorado" {{$company->state=="Colorado" ? 'selected':''}}>Colorado</option>
                      <option value="Connecticut" {{$company->state=="Connecticut" ? 'selected':''}}>Connecticut</option>
                      <option value="Delaware" {{$company->state=="Delaware" ? 'selected':''}}>Delaware</option>
                      <option value="Florida" {{$company->state=="Florida" ? 'selected':''}}>Florida</option>
                      <option value="Georgia" {{$company->state=="Georgia" ? 'selected':''}}>Georgia</option>
                      <option value="Idaho" {{$company->state=="Idaho" ? 'selected':''}}>Idaho</option>
                      <option value="Illinois" {{$company->state=="Illinois" ? 'selected':''}}>Illinois</option>
                      <option value="Indiana" {{$company->state=="Indiana" ? 'selected':''}}>Indiana</option>
                      <option value="Iowa" {{$company->state=="Iowa" ? 'selected':''}}>Iowa</option>
                      <option value="Kansas" {{$company->state=="Kansas" ? 'selected':''}}>Kansas</option>
                      <option value="Kentucky" {{$company->state=="Kentucky" ? 'selected':''}}>Kentucky</option>
                      <option value="Louisiana" {{$company->state=="Louisiana" ? 'selected':''}}>Louisiana</option>
                      <option value="Maine" {{$company->state=="Maine" ? 'selected':''}}>Maine</option>
                      <option value="Maryland" {{$company->state=="Maryland" ? 'selected':''}}>Maryland</option>
                      <option value="Massachusetts" {{$company->state=="Massachusetts" ? 'selected':''}}>Massachusetts</option>
                      <option value="Michigan" {{$company->state=="Michigan" ? 'selected':''}}>Michigan</option>
                      <option value="Minnesota" {{$company->state=="Minnesota" ? 'selected':''}}>Minnesota</option>
                      <option value="Mississippi" {{$company->state=="Mississippi" ? 'selected':''}}>Mississippi</option>
                                    <option value="Missouri" {{$company->state=="Missouri" ? 'selected':''}}>
                        Missouri</option>
                                    <option value="Montana" {{$company->state=="Montana" ? 'selected':''}}>
                        Montana</option>
                                    <option value="Nebraska" {{$company->state=="Nebraska" ? 'selected':''}}>
                        Nebraska</option>
                                    <option value="Nevada" {{$company->state=="Nevada" ? 'selected':''}}>
                        Nevada</option>
                                    <option value="New Hampshire" {{$company->state=="New Hampshire" ? 'selected':''}}>
                        New Hampshire</option>
                                    <option value="New Jersey" {{$company->state=="New Jersey" ? 'selected':''}}>
                        New Jersey</option>
                                    <option value="New Mexico" {{$company->state=="New Mexico" ? 'selected':''}}>
                        New Mexico</option>
                                    <option value="New York" {{$company->state=="New York" ? 'selected':''}}>
                        New York</option>
                                    <option value="North Carolina" {{$company->state=="North Carolina" ? 'selected':''}}>
                        North Carolina</option>
                                    <option value="North Dakota" {{$company->state=="North Dakota" ? 'selected':''}}>
                        North Dakota</option>
                                    <option value="Ohio" {{$company->state=="Ohio" ? 'selected':''}}>
                        Ohio</option>
                                    <option value="Oklahoma" {{$company->state=="Oklahoma" ? 'selected':''}}>
                        Oklahoma</option>
                                    <option value="Oregon" {{$company->state=="Oregon" ? 'selected':''}}>
                        Oregon</option>
                                    <option value="Pennsylvania" {{$company->state=="Pennsylvania" ? 'selected':''}}>
                        Pennsylvania</option>
                                    <option value="Rhode Island" {{$company->state=="Rhode Island" ? 'selected':''}}>
                        Rhode Island</option>
                                    <option value="South Carolina" {{$company->state=="South Carolina" ? 'selected':''}}>
                        South Carolina</option>
                                    <option value="South Dakota" {{$company->state=="South Dakota" ? 'selected':''}}>
                        South Dakota</option>
                                    <option value="Tennessee" {{$company->state=="Tennessee" ? 'selected':''}}>
                        Tennessee</option>
                                    <option value="Texas" {{$company->state=="Texas" ? 'selected':''}}>
                        Texas</option>
                                    <option value="Utah" {{$company->state=="Utah" ? 'selected':''}}>
                        Utah</option>
                                    <option value="Vermont" {{$company->state=="Vermont" ? 'selected':''}}>
                        Vermont</option>
                                    <option value="Virginia" {{$company->state=="Virginia" ? 'selected':''}}>
                        Virginia</option>
                                    <option value="Washington" {{$company->state=="Washington" ? 'selected':''}}>
                        Washington</option>
                                    <option value="West Virginia" {{$company->state=="West Virginia" ? 'selected':''}}>
                        West Virginia</option>
                                    <option value="Wisconsin" {{$company->state=="Wisconsin" ? 'selected':''}}>
                        Wisconsin</option>
                                    <option value="Wyoming" {{$company->state=="Wyoming" ? 'selected':''}}>
                        Wyoming</option>
                            </select>
                    </div>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-12 mb-2">
                    <div class="dash-input mb-3">
                        <label class="input-lables pb-2" for="exampleFormControlInput1" class="pb-1">{{__('messages.ZIP')}}</label>
                        <input type="text" class="form-control login-input dis-input" id="exampleFormControlInput1" name="zip" placeholder="{{__('messages.ZIP')}}" value="{{$company->zip}}"/>
                    </div>
                </div>

                <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-2">
                    <div class="dash-input mb-3">
                        <label class="input-lables pb-2" for="exampleFormControlInput1" class="pb-1">{{__('messages.Contact Person Name')}}</label>
                        <input type="text" class="form-control login-input dis-input" id="exampleFormControlInput1" name="contact_person_name" placeholder="{{__('messages.Contact Person Name')}}" value="{{$company->contact_person_name}}"/>
                    </div>
                    @error('contact_person_name')
                                <span class="invalid-feedback" role="alert" style="display: block;">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-2">
                    <div class="dash-input mb-3">
                        <label class="input-lables pb-2" for="exampleFormControlInput1" class="pb-1">{{__('messages.Contact Person Email')}}</label>
                        <input type="text" class="form-control login-input dis-input" id="exampleFormControlInput1" name="contact_person_email" placeholder="{{__('messages.Contact Person Email')}}" value="{{$company->contact_person_email}}"/>
                    </div>
                    @error('contact_person_email')
                                <span class="invalid-feedback" role="alert" style="display: block;">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-12 mb-2">
                    <div class="dash-input mb-3">
                        <label class="input-lables pb-2" for="exampleFormControlInput1" class="pb-1">{{__('messages.Contact Person Phone')}}</label>
                        <input type="text" class="form-control login-input dis-input" id="exampleFormControlInput1"  name="contact_person_phone" placeholder="{{__('messages.Contact Person Phone')}}" value="{{$company->contact_person_phone}}"/>
                    </div>
                    @error('contact_person_phone')
                                <span class="invalid-feedback" role="alert" style="display: block;">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                </div>
            </div>
            <div class="buttons">
                <a href="{{route('companies')}}" class="cancelBtn">{{__('messages.Cancel')}}</a>
                <button type="submit"  class="mainBtn">{{__('messages.Submit')}}</button>
                @if($company->role == "trucker")
                <button type="button" class="mainBtn ms-2" data-bs-toggle="modal" data-bs-target="#changePassword">
                    {{__('messages.Change Password')}}
                </button>
                @endif
            </div>
        </div>
</form>
    </div>

    <!-- Change Password Modal -->
    @if($company->role == "trucker")
    <div class="change_pas_modal modal-comm">
        <div class="modal fade" id="changePassword" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="closeBtn" data-bs-dismiss="modal" aria-label="Close">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 28 28" fill="none">
                                <path d="M14 10.8894L24.8894 0L28 3.11062L17.1106 14L28 24.8894L24.8894 28L14 17.1106L3.11062 28L0 24.8894L10.8894 14L0 3.11062L3.11062 0L14 10.8894Z" />
                            </svg>
                        </button>
                    </div>
                    <div class="modal-body">
                        <h3>{{ __('messages.Change Password') }}</h3>
                        <div class="text-center mt-3">
                            <form method="post" action="{{ route('companies.changePassword', $company->id) }}" id="changePasswordForm">
                                <div class="dash-input mb-3 position-relative">
                                    <input type="password" name="password" placeholder="{{ __('messages.Password') }}" class="form-control" id="password">
                                    <span class="toggle-password position-absolute" toggle="#password">
                                        <i class="fa fa-eye-slash"></i>
                                    </span>
                                </div>
                                <div class="dash-input mb-3 position-relative">
                                    <input type="password" name="password_confirmation" placeholder="{{ __('messages.Confirm Password') }}" class="form-control" id="password_confirmation">
                                    <span class="toggle-password position-absolute" toggle="#password_confirmation">
                                        <i class="fa fa-eye-slash"></i>
                                    </span>
                                </div>
                            </form>
                            <div class="buttons pt-3">
                                <button type="button" class="cancelBtn" data-bs-dismiss="modal">{{ __('messages.Close') }}</button>
                                <button type="submit" id="submitBtn" class="mainBtn">{{ __('messages.Submit') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('#submitBtn').click(function() {
        // Get values
        var password = $('#password').val();
        var password_confirmation = $('#password_confirmation').val();

        // Validate password and confirm password
        if (password.length < 8) {
            alert('Password must be at least 8 characters long.');
            return;
        }

        if (password !== password_confirmation) {
            alert('Password and Confirm Password must be the same.');
            return;
        }

        // Prepare the form data for AJAX submission
        var formData = {
            password: password,
            password_confirmation: password_confirmation,
            _token: '{{ csrf_token() }}'  // CSRF token for security
        };

        // AJAX call to submit the form
        $.ajax({
            url: $('#changePasswordForm').attr('action'),
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.status === 200) {
                    alert('Password changed successfully');
                    // Close the modal (Bootstrap modal)
                    window.location.reload();
                } else {
                    alert('An error occurred while changing the password.');
                }
            },
            error: function(xhr, status, error) {
                // Handle errors (for example, validation errors)
                var errors = xhr.responseJSON.errors;
                if (errors) {
                    if (errors.password) {
                        alert(errors.password[0]); // Display password validation error
                    }
                    if (errors.password_confirmation) {
                        alert(errors.password_confirmation[0]); // Display password confirmation error
                    }
                }
            }
        });
    });
});

document.querySelectorAll(".toggle-password").forEach(function (toggle) {
    toggle.addEventListener("click", function () {
        let input = document.querySelector(this.getAttribute("toggle"));
        let icon = this.querySelector("i");

        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove("fa-eye-slash");
            icon.classList.add("fa-eye");
        } else {
            input.type = "password";
            icon.classList.remove("fa-eye");
            icon.classList.add("fa-eye-slash");
        }
    });
});
</script>
@endsection
