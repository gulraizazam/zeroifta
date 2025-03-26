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
        <form method="post" action="{{route('driver_vehicles.store')}}" enctype="multipart/form-data">
            @csrf
            <div class="row pt-3">
                <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-2">
                    <div class="dash-input mb-3">
                        <label class="input-lables pb-2" for="exampleFormControlInput1" class="pb-1">{{__('messages.Driver')}}</label>
                        <select name="driver_id" class="form-control login-input" required>
                              <option value="">Select</option>
                              @foreach($drivers as $driver)
                              <option value="{{$driver->driver->id}}">{{$driver->driver->name}}</option>
                              @endforeach
                            </select>
                    </div>
                    @error('driver_id')
                            <span class="invalid-feedback" role="alert" style="display: block;">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 col-12 mb-2">
                    <div class="dash-input mb-3">
                        <label class="input-lables pb-2" for="exampleFormControlInput1" class="pb-1">{{__('messages.Vehicle')}}</label>
                        <select name="vehicle_id" class="form-control login-input" required>
                        <option value="">Select</option>
                        @foreach($vehicles as $vehicle)
                        <option value="{{$vehicle->id}}">{{$vehicle->license_plate_number}} - {{$vehicle->make}} </option>
                        @endforeach
                      </select>
                    </div>
                    @error('vehicle_id')
                            <span class="invalid-feedback" role="alert" style="display: block;">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                </div>

            </div>
            <div class="buttons mt-5">
                <a href="{{route('driver_vehicles')}}" class="cancelBtn">{{__('messages.Cancel')}}</a>
                <button type="submit"  class="mainBtn">{{__('messages.Submit')}}</a>
            </div>
        </div>
</form>
    </div>
</div>
<div class="modal fade" id="reassignModal" tabindex="-1" role="dialog" aria-labelledby="reassignModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="justify-content: left !important;">
                <h5 class="modal-title" id="reassignModalLabel">{{__('messages.Reassign Vehicle')}}</h5>

            </div>
            <div class="modal-body">
                <!-- Message will be populated dynamically -->
            </div>
            <form id="reassignForm" action="{{route('driver_vehicles.reassign')}}" method="post">
                <input type="hidden" name="driver_vehicle_id" value="" id="driverVehicleId">
                <input type="hidden" name="driver_id" value="">
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="hideModal()">{{__('messages.Cancel')}}</button>
                    <button type="submit" class="btn btn-primary">{{__('messages.Reassign')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
    document.querySelector('form').addEventListener('submit', function (e) {
    e.preventDefault();

    const form = this;
    const formData = new FormData(form);
    const action = form.getAttribute('action');

    fetch(action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
    })
        .then((response) => response.json())
        .then((data) => {
            console.log(data);
            if (data.status === 'already_assigned') {
                // Show modal
                const modal = document.getElementById('reassignModal');
                modal.querySelector('.modal-body').innerText = data.message;
                $("#driverVehicleId").val(data.driver_vehicle_id);
                //modal.querySelector('form').dataset.driverVehicleId = data.driver_vehicle_id;
                modal.querySelector('form input[name="driver_id"]').value = formData.get('driver_id');
                $('#reassignModal').modal('show');
            } else if (data.status === 'success') {
                alert(data.message);
                window.location.href = '/driver/vehicles'; // Redirect on success
            }
        })
        .catch((error) => console.error(error));
});
function hideModal(){
    $("#reassignModal").modal('hide');
}
// Reassign vehicle
document.querySelector('#reassignForm').addEventListener('submit', function (e) {
    e.preventDefault();

    const form = this;
    const formData = new FormData(form);
    const action = form.getAttribute('action');

    fetch(action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.status === 'success') {
                alert(data.message);
                window.location.href = '/driver/vehicles'; // Redirect on success
            }
        })
        .catch((error) => console.error(error));
});
</script>
@endsection
