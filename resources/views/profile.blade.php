@extends('layouts.new_main')
@section('content')
<style>
.custom-file-input-wrapper {
    position: relative;
    display: inline-block;
    width: 100%;
}

.custom-file-label {
    display: block;
    border: 1px solid #ccc;
    padding: 10px;
    background-color: #f8f9fa;
    cursor: pointer;
    text-align: left;
}

.custom-file-input {
    opacity: 0;
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    cursor: pointer;
}

  </style>
<div class="dashbord-inner">
  @if(Session::has('success'))
  <div class="alert alert-success alert-dismissible fade show" role="alert" style="background-color: #13975b;color:white">
    {{Session::get('success')}}
      <!-- <button type="button" class="close" data-bs-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button> -->
  </div>
  @endif
  <div class="profile-area mb-4">
    <div class="style-bg">
      <div class="sec2-style">
        <div class="prof-img">
          <div class="up-head">
            <div class="up-headLeft">
              @if(Auth::user()->image)
              <div class="prof-wrap">
                <img src="{{asset('images')}}/{{Auth::user()->image}}" alt="profile_image" class="profile-img border-radius-lg shadow-sm">
              </div>

              @else
              <img src="{{asset('assets/user_placeholder.svg')}}" alt="profile_image" class="w-100 border-radius-lg shadow-sm rounded-image" style="height: 100px;">
              <!-- <span class="profile-ph">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200" fill="none">
                  <path d="M154.167 168.725V133.333C154.167 133.333 133.333 120.833 100 120.833C66.6667 120.833 45.8333 133.333 45.8333 133.333V168.725M12.5 100C12.5 51.675 51.675 12.5 100 12.5C148.325 12.5 187.5 51.675 187.5 100C187.5 148.325 148.325 187.5 100 187.5C51.675 187.5 12.5 148.325 12.5 100ZM99.3833 104.167C99.3833 104.167 70.8333 89 70.8333 66.6667C70.8333 50.5583 83.9083 37.5 100.033 37.5C103.862 37.4989 107.654 38.2528 111.191 39.7186C114.728 41.1844 117.942 43.3333 120.648 46.0423C123.354 48.7513 125.499 51.9673 126.961 55.5063C128.423 59.0452 129.172 62.8377 129.167 66.6667C129.167 89 100.617 104.167 100.617 104.167H99.3833Z" stroke="white" stroke-width="3" />
                </svg>
              </span> -->

              @endif
              <div class="up-info">
                <p class="head-18Med">{{Auth::user()->name}}</p>
                <p class="head-14Med white">{{Auth::user()->email}}</p>
              </div>
            </div>

          </div>
        </div>
        <!-- Profile Inner -->
        <div class="sec-stylePad">
          <form method="post" action="{{route('profile.update')}}" enctype="multipart/form-data">
            @csrf
            <div class="row pt-3">

              <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-2">
                <div class="dash-input mb-3">
                  <label class="input-lables pb-2" for="exampleFormControlInput1" class="pb-1">{{__('messages.Name')}}</label>
                  <input type="text" class="form-control dis-input" id="exampleFormControlInput1" placeholder="{{__('messages.Name')}}" name="name" value="{{Auth::user()->name}}" />
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
                  <input type="email" class="form-control dis-input" id="exampleFormControlInput1" placeholder="{{__('messages.Email Address')}}" name="email" value="{{Auth::user()->email}}" />
                </div>
                @error('email')
                <span class="invalid-feedback" role="alert" style="display: block;">
                  <strong>{{ $message }}</strong>
                </span>
                @enderror
              </div>

              <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-2">
                <div class="dash-input mb-3">
                  <label class="input-lables pb-2" for="exampleFormControlInput1" class="pb-1">{{__('messages.Phone')}}</label>
                  <input type="number" class="form-control dis-input" id="exampleFormControlInput1" placeholder="{{__('messages.Phone')}}" name="phone" value="{{Auth::user()->phone}}" />
                </div>
                @error('phone')
                <span class="invalid-feedback" role="alert" style="display: block;">
                  <strong>{{ $message }}</strong>
                </span>
                @enderror
              </div>
              <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-2">
                <div class="dash-input mb-3">
                    <label class="input-lables pb-2" for="profilePicture">{{ __('messages.Profile Picture') }}</label>


                        <input
                            type="file"
                            class="form-control dis-input choose-file-input"

                            name="image"
                            accept="image/png, image/jpg, image/jpeg"
                            />

                </div>
            </div>
              <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-2">
                <div class="dash-input mb-3">
                  <label class="input-lables pb-2" for="exampleFormControlInput1" class="pb-1">{{__('messages.MC')}}</label>
                  <input type="number" class="form-control dis-input" maxlength="10" id="exampleFormControlInput1" placeholder="{{__('messages.MC')}}" name="mc" value="{{Auth::user()->mc}}" />
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
                  <input type="number" class="form-control dis-input" id="exampleFormControlInput1" maxlength="10" placeholder="{{__('messages.DOT')}}" name="dot" value="{{Auth::user()->dot}}" />
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
                  <input type="text" class="form-control dis-input" id="exampleFormControlInput1" placeholder="{{__('messages.City')}}" name="city" value="{{Auth::user()->city}}" />
                </div>
              </div>
              <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-2">
                <div class="dash-input mb-3">
                  <label class="input-lables pb-2" for="exampleFormControlInput1" class="pb-1">{{__('messages.State')}}</label>
                  <input type="text" class="form-control dis-input" id="exampleFormControlInput1" placeholder="{{__('messages.State')}}" name="state" value="{{Auth::user()->state}}" />
                </div>
              </div>



              <div class="col-lg-12 col-md-12 col-sm-12 col-12 mb-2">
                <div class="dash-input mb-3">
                  <button type="submit" class="mainBtn">{{__('messages.Submit')}}</button>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
@section('scripts')
<script>
function updateFileLabel(input) {
    const fileName = input.files[0]?.name || '{{ __('messages.No File chosen') }}';
    document.getElementById('fileLabel').textContent = fileName;
}
document.querySelector("input").addEventListener("input", function () {
  if (this.value.length > 10) {
    alert("Maximum 10 characters allowed");
    this.value = this.value.slice(0, 10);
  }
});
  </script>
@endsection
