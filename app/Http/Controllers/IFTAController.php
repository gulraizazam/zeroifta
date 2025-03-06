<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessTripStart;
use App\Models\CompanyDriver;
use App\Models\DriverVehicle;
use App\Models\FcmToken;
use App\Models\FuelStation;
use App\Models\Notification as ModelsNotification;
use App\Models\Trip;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

use App\Models\Tripstop;
use App\Models\User;
use App\Models\Vehicle;
use App\Services\FcmService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class IFTAController extends Controller
{
    public function findCheapestFuelStops(Request $request)
    {
        $route = $request->route; // Assuming route data is passed in the request
        $currentGallons = $request->currentGallons;
        $reserveGallons = 45; // Example reserve
        $desiredAdditionalEndingGallons = 0; // Example desired ending gallons

        list($routeWithStops, $remainingGallons) = $this->stopsAlgorithm($route, $currentGallons, $reserveGallons, $desiredAdditionalEndingGallons);

        return response()->json([
            'routeWithStops' => $routeWithStops,
            'remainingGallons' => $remainingGallons
        ]);
    }

    private function stopsAlgorithm($route, $currentGallons, $reserveGallons, $desiredAdditionalEndingGallons)
{


    // Calculate the total fuel needed for the route
    $fuelNeeded = $this->gallonsOfFuel($route['miles'], $route['truckMPG']) + $reserveGallons + $desiredAdditionalEndingGallons;

    // If the current fuel is sufficient, no stop is needed
    if ($fuelNeeded <= $currentGallons) {
        return [[], $currentGallons - $fuelNeeded];
    } else {
        // If a fuel stop is necessary
        $fuelStop = $this->findCheapestFuelStop($route);
        $routeSegmentA = $this->createRouteSegment($route['start'], $fuelStop['location'], $route);

        // Calculate for the first segment
        $gallonsUsedSegmentA = $this->gallonsOfFuel($routeSegmentA['miles'], $routeSegmentA['truckMPG']);
        $remainingGallonsAfterSegmentA = $currentGallons - $gallonsUsedSegmentA;

        // Calculate the amount of fuel to buy
        $gallonsToBuy = min(
            $this->amountOfFuelNeeded($routeSegmentA['miles'], $routeSegmentA['truckMPG']) + $reserveGallons + $desiredAdditionalEndingGallons,
            $route['truckTankCapacity']
        ) - $remainingGallonsAfterSegmentA;

        $stopsData = [
            ['fuelStop' => $fuelStop, 'gallonsToBuy' => $gallonsToBuy]
        ];

        // Update the route for the next segment
        $remainingMiles = $route['miles'] - $routeSegmentA['miles'];



        if ($remainingMiles > 0) {
            $routeSegmentB = $this->createRouteSegment($fuelStop['location'], $route['destination'], [
                'miles' => $remainingMiles,
                'truckMPG' => $route['truckMPG'],
                'truckTankCapacity' => $route['truckTankCapacity'],
                'start' => $fuelStop['location'],
                'destination' => $route['destination']
            ]);

            $remainingGallonsAfterBuying = $gallonsToBuy + $remainingGallonsAfterSegmentA;

            // Recursive call for the remaining segment
            list($segBstopsData, $gallonsRemaining) = $this->stopsAlgorithm($routeSegmentB, $remainingGallonsAfterBuying, $reserveGallons, $desiredAdditionalEndingGallons);

            return [array_merge($stopsData, $segBstopsData), $gallonsRemaining];
        } else {
            // In case there are no remaining miles, return the current data
            return [$stopsData, $gallonsToBuy + $remainingGallonsAfterSegmentA];
        }
    }
}


    private function gallonsOfFuel($miles, $MPG)
    {
        return $miles / $MPG;
    }

    private function amountOfFuelNeeded($miles, $MPG)
    {
        return $this->gallonsOfFuel($miles, $MPG);
    }

    private function findCheapestFuelStop($route)
    {
        // Implement your logic to find the cheapest fuel stop along the route.
        // This is a placeholder for demonstration purposes.
        return [
            'location' => 'Some Fuel Station',
            //'pricePerGallon' => 3.50
        ];
    }

    private function createRouteSegment($start, $end, $route)
    {
        // Check if 'segments' key exists in the route array
        if (isset($route['segments'])) {
            // Find the segment of the route that starts at $start and ends at $end
            foreach ($route['segments'] as $segment) {
                if ($segment['start'] === $start && $segment['end'] === $end) {
                    return [
                        'start' => $segment['start'],
                        'end' => $segment['end'],
                        'miles' => $segment['miles'],
                        'truckMPG' => $segment['truckMPG'],
                        'truckTankCapacity' => $route['truckTankCapacity']
                    ];
                }
            }
        }

        // Fallback if the 'segments' key doesn't exist or the segment is not found
        return [
            'start' => $start,
            'end' => $end,
            'miles' => $route['miles'], // Using full route miles if segment not found
            'truckMPG' => $route['truckMPG'],
            'truckTankCapacity' => $route['truckTankCapacity']
        ];
    }

    public function findFuelStations(Request $request)
{
    $request->validate([
        'start_lat' => 'required|numeric',
        'start_lng' => 'required|numeric',
        'end_lat' => 'required|numeric',
        'end_lng' => 'required|numeric',
    ]);

    $startLat = $request->input('start_lat');
    $startLng = $request->input('start_lng');
    $endLat = $request->input('end_lat');
    $endLng = $request->input('end_lng');

    $apiKey = 'AIzaSyBtQuABE7uPsvBnnkXtCNMt9BpG9hjeDIg';

    // Step 1: Get the route
    $directionsUrl = "https://maps.googleapis.com/maps/api/directions/json?origin=$startLat,$startLng&destination=$endLat,$endLng&key=$apiKey";
    $directionsResponse = Http::get($directionsUrl);

    if ($directionsResponse->failed()) {
        return response()->json(['error' => 'Failed to fetch directions'], 500);
    }

    $directionsData = $directionsResponse->json();

    if (empty($directionsData['routes'])) {
        return response()->json(['error' => 'No route found for the given coordinates'], 404);
    }

    $steps = collect($directionsData['routes'][0]['legs'][0]['steps']);

    // Step 2: Extract waypoints
    $waypoints = $steps->map(fn($step) => [
        'lat' => $step['end_location']['lat'],
        'lng' => $step['end_location']['lng'],
    ]);

    // Step 3: Find truck stops along the waypoints
    $truckStops = collect();
    foreach ($waypoints as $point) {
        $lat = $point['lat'];
        $lng = $point['lng'];

        $placesUrl = "https://maps.googleapis.com/maps/api/place/nearbysearch/json?location=$lat,$lng&radius=5000&keyword=truck+stop&key=$apiKey";
        $placesResponse = Http::get($placesUrl);

        if ($placesResponse->failed()) {
            continue;
        }

        $placesData = $placesResponse->json();
        foreach ($placesData['results'] as $place) {
            $truckStops->push([
                'name' => $place['name'],
                'address' => $place['vicinity'],
                'lat' => $place['geometry']['location']['lat'],
                'lng' => $place['geometry']['location']['lng'],
                'place_id' => $place['place_id'],
            ]);
        }
    }

    // Remove duplicates by `place_id`
    $uniqueTruckStops = $truckStops->unique('place_id')->values();

    return response()->json(['truck_stops' => $uniqueTruckStops]);
}
    public function updateTrip(Request $request)
    {
        $validatedData =$request->validate([
            'trip_id' => 'required|exists:trips,id',
            'start_lat' => 'required',
            'start_lng' => 'required',
            'end_lat' => 'required',
            'end_lng' => 'required',
            'truck_mpg' => 'required',
            'fuel_tank_capacity' => 'required',
            'total_gallons_present' => 'required',
        ]);

        $updatedStartLat = $request->start_lat;
        $updatedStartLng = $request->start_lng;
        $updatedEndLat = $request->end_lat;
        $updatedEndLng = $request->end_lng;
        $startLat = $request->start_lat;
        $startLng = $request->start_lng;
        $endLat = $request->end_lat;
        $endLng = $request->end_lng;
        $truckMpg = $request->truck_mpg;
        $fuelTankCapacity = $request->fuel_tank_capacity;
        $currentFuel = $request->total_gallons_present;
        // Replace with your Google API key
        $apiKey = 'AIzaSyBtQuABE7uPsvBnnkXtCNMt9BpG9hjeDIg';
        $stops = Tripstop::where('trip_id', $request->trip_id)->get();
        if ($stops->isNotEmpty()) {
            $waypoints = $stops->map(fn($stop) => "{$stop->stop_lat},{$stop->stop_lng}")->implode('|');
        }
        $url = "https://maps.googleapis.com/maps/api/directions/json?origin={$updatedStartLat},{$updatedStartLng}&destination={$updatedEndLat},{$updatedEndLng}&key={$apiKey}";
        if (isset($waypoints)) {
            $url .= "&waypoints=optimize:true|{$waypoints}";
        }
        // Fetch data from Google Maps API
        $response = Http::get($url);

        if ($response->successful()) {
            $data = $response->json();
            if($data['routes'] && $data['routes'][0]){
                if (!empty($data['routes'][0]['legs'])) {
                    $steps = $data['routes'][0]['legs'][0]['steps'];
                    $decodedCoordinates = [];
                $stepSize = 7; // Sample every 10th point

                foreach ($steps as $step) {
                    if (isset($step['polyline']['points'])) {
                        $points = $this->decodePolyline($step['polyline']['points']);
                        // Sample every 10th point
                        for ($i = 0; $i < count($points); $i += $stepSize) {
                            $decodedCoordinates[] = $points[$i];
                        }
                    }
                }
                    $polylinePoints = [];

                    foreach ($data['routes'][0]['legs'] as $leg) {
                        if (!empty($leg['steps'])) {
                            foreach ($leg['steps'] as $step) {
                                if (isset($step['polyline']['points'])) {
                                    $polylinePoints[] = $step['polyline']['points'];
                                }
                            }
                        }
                    }

                    // Filter out any null values if necessary
                    $polylinePoints = array_filter($polylinePoints);
                }
                $route = $data['routes'][0];
                if($route){
                    $totalDistance = 0;
                    $totalDuration = 0;

                    foreach ($route['legs'] as $leg) {
                        $totalDistance += $leg['distance']['value']; // Distance in meters
                        $totalDuration += $leg['duration']['value']; // Duration in seconds
                    }

                    // Convert meters to miles
                    $totalDistanceMiles = round($totalDistance * 0.000621371, 2);

                    // Convert seconds to hours and minutes
                    $hours = floor($totalDuration / 3600);
                    $minutes = floor(($totalDuration % 3600) / 60);

                    // Format distance
                    $formattedDistance = $totalDistanceMiles . ' miles';

                    // Format duration
                    if ($hours > 0) {
                        $formattedDuration = "{$hours} hr {$minutes} min";
                    } else {
                        $formattedDuration = "{$minutes} min";
                    }
                }
                if (isset($data['routes'][0]['overview_polyline']['points'])) {
                    $encodedPolyline = $data['routes'][0]['overview_polyline']['points'];
                    $decodedPolyline = $this->decodePolyline($encodedPolyline);

                    // Filter coordinates based on distance from start and end points
                    $finalFilteredPolyline = array_filter($decodedPolyline, function ($coordinate) use ($updatedStartLat, $updatedStartLng, $updatedEndLat, $updatedEndLng) {
                        // Ensure $coordinate is valid
                        if (isset($coordinate['lat'], $coordinate['lng'])) {
                            // Calculate distances from both start and end points
                            $distanceFromStart = $this->haversineDistanceFilter($updatedStartLat, $updatedStartLng, $coordinate['lat'], $coordinate['lng']);
                            $distanceFromEnd = $this->haversineDistanceFilter($updatedEndLat, $updatedEndLng, $coordinate['lat'], $coordinate['lng']);

                            // Keep coordinates if they are sufficiently far from both points
                            return $distanceFromStart > 9 && $distanceFromEnd > 9;
                        }
                        return false; // Skip invalid coordinates
                    });

                    // Reset array keys to ensure a clean array structure
                    $finalFilteredPolyline = array_values($finalFilteredPolyline);
                    $matchingRecords = $this->loadAndParseFTPData($finalFilteredPolyline);
                   // $matchingRecords = $this->findMatchingRecords($finalFilteredPolyline, $ftpData);
                    $reserve_fuel = $request->reserve_fuel;

                 $totalFuel = $currentFuel+$reserve_fuel;
                $tripDetailResponse = [
                    'data' => [
                        'trip' => [
                            'start' => [
                                'latitude' => $updatedStartLat,
                                'longitude' => $updatedStartLng
                            ],
                            'end' => [
                                'latitude' => $updatedEndLat,
                                'longitude' => $updatedEndLng
                            ]
                        ],
                        'vehicle' => [
                            'mpg' => $truckMpg,
                            'fuelLeft' => $totalFuel
                        ],
                        'fuelStations' => $matchingRecords,
                        'polyline'=>$decodedCoordinates

                    ]
                ];

                $result = $this->markOptimumFuelStations($tripDetailResponse);
                if($result==false){
                    $result = $matchingRecords;
                }
                   // $result = $this->findOptimalFuelStation($startLat, $startLng, $truckMpg, $currentFuel, $matchingRecords, $endLat, $endLng);
                    $trip = Trip::find($request->trip_id);
                    $trip->update([
                        'updated_start_lat' => $updatedStartLat,
                        'updated_start_lng' => $updatedStartLng,
                        'updated_end_lat' => $updatedEndLat,
                        'updated_end_lng' => $updatedEndLng,
                    ]);
                    foreach ($result as $value) {
                        FuelStation::updateOrCreate(
                            [
                                'trip_id' => $trip->id, // Condition to check if the record exists
                                'latitude' => $value['ftpLat'],
                                'longitude' => $value['ftpLng']
                            ],
                            [
                                'name' => $value['fuel_station_name'],
                                'price' => $value['price'],
                                'lastprice' => $value['lastprice'],
                                'discount' => $value['discount'],
                                'ifta_tax' => $value['IFTA_tax'],
                                'is_optimal' => $value['isOptimal'] ?? false,
                                'address' => $value['address'],
                                'gallons_to_buy' => $value['gallons_to_buy'],
                                'trip_id' => $trip->id,
                                'user_id' => $trip->user_id,
                            ]
                        );
                    }
                    $trip->distance = $formattedDistance;
                    $trip->duration = $formattedDuration;
                    $stops = Tripstop::where('trip_id', $trip->id)->get();
                    $driverVehicle = DriverVehicle::where('driver_id', $trip->user_id)->first();
                    if($driverVehicle){
                        $vehicle = Vehicle::where('id', $driverVehicle->vehicle_id)->first();
                        $vehicle->update([
                            'fuel_left'=> $currentFuel,
                            'mpg'=>$truckMpg,
                            'reserve_fuel'=>$request->reserve_fuel,
                        ]);
                        if($vehicle && $vehicle->vehicle_image != null){
                            $vehicle->vehicle_image = url('/vehicles/'.$vehicle->vehicle_image);
                        }
                    }else{

                        $vehicle=null;
                    }


                    // Create a separate key for the polyline
                    $responseData = [
                        'trip_id' => $request->trip_id,
                        'trip' => $trip,
                        'fuel_stations' => $result, // Fuel stations with optimal station marked
                        'polyline' => $decodedPolyline,
                        'encoded_polyline'=>$encodedPolyline,
                        'polyline_paths' => $polylinePoints ?? [],
                        'stops' => $stops,
                        'vehicle' => $vehicle
                    ];
                    $findDriver = User::where('id', $trip->user_id)->first();
                    if($findDriver){

                     $findCompany = CompanyDriver::where('driver_id',$findDriver->id)->first();
                     if ($findCompany) {
                        $driverFcm = FcmToken::where('user_id', $findDriver->id)->pluck('token')->toArray();
                        $companyFcmTokens = FcmToken::where('user_id', $findCompany->company_id)
                        ->pluck('token')
                        ->toArray();

                        if (!empty($companyFcmTokens)) {
                            $factory = (new Factory)->withServiceAccount(storage_path('app/zeroifta.json'));
                            $messaging = $factory->createMessaging();

                            // Create the notification payload
                            $message = CloudMessage::new()
                                ->withNotification(Notification::create('Trip Updated', $findDriver->name . 'has updated a trip.'))
                                ->withData([
                                    'trip_id' => (string) $trip->id,  // Include trip ID for reference
                                    'driver_name' => $findDriver->name, // Driver's name
                                    'sound' => 'default',  // This triggers the sound
                                ]);

                            // Send notification to all FCM tokens of the company
                            $response = $messaging->sendMulticast($message, $companyFcmTokens);
                        }
                        if (!empty($driverFcm)) {
                            $factory = (new Factory)->withServiceAccount(storage_path('app/zeroifta.json'));
                            $messaging = $factory->createMessaging();

                            $message = CloudMessage::new()
                                ->withNotification(Notification::create('Trip Updated', 'Trip updated successfully'))
                                ->withData([
                                    'sound' => 'default', // This triggers the sound
                                ]);

                            $response = $messaging->sendMulticast($message, $driverFcm);
                            ModelsNotification::create([
                                'user_id' => $findCompany->company_id,
                                'title' => 'Trip Updated',
                                'body' => $findDriver->name . ' has updated a trip.',
                            ]);
                        }
                    }
                    }
                    return response()->json([
                        'status' => 200,
                        'message' => 'Fuel stations fetched successfully.',
                        'data' => $responseData,
                    ]);
                }

                return response()->json([
                    'status' => 404,
                    'message' => 'No route found.',
                    'data'=>(object)[]
                ], 404);
            }else{
                return response()->json([
                    'status' => 500,
                    'message' => 'Failed to fetch data from Google Maps API.',
                    'data'=>(object)[]
                ]);
            }

        }

        return response()->json([
            'status' => 500,
            'message' => 'Failed to fetch data from Google Maps API.',
            'data'=>(object)[]
        ]);
}
    public function getDecodedPolyline(Request $request, FcmService $firebaseService)
    {
        // Increase execution time to handle long requests
        ini_set('max_execution_time', 600);
        ///validating all data///
        $validatedData =$request->validate([
            'user_id'   => 'required|exists:users,id',
            'start_lat' => 'required',
            'start_lng' => 'required',
            'end_lat' => 'required',
            'end_lng' => 'required',
            'truck_mpg' => 'required',
            'fuel_tank_capacity' => 'required',
            'total_gallons_present' => 'required',
            //'reserve_fuel'=>'required'
        ]);
        // Check if an active trip already exists for the user
        $findTrip = Trip::where('user_id', $validatedData['user_id'])->where('status', 'active')->first();

        if ($findTrip) {
            return response()->json(['status' => 422, 'message' => 'Trip already exists for this user', 'data' => $findTrip]);
        }
         // Set trip status to active
        $validatedData['status']='active';
         // Fetch the vehicle associated with the driver
        $vehicle_id = DriverVehicle::where('driver_id', $validatedData['user_id'])->first();
        if ($vehicle_id) {
            $validatedData['vehicle_id'] = $vehicle_id->vehicle_id;
        }
         // Extract input parameters
        $startLat = $request->start_lat;
        $startLng = $request->start_lng;
        $endLat = $request->end_lat;
        $endLng = $request->end_lng;
        $truckMpg = $request->truck_mpg;
        $fuelTankCapacity = $request->fuel_tank_capacity;
        $currentFuel = $request->total_gallons_present;
        // Fetch route data from Google Maps API
        $apiKey = 'AIzaSyBtQuABE7uPsvBnnkXtCNMt9BpG9hjeDIg';
        $url = "https://maps.googleapis.com/maps/api/directions/json?origin={$startLat},{$startLng}&destination={$endLat},{$endLng}&key={$apiKey}";

        // Fetch data from Google Maps API
        $response = Http::get($url);

        if ($response->successful()) {
            $data = $response->json();
           if($data['routes'] && $data['routes'][0]){
            if (!empty($data['routes'][0]['legs'][0]['steps'])) {
                $steps = $data['routes'][0]['legs'][0]['steps'];
                $filteredPolylines = [];

                foreach ($steps as $index => $step) {
                    if (isset($step['polyline']['points'])) {
                        // Only process steps with odd indices
                        if ($index % 2 !== 0) {
                            $filteredPolylines[] = $step['polyline']['points'];
                        }
                    }
                }

                // Now decode only the selected odd-indexed polylines
                $decodedCoordinates = [];
                foreach ($filteredPolylines as $polyline) {
                    $decodedCoordinates = array_merge($decodedCoordinates, $this->decodePolyline($polyline));
                }
            }

            $route = $data['routes'][0];
            // Extract and format distance & duration
            $distanceText = isset($route['legs'][0]['distance']['text']) ? $route['legs'][0]['distance']['text'] : null;
            $durationText = isset($route['legs'][0]['duration']['text']) ? $route['legs'][0]['duration']['text'] : null;

            // Format distance (e.g., "100 miles")
            if ($distanceText) {
                $distanceParts = explode(' ', $distanceText);
                $formattedDistance = $distanceParts[0] . ' miles'; // Ensuring it always returns distance in miles
            }

            // Format duration (e.g., "2 hr 20 min")
            if ($durationText) {
                $durationParts = explode(' ', $durationText);
                $hours = isset($durationParts[0]) ? $durationParts[0] : 0;
                $minutes = isset($durationParts[2]) ? $durationParts[2] : 0;
                $formattedDuration = $hours . ' hr ' . $minutes . ' min'; // Formatting as "2 hr 20 min"

            }

            if (isset($data['routes'][0]['overview_polyline']['points'])) {
                $encodedPolyline = $data['routes'][0]['overview_polyline']['points'];
                $decodedPolyline = $this->decodePolyline($encodedPolyline);

                // Filter coordinates based on distance from start and end points
                $finalFilteredPolyline = array_filter($decodedPolyline, function ($coordinate) use ($startLat, $startLng, $endLat, $endLng) {
                    // Ensure $coordinate is valid
                    if (isset($coordinate['lat'], $coordinate['lng'])) {
                        // Calculate distances from both start and end points
                        $distanceFromStart = $this->haversineDistanceFilter($startLat, $startLng, $coordinate['lat'], $coordinate['lng']);
                        $distanceFromEnd = $this->haversineDistanceFilter($endLat, $endLng, $coordinate['lat'], $coordinate['lng']);

                        // Keep coordinates if they are sufficiently far from both points
                        return $distanceFromStart > 9 && $distanceFromEnd > 9;
                    }
                    return false; // Skip invalid coordinates
                });

                // Reset array keys to ensure a clean array structure
                $finalFilteredPolyline = array_values($finalFilteredPolyline);
                $matchingRecords = $this->loadAndParseFTPData($finalFilteredPolyline);
                //$matchingRecords = $this->findMatchingRecords($finalFilteredPolyline, $ftpData);
                $reserve_fuel = $request->reserve_fuel;
                 $totalFuel = $currentFuel+$reserve_fuel;
                $tripDetailResponse = [
                    'data' => [
                        'trip' => [
                            'start' => [
                                'latitude' => $startLat,
                                'longitude' => $startLng
                            ],
                            'end' => [
                                'latitude' => $endLat,
                                'longitude' => $endLng
                            ]
                        ],
                        'vehicle' => [
                            'mpg' => $truckMpg,
                            'fuelLeft' => $totalFuel
                        ],
                        'fuelStations' => $matchingRecords,
                        'polyline'=>$decodedCoordinates

                    ]
                ];
                // Find optimum fuel stations
                $result = $this->markOptimumFuelStations($tripDetailResponse);
                if($result==false){
                    $result = $matchingRecords;

                }
                // Create a new trip record
                $fuelStations = [];
                $validatedData['updated_start_lat'] = $request->start_lat;
                $validatedData['updated_start_lng'] = $request->start_lng;
                $validatedData['updated_end_lat'] = $request->end_lat;
                $validatedData['updated_end_lng'] = $request->end_lng;
                $trip = Trip::create($validatedData);
               foreach ($result as  $value) {
                    // Prepare fuel station data for processing
                    $fuelStations[] = [
                        'name' => $value['fuel_station_name'],
                        'latitude' => $value['ftpLat'],
                        'longitude' => $value['ftpLng'],
                        'price' => $value['price'],
                        'lastprice' => $value['lastprice'],
                        'discount' => $value['discount'],
                        'ifta_tax' => $value['IFTA_tax'],
                        'is_optimal' => $value['isOptimal'] ?? false,
                        'address' => $value['address'] ?? 'N/A',
                        'gallons_to_buy' => $value['gallons_to_buy'] ?? 0,
                        'trip_id' => $trip->id,
                        'user_id' => $validatedData['user_id'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
               }
               // Dispatch background process
               ProcessTripStart::dispatch($fuelStations, $trip, $validatedData);
                $trip->distance = $formattedDistance;
                $trip->duration = $formattedDuration;
                $trip->user_id = (int)$trip->user_id;
                $vehicleFind = DriverVehicle::where('driver_id', $trip->user_id)->pluck('vehicle_id')->first();
                if($vehicleFind){
                    // Update vehicle details
                    $vehicle = Vehicle::where('id', $vehicleFind)->first();
                    $vehicle->fuel_left= $currentFuel;
                    $vehicle->mpg=$truckMpg;
                    $vehicle->reserve_fuel=$request->reserve_fuel;
                    $vehicle->update();
                    if($vehicle && $vehicle->vehicle_image != null){
                        $vehicle->vehicle_image =url('/vehicles/'.$vehicle->vehicle_image);
                    }
                }else{
                    $vehicle = null;
                }

                $responseData = [
                    'trip_id'=>$trip->id,
                    'trip' => $trip,
                    'fuel_stations' => $result,
                    'polyline' => $decodedPolyline,
                    'encoded_polyline'=>$encodedPolyline,
                    'polyline_paths'=>$polylinePoints ?? [],
                    'stops'=>[],
                    'vehicle' => $vehicle
                ];

                return response()->json([
                    'status' => 200,
                    'message' => 'Fuel stations fetched successfully.',
                    'data' => $responseData,
                ]);
            }


           }else{
            return response()->json([
                'status' => 500,
                'message' => 'Failed to fetch data from Google Maps API.',
                'data'=>(object)[]
            ]);
           }

        }

        return response()->json([
            'status' => false,
            'message' => 'Failed to fetch polyline.',
        ], 500);
    }
    private function decodePolyline($encoded)
    {
        $points = [];
        $index = 0;
        $len = strlen($encoded);
        $lat = 0;
        $lng = 0;

        while ($index < $len) {
            $b = 0;
            $shift = 0;
            $result = 0;

            do {
                $b = ord($encoded[$index++]) - 63;
                $result |= ($b & 0x1f) << $shift;
                $shift += 5;
            } while ($b >= 0x20);

            $dlat = (($result & 1) ? ~($result >> 1) : ($result >> 1));
            $lat += $dlat;

            $shift = 0;
            $result = 0;

            do {
                $b = ord($encoded[$index++]) - 63;
                $result |= ($b & 0x1f) << $shift;
                $shift += 5;
            } while ($b >= 0x20);

            $dlng = (($result & 1) ? ~($result >> 1) : ($result >> 1));
            $lng += $dlng;

            $points[] = [
                'lat' => number_format($lat * 1e-5, 5),
                'lng' => number_format($lng * 1e-5, 5),
            ];
        }

        return $points;
    }
    function haversineDistanceFilter($lat1, $lng1, $lat2, $lng2) {
        $earthRadius = 3958.8; // Earth radius in miles

        // Convert degrees to radians
        $lat1 = deg2rad($lat1);
        $lng1 = deg2rad($lng1);
        $lat2 = deg2rad($lat2);
        $lng2 = deg2rad($lng2);

        // Haversine formula
        $dLat = $lat2 - $lat1;
        $dLng = $lng2 - $lng1;

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos($lat1) * cos($lat2) *
             sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c; // Distance in miles
    }
    private function loadAndParseFTPData(array $decodedPolyline)
    {
        $filePath = 'EFSLLCpricing';

        // Connect to the FTP disk
        $ftpDisk = Storage::disk('ftp');
        if (!$ftpDisk->exists($filePath)) {
            throw new \Exception("FTP file not found.");
        }

        $fileContent = $ftpDisk->get($filePath);
        $rows = explode("\n", trim($fileContent));
        $filteredData = [];
        $uniqueRecords = [];

        foreach ($rows as $line) {
            $row = explode('|', $line);

            if (!isset($row[8], $row[9])) {
                continue; // Skip invalid data
            }

            $lat2 = number_format((float) trim($row[8]), 4);
            $lng2 = number_format((float) trim($row[9]), 4);

            // Check if this station is near the route
            foreach ($decodedPolyline as $decoded) {
                $lat1 = $decoded['lat'];
                $lng1 = $decoded['lng'];
                $distance = $this->haversineDistance($lat1, $lng1, $lat2, $lng2);

                if ($distance < 12000) { // Within 500 meters
                    $uniqueKey = $lat2 . ',' . $lng2;

                    if (!isset($uniqueRecords[$uniqueKey])) {
                        $filteredData[] = [
                            'fuel_station_name' => (string) $row[1] ?? 'N/A',
                            'ftpLat' => (string) $lat2,
                            'ftpLng' => (string) $lng2,
                            'lastprice' => (float) $row[10] ?? 0.00,
                            'price' => (float) $row[11] ?? 0.00,
                            'discount' => (float) $row[12] ?? 0.00,
                            'IFTA_tax' => (float) $row[18] ?? 0.00,
                            'address' => (string) $row[3] ?? 'N/A',
                        ];
                        $uniqueRecords[$uniqueKey] = true;
                    }
                    break; // No need to check further once it's matched
                }
            }
        }

        return $filteredData;
    }
    private function haversineDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6371000; // Earth's radius in meters

        $latDelta = deg2rad($lat2 - $lat1);
        $lngDelta = deg2rad($lng2 - $lng1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($lngDelta / 2) * sin($lngDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c; // Distance in meters
    }
    function markOptimumFuelStations($tripDetailResponse)
    {
        if (!$tripDetailResponse) {
            return null;
        }

        $mutableData = $tripDetailResponse;
        $startLat = $tripDetailResponse['data']['trip']['start']['latitude'] ?? null;
        $startLng = $tripDetailResponse['data']['trip']['start']['longitude'] ?? null;
        $endLat = $tripDetailResponse['data']['trip']['end']['latitude'] ?? null;
        $endLng = $tripDetailResponse['data']['trip']['end']['longitude'] ?? null;
        $start = $tripDetailResponse['data']['trip']['start'] ?? null;
        $fuelStations = collect($tripDetailResponse['data']['fuelStations']);
        $optimalStation = $fuelStations->firstWhere('isOptimal', true);

        // Calculate truck's travelable distance
        $truckTravelableDistanceInMiles = 0;
        if (!empty($tripDetailResponse['data']['vehicle']['mpg'])) {
            $mpg = floatval($tripDetailResponse['data']['vehicle']['mpg']);
            $fuelLeft = floatval($tripDetailResponse['data']['vehicle']['fuelLeft'] ?? 0);
            $truckTravelableDistanceInMiles = $mpg * $fuelLeft;
        }
        $polyline = $tripDetailResponse['data']['polyline'];
        // Add distanceFromStart to every fuel station
        $fuelStations = $fuelStations->map(function ($fuelStation) use ($start,$polyline) {
            if ($start) {
                $fuelStation['distanceFromStart'] = $this->getDistance($start, $fuelStation,$polyline);
            }
            return $fuelStation;
        });

        // Also, add distanceFromStart to the optimal station if it exists
        if ($optimalStation && $start) {
            $optimalStation['distanceFromStart'] = $this->getDistance($start, $optimalStation,$polyline);
        }
        $optimalFuelStations = [];
        // Find the cheapest station and mark it as isOptimal
        $cheapestStation = $fuelStations->sortBy('price')->first();
        if ($cheapestStation) {
            $cheapestStation['isOptimal'] = true;
            $optimalFuelStations[] = $cheapestStation;
            $fuelStations = $fuelStations->reject(fn($fs) => $fs['ftpLat'] === $cheapestStation['ftpLat'] && $fs['ftpLng'] === $cheapestStation['ftpLng'])->push($cheapestStation);
        }

        // Separate stations into in-range and out-of-range based on truck's fuel capacity
        $fuelStationsInRange = $fuelStations->filter(fn($fs) => $fs['distanceFromStart'] < $truckTravelableDistanceInMiles);
        $fuelStationsOutsideRange = $fuelStations->reject(fn($fs) => $fs['distanceFromStart'] < $truckTravelableDistanceInMiles);
        if($fuelStationsInRange->isEmpty()){
            return false;
        }
        // Find the cheapest stations
        $firstCheapestInRange = $fuelStationsInRange->sortBy('price')->first();

        $secondCheapestInRange = $fuelStationsOutsideRange
        ->sortBy('price')
        ->first(function ($station) use ($firstCheapestInRange, $cheapestStation) {
            return $station['price'] < $firstCheapestInRange['price'] &&
                   $station['price'] > $cheapestStation['price'] &&
                   $station['distanceFromStart'] < $cheapestStation['distanceFromStart'];
        });
           // dd($secondCheapestInRange);

        // Find mid-optimal station
        $midOptimal = $fuelStationsOutsideRange->filter(fn($fs) =>
            $firstCheapestInRange &&
            $secondCheapestInRange &&
            $fs['price'] < $firstCheapestInRange['price'] &&
            $fs['distanceFromStart'] < $secondCheapestInRange['distanceFromStart']
        )->sortBy('distanceFromStart')->first();

        // Mark stations as optimal
        if ($secondCheapestInRange && $firstCheapestInRange && $secondCheapestInRange['price'] < $firstCheapestInRange['price']) {
            $secondCheapestInRange['secondOptimal'] = true;
        }

        // Remove stations if they are farther than optimal
        if ($optimalStation) {
            if ($secondCheapestInRange && $secondCheapestInRange['distanceFromStart'] > $optimalStation['distanceFromStart']) {
                $secondCheapestInRange = null;
            }
            if ($firstCheapestInRange && $firstCheapestInRange['distanceFromStart'] > $optimalStation['distanceFromStart']) {
                $firstCheapestInRange = null;
            }
        }

        // Append optimal stations
        if ($firstCheapestInRange) {
            $firstCheapestInRange['firstOptimal'] = true;
            $optimalFuelStations[] = $firstCheapestInRange;
            $fuelStations = $fuelStations->reject(fn($fs) => $fs['ftpLat'] === $firstCheapestInRange['ftpLat'] && $fs['ftpLng'] === $firstCheapestInRange['ftpLng'])->push($firstCheapestInRange);
        }
        if ($secondCheapestInRange) {
            $optimalFuelStations[] = $secondCheapestInRange;
            $fuelStations = $fuelStations->reject(fn($fs) => $fs['ftpLat'] === $secondCheapestInRange['ftpLat'] && $fs['ftpLng'] === $secondCheapestInRange['ftpLng'])->push($secondCheapestInRange);
        }
        if ($midOptimal) {
            $midOptimal['midOptimal'] = true;
            $optimalFuelStations[] = $midOptimal;
            $fuelStations = $fuelStations->reject(fn($fs) => $fs['ftpLat'] === $midOptimal['ftpLat'] && $fs['ftpLng'] === $midOptimal['ftpLng'])->push($midOptimal);
        }

        // Add optimal station back
        if ($optimalStation) {
            $fuelStations->push($optimalStation);
        }
        $fuelStations = $fuelStations->map(function ($station) use ($firstCheapestInRange) {
            if (!isset($station['gallons_to_buy']) || $station['gallons_to_buy'] === null) {
                // Only set to null if it's completely missing, don't overwrite existing values
                $station['gallons_to_buy'] = $station['gallons_to_buy'] ?? null;
            }
            return $station;
        });
        // Calculate gallons_to_buy for firstOptimal to midOptimal
        if ($firstCheapestInRange && $midOptimal) {
            // Calculate fuel used to reach firstOptimal
            $distanceToFirstOptimal = $firstCheapestInRange['distanceFromStart'];
            $fuelUsedToFirstOptimal = $distanceToFirstOptimal / $mpg;

            // Fuel left after reaching firstOptimal
            $fuelLeftAfterFirstOptimal = max(0, $fuelLeft - $fuelUsedToFirstOptimal);

            // Calculate fuel needed from firstOptimal to midOptimal
            $distanceBetweenFirstAndMid = $midOptimal['distanceFromStart'] - $firstCheapestInRange['distanceFromStart'];
            $fuelNeededForMid = $distanceBetweenFirstAndMid / $mpg;

            // If fuel left is not enough, buy fuel at firstOptimal
            if ($fuelLeftAfterFirstOptimal < $fuelNeededForMid) {
                $gallonsToBuyFirst = $fuelNeededForMid - $fuelLeftAfterFirstOptimal;

                // Update fuel stations in the original collection
                $fuelStations = $fuelStations->map(function ($station) use ($firstCheapestInRange, $gallonsToBuyFirst) {
                    if ($station['fuel_station_name'] === $firstCheapestInRange['fuel_station_name']) {
                        $station['gallons_to_buy'] = $gallonsToBuyFirst;
                    }
                    return $station;
                });

                // Update fuel left after refueling at firstOptimal
                $fuelLeftAfterFirstOptimal += $gallonsToBuyFirst;
            }

            // Calculate fuel used to reach midOptimal
            $fuelUsedToMidOptimal = $fuelNeededForMid;
            $fuelLeftAfterMidOptimal = max(0, $fuelLeftAfterFirstOptimal - $fuelUsedToMidOptimal);

            // Calculate fuel needed from midOptimal to secondOptimal
            if ($midOptimal && $secondCheapestInRange) {
                $distanceBetweenMidAndSecond = $secondCheapestInRange['distanceFromStart'] - $midOptimal['distanceFromStart'];
                $fuelNeededForSecond = $distanceBetweenMidAndSecond / $mpg;

                // If fuel left is not enough, buy fuel at midOptimal
                if ($fuelLeftAfterMidOptimal < $fuelNeededForSecond) {
                    $gallonsToBuyMid = $fuelNeededForSecond - $fuelLeftAfterMidOptimal;

                    // Update midOptimal in the collection
                    $fuelStations = $fuelStations->map(function ($station) use ($midOptimal, $gallonsToBuyMid) {
                        if ($station['fuel_station_name'] === $midOptimal['fuel_station_name']) {
                            $station['gallons_to_buy'] = $gallonsToBuyMid;
                        }
                        return $station;
                    });

                    // Update fuel left after refueling at midOptimal
                    $fuelLeftAfterMidOptimal += $gallonsToBuyMid;
                }

                // Calculate fuel used to reach secondOptimal
                $fuelUsedToSecondOptimal = $fuelNeededForSecond;
                $fuelLeftAfterSecondOptimal = max(0, $fuelLeftAfterMidOptimal - $fuelUsedToSecondOptimal);

                // Calculate fuel needed from secondOptimal to isOptimal
                if ($secondCheapestInRange && $cheapestStation) {
                    $distanceBetweenSecondAndLast = $cheapestStation['distanceFromStart'] - $secondCheapestInRange['distanceFromStart'];
                    $fuelNeededForLast = $distanceBetweenSecondAndLast / $mpg;

                    // If fuel left is not enough, buy fuel at secondOptimal
                    if ($fuelLeftAfterSecondOptimal < $fuelNeededForLast) {
                        $gallonsToBuySecond = $fuelNeededForLast - $fuelLeftAfterSecondOptimal;

                        // Update secondOptimal in the collection
                        $fuelStations = $fuelStations->map(function ($station) use ($secondCheapestInRange, $gallonsToBuySecond) {
                            if ($station['fuel_station_name'] === $secondCheapestInRange['fuel_station_name']) {
                                $station['gallons_to_buy'] = $gallonsToBuySecond;
                            }
                            return $station;
                        });

                        // Update fuel left after refueling at secondOptimal
                        $fuelLeftAfterSecondOptimal += $gallonsToBuySecond;
                    }

                    // Calculate fuel used to reach isOptimal
                    $fuelUsedToIsOptimal = $fuelNeededForLast;
                    $fuelLeftAfterIsOptimal = max(0, $fuelLeftAfterSecondOptimal - $fuelUsedToIsOptimal);

                    // Calculate fuel needed from isOptimal to end of trip
                    if ($cheapestStation && $endLat && $endLng) {
                        $distanceFromIsOptimalToEnd = $this->calculateDistance(
                            $cheapestStation['ftpLat'], $cheapestStation['ftpLng'],
                            $endLat, $endLng
                        );
                        $fuelNeededToEnd = $distanceFromIsOptimalToEnd / $mpg;

                        // If fuel left is not enough, buy fuel at isOptimal
                        if ($fuelLeftAfterIsOptimal < $fuelNeededToEnd) {
                            $gallonsToBuyIsOptimal = $fuelNeededToEnd - $fuelLeftAfterIsOptimal;

                            // Update isOptimal in the collection
                            $fuelStations = $fuelStations->map(function ($station) use ($cheapestStation, $gallonsToBuyIsOptimal) {
                                if ($station['fuel_station_name'] === $cheapestStation['fuel_station_name']) {
                                    $station['gallons_to_buy'] = ($station['gallons_to_buy'] ?? 0) + $gallonsToBuyIsOptimal;
                                }
                                return $station;
                            });

                            // Update fuel left after refueling at isOptimal
                            $fuelLeftAfterIsOptimal += $gallonsToBuyIsOptimal;
                        }
                    }
                }
            }
        }
        if ($cheapestStation) {
            // Calculate fuel used to reach isOptimal
            $distanceToIsOptimal = $cheapestStation['distanceFromStart'];
            $fuelUsedToIsOptimal = $distanceToIsOptimal / $mpg;

            // Fuel left after reaching isOptimal
            $fuelLeftAfterIsOptimal = max(0, $fuelLeft - $fuelUsedToIsOptimal);

            // Calculate fuel needed from isOptimal to end of trip
            if ($endLat && $endLng) {
                $distanceFromIsOptimalToEnd = $this->calculateDistance(
                    $cheapestStation['ftpLat'], $cheapestStation['ftpLng'],
                    $endLat, $endLng
                );
                $fuelNeededToEnd = $distanceFromIsOptimalToEnd / $mpg;

                // If fuel left is not enough, buy fuel at isOptimal
                if ($fuelLeftAfterIsOptimal < $fuelNeededToEnd) {
                    $gallonsToBuyIsOptimal = $fuelNeededToEnd - $fuelLeftAfterIsOptimal;

                    // Update isOptimal in the collection
                    $fuelStations = $fuelStations->map(function ($station) use ($cheapestStation, $gallonsToBuyIsOptimal) {
                        if ($station['fuel_station_name'] === $cheapestStation['fuel_station_name']) {
                            $station['gallons_to_buy'] = ($station['gallons_to_buy'] ?? 0) + $gallonsToBuyIsOptimal;
                        }
                        return $station;
                    });

                    // Update fuel left after refueling at isOptimal
                    $fuelLeftAfterIsOptimal += $gallonsToBuyIsOptimal;
                }
            }
        }

        if ($firstCheapestInRange && $secondCheapestInRange && !$midOptimal) {
            // Calculate fuel used to reach firstOptimal
            $distanceToFirstOptimal = $firstCheapestInRange['distanceFromStart'];
            $fuelUsedToFirstOptimal = $distanceToFirstOptimal / $mpg;

            // Fuel left after reaching firstOptimal
            $fuelLeftAfterFirstOptimal = max(0, $fuelLeft - $fuelUsedToFirstOptimal);

            // Calculate fuel needed from firstOptimal to secondOptimal
            $distanceBetweenFirstAndSecond = $secondCheapestInRange['distanceFromStart'] - $firstCheapestInRange['distanceFromStart'];
            $fuelNeededForSecond = $distanceBetweenFirstAndSecond / $mpg;

            // If fuel left is not enough, buy fuel at firstOptimal
            if ($fuelLeftAfterFirstOptimal < $fuelNeededForSecond) {
                $gallonsToBuyFirst = $fuelNeededForSecond - $fuelLeftAfterFirstOptimal;

                // Update fuel stations in the original collection
                $fuelStations = $fuelStations->map(function ($station) use ($firstCheapestInRange, $gallonsToBuyFirst) {
                    if ($station['fuel_station_name'] === $firstCheapestInRange['fuel_station_name']) {
                        $station['gallons_to_buy'] = $gallonsToBuyFirst;
                    }
                    return $station;
                });

                // Update fuel left after refueling at firstOptimal
                $fuelLeftAfterFirstOptimal += $gallonsToBuyFirst;
            }

            // Calculate fuel used to reach secondOptimal
            $fuelUsedToSecondOptimal = $fuelNeededForSecond;
            $fuelLeftAfterSecondOptimal = max(0, $fuelLeftAfterFirstOptimal - $fuelUsedToSecondOptimal);

            // Calculate fuel needed from secondOptimal to isOptimal
            if ($secondCheapestInRange && $cheapestStation) {
                $distanceBetweenSecondAndIsOptimal = $cheapestStation['distanceFromStart'] - $secondCheapestInRange['distanceFromStart'];
                $fuelNeededForIsOptimal = $distanceBetweenSecondAndIsOptimal / $mpg;

                // If fuel left is not enough, buy fuel at secondOptimal
                if ($fuelLeftAfterSecondOptimal < $fuelNeededForIsOptimal) {
                    $gallonsToBuySecond = $fuelNeededForIsOptimal - $fuelLeftAfterSecondOptimal;

                    // Update secondOptimal in the collection
                    $fuelStations = $fuelStations->map(function ($station) use ($secondCheapestInRange, $gallonsToBuySecond) {
                        if ($station['fuel_station_name'] === $secondCheapestInRange['fuel_station_name']) {
                            $station['gallons_to_buy'] = $gallonsToBuySecond;
                        }
                        return $station;
                    });

                    // Update fuel left after refueling at secondOptimal
                    $fuelLeftAfterSecondOptimal += $gallonsToBuySecond;
                }
            }
        }
        if ($firstCheapestInRange && $cheapestStation && !$midOptimal && !$secondCheapestInRange) {
            // Calculate fuel used to reach firstOptimal
            $distanceToFirstOptimal = $firstCheapestInRange['distanceFromStart'];
            $fuelUsedToFirstOptimal = $distanceToFirstOptimal / $mpg;

            // Fuel left after reaching firstOptimal
            $fuelLeftAfterFirstOptimal = max(0, $fuelLeft - $fuelUsedToFirstOptimal);

            // Calculate fuel needed from firstOptimal to isOptimal
            $distanceBetweenFirstAndIsOptimal = $cheapestStation['distanceFromStart'] - $firstCheapestInRange['distanceFromStart'];
            $fuelNeededForIsOptimal = $distanceBetweenFirstAndIsOptimal / $mpg;

            // If fuel left is not enough, buy fuel at firstOptimal
            if ($fuelLeftAfterFirstOptimal < $fuelNeededForIsOptimal) {
                $gallonsToBuyFirst = $fuelNeededForIsOptimal - $fuelLeftAfterFirstOptimal;

                // Update fuel stations in the original collection
                $fuelStations = $fuelStations->map(function ($station) use ($firstCheapestInRange, $gallonsToBuyFirst) {
                    if ($station['fuel_station_name'] === $firstCheapestInRange['fuel_station_name']) {
                        $station['gallons_to_buy'] = $gallonsToBuyFirst;
                    }
                    return $station;
                });

                // Update fuel left after refueling at firstOptimal
                $fuelLeftAfterFirstOptimal += $gallonsToBuyFirst;
            }

            // Calculate fuel used to reach isOptimal
            $fuelUsedToIsOptimal = $fuelNeededForIsOptimal;
            $fuelLeftAfterIsOptimal = max(0, $fuelLeftAfterFirstOptimal - $fuelUsedToIsOptimal);

            // Calculate fuel needed from isOptimal to end of trip
            if ($endLat && $endLng) {
                $distanceFromIsOptimalToEnd = $this->calculateDistance(
                    $cheapestStation['ftpLat'], $cheapestStation['ftpLng'],
                    $endLat, $endLng
                );
                $fuelNeededToEnd = $distanceFromIsOptimalToEnd / $mpg;

                // If fuel left is not enough, buy fuel at isOptimal
                if ($fuelLeftAfterIsOptimal < $fuelNeededToEnd) {
                    $gallonsToBuyIsOptimal = $fuelNeededToEnd - $fuelLeftAfterIsOptimal;

                    // Update isOptimal in the collection
                    $fuelStations = $fuelStations->map(function ($station) use ($cheapestStation, $gallonsToBuyIsOptimal) {
                        if ($station['fuel_station_name'] === $cheapestStation['fuel_station_name']) {
                            $station['gallons_to_buy'] = ($station['gallons_to_buy'] ?? 0) + $gallonsToBuyIsOptimal;
                        }
                        return $station;
                    });

                    // Update fuel left after refueling at isOptimal
                    $fuelLeftAfterIsOptimal += $gallonsToBuyIsOptimal;
                }
            }
        }

        // ✅ Now handle `isOptimal` and `secondOptimal`
        if ($fuelStations) {
            $fuelStations = $fuelStations->map(function ($station) use ($mpg, $fuelLeft, $endLat, $endLng, $fuelStations) {
                // Ensure keys exist before accessing them
                $isOptimal = $station['isOptimal'] ?? false;
                $secondOptimal = $station['secondOptimal'] ?? false;

                // ✅ Case 1: If the same station is both `isOptimal` and `secondOptimal`
                if ($isOptimal && $secondOptimal) {
                    $distanceFromIsOptimalToEnd = $this->calculateDistance(
                        $station['ftpLat'], $station['ftpLng'],
                        $endLat, $endLng
                    );

                    $fuelUsedToReachIsOptimal = ($station['distanceFromStart'] ?? 0) / $mpg;
                    $fuelLeftAtIsOptimal = max(0, $fuelLeft - $fuelUsedToReachIsOptimal);

                    $fuelNeededToEnd = $distanceFromIsOptimalToEnd / $mpg;
                    $gallonsToBuy = max(0, $fuelNeededToEnd - $fuelLeftAtIsOptimal);

                    $station['gallons_to_buy'] = $gallonsToBuy;
                }

                // ✅ Case 2: `secondOptimal` is true but it is NOT `isOptimal`
                elseif ($secondOptimal) {
                    // Find the next `isOptimal` station after this `secondOptimal`
                    $nextIsOptimal = collect($fuelStations)->first(function ($s) use ($station) {
                        return ($s['isOptimal'] ?? false) && ($s['distanceFromStart'] ?? 0) > ($station['distanceFromStart'] ?? 0);
                    });

                    if ($nextIsOptimal) {
                        // Distance & fuel needed to reach `isOptimal`
                        $distanceToIsOptimal = ($nextIsOptimal['distanceFromStart'] ?? 0) - ($station['distanceFromStart'] ?? 0);
                        $fuelNeededToIsOptimal = $distanceToIsOptimal / $mpg;

                        // Fuel left at `secondOptimal`
                        $fuelUsedToSecondOptimal = ($station['distanceFromStart'] ?? 0) / $mpg;
                        $fuelLeftAtSecondOptimal = max(0, $fuelLeft - $fuelUsedToSecondOptimal);

                        // If fuel is not enough, calculate gallons to buy
                        $gallonsToBuyAtSecondOptimal = max(0, $fuelNeededToIsOptimal - $fuelLeftAtSecondOptimal);

                        // ✅ Update `secondOptimal` station
                        $station['gallons_to_buy'] = $gallonsToBuyAtSecondOptimal;

                        // Calculate fuel needed from `isOptimal` to end location
                        $distanceFromIsOptimalToEnd = $this->calculateDistance(
                            $nextIsOptimal['ftpLat'], $nextIsOptimal['ftpLng'],
                            $endLat, $endLng
                        );

                        $fuelNeededToEnd = $distanceFromIsOptimalToEnd / $mpg;
                        $fuelLeftAtIsOptimal = max(0, $fuelLeftAtSecondOptimal - $fuelNeededToIsOptimal);
                        $gallonsToBuyAtIsOptimal = max(0, $fuelNeededToEnd - $fuelLeftAtIsOptimal);

                        // ✅ Update `isOptimal` station in the collection
                        $fuelStations = $fuelStations->map(function ($s) use ($nextIsOptimal, $gallonsToBuyAtIsOptimal) {
                            if ($s['fuel_station_name'] === $nextIsOptimal['fuel_station_name']) {
                                $s['gallons_to_buy'] = $gallonsToBuyAtIsOptimal;
                            }
                            return $s;
                        });
                    }
                }

                return $station;
            });
        }

        $fuelStations = $fuelStations->map(function ($station) use ($start,$polyline) {
            if (!isset($station['distanceFromStart'])) {
                $station['distanceFromStart'] = $this->getDistance($start, $station,$polyline);
            }
            return $station;
        });

        $mutableData['data']['fuelStations'] = $fuelStations->values()->all();

        //$distances = $this->optimizedFuelStationsWithDistance($mutableData);

        return $fuelStations->values()->all();
    }


public function optimizedFuelStationsWithDistance($tripData)
{
    if (!$tripData) return null;

    $fuelStations = $tripData['data']['fuelStations'] ?? [];

    $start = $tripData['data']['trip']['start'] ?? null;
    $encodedPolylines = $tripData['data']['polyline'] ?? [$tripData['data']['polyline'] ?? ''];

    $totalCoordinates = $tripData['data']['polyline'] ?? [];

    // Decode all polylines into coordinate points
    // foreach ($encodedPolylines as $encodedPolyline) {
    //     if ($encodedPolyline) {
    //         $decodedPoints = $this->decodePolyline1($encodedPolyline);
    //         $totalCoordinates = array_merge($totalCoordinates, $decodedPoints);
    //     }
    // }

    foreach ($fuelStations as $index => &$fuelStation) {
        if (!$start) continue;

        $fuelLat = $fuelStation['ftpLat'] ?? null;
        $fuelLng = $fuelStation['ftpLng'] ?? null;

        if (!$fuelLat || !$fuelLng) continue;

        $fuelStationCoordinate = ['lat' => (float) $fuelLat, 'lng' => (float) $fuelLng];

        // Find the nearest polyline coordinate within 10 miles
        $nearestCoordinate = null;
        $nearestDistance = PHP_FLOAT_MAX;

        foreach ($totalCoordinates as $coordinate) {
            $distance = $this->calculateDistance1($coordinate, $fuelStationCoordinate);

            if ($distance <= 10 && $distance < $nearestDistance) {
                $nearestDistance = $distance;
                $nearestCoordinate = $coordinate;
            }
        }

        if ($nearestCoordinate) {
            // Calculate distance from start to nearest polyline point
            $totalDistance = 0;

            for ($i = 0; $i < count($totalCoordinates) - 1; $i++) {
                $distance = $this->calculateDistance1($totalCoordinates[$i], $totalCoordinates[$i + 1]);
                $totalDistance += $distance;

                if ($totalCoordinates[$i + 1]['lat'] == $nearestCoordinate['lat'] &&
                    $totalCoordinates[$i + 1]['lng'] == $nearestCoordinate['lng']) {
                    break;
                }
            }

            // Total fuel station distance = start to nearest polyline coordinate + nearest coordinate to fuel station
            $fuelStationDistance = ($totalDistance / 1600) + $nearestDistance;

            if (isset($fuelStation['distanceFromStart'])) {
                if ($fuelStationDistance > $fuelStation['distanceFromStart']) {
                    $fuelStation['distanceFromStart'] += (int) ($fuelStationDistance - $fuelStation['distanceFromStart']);
                }
            }
        }
    }

    Log::info("Optimized fuel stations: ", $fuelStations);
    return $fuelStations;
}

/**
 * Decode a Google Maps encoded polyline into an array of coordinates
 *
 * @param string $encoded
 * @return array
 */
private function decodePolyline1(string $encoded): array
{
    $points = [];
    $index = 0;
    $len = strlen($encoded);
    $lat = 0;
    $lng = 0;

    while ($index < $len) {
        $shift = 0;
        $result = 0;

        do {
            $b = ord($encoded[$index++]) - 63;
            $result |= ($b & 0x1f) << $shift;
            $shift += 5;
        } while ($b >= 0x20);

        $dlat = (($result & 1) ? ~($result >> 1) : ($result >> 1));
        $lat += $dlat;

        $shift = 0;
        $result = 0;

        do {
            $b = ord($encoded[$index++]) - 63;
            $result |= ($b & 0x1f) << $shift;
            $shift += 5;
        } while ($b >= 0x20);

        $dlng = (($result & 1) ? ~($result >> 1) : ($result >> 1));
        $lng += $dlng;

        $points[] = ['lat' => $lat / 1E5, 'lng' => $lng / 1E5];
    }

    return $points;
}

/**
 * Calculate distance between two coordinates using Haversine formula
 *
 * @param array $coord1 ['lat' => x, 'lng' => y]
 * @param array $coord2 ['lat' => x, 'lng' => y]
 * @return float Distance in miles
 */
private function calculateDistance1(array $coord1, array $coord2): float
{
    $earthRadius = 3958.8; // Radius in miles
    $lat1 = deg2rad($coord1['lat']);
    $lng1 = deg2rad($coord1['lng']);
    $lat2 = deg2rad($coord2['lat']);
    $lng2 = deg2rad($coord2['lng']);

    $latDelta = $lat2 - $lat1;
    $lngDelta = $lng2 - $lng1;

    $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
        cos($lat1) * cos($lat2) * pow(sin($lngDelta / 2), 2)));

    return $earthRadius * $angle;
}
public function getDistance($start, $fuelStation, $polyline)
{

    $userLocation = ['lat' => $start['latitude'], 'lng' => $start['longitude']];
    $stationLocation = ['lat' => $fuelStation['ftpLat'], 'lng' => $fuelStation['ftpLng']];

    return $this->calculatePolylineDistance($userLocation, $stationLocation, $polyline);
}

private function haversineDistance1($p1, $p2)
{

    $earthRadius = 3958.8; // Radius in meters
    $lat1 = deg2rad($p1['lat']);
    $lon1 = deg2rad($p1['lng']);
    $lat2 = deg2rad($p2['lat']);
    $lon2 = deg2rad($p2['lng']);

    $dLat = $lat2 - $lat1;
    $dLon = $lon2 - $lon1;

    $a = sin($dLat / 2) ** 2 + cos($lat1) * cos($lat2) * sin($dLon / 2) ** 2;
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

    return $earthRadius * $c; // Distance in meters
}

private function findNearestPoint($location, $polyline)
{
    $minDistance = PHP_FLOAT_MAX;
    $nearestIndex = 0;

    foreach ($polyline as $index => $point) {
        $distance = $this->haversineDistance1($location, $point);
        if ($distance < $minDistance) {
            $minDistance = $distance;
            $nearestIndex = $index;
        }
    }
    return $nearestIndex;
}

private function calculatePolylineDistance($userLocation, $destination, $polyline)
{
    $startIndex = $this->findNearestPoint($userLocation, $polyline);
    $endIndex = $this->findNearestPoint($destination, $polyline);

    $totalDistance = 0.0;
    for ($i = $startIndex; $i < $endIndex; $i++) {
        $totalDistance += $this->haversineDistance1($polyline[$i], $polyline[$i + 1]);
    }
    return $totalDistance;
}

function calculateDistance($lat1, $lng1, $lat2, $lng2) {
    $earthRadius = 3958.8; // Radius of Earth in miles

    $lat1 = deg2rad($lat1);
    $lng1 = deg2rad($lng1);
    $lat2 = deg2rad($lat2);
    $lng2 = deg2rad($lng2);

    $dLat = $lat2 - $lat1;
    $dLng = $lng2 - $lng1;

    $a = sin($dLat / 2) * sin($dLat / 2) +
        cos($lat1) * cos($lat2) *
        sin($dLng / 2) * sin($dLng / 2);

    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

    return $earthRadius * $c; // Distance in miles
}
}
