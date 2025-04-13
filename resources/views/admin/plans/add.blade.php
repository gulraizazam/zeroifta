@extends('layouts.new_main')
@section('content')
<div class="dashbord-inner">
    <!-- Section 1 -->
    <div class="profileForm-area mb-4">
        <div class="sec1-style">
            <form method="post" action="{{route('plans.store')}}">
                @csrf
                <div class="row pt-3">
                    <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-2">
                        <div class="dash-input mb-3">
                            <label class="input-lables pb-2" for="name">{{__('messages.Name')}}</label>
                            <input type="text" class="form-control login-input" id="name" placeholder="{{__('messages.Name')}}" name="name" value="{{old('name')}}"/>
                        </div>
                        @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-2">
                        <div class="dash-input mb-3">
                            <label class="input-lables pb-2" for="price">{{__('messages.Price')}}</label>
                            <input type="number" class="form-control login-input" id="price" placeholder="{{__('messages.Price')}}" name="price" value="{{old('price')}}" />
                        </div>
                        @error('price')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-2">
                        <div class="dash-input mb-1">
                            <label class="input-lables pb-2" for="exampleFormControlInput1" class="pb-1">{{__('messages.Billing Period')}}</label>
                            <select class="form-control login-input" id="exampleFormControlInput1" name="billing_period" required>
                                    <option value="month">Monthly</option>
                                    <option value="year">Yearly</option>
                                </select>
                                @error('price_type')
                                <span class="invalid-feedback" role="alert" style="display: block;">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-2">
                        <div class="dash-input mb-1">
                            <label class="input-lables pb-2" for="exampleFormControlInput1" class="pb-1">{{__('messages.Is Recurring?')}}</label>
                            <select class="form-control login-input" id="recurring" name="recurring" required>
                                      <option value="1">{{__('messages.Yes')}}</option>
                                      <option value="0">{{__('messages.No')}}</option>
                                  </select>
                                  @error('recurring')
                                <span class="invalid-feedback" role="alert" style="display: block;">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-lg-12 col-md-12 col-sm-12 col-12 mb-2">
                        <div class="dash-input mb-3">
                            <label class="input-lables pb-2" for="description">{{__('messages.Plan Description')}}</label>
                            <div class="textArea dash-input">
                                <textarea class="" name="description" id="description" rows="3" placeholder="{{__('messages.Plan Description')}}">{{old('description')}}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Features Section -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h4 class="card-title">{{__('messages.Plan Features')}}</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="alert alert-info">
                                    {{__('messages.Select the features that will be available in this plan')}}
                                </div>
                                
                                <div class="features-grid">
                                    @php
                                        $availableFeatures = [
                                            'vehicles.all' => 'View All Vehicles',
                                            'vehicles.create' => 'Create Vehicle',
                                            'vehicles.edit' => 'Edit Vehicle',
                                            'vehicles.delete' => 'Delete Vehicle',
                                            'vehicles.import' => 'Import Vehicles',
                                            'driver_vehicles.index' => 'View Driver Vehicles',
                                            'driver_vehicles.create' => 'Assign Vehicles to Drivers',
                                            'driver_vehicles.edit' => 'Edit Driver Vehicle Assignment',
                                            'driver_vehicles.delete' => 'Remove Driver Vehicle Assignment',
                                            'drivers.all' => 'View All Drivers',
                                            'drivers.create' => 'Create Driver',
                                            'drivers.edit' => 'Edit Driver',
                                            'drivers.delete' => 'Delete Driver',
                                            'drivers.import' => 'Import Drivers',
                                            'drivers.track' => 'Track Drivers',
                                            'fleet.view' => 'View Fleet Management',
                                        ];
                                        $selectedFeatures = old('features', []);
                                    @endphp

                                    @foreach($availableFeatures as $key => $label)
                                    <div class="feature-item">
                                        <div class="form-check">
                                            <input type="checkbox" 
                                                   class="form-check-input" 
                                                   name="features[]" 
                                                   value="{{ $key }}"
                                                   id="feature_{{ $key }}"
                                                   {{ in_array($key, $selectedFeatures) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="feature_{{ $key }}">
                                                {{ __('messages.' . $label) }}
                                            </label>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="buttons mt-5">
                    <a href="{{route('plans')}}" class="cancelBtn">{{__('messages.Cancel')}}</a>
                    <button type="submit" class="mainBtn">{{__('messages.Submit')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
.features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 1rem;
    margin-top: 1rem;
}

.feature-item {
    padding: 0.5rem;
    border: 1px solid #eee;
    border-radius: 0.25rem;
    background-color: var(--card-bg-color);
}

.dark-mode .feature-item {
    border-color: #4a5568;
}

.form-check-input:checked {
    background-color: #0c388b;
    border-color: #0c388b;
}

.dark-mode .form-check-input:checked {
    background-color: #fff;
    border-color: #fff;
}
</style>
@endpush

@endsection
