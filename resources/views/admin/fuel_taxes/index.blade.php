@extends('layouts.new_main')
@section('content')
<div class="dashbord-inner">
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
  <!-- Section 1 -->
  <div class="manage-comp mb-4">
    <div class="Filters-main mb-3 mb-md-4">
      <div class="sec1-style">
        <div class="tabele_filter">
          <div class="tabFilt_left">
            <!-- Show Filter -->
            <div class="sd-filter">

            </div>
            <!-- Sort By Filter -->
            <div class="sd2-filter">

            </div>
          </div>
          <div class="filter-btn">
            <a class="blueLine_btn" href="{{route('fuel_taxes.create')}}">{{__('messages.Add Fuel Tax')}} +</a>
          </div>
        </div>
      </div>
    </div>

    <div class="sec1-style">
      <div class="data_table table-span table-responsive">
        <table id="example" class="table table-comm">
          <thead>
            <tr>
              <th scope="col">
                <span>
                  <input id="remember-me" name="remember-me" type="checkbox" />
                </span>
              </th>
              <th scope="col">{{__('messages.State')}}</th>
              <th scope="col">{{__('messages.Alpha Code')}}</th>
              <th scope="col">{{__('messages.IFTA Tax')}}</th>
              <th scope="col">{{__('messages.Action')}}</th>
            </tr>
          </thead>
          <tbody>
            @foreach($fuelTaxes as $tax)
            <tr>
              <td>
                <span>
                  <input id="remember-me" name="remember-me" type="checkbox" />
                </span>
              </td>
              <td>
                <div class="table-conTab">
                  <span> <a href="{{route('fuel_taxes.edit',$tax->id)}}" style="color:var(--gray1)"> {{$tax->name}} </a></span>
                </div>
              </td>
              <td>
                <span>{{$tax->code}}</span>
              </td>
              <td><span>${{$tax->tax}}</span></td>
              <td>
                <div class="tabAction-list">
                  <span class="tabEdit-icon">
                    <a href="{{route('fuel_taxes.edit',$tax->id)}}">
                      <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none">
                        <path
                          d="M15.75 0.000749946C15.1755 0.000749946 14.613 0.2235 14.1795 0.657L6.75 8.06325L6.58575 8.2275L6.53925 8.46225L6.02325 11.0872L5.78925 12.1883L6.891 11.9543L9.516 11.4383L9.75 11.3918L9.91425 11.2275L17.3205 3.798C17.6309 3.4868 17.8422 3.09066 17.9278 2.65953C18.0135 2.2284 17.9695 1.78157 17.8015 1.37538C17.6336 0.96919 17.3491 0.621817 16.984 0.377053C16.6189 0.132289 16.1895 0.00184189 15.75 0.000749946ZM15.75 1.45425C15.9255 1.45425 16.0988 1.54425 16.266 1.7115C16.5998 2.046 16.5998 2.409 16.266 2.74275L9 10.0095L7.71075 10.2675L7.96875 8.97825L15.2348 1.71225C15.4013 1.54575 15.5745 1.45425 15.75 1.45425ZM0 2.9775V17.9775H15V8.0865L13.5 9.5865V16.4775H1.5V4.4775H8.391L9.891 2.9775H0Z"
                          fill="#092E75" />
                      </svg>
                    </a>
                  </span>
                  <span class="tabDel-icon">
                    <a class="hover" data-bs-toggle="modal" data-bs-target="#exampleModal-{{$tax->id}}">
                      <svg xmlns="http://www.w3.org/2000/svg" width="20" height="21" viewBox="0 0 20 21" fill="none">
                        <path
                          d="M8 3.5H12C12 2.96957 11.7893 2.46086 11.4142 2.08579C11.0391 1.71071 10.5304 1.5 10 1.5C9.46957 1.5 8.96086 1.71071 8.58579 2.08579C8.21071 2.46086 8 2.96957 8 3.5ZM6.5 3.5C6.5 3.04037 6.59053 2.58525 6.76642 2.16061C6.94231 1.73597 7.20012 1.35013 7.52513 1.02513C7.85013 0.700121 8.23597 0.442313 8.66061 0.266422C9.08525 0.0905302 9.54037 0 10 0C10.4596 0 10.9148 0.0905302 11.3394 0.266422C11.764 0.442313 12.1499 0.700121 12.4749 1.02513C12.7999 1.35013 13.0577 1.73597 13.2336 2.16061C13.4095 2.58525 13.5 3.04037 13.5 3.5H19.25C19.4489 3.5 19.6397 3.57902 19.7803 3.71967C19.921 3.86032 20 4.05109 20 4.25C20 4.44891 19.921 4.63968 19.7803 4.78033C19.6397 4.92098 19.4489 5 19.25 5H17.93L16.76 17.111C16.6702 18.039 16.238 18.9002 15.5477 19.5268C14.8573 20.1534 13.9583 20.5004 13.026 20.5H6.974C6.04186 20.5001 5.1431 20.153 4.45295 19.5265C3.7628 18.8999 3.33073 18.0388 3.241 17.111L2.07 5H0.75C0.551088 5 0.360322 4.92098 0.21967 4.78033C0.0790175 4.63968 0 4.44891 0 4.25C0 4.05109 0.0790175 3.86032 0.21967 3.71967C0.360322 3.57902 0.551088 3.5 0.75 3.5H6.5ZM8.5 8.25C8.5 8.05109 8.42098 7.86032 8.28033 7.71967C8.13968 7.57902 7.94891 7.5 7.75 7.5C7.55109 7.5 7.36032 7.57902 7.21967 7.71967C7.07902 7.86032 7 8.05109 7 8.25V15.75C7 15.9489 7.07902 16.1397 7.21967 16.2803C7.36032 16.421 7.55109 16.5 7.75 16.5C7.94891 16.5 8.13968 16.421 8.28033 16.2803C8.42098 16.1397 8.5 15.9489 8.5 15.75V8.25ZM12.25 7.5C12.4489 7.5 12.6397 7.57902 12.7803 7.71967C12.921 7.86032 13 8.05109 13 8.25V15.75C13 15.9489 12.921 16.1397 12.7803 16.2803C12.6397 16.421 12.4489 16.5 12.25 16.5C12.0511 16.5 11.8603 16.421 11.7197 16.2803C11.579 16.1397 11.5 15.9489 11.5 15.75V8.25C11.5 8.05109 11.579 7.86032 11.7197 7.71967C11.8603 7.57902 12.0511 7.5 12.25 7.5ZM4.734 16.967C4.78794 17.5236 5.04724 18.0403 5.46137 18.4161C5.87549 18.792 6.41475 19.0001 6.974 19H13.026C13.5853 19.0001 14.1245 18.792 14.5386 18.4161C14.9528 18.0403 15.2121 17.5236 15.266 16.967L16.424 5H3.576L4.734 16.967Z"
                          fill="#B60F0F" />
                      </svg>
                    </a>
                  </span>
                </div>
              </td>
            </tr>
            <!-- Modal Div Start -->
            <div class="delete_modal modal-comm">
              <!-- Modal -->
              <div class="modal fade" id="exampleModal-{{$tax->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                      <div class="text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 73 72" fill="none">
                          <path
                            d="M36.5004 16.533C38.1834 16.533 39.8574 17.595 41.1324 19.719L58.8684 49.281C61.4214 53.526 59.4504 57 54.5004 57H18.5004C13.5504 57 11.5794 53.523 14.1324 49.281L31.8684 19.719C33.1434 17.595 34.8174 16.533 36.5004 16.533ZM36.5004 10.533C32.6124 10.533 29.0544 12.753 26.7234 16.626L8.9874 46.194C6.6294 50.121 6.3714 54.309 8.2824 57.684C10.1934 61.059 13.9194 63 18.5004 63H54.5004C59.0814 63 62.8104 61.062 64.7184 57.687C66.6264 54.312 66.3714 50.124 64.0134 46.197L46.2774 16.635C43.9464 12.753 40.3884 10.533 36.5004 10.533Z"
                            fill="#B60F0F" />
                          <path
                            d="M36.4996 51.9001C38.6535 51.9001 40.3996 50.154 40.3996 48.0001C40.3996 45.8462 38.6535 44.1001 36.4996 44.1001C34.3457 44.1001 32.5996 45.8462 32.5996 48.0001C32.5996 50.154 34.3457 51.9001 36.4996 51.9001Z"
                            fill="#B60F0F" />
                          <path
                            d="M40.9997 30C40.9997 27.51 38.9867 25.5 36.4997 25.5C35.7634 25.5009 35.0385 25.6824 34.3887 26.0286C33.7389 26.3748 33.184 26.8752 32.7727 27.4859C32.3613 28.0966 32.1061 28.7989 32.0294 29.5311C31.9527 30.2634 32.0568 31.0033 32.3327 31.686C33.9947 35.814 36.4997 42 36.4997 42L40.6727 31.686C40.8767 31.167 40.9997 30.597 40.9997 30Z"
                            fill="#B60F0F" />
                        </svg>
                        <div class="pt-3">
                          <p class="gray1">{{__('messages.Are you sure you want to delete this?')}}</p>
                        </div>
                        <div class="buttons pt-3">
                          <button type="button" class="cancelBtn" data-bs-dismiss="modal">{{__('messages.Close')}}</button>
                          <a href="{{route('fuel_taxes.delete',$tax->id)}}" class="mainBtn">{{__('messages.Submit')}}</a>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            @endforeach

          </tbody>
        </table>
      </div>
      <!-- Pegination Area -->

    </div>
  </div>
</div>
@endsection
