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
            <a class="blueLine_btn" href="{{route('payment_method.add')}}">{{__('messages.Add Payment Method')}}+</a>

          </div>
        </div>
      </div>
    </div>

    <div class="sec1-style">
      <div class="data_table table-span table-responsive">
        <table id="example" class="table table-comm">
          <thead>
            <tr>

              <th scope="col" class="table-text-left">{{__('messages.Name')}}</th>
              <th scope="col" class="table-text-left">{{__('messages.Brand')}}</th>
              <th scope="col" class="table-text-left">{{__('messages.Expiry Month')}}</th>
              <th scope="col" class="table-text-left">{{__('messages.Expiry Year')}}</th>
              <th scope="col" class="table-text-left">{{__('messages.Last4')}}</th>
              <th scope="col" class="table-text-left">{{__('messages.Is Default')}}</th>
              <th scope="col" class="table-text-left">{{__('messages.Action')}}</th>
            </tr>
          </thead>
          <tbody>
            @foreach($filteredMethods as $method)
            <tr data-payment-method-id="{{ $method['id'] }}">


              <td class="table-text-left">
                <div class="table-conTab">

                  <span> {{$method['name']}}</span>
                </div>
              </td>
              <td class="table-text-left">
                <div>
                  <p>{{$method['brand']}}</p>

                </div>
              </td>
              <td class="table-text-left">{{$method['expiry_month']}}</td>
              <td class="table-text-left">{{$method['expiry_year']}}</td>
              <td class="table-text-left">{{$method['last4']}}</td>

              <td class="table-text-left">
                @if($method['is_default'] == true)
                <span style="color: green;">{{__('messages.Yes')}}</span>
                @else
                <span style="color: red;">{{__('messages.No')}}</span>
                @endif
            </td>
              <td class="table-text-left">
                <div class="tabAction-list">
                @if($method['is_default'] == true)
                <span class="label label-success">{{__('messages.Default')}}</span>
                @else
                <a href="{{route('make_default',$method['id'])}}" class="set-default-btn btn btn-primary">{{__('messages.Set as Default')}}</a>
                 @endif


                </div>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <!-- Pegination Area -->

    </div>
  </div>
</div>
@endsection
