<?php

namespace App\Http\Controllers;

use App\Models\CompanyDriver;
use App\Models\DriverVehicle;
use App\Models\Trip;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class VehicleController extends Controller
{
    public function index(Request $request)
    {
        $vehicle = DriverVehicle::with('vehicle')->where('driver_vehicles.driver_id',$request->driver_id)->first();
        if($vehicle){
            $vehicle->vehicle->vehicle_image = url('/vehicles/' .$vehicle->vehicle->vehicle_image);
            return response()->json(['status'=>200,'message'=>'vehicle found','data'=> $vehicle],200);
        }else{
            return response()->json(['status'=>404,'message'=>'vehicle not found','data'=> (object)[]],404);
        }
    }
    public function addVehicle(Request $request)
    {
        $data = $request->validate([
            'vehicle_type' => 'required',
            'vehicle_number' => 'required',
            'odometer_reading' => 'required',
            'mpg' => 'required',
            'image' => 'required|mimes:jpeg,png,jpg,gif|max:1024',

        ]);
        $companyId = CompanyDriver::where('driver_id',Auth::id())->first();
        $vehicle = new Vehicle();
        $vehicle->vehicle_type = $request->vehicle_type;
        $vehicle->vehicle_number = $request->vehicle_number;
        $vehicle->odometer_reading	 = $request->odometer_reading;
        $vehicle->company_id =  $companyId->company_id;
        $vehicle->mpg= $request->mpg;
        $vehicle->fuel_tank_capacity= $request->fuel_tank_capacity;
        if($request->hasFile('image')){
            $imageName = time().'.'.$request->image->extension();
            $request->image->move(public_path('vehicles'), $imageName);
            $vehicle->vehicle_image= $imageName;
        }
        $vehicle->save();
        return response()->json(['status'=>200,'message'=>'Vehcile Added Successfully','data'=>$vehicle],200);
    }
    public function update(Request $request)
    {
        $vehicle = Vehicle::where('vehicle_number',$request->vehicle_number)->first();
        if($vehicle){
            $vehicle->odometer_reading = $request->odometer_reading;
            $vehicle->mpg = $request->mpg;
            $vehicle->fuel_tank_capacity = $request->fuel_tank_capacity;
            $vehicle->fuel_left = $request->fuel_left;
            $vehicle->save();
            return response()->json(['status'=>200,'message'=>'vehicle updated successfully','data'=> $vehicle],200);
        }else{
            return response()->json(['status'=>404,'message'=>'vehicle not found','data'=> (object)[]],404);
        }
    }
    public function allVehicles(Request $request)
{
    $driverVehicles = DriverVehicle::where('driver_id', $request->driver_id)->get();
    $vehicles = Vehicle::whereIn('id', $driverVehicles->pluck('vehicle_id'))
        ->select('id', 'vehicle_image', 'vehicle_number', 'mpg', 'odometer_reading', 'fuel_left', 'fuel_tank_capacity', 'model', 'make', 'make_year', 'license_plate_number', 'vin')
        ->get();

    if ($vehicles->isNotEmpty()) {
        // Count occurrences of each VIN
        $vinCounts = $vehicles->pluck('vin')->countBy();

        foreach ($vehicles as $vehicle) {
            if (isset($vehicle->vehicle_image)) {
                $vehicle->vehicle_image = url('/vehicles/' . $vehicle->vehicle_image);
            }

            // Check if the VIN appears more than once
            $vehicle->is_duplicate_vin = $vinCounts[$vehicle->vin] > 1;
        }

        return response()->json(['status' => 200, 'message' => 'vehicles found', 'data' => $vehicles], 200);
    } else {
        return response()->json(['status' => 404, 'message' => 'vehicles not found', 'data' => []], 404);
    }
}


    public function allTrips(Request $request)
    {
        $trips = Trip::where('user_id', $request->driver_id)->orderBy('created_at', 'desc')->get();

        if ($trips->isEmpty()) {
            return response()->json(['status' => 404, 'message' => 'Trips not found', 'data' => []], 404);
        }

        // Preload vehicle data for all trips
        $driverVehicles = DriverVehicle::whereIn('driver_id', [$request->driver_id])->pluck('vehicle_id', 'driver_id');
        $vehicles = Vehicle::whereIn('id', $driverVehicles)->get()->keyBy('id');

        // API key (move to .env for security)
        $apiKey = 'AIzaSyA0HjmGzP9rrqNBbpH7B0zwN9Gx9MC4w8w';

        $geocodedTrips = $trips->map(function ($trip) use ($driverVehicles, $vehicles, $apiKey) {
            $vehicleId = $driverVehicles[$trip->user_id] ?? null;
            $vehicle = $vehicles[$vehicleId] ?? null;

            if ($vehicle && isset($vehicle->vehicle_image)) {
                // Ensure the image URL is not repeatedly prefixed
                if (!str_starts_with($vehicle->vehicle_image, 'https://')) {
                    $vehicle->vehicle_image = url('/vehicles/' . $vehicle->vehicle_image);
                }
            }

            // Caching geocode addresses
            $pickupAddress = Cache::remember(
                "pickup_{$trip->start_lat}_{$trip->start_lng}",
                86400,
                fn() => $this->getAddressFromCoordinates($trip->start_lat, $trip->start_lng, $apiKey)
            );

            $dropoffAddress = Cache::remember(
                "dropoff_{$trip->end_lat}_{$trip->end_lng}",
                86400,
                fn() => $this->getAddressFromCoordinates($trip->end_lat, $trip->end_lng, $apiKey)
            );

            // Caching route distance and duration
            $directions = Cache::remember(
                "directions_{$trip->start_lat}_{$trip->start_lng}_{$trip->end_lat}_{$trip->end_lng}",
                86400,
                fn() => $this->getDirections($trip->start_lat, $trip->start_lng, $trip->end_lat, $trip->end_lng, $apiKey)
            );

            return [
                'id' => $trip->id,
                'user_id' => $trip->user_id,
                'pickup' => $pickupAddress,
                'dropoff' => $dropoffAddress,
                'start_lat' => $trip->start_lat,
                'start_lng' => $trip->start_lng,
                'end_lat' => $trip->end_lat,
                'end_lng' => $trip->end_lng,
                'status' => $trip->status,
                'vehicle' => $vehicle,
                'distance' => $directions['distance'] ?? null,
                'duration' => $directions['duration'] ?? null,
                'created_at' => $trip->created_at,
                'updated_at' => $trip->updated_at,
            ];
        });

        return response()->json(['status' => 200, 'message' => 'Trips found', 'data' => $geocodedTrips], 200);
    }

    private function getAddressFromCoordinates($latitude, $longitude, $apiKey)
    {
        $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng={$latitude},{$longitude}&key={$apiKey}";
        $response = Http::get($url);

        if ($response->successful() && isset($response->json()['results'][0]['address_components'])) {
            foreach ($response->json()['results'][0]['address_components'] as $component) {
                if (in_array('administrative_area_level_1', $component['types'])) {
                    return $component['long_name']; // State name
                }
            }
        }

        return 'Address not found';
    }

    private function getDirections($startLat, $startLng, $endLat, $endLng, $apiKey)
    {
        $url = "https://maps.googleapis.com/maps/api/directions/json?origin={$startLat},{$startLng}&destination={$endLat},{$endLng}&key={$apiKey}";
        $response = Http::get($url);

        if ($response->successful() && isset($response->json()['routes'][0])) {
            $route = $response->json()['routes'][0];
            return [
                'distance' => $route['legs'][0]['distance']['text'] ?? null,
                'duration' => $route['legs'][0]['duration']['text'] ?? null,
            ];
        }

        return null;
    }


    // public function allTrips(Request $request)
    // {
    //     $trips = Trip::where('user_id', $request->driver_id)->orderBy('created_at', 'desc')->get();

    //     if ($trips->isEmpty()) {
    //         return response()->json(['status' => 404, 'message' => 'trips not found', 'data' => []], 404);
    //     }

    //     $geocodedTrips = $trips->map(function ($trip) {
    //         $pickup = $this->getAddressFromCoordinates($trip->start_lat, $trip->start_lng);
    //         $dropoff = $this->getAddressFromCoordinates($trip->end_lat, $trip->end_lng);
    //         $driverVehicle = DriverVehicle::where('driver_id', $trip->user_id)->pluck('vehicle_id')->first();
    //         $vehicle = Vehicle::where('id', $driverVehicle)->first();
    //         if(isset($vehicle->vehicle_image)){
    //             $vehicle->vehicle_image = 'http://zeroifta.alnairtech.com/vehicles/' . $vehicle->vehicle_image ?? null;

    //         }
    //         $startLat = $trip->start_lat;
    //         $startLng = $trip->start_lng;
    //         $endLat = $trip->end_lat;
    //         $endLng = $trip->end_lng;
    //         $apiKey = 'AIzaSyBtQuABE7uPsvBnnkXtCNMt9BpG9hjeDIg';
    //     $url = "https://maps.googleapis.com/maps/api/directions/json?origin={$startLat},{$startLng}&destination={$endLat},{$endLng}&key={$apiKey}";
    //     $response = Http::get($url);
    //     if ($response->successful()) {
    //         $data = $response->json();
    //         $route = $data['routes'][0];

    //         $distanceText = isset($route['legs'][0]['distance']['text']) ? $route['legs'][0]['distance']['text'] : null;
    //         $durationText = isset($route['legs'][0]['duration']['text']) ? $route['legs'][0]['duration']['text'] : null;

    //         // Format distance (e.g., "100 miles")
    //         if ($distanceText) {
    //             $distanceParts = explode(' ', $distanceText);
    //             $formattedDistance = $distanceParts[0] . ' miles'; // Ensuring it always returns distance in miles
    //         }

    //         // Format duration (e.g., "2 hr 20 min")
    //         if ($durationText) {
    //             $durationParts = explode(' ', $durationText);
    //             $hours = isset($durationParts[0]) ? $durationParts[0] : 0;
    //             $minutes = isset($durationParts[2]) ? $durationParts[2] : 0;
    //             $formattedDuration = $hours . ' hr ' . $minutes . ' min'; // Formatting as "2 hr 20 min"

    //         }
    //     }
    //         return [
    //             'id' => $trip->id,
    //             'user_id' => $trip->user_id,
    //             'pickup' => $pickup,
    //             'dropoff' => $dropoff,
    //             'start_lat' => $trip->start_lat,
    //             'start_lng' => $trip->start_lng,
    //             'end_lat' => $trip->end_lat,
    //             'end_lng' => $trip->end_lng,
    //             'status' => $trip->status,
    //             'vehicle' => $vehicle,
    //             'distance' => $formattedDistance,
    //             'duration' => $formattedDuration,
    //             'created_at' => $trip->created_at,
    //             'updated_at' => $trip->updated_at,
    //         ];
    //     });

    //     return response()->json(['status' => 200, 'message' => 'trips found', 'data' => $geocodedTrips], 200);
    // }
    // private function getAddressFromCoordinates($latitude, $longitude)
    // {
    //     $apiKey = 'AIzaSyBtQuABE7uPsvBnnkXtCNMt9BpG9hjeDIg'; // Add your API key in .env
    //     $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng={$latitude},{$longitude}&key={$apiKey}";

    //     $response = file_get_contents($url);
    //     $response = json_decode($response, true);

    //     if (isset($response['results'][0]['address_components'])) {
    //         foreach ($response['results'][0]['address_components'] as $component) {
    //             if (in_array('administrative_area_level_1', $component['types'])) {
    //                 return $component['long_name']; // State name
    //             }
    //         }
    //     }

    //     return 'Address not found';
    // }

    public function delete($id)
    {
        $checkTrip = Trip::where('vehicle_id',$id)->first();
        if($checkTrip){
            return response()->json(['status' => 400, 'message' => 'Vehicle is in use', 'data' => (object)[]], 400);
        }else{
            $vehicle = Vehicle::findOrFail($id);
            $vehicle->delete();
            return response()->json(['status' => 200, 'message' => 'Vehicle deleted successfully', 'data' => (object)[]], 200);
        }
    }
    public function removeVehicleByTrucker(Request $request)
    {
        $vehicle = Vehicle::where('vin', $request->vin)->where('owner_type', 'company')->first();

       if(!$vehicle){
        return response()->json([
            'status' => 404,
            'message' => 'no vehicle found against this vin',
            'data' => (object)[],
        ], 404);
       }
        if ($vehicle->owner_type !== 'company') {
            return response()->json([
                'status' => 422,
                'message' => 'you are not owner of this vehicle',
                'data' => (object)[],
            ], 422);
        }

        // Check for active trips
        $activeTrips = Trip::where('vehicle_id', $vehicle->id)->where('status', 'active')->exists();

        if ($activeTrips) {
            return response()->json([
                'status' => 400,
                'message' => 'Cannot remove vehicle with active trips',
                'data' => (object)[],
            ], 400);
        }

        $vehicle->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Vehicle removed successfully',
            'data' => (object)[],
        ], 200);
    }
}
