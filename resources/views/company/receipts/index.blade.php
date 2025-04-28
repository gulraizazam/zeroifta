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
           
          </div>
        </div>
      </div>
    </div>

    <div class="sec1-style">
      <div class="data_table table-span table-responsive">
        <table id="example" class="table table-comm">
          <thead>
            <tr>
             
              <th scope="col" class="table-text-left">{{__('messages.Fuel Station Name')}}</th>
              <th scope="col" class="table-text-left">{{__('messages.Price Per Gallon')}}</th>

              <th scope="col" class="table-text-left">{{__('messages.Gallons Bought')}}</th>
              <th scope="col" class="table-text-left">{{__('messages.Receipt Image')}}</th>
              
             
            </tr>
          </thead>
          <tbody>
            @foreach($receipts as $receipt)
            <tr>
             

              <td>
                <p>{{$receipt->fuel_station_name ?? 'N/A'}}</p>
              </td>
              <td class="table-text-left">
                <div>
                  <p>{{$receipt->price_per_gallon ?? 'N/A'}}</p>

                </div>
              </td>
              <td class="table-text-left">{{$receipt->gallons_bought}}</td>
              <td class="table-text-left">
                
                <img src="{{asset('receipts')}}/{{$receipt->receipt_image}}" class="avatar avatar-sm me-3" alt="user1" style="height: 30px;">
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
