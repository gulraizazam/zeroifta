@extends('layouts.new_main')
@section('content')
<!-- Add AntV dependencies -->
<script src="https://unpkg.com/@antv/g2plot@latest/dist/g2plot.min.js"></script>

<div class="dash-countMain">
  <div class="container-fluid">
    <!-- Dashboards Count -->
    <div class="dash-countInn res-pad">
      <div class="row">

        <div class="col-lg-3 col-md-6 col-sm-6 col-12 mb-4">
        @if(Auth::user()->role=="admin")
      <a href="{{route('companies')}}" >
      @else
      <a href="{{route('drivers.all')}}" >
      @endif
          <div class="t-comp comm-counts">
            <div class="dc_inn">
              <div>
                <img src="assets/img/company.png" alt="ZeroIfta Image" />
              </div>
              @if(Auth::user()->role=="admin")
              <div class="count-content">
              <h5 class="head-16Med" style="color: black;">{{__('messages.Total Companies')}}</h5>
                <h6 class="head-24Med blue">{{\App\Models\User::whereRole('company')->count()??0}}</h6>
              </div>
              @else
              <div class="count-content">
                <h5 class="head-16Med" style="color: black;">{{__('messages.Total Drivers')}}</h5>
                <h6 class="head-24Med blue">{{\App\Models\CompanyDriver::where('company_id',Auth::id())->count()??0}}</h6>
              </div>
              @endif

            </div>
          </div>
          </a>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6 col-12 mb-4">
        @if(Auth::user()->role=="admin")
        <a href="{{route('plans')}}">
        @else
        <a href="{{route('allvehicles')}}" >
            @endif
          <div class="t-subs comm-counts">
            <div class="dc_inn">
              <div>
                <img src="assets/img/subscription.png" alt="ZeroIfta Image" />
              </div>
              @if(Auth::user()->role=="admin")
              <div class="count-content">
                <h5 class="head-16Med" style="color: black;">{{__('messages.Subscription')}}</h5>
                <h6 class="head-24Med blue">{{\App\Models\Plan::count()??0}}</h6>
              </div>
              @else
              <div class="count-content">
                <h5 class="head-16Med" style="color: black;">{{__('messages.Total Vehicles')}}</h5>
                <h6 class="head-24Med blue">{{\App\Models\Vehicle::where('company_id',Auth::id())->count()??0}}</h6>
              </div>
              @endif
            </div>
          </div>
          </a>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12 mb-4">
        @if(Auth::user()->role=="admin")
        <a href="{{route('payments')}}">
        @else
        <a href="{{route('driver_vehicles')}}" >
            @endif
          <div class="t-paym comm-counts">
            <div class="dc_inn">
              <div>
                <img src="assets/img/payment.png" alt="ZeroIfta Image" />
              </div>
              @if(Auth::user()->role=="admin")
              <div class="count-content">
                <h5 class="head-16Med" style="color: black;">{{__('messages.Payments')}}</h5>
                <h6 class="head-24Med blue">{{\App\Models\Payment::count()??0}}</h6>
              </div>
              @else
              <div class="count-content">
                <h5 class="head-16Med" style="color: black;">{{__('messages.Driver Vehicles')}}</h5>
                <h6 class="head-24Med blue">{{\App\Models\DriverVehicle::where('company_id',Auth::id())->count()??0}}</h6>
              </div>
              @endif
            </div>
          </div>
          </a>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12 mb-4">
        @if(Auth::user()->role=="admin")
        <a href="{{route('admin.contactus')}}">
        @else
        <a href="{{route('company.contactus')}}" >
            @endif
          <div class="t-form comm-counts">
            <div class="dc_inn">
              <div>
                <img src="assets/img/contact.png" alt="ZeroIfta Image" />
              </div>
              @if(Auth::user()->role=="admin")
              <div class="count-content">
                <h5 class="head-16Med" style="color: black;">{{__('messages.Contact Form')}}</h5>
                <h6 class="head-24Med blue">{{\App\Models\CompanyContactUs::count()??0}}</h6>
              </div>
              @else
              <div class="count-content">
                <h5 class="head-16Med" style="color: black;">{{__('messages.Contact Form')}}</h5>
                <h6 class="head-24Med blue">{{\App\Models\Contactus::where('company_id',Auth::id())->count()??0}}</h6>
              </div>
              @endif
            </div>
          </div>
          </a>
        </div>
      </div>
    </div>
    <!-- Dashboard Inner -->
    <div class="dashbord-inner res-pad">
      <!-- Section 1 -->
      <div class="chart-area mb-4">
        <div class="sec1-style">
          <div class="inHead-span">
            <h2 class="head-20Med">@if(Auth::user()->role=='admin')
            {{__('messages.Companies overview')}}
              @else
              {{__('messages.Drivers overview')}}
              @endif</h2>
          </div>
          <div>
            <div class="chart">
              <div id="line-chart" style="height: 300px;"></div>
              <div id="column-chart" style="height: 300px;"></div>
            </div>
          </div>
        </div>
      </div>
      <!-- Section 2 -->
      <div class="table-area mb-4">
        <div class="row">
          @if(Auth::user()->role=='admin')
          <div class="col-lg-6 col-md-12 col.sm-12 col-12 mb-4">
            @else
            <div class="col-lg-6 col-md-12 col.sm-12 col-12 mb-4">
              @endif
              <div class="sec1-style">
                <div class="inHead-span">
                  <h2 class="head-20Med">@if(Auth::user()->role=='admin')
                  {{__('messages.Companies overview')}}
                    @else
                    {{__('messages.Drivers')}}
                    @endif</h2>
                </div>
                @if(Auth::user()->role=='admin')
                <div class="table-span table-responsive">
                  <table class="table table-comm">
                    <thead>
                      <tr>
                        <th scope="col">{{__('messages.Name')}}</th>
                        <th scope="col">{{__('messages.Email')}}</th>
                        <th scope="col">{{__('messages.Phone')}}</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($data as $record)
                      <tr>
                        <td><a href="{{route('companies.edit',$record->id)}}" style="color:var(--gray1)">{{$record->name}}</a></td>
                        <td>{{$record->email}}</td>
                        <td>{{$record->phone}}</td>
                      </tr>
                      @endforeach

                    </tbody>
                  </table>
                </div>
                @else
                <div class="table-span table-responsive">
                  <table class="table table-comm">
                    <thead>
                      <tr>
                        <th scope="col">{{__('messages.Name')}}</th>
                        <th scope="col">{{__('messages.Email')}}</th>
                        <th scope="col">{{__('messages.Phone')}}</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($data as $record)
                      <tr>
                        <td>{{$record->driver->name ??'N/A'}}</td>
                        <td>{{$record->driver->email??'N/A'}}</td>
                        <td>{{$record->driver->phone??'N/A'}}</td>
                      </tr>
                      @endforeach

                    </tbody>
                  </table>
                </div>
                @endif
              </div>
            </div>
            @if(Auth::user()->role=='admin' || Auth::user()->role=='company')
            @php
            $contacts = \App\Models\CompanyContactUs::with('company')->take(5)->latest()->get();
            @endphp
            <div class="col-lg-6 col-md-12 col.sm-12 col-12 mb-4">
              <div class="sec1-style">
                <div class="inHead-span">
                  <h2 class="head-20Med">{{__('messages.Contact Forms')}}</h2>
                </div>
                <div>
                  @foreach($contacts as $con)
                  <div class="form_div mb-3">
                    <div class="user-area">
                      <div>
                        @if($con->company && $con->company->image !== null)
                        <img src="{{asset('images')}}/{{$con->company->image}}" alt="kal" class="border-radius-lg shadow" style="height: 30px;">
                        @else
                        <img src="{{asset('images/com.png')}}" alt="kal" class="border-radius-lg shadow">
                        @endif
                      </div>
                      <div class="fc-content">
                        <h6 class="head-16 grayWhite"><a href="{{route('admin.contactus')}}">{{$con->company->name??'N/A'}}</a></h6>
                        <p class="gray1 font-14">
                          {{$con->description ? Str::limit($con->message,20,'...'):'N/A'}}
                        </p>
                      </div>
                    </div>
                    <div class="ub-div">
                      <a class="blueBtn" href="{{route('admin.contactus')}}">
                        <span class="">{{__('messages.View Detail')}}</span>
                        <span class="grayfilled-svg">
                          <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="">
                            <path d="m560-240-56-58 142-142H160v-80h486L504-662l56-58 240 240-240 240Z" />
                          </svg>
                        </span>
                      </a>
                    </div>
                  </div>
                  @endforeach

                </div>
              </div>
            </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>

</div>


@endsection

@section('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Sample data - replace this with your actual data from backend
    const data = @json($chartData ?? []);

    // Line Chart Configuration
    const lineChart = new G2Plot.Line('line-chart', {
      data: data,
      xField: 'date',
      yField: 'Drivers',
      color: '#092e75',
      height: 300,
      tooltip: {
        showCrosshairs: true,
        shared: true,
      },
      point: {
        size: 3,
      },
    });

    // Column Chart Configuration
    const columnChart = new G2Plot.Column('column-chart', {
      data: data,
      xField: 'date',
      yField: 'Drivers',
      height: 300,
      color: '#092e75',
      tooltip: {
        showCrosshairs: true,
        shared: true,
      },
    });

    // Render both charts
    lineChart.render();
    columnChart.render();

    // Sync tooltips between charts
    lineChart.on('plot:mousemove', (evt) => {
      const point = lineChart.getXY(evt.data.data);
      columnChart.showTooltip(point);
    });

    columnChart.on('plot:mousemove', (evt) => {
      const point = columnChart.getXY(evt.data.data);
      lineChart.showTooltip(point);
    });

    lineChart.on('plot:mouseleave', () => {
      columnChart.hideTooltip();
    });

    columnChart.on('plot:mouseleave', () => {
      lineChart.hideTooltip();
    });
  });
</script>
@endsection
