<?php

namespace App\Http\Controllers;

use App\Models\CompanyDriver;
use App\Models\DriverVehicle;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class IndependentTruckerController extends Controller
{
    public function store(Request $request)
    {

        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'phone' => 'required|numeric',
            'password' => 'required|string|min:8|confirmed',
            'username' => 'required|string|max:255',
            'driver_id' => 'required|string|max:255',
            'license_number' => 'required|string|max:255',
            'license_state' => 'required|string|max:255',
           'license_start_date' => 'required|date|before_or_equal:today',
        ]);
        // $company = new User();
        // $company->name=$request->first_name.' '.$request->last_name;;
        // $company->email=$request->email;
        // $company->password=Hash::make($request->password);
        // $company->role="company";
        // $company->register_type = 'trucker';
        // $company->phone=$request->phone;
        // $company->save();
        
        $driver = new User();
        $driver->first_name = $request->first_name;
        $driver->last_name = $request->last_name;
        $driver->name = $request->first_name.' '.$request->last_name;
        $driver->username = $request->username;
        $driver->driver_id = $request->driver_id;
        $driver->license_number = $request->license_number;
        $driver->license_state = $request->license_state;
        $driver->license_start_date = $request->license_start_date;
        $driver->email = $request->email;
        $driver->phone	 = $request->phone;
        $driver->password= Hash::make($request->password);
        $driver->role='trucker';


        $driver->save();
        $companyDriver = new CompanyDriver();
        $companyDriver->driver_id =$driver->id;
        $companyDriver->company_id =$driver->id;
        $companyDriver->save();
        return response()->json([
            'status'=>200,
            'message'=>'Independent trucker added',
            'data'=>$driver
        ]);
    }
    public function addVehicle(Request $request)

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
            'odometer_reading' => 'required',
            'mpg' => 'required',
            'image' => 'required|mimes:jpeg,png,jpg,gif|max:1024',

        ]);
        $vehicle = new Vehicle();
        $vehicle->vehicle_type = $request->vehicle_type;
        $vehicle->vehicle_number = $request->vehicle_number;
        $vehicle->odometer_reading	 = $request->odometer_reading;
        $vehicle->company_id = $request->driver_id;
        $vehicle->mpg= $request->mpg;
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
        $vehicle->save();

        $driver_vehicle = new DriverVehicle();
        $driver_vehicle->driver_id = $request->driver_id;
        $driver_vehicle->vehicle_id = $vehicle->id;
        $driver_vehicle->company_id = $request->driver_id;
        $driver_vehicle->save();
        return response()->json(['status'=>200,'message'=>'Vehicle add successfully','data'=>$vehicle]);
       
    }
}
