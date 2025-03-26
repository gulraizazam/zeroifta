@extends('layouts.new_main')
@section('content')
<div class="dashbord-inner">
    <!-- Section 1 -->
    @if(Session::has('error'))
  <div class="alert alert-danger alert-dismissible fade show" role="alert" style="background-color: #dd4957;color:white">
    {{Session::get('error')}}
    <!-- <button type="button" class="close" data-bs-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button> -->
  </div>
  @endif
    <div class="profileForm-area mb-4">
        <div class="sec1-style">
        <form method="post" action="{{route('vehicle.store')}}" enctype="multipart/form-data">
            @csrf
            <div class="row pt-3">
                <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-2">
                    <div class="dash-input mb-3">
                        <label class="input-lables pb-2" for="exampleFormControlInput1" class="pb-1">{{__('messages.Vehicle Number')}}</label>
                        <input type="text" required class="form-control login-input" id="exampleFormControlInput1" placeholder="{{__('messages.Vehicle Number')}}" name="vehicle_id" value="{{old('vehicle_id')}}"/>
                    </div>
                    @error('vehicle_id')
                            <span class="invalid-feedback" role="alert" style="display: block;">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-2">
                    <div class="dash-input mb-3">
                        <label class="input-lables pb-2" for="exampleFormControlInput1" class="pb-1">{{__('messages.VIN')}}</label>
                        <input type="text" required class="form-control login-input" id="vinInput" placeholder="{{__('messages.VIN')}}" name="vin" value="{{old('vin')}}"/>
                    </div>
                    @error('vin')
                            <span class="invalid-feedback" role="alert" style="display: block;">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                        <button type="button" id="checkVinBtn" class="btn btn-primary">{{__('messages.Check VIN')}}</button>
                </div>

                <!-- <div id="vehicleInfo" style="display: none;">
                    <h3>Vehicle Information</h3>
                    <p><strong>Make:</strong> <span id="make"></span></p>
                    <p><strong>Model:</strong> <span id="model"></span></p>
                    <p><strong>Year:</strong> <span id="year"></span></p>
                </div> -->
                <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-2">
                    <div class="dash-input mb-3">
                        <label class="input-lables pb-2" for="exampleFormControlInput1" class="pb-1">{{__('messages.Vehicle Model Year')}}</label>
                        <select name="year" id="year" class="form-control login-input">
                            <option value="">Select Year</option>
                            @for ($year = date('Y'); $year >= 1970; $year--)
                                <option value="{{ $year }}" {{ old('year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                            @endfor
                        </select>

                    </div>
                    @error('year')
                            <span class="invalid-feedback" role="alert" style="display: block;">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                </div>

                <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-2">
                    <div class="dash-input mb-3">
                        <label class="input-lables pb-2" for="exampleFormControlInput1" class="pb-1">{{__('messages.Vehicle Make')}}</label>
                        <select name="truck_make" id="truck_make" class="form-control login-input">
                            <option value="">Select Truck Make</option>
                            <option value="ford" {{ old('truck_make') == 'ford' ? 'selected' : '' }}>Ford</option>
                            <option value="chevrolet" {{ old('truck_make') == 'chevrolet' ? 'selected' : '' }}>Chevrolet</option>
                            <option value="ram" {{ old('truck_make') == 'ram' ? 'selected' : '' }}>Ram</option>
                            <option value="gmc" {{ old('truck_make') == 'gmc' ? 'selected' : '' }}>GMC</option>
                            <option value="jeep" {{ old('truck_make') == 'jeep' ? 'selected' : '' }}>Jeep</option>
                            <option value="dodge" {{ old('truck_make') == 'dodge' ? 'selected' : '' }}>Dodge</option>
                            <option value="international" {{ old('truck_make') == 'international' ? 'selected' : '' }}>International</option>
                            <option value="peterbilt" {{ old('truck_make') == 'peterbilt' ? 'selected' : '' }}>Peterbilt</option>
                            <option value="kenworth" {{ old('truck_make') == 'kenworth' ? 'selected' : '' }}>Kenworth</option>
                            <option value="freightliner" {{ old('truck_make') == 'freightliner' ? 'selected' : '' }}>Freightliner</option>
                            <option value="mack" {{ old('truck_make') == 'mack' ? 'selected' : '' }}>Mack</option>
                            <option value="western-star" {{ old('truck_make') == 'western-star' ? 'selected' : '' }}>Western Star</option>
                        </select>
                    </div>
                    @error('truck_make')
                            <span class="invalid-feedback" role="alert" style="display: block;">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-2">
                    <div class="dash-input mb-3">
                        <label class="input-lables pb-2" for="exampleFormControlInput1" class="pb-1">{{__('messages.Vehicle Model')}}</label>
                        <input type="text" required class="form-control login-input" id="vehicle_model_type" placeholder="{{__('messages.Vehicle Model')}}" name="vehicle_model" value="{{old('vehicle_model')}}" />
                    </div>
                    @error('vehicle_model')
                            <span class="invalid-feedback" role="alert" style="display: block;">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-2">
                    <div class="dash-input mb-3">
                        <label class="input-lables pb-2" for="exampleFormControlInput1" class="pb-1">{{__('messages.Fuel Type')}}</label>
                        <select name="fuel_type" id="fuel_type" class="form-control login-input">
                            <option value="">Select Fuel Type</option>
                            <option value="gasoline" {{ old('fuel_type') == 'gasoline' ? 'selected' : '' }}>Gasoline</option>
                            <option value="diesel" {{ old('fuel_type') == 'diesel' ? 'selected' : '' }}>Diesel</option>
                        </select>
                        <i class="fa fa-chevron-down dropdown-icon"></i>
                    </div>
                    @error('fuel_type')
                            <span class="invalid-feedback" role="alert" style="display: block;">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-2">
                    <div class="dash-input mb-3">
                        <label class="input-lables pb-2" for="exampleFormControlInput1" class="pb-1">{{__('messages.License State')}}</label>
                        <select name="license_state" id="license_state" class="form-control login-input">
                            <option value="">Select License State</option>
                            <option value="AL" {{ old('license_state') == 'AL' ? 'selected' : '' }}>Alabama</option>
                            <option value="AK" {{ old('license_state') == 'AK' ? 'selected' : '' }}>Alaska</option>
                            <option value="AZ" {{ old('license_state') == 'AZ' ? 'selected' : '' }}>Arizona</option>
                            <option value="AR" {{ old('license_state') == 'AR' ? 'selected' : '' }}>Arkansas</option>
                            <option value="CA" {{ old('license_state') == 'CA' ? 'selected' : '' }}>California</option>
                            <option value="CO" {{ old('license_state') == 'CO' ? 'selected' : '' }}>Colorado</option>
                            <option value="CT" {{ old('license_state') == 'CT' ? 'selected' : '' }}>Connecticut</option>
                            <option value="DE" {{ old('license_state') == 'DE' ? 'selected' : '' }}>Delaware</option>
                            <option value="DC" {{ old('license_state') == 'DC' ? 'selected' : '' }}>District Of Columbia</option>
                            <option value="FL" {{ old('license_state') == 'FL' ? 'selected' : '' }}>Florida</option>
                            <option value="GA" {{ old('license_state') == 'GA' ? 'selected' : '' }}>Georgia</option>
                            <option value="HI" {{ old('license_state') == 'HI' ? 'selected' : '' }}>Hawaii</option>
                            <option value="ID" {{ old('license_state') == 'ID' ? 'selected' : '' }}>Idaho</option>
                            <option value="IL" {{ old('license_state') == 'IL' ? 'selected' : '' }}>Illinois</option>
                            <option value="IN" {{ old('license_state') == 'IN' ? 'selected' : '' }}>Indiana</option>
                            <option value="IA" {{ old('license_state') == 'IA' ? 'selected' : '' }}>Iowa</option>
                            <option value="KS" {{ old('license_state') == 'KS' ? 'selected' : '' }}>Kansas</option>
                            <option value="KY" {{ old('license_state') == 'LA' ? 'selected' : '' }}>Kentucky</option>
                            <option value="LA" {{ old('license_state') == 'LA' ? 'selected' : '' }}>Louisiana</option>
                            <option value="ME" {{ old('license_state') == 'ME' ? 'selected' : '' }}>Maine</option>
                            <option value="MD" {{ old('license_state') == 'MD' ? 'selected' : '' }}>Maryland</option>
                            <option value="MA" {{ old('license_state') == 'MA' ? 'selected' : '' }}>Massachusetts</option>
                            <option value="MI" {{ old('license_state') == 'MI' ? 'selected' : '' }}>Michigan</option>
                            <option value="MN" {{ old('license_state') == 'MN' ? 'selected' : '' }}>Minnesota</option>
                            <option value="MS" {{ old('license_state') == 'MS' ? 'selected' : '' }}>Mississippi</option>
                            <option value="MO" {{ old('license_state') == 'MO' ? 'selected' : '' }}>Missouri</option>
                            <option value="MT" {{ old('license_state') == 'MT' ? 'selected' : '' }}>Montana</option>
                            <option value="NE" {{ old('license_state') == 'NE' ? 'selected' : '' }}>Nebraska</option>
                            <option value="NV" {{ old('license_state') == 'NV' ? 'selected' : '' }}>Nevada</option>
                            <option value="NH" {{ old('license_state') == 'NH' ? 'selected' : '' }}>New Hampshire</option>
                            <option value="NJ" {{ old('license_state') == 'NJ' ? 'selected' : '' }}>New Jersey</option>
                            <option value="NM" {{ old('license_state') == 'NM' ? 'selected' : '' }}>New Mexico</option>
                            <option value="NY" {{ old('license_state') == 'NY' ? 'selected' : '' }}>New York</option>
                            <option value="NC" {{ old('license_state') == 'NC' ? 'selected' : '' }}>North Carolina</option>
                            <option value="ND" {{ old('license_state') == 'ND' ? 'selected' : '' }}>North Dakota</option>
                            <option value="OH" {{ old('license_state') == 'OH' ? 'selected' : '' }}>Ohio</option>
                            <option value="OK" {{ old('license_state') == 'OK' ? 'selected' : '' }}>Oklahoma</option>
                            <option value="OR" {{ old('license_state') == 'OR' ? 'selected' : '' }}>Oregon</option>
                            <option value="PA" {{ old('license_state') == 'PA' ? 'selected' : '' }}>Pennsylvania</option>
                            <option value="RI" {{ old('license_state') == 'RI' ? 'selected' : '' }}>Rhode Island</option>
                            <option value="SC" {{ old('license_state') == 'SC' ? 'selected' : '' }}>South Carolina</option>
                            <option value="SD" {{ old('license_state') == 'SD' ? 'selected' : '' }}>South Dakota</option>
                            <option value="TN" {{ old('license_state') == 'TN' ? 'selected' : '' }}>Tennessee</option>
                            <option value="TX" {{ old('license_state') == 'TX' ? 'selected' : '' }}>Texas</option>
                            <option value="UT" {{ old('license_state') == 'UT' ? 'selected' : '' }}>Utah</option>
                            <option value="VT" {{ old('license_state') == 'VT' ? 'selected' : '' }}>Vermont</option>
                            <option value="VA" {{ old('license_state') == 'VA' ? 'selected' : '' }}>Virginia</option>
                            <option value="WA" {{ old('license_state') == 'WA' ? 'selected' : '' }}>Washington</option>
                            <option value="WV" {{ old('license_state') == 'WV' ? 'selected' : '' }}>West Virginia</option>
                            <option value="WI" {{ old('license_state') == 'WY' ? 'selected' : '' }}>Wisconsin</option>
                            <option value="WY" {{ old('license_state') == 'WY' ? 'selected' : '' }}>Wyoming</option>

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
                        <label class="input-lables pb-2" for="exampleFormControlInput1" class="pb-1">{{__('messages.License Plate No.')}}</label>
                        <input type="text" required  class="form-control login-input" id="exampleFormControlInput1" placeholder="{{__('messages.License Plate No.')}}" name="license_number" value="{{old('license_number')}}" />
                    </div>
                    @error('license_number')
                            <span class="invalid-feedback" role="alert" style="display: block;">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                </div>
                <div class="col-lg-4 col-md-4 col-sm-12 col-12 mb-2">
                    <div class="dash-input mb-3">
                        <label class="input-lables pb-2" for="exampleFormControlInput1" class="pb-1">{{__('messages.Odometer Reading')}}</label>
                        <input type="text" required  class="form-control login-input" id="exampleFormControlInput1" placeholder="{{__('messages.Odometer Reading')}}" name="odometer_reading" value="{{old('odometer_reading')}}" />
                    </div>
                    @error('odometer_reading')
                            <span class="invalid-feedback" role="alert" style="display: block;">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                </div>
                <div class="col-lg-2 col-md-2 col-sm-12 col-12 mb-2">
                    <div class="dash-input mb-3">
                        <label class="input-lables pb-2" for="exampleFormControlInput1" class="pb-1">{{__('messages.Type')}}</label>
                        <select class="form-control login-input" name="odometer_reading_type">
                            <option value="">Select</option>
                            <option value="miles" {{ old('odometer_reading_type') == 'miles' ? 'selected' : '' }}>Miles</option>
                            <option value="km" {{ old('odometer_reading_type') == 'km' ? 'selected' : '' }}>KM</option>
                        </select>
                    </div>
                    @error('odometer_reading_type')
                            <span class="invalid-feedback" role="alert" style="display: block;">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-2">
                    <div class="dash-input mb-3">
                        <label class="input-lables pb-2" for="exampleFormControlInput1" class="pb-1">{{__('messages.MPG')}}</label>
                        <input type="text" required class="form-control login-input" id="exampleFormControlInput1" placeholder="{{__('messages.MPG')}}" name="mpg" value="{{old('mpg')}}" />
                    </div>
                    @error('mpg')
                            <span class="invalid-feedback" role="alert" style="display: block;">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                </div>
                <div class="col-lg-4 col-md-4 col-sm-12 col-12 mb-2">
                    <div class="dash-input mb-3">
                        <label class="input-lables pb-2" for="exampleFormControlInput1" class="pb-1">{{__('messages.Fuel Tank Capacity')}}</label>
                        <input type="text" required class="form-control login-input" id="exampleFormControlInput1" placeholder="{{__('messages.Fuel Tank Capacity')}}" name="fuel_tank_capacity" value="{{old('fuel_tank_capacity')}}" />
                    </div>
                    @error('fuel_tank_capacity')
                            <span class="invalid-feedback" role="alert" style="display: block;">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                </div>
                <div class="col-lg-2 col-md-2 col-sm-12 col-12 mb-2">
                    <div class="dash-input mb-3">
                        <label class="input-lables pb-2" for="exampleFormControlInput1" class="pb-1">{{__('messages.Type')}}</label>
                        <select class="form-control login-input" name="fuel_tank_type">
                            <option value="">Select</option>
                            <option value="gallons" {{ old('fuel_tank_type') == 'gallons' ? 'selected' : '' }}>Gallons</option>
                            <option value="litres" {{ old('fuel_tank_type') == 'litres' ? 'selected' : '' }}>Litres</option>
                        </select>
                    </div>
                    @error('fuel_tank_type')
                            <span class="invalid-feedback" role="alert" style="display: block;">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-2">
                    <div class="dash-input mb-3">
                        <label class="input-lables pb-2" for="exampleFormControlInput1" class="pb-1">{{__('messages.Secondary Fuel Tank Capacity')}}</label>
                        <input type="text" class="form-control login-input" id="exampleFormControlInput1" placeholder="{{__('messages.Secondary Fuel Tank Capacity')}}" name="secondary_fuel_tank_capacity" value="{{old('secondary_fuel_tank_capacity')}}" />
                    </div>
                    @error('secondary_fuel_tank_capacity')
                            <span class="invalid-feedback" role="alert" style="display: block;">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                </div>

                <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-2">
                    <div class="dash-input mb-3">
                        <label class="input-lables pb-2" for="exampleFormControlInput1" class="pb-1">{{__('messages.Image')}}</label>
                        <input type="file" required name="image" class="form-control login-input choose-file-input" accept="image/png, image/jpg, image/jpeg">

                    </div>
                    @error('image')
                            <span class="invalid-feedback" role="alert" style="display: block;">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                </div>


            </div>
            <div class="buttons mt-5">
                <a href="{{route('allvehicles')}}" class="cancelBtn">{{__('messages.Cancel')}}</a>
                <button type="submit"  class="mainBtn">{{__('messages.Submit')}}</a>
            </div>
        </div>
        </form>
    </div>
</div>


@endsection
@section('scripts')
<script>
document.getElementById('checkVinBtn').addEventListener('click', function () {
    const vin = document.getElementById('vinInput').value;

    fetch("{{ route('vehicle.checkVin') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ vin })
    })
    .then(response => response.json())
    .then(data => {

        if (data.success) {
            // Populate vehicle details

            document.getElementById('truck_make').value = data.data.make.toLowerCase(); // Match make by value
            document.getElementById('year').value = data.data.year; // Match year by value
            document.getElementById('vehicle_model_type').value = data.data.model; // Set model input
        } else {

            alert(data.message);
            document.getElementById('truck_make').value = ''; // Match make by value
            document.getElementById('year').value =''; // Match year by value
            document.getElementById('vehicle_model_type').value = ''; // Set model input
        }
    })
    .catch(error => console.error('Error:', error));
});
</script>
@endsection
