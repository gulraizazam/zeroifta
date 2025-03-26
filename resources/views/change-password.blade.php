@extends('layouts.new_main')
@section('content')
<div class="dashbord-inner">

  <div class="profile-area mb-4">

    <div class="style-bg">
      <div class="sec2-style">
        <div class="prof-img">
          <div class="up-head">
            <div class="up-headLeft">
              @if(Auth::user()->image)
              <img src="{{asset('images')}}/{{Auth::user()->image}}" alt="profile_image" class="w-100 border-radius-lg shadow-sm" style="height: 100px;border-radius:100%">
              @else
              <!-- <img src="{{asset('assets/user.png')}}" alt="profile_image" class="w-100 border-radius-lg shadow-sm" style="height: 100px;"> -->
              <span class="profile-ph">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200" fill="none">
                  <path d="M154.167 168.725V133.333C154.167 133.333 133.333 120.833 100 120.833C66.6667 120.833 45.8333 133.333 45.8333 133.333V168.725M12.5 100C12.5 51.675 51.675 12.5 100 12.5C148.325 12.5 187.5 51.675 187.5 100C187.5 148.325 148.325 187.5 100 187.5C51.675 187.5 12.5 148.325 12.5 100ZM99.3833 104.167C99.3833 104.167 70.8333 89 70.8333 66.6667C70.8333 50.5583 83.9083 37.5 100.033 37.5C103.862 37.4989 107.654 38.2528 111.191 39.7186C114.728 41.1844 117.942 43.3333 120.648 46.0423C123.354 48.7513 125.499 51.9673 126.961 55.5063C128.423 59.0452 129.172 62.8377 129.167 66.6667C129.167 89 100.617 104.167 100.617 104.167H99.3833Z" stroke="white" stroke-width="3"></path>
                </svg>
              </span>
              @endif
              <div class="up-info">
                <p class="head-18Med">{{Auth::user()->name}}</p>
                <p class="head-14Med white">{{Auth::user()->email}}</p>
              </div>
            </div>
            <!-- <div class="edit-icon">
                            <a class="hover" href="profile-management-form.html">
                                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 45 45" fill="none">
                                    <path
                                        d="M35.1562 5.66864C34.0791 5.66864 33.0244 6.0863 32.2116 6.89911L18.2812 20.7858L17.9733 21.0938L17.8861 21.534L16.9186 26.4558L16.4798 28.5202L18.5456 28.0815L23.4675 27.114L23.9062 27.0268L24.2142 26.7188L38.1009 12.7885C38.683 12.205 39.0792 11.4622 39.2397 10.6539C39.4002 9.84548 39.3178 9.00768 39.0029 8.24607C38.688 7.48447 38.1546 6.83314 37.47 6.37421C36.7855 5.91528 35.9804 5.67069 35.1562 5.66864ZM35.1562 8.39395C35.4853 8.39395 35.8102 8.5627 36.1237 8.8763C36.7495 9.50349 36.7495 10.1841 36.1237 10.8099L22.5 24.435L20.0827 24.9188L20.5664 22.5015L34.1902 8.87771C34.5023 8.56552 34.8272 8.39395 35.1562 8.39395ZM5.625 11.25V39.3751H33.75V20.8294L30.9375 23.6419V36.5626H8.4375V14.0625H21.3581L24.1706 11.25H5.625Z"
                                        fill="white"
                                    />
                                </svg>
                            </a>
                        </div> -->
          </div>
        </div>
        <!-- Profile Inner -->
        <div class="sec-stylePad">
          @if(Session::has('success'))
          <div class="alert alert-success alert-dismissible fade show" role="alert" style="background-color: #13975b;color:white">
            {{Session::get('success')}}
            <!-- <button type="button" class="close" data-bs-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button> -->
          </div>
          @endif
          @if(Session::has('error'))
          <div class="alert alert-danger alert-dismissible fade show" role="alert" style="background-color: #dd4957;color:white">
            {{Session::get('error')}}
            <!-- <button type="button" class="close" data-bs-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button> -->
          </div>
          @endif
          <form method="post" action="{{route('passwords.updatePass')}}" enctype="multipart/form-data">
            @csrf
            <div class="row pt-3">

              <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-2">
                <div class="dash-input mb-3" style="position: relative;">
                  <label class="input-lables pb-2" for="exampleFormControlInput1" class="pb-1">{{__('messages.Current Password')}}</label>
                  <input type="password" class="form-control dis-input" id="current_password" placeholder="{{__('messages.Current Password')}}" name="current_password" required />
                  <div class="show-pass1 position-absolute"
                            style="right: 10px; top: 65%; transform: translateY(-50%); cursor: pointer;"
                            onclick="togglePasswordVisibility('current_password', 'show-icon1', 'hide-icon1')">

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
                @error('current_password')
                <span class="invalid-feedback" role="alert" style="display: block;">
                  <strong>{{ $message }}</strong>
                </span>
                @enderror
              </div>
              <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-2">
                <div class="dash-input mb-3" style="position: relative;">
                  <label class="input-lables pb-2" for="exampleFormControlInput1" class="pb-1">{{__('messages.New Password')}}</label>
                  <input type="password" class="form-control dis-input" id="password" placeholder="{{__('messages.New Password')}}" name="password" required />
                  <div class="show-pass1" style="position: absolute; right: 10px; top: 65%; transform: translateY(-50%); cursor: pointer;" onclick="togglePasswordVisibility('password', 'show-icon2', 'hide-icon2')">
                    <span id="show-icon2" style="display: inline;" >
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
                @error('password')
                <span class="invalid-feedback" role="alert" style="display: block;">
                  <strong>{{ $message }}</strong>
                </span>
                @enderror
              </div>
              <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-2">
                <div class="dash-input mb-3" style="position: relative;">
                  <label class="input-lables pb-2" for="exampleFormControlInput1" class="pb-1">{{__('messages.Confirm Password')}}</label>
                  <input type="password" class="form-control dis-input" id="confirm_password" placeholder="{{__('messages.Confirm Password')}}" name="password_confirmation" required />
                  <div class="show-pass3" style="position: absolute; right: 10px; top: 65%; transform: translateY(-50%); cursor: pointer;" onclick="togglePasswordVisibility('confirm_password', 'show-icon3', 'hide-icon3')">
                    <span id="show-icon3" style="display: inline;" >
                        <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="#a5a9ad">
                            <path d="m644-428-58-58q9-47-27-88t-93-32l-58-58q17-8 34.5-12t37.5-4q75 0 127.5 52.5T660-500q0 20-4 37.5T644-428Zm128 126-58-56q38-29 67.5-63.5T832-500q-50-101-143.5-160.5T480-720q-29 0-57 4t-55 12l-62-62q41-17 84-25.5t90-8.5q151 0 269 83.5T920-500q-23 59-60.5 109.5T772-302Zm20 246L624-222q-35 11-70.5 16.5T480-200q-151 0-269-83.5T40-500q21-53 53-98.5t73-81.5L56-792l56-56 736 736-56 56ZM222-624q-29 26-53 57t-41 67q50 101 143.5 160.5T480-280q20 0 39-2.5t39-5.5l-36-38q-11 3-21 4.5t-21 1.5q-75 0-127.5-52.5T300-500q0-11 1.5-21t4.5-21l-84-82Zm319 93Zm-151 75Z"/>
                        </svg>
                    </span>
                    <span id="hide-icon3" style="display: none;">
                        <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="#a5a9ad">
                            <path d="M480-320q75 0 127.5-52.5T660-500q0-75-52.5-127.5T480-680q-75 0-127.5 52.5T300-500q0 75 52.5 127.5T480-320Zm0-72q-45 0-76.5-31.5T372-500q0-45 31.5-76.5T480-608q45 0 76.5 31.5T588-500q0 45-31.5 76.5T480-392Zm0 192q-146 0-266-81.5T40-500q54-137 174-218.5T480-800q146 0 266 81.5T920-500q-54 137-174 218.5T480-200Zm0-300Zm0 220q113 0 207.5-59.5T832-500q-50-101-144.5-160.5T480-720q-113 0-207.5 59.5T128-500q50 101 144.5 160.5T480-280Z"/>
                        </svg>
                    </span>
                </div>
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
</script>

@endsection
