<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Imports\VehiclesImport;
use App\Models\DriverVehicle;
use App\Models\Trip;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Facades\Excel;

class VehiclesController extends Controller
{
    public function index()
    {
        $vehicles= Vehicle::where('company_id',Auth::id())->orderBy('id','desc')->get()->map(function ($vehicle) {
            $isAssigned = DriverVehicle::where('vehicle_id', $vehicle->id)->exists();
            $vehicle->vehicle_assigned = $isAssigned ? 'Vehicle Assigned' : 'Vehicle Not Assigned';

            return $vehicle;
        });
        return view('company.vehicles.index',get_defined_vars());
    }
    public function create()
    {
        return view('company.vehicles.add');
    }
    public function store(Request $request)

    {

        $data = $request->validate([
            'vehicle_id'=>'required',
           'vin' => [
                'required',
                Rule::unique('vehicles')->where(function ($query) use ($request) {
                    return $query->where('owner_type', 'company');
                }),
            ],
            "year"=>'required',
            "truck_make"=>'required',
            "vehicle_model"=>'required',
            "fuel_type"=>'required',
            "license_state"=>'required',
            "license_number"=>'required',
            "fuel_tank_type"=>'required',
            "odometer_reading_type"=>'required',
            'odometer_reading' => 'required',
            'mpg' => 'required',
            'image' => 'required|mimes:jpeg,png,jpg,gif|max:1024',

        ]);
        $checkExistingVehicle = Vehicle::where('vehicle_id', $request->vehicle_id)->first();
        //dd($checkExistingVehicle);
        if ($checkExistingVehicle && $checkExistingVehicle->vin !== $request->vin) {
            return redirect()->back()->withError('This vehicle ID is already associated with a different VIN.');
        }
        $existingVehicle = Vehicle::where('vin', $request->vin)
            ->where('owner_type', 'company')
            ->where('owner_id',Auth::id())
            ->first();

        if ($existingVehicle) {
            return redirect()->back()->withError('This vehicle already belongs to the company.');

        }
        $apiUrl = "https://vpic.nhtsa.dot.gov/api/vehicles/DecodeVINValues/{$request->vin}?format=json";
        $response = Http::get($apiUrl);

        if ($response->successful()) {
            $data = $response->json();
            if (isset($data['Results'][0])) {
                $result = $data['Results'][0];


                if(isset($result['Make'])&& $result['Make']!='' && isset($result['Model'])&& $result['Model']!='' && isset($result['ModelYear']) && $result['ModelYear']!=''){
                    $vehicle = new Vehicle();
                    $vehicle->vehicle_type = $request->vehicle_type;
                    $vehicle->vehicle_number = $request->license_number;
                    $vehicle->odometer_reading	 = $request->odometer_reading;
                    $vehicle->company_id = Auth::id();
                    $vehicle->mpg= $request->mpg;
                    $vehicle->odometer_reading_type = $request->odometer_reading_type;
                    $vehicle->fuel_tank_type = $request->fuel_tank_type;
                    $vehicle->fuel_tank_capacity= $request->fuel_tank_capacity;
                    $vehicle->vehicle_id = $request->vehicle_id;
                    $vehicle->vin = $request->vin;
                    $vehicle->model = $request->vehicle_model;
                    $vehicle->make = $request->truck_make;
                    $vehicle->owner_id =Auth::id();
                    $vehicle->owner_type = 'company';
                    $vehicle->make_year = $request->year;
                    $vehicle->fuel_type = $request->fuel_type;
                    $vehicle->license = $request->license_state;
                    $vehicle->license_plate_number = $request->license_number;
                    $vehicle->secondary_tank_capacity= $request->secondary_fuel_tank_capacity;
                    if($request->hasFile('image')){
                        $imageName = time().'.'.$request->image->extension();
                        $request->image->move(public_path('vehicles'), $imageName);
                        $vehicle->vehicle_image= $imageName;
                    }
                    $vehicle->save();
                    return redirect('vehicles/all')->withSuccess('Vehicle Added Successfully');
                }else{
                   return redirect()->back()->withError('Invalid VIN. Please try again.')->withInput();
                }
                // Extract useful data

            }
            return redirect()->back()->withError('Invalid VIN. Please try again.')->withInput();
         }
         return redirect()->back()->withError('Invalid VIN. Please try again.')->withInput();

    }
    public function checkVin(Request $request)
    {
        $request->validate(['vin' => 'required|string|max:255']);
        $vin = $request->vin;

        // Call the external API
        $apiUrl = "https://vpic.nhtsa.dot.gov/api/vehicles/DecodeVINValues/{$vin}?format=json";
        $response = Http::get($apiUrl);

        if ($response->successful()) {
            $data = $response->json();

            if (isset($data['Results'][0])) {
                $result = $data['Results'][0];
                if(isset($result['Make'])&& $result['Make']!='' && isset($result['Model'])&& $result['Model']!='' && isset($result['ModelYear']) && $result['ModelYear']!=''){
                    return response()->json([
                        'success' => true,
                        'data' => [
                            'make' => $result['Make'] ?? 'N/A',
                            'model' => $result['Model'] ?? 'N/A',
                            'year' => $result['ModelYear'] ?? 'N/A',
                        ],
                    ]);
                }else{
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid VIN or API error. Please try again.',
                    ]);
                }
                // Extract useful data

            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid VIN or API error. Please try again.',
        ]);
    }
    public function edit($id)
    {
        $vehicle = Vehicle::find($id);
        return view('company.vehicles.edit',get_defined_vars());
    }
    public function update(Request $request,$id)
    {
        $data = $request->validate([
            'vehicle_id'=>'required',
            "vin"=>'required',
            "year"=>'required',
            "truck_make"=>'required',
            "vehicle_model"=>'required',
            "fuel_type"=>'required',
            "license_state"=>'required',
            "license_number"=>'required',
            "fuel_tank_type"=>'required',
            "odometer_reading_type"=>'required',
            'odometer_reading' => 'required',
            'mpg' => 'required',
            'image' => 'mimes:jpeg,png,jpg,gif|max:1024',


        ]);
        $vehicle = Vehicle::find($id);
        $vehicle->vehicle_type = $request->vehicle_type;
        $vehicle->vehicle_number = $request->vehicle_number;
        $vehicle->odometer_reading	 = $request->odometer_reading;
        $vehicle->company_id = Auth::id();
        $vehicle->mpg= $request->mpg;
        $vehicle->odometer_reading_type = $request->odometer_reading_type;
        $vehicle->fuel_tank_type = $request->fuel_tank_type;
        $vehicle->fuel_tank_capacity= $request->fuel_tank_capacity;
        $vehicle->vehicle_id = $request->vehicle_id;
        $vehicle->vin = $request->vin;
        $vehicle->model = $request->vehicle_model;
        $vehicle->make = $request->truck_make;
        $vehicle->make_year = $request->year;
        $vehicle->fuel_type = $request->fuel_type;
        $vehicle->license = $request->license_state;
        $vehicle->license_plate_number = $request->license_number;
        $vehicle->secondary_tank_capacity= $request->secondary_fuel_tank_capacity;
        if($request->hasFile('image')){
            $imageName = time().'.'.$request->image->extension();
            $request->image->move(public_path('vehicles'), $imageName);
            $vehicle->vehicle_image= $imageName;
        }
        $vehicle->update();
        return redirect('vehicles/all')->withSuccess('vehicle Updated Successfully');
    }
    public function delete($id)
    {
        $vehicle = Vehicle::find($id);
        $checkVehicle = DriverVehicle::where('vehicle_id',$id)->first();
        if($checkVehicle){
            return redirect()->back()->withError('Vehicle is assigned to a driver.Can not delete this vehicle.');
        }
        if($vehicle){
            $vehicle->delete();
            return redirect('vehicles/all')->withError('vehicle Deleted Successfully');
        }else{
            return redirect('vehicles/all')->withError('No vehicle found');
        }

    }
    public function importForm()
    {
       return view('company.vehicles.import');
    }
    public function import(Request $request)
{
    // Validate that the file is present and is an accepted format
    $request->validate([
        'file' => 'required|mimes:xlsx,xls,csv',
    ]);

    // Initialize counters for successful and failed records
    $createdCount = 0;
    $failedCount = 0;
    $failedRecords = [];

    // Import the data using the VehiclesImport class
    Excel::import(new VehiclesImport($createdCount, $failedCount, $failedRecords), $request->file('file'));

    // After the import, you can process success and failure messages
    return redirect('vehicles/all')
        ->with('success', "{$createdCount} vehicles imported. {$failedCount} vehicles failed to import.");
}
    public function removeVehicleByCompany($id)
    {
        $vehicle = Vehicle::findOrFail($id);

        if ($vehicle->owner_type !== 'company') {
            return redirect()->back()->withError('This vehicle does not belong to any owner.');
        }

        $activeTrips = Trip::where('vehicle_id', $vehicle->id)->where('status', 'active')->exists();

        if ($activeTrips) {
            return redirect()->back()->withError('Cannot remove vehicle with active trips.');

        }

        $vehicle->delete();

        return redirect()->back()->withSuccess('Vehicle removed successfully.');
    }
}
