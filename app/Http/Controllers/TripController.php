<?php

namespace App\Http\Controllers;

use App\Models\CompanyDriver;
use App\Models\DriverVehicle;
use App\Models\FcmToken;
use App\Models\FuelStation;
use App\Models\Notification as ModelsNotification;
use App\Models\Trip;
use App\Models\Tripstop;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use App\Models\User;
use App\Models\Vehicle;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class TripController extends Controller
{


    public function store(Request $request)
    {

        // Step 1: Validate request data
        $validatedData = $request->validate([
            'user_id'   => 'required|exists:users,id',
            'start_lat' => 'required|numeric',
            'start_lng' => 'required|numeric',
            'end_lat'   => 'required|numeric',
            'end_lng'   => 'required|numeric',
        ]);

        //Step 2: Check if a trip already exists for this user
        $findTrip = Trip::where('user_id', $validatedData['user_id'])->where('status', 'active')->first();
        if ($findTrip) {
            return response()->json(['status' => 422, 'message' => 'Trip already exists for this user', 'data' => (object)[]],422);
        }
        $validatedData['status']='active';


        $trip = Trip::create($validatedData);

        // Step 4: Find and save gas stations along the route
        $gasStations = $this->findGasStations($validatedData['start_lat'], $validatedData['start_lng'], $validatedData['end_lat'], $validatedData['end_lng']);

        $ftpData = $this->loadAndParseFTPData();

       if($gasStations){
        foreach ($gasStations as $station) {
            $lat = number_format((float) $station['latitude'], 4);
            $lng = number_format((float) $station['longitude'], 4);

            //if (isset($ftpData[$lat][$lng])) {
                $price = $ftpData[$lat][$lng]['price'] ?? 0.00;

                // Save the valid gas station
                $fuelStation = FuelStation::create([
                    'user_id'   => $validatedData['user_id'],
                    'trip_id'   => $trip->id,
                    'name'      => $station['name'],
                    'latitude'  => $station['latitude'],
                    'longitude' => $station['longitude'],
                    'price'     => $price,
                ]);
                $storedStations[] = $fuelStation;
            //}

        }
       }


        return response()->json(['status' => 200, 'message' => 'Trip and gas stations stored successfully','data'    => [
            'trip'          => $trip,
            'gas_stations'  => $storedStations ?? [],
        ]]);
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
        $bearing = $request->bearing;
        if($bearing <0){
            $userBearing =0;
        }else{
            $userBearing = $bearing;
        }
        // Replace with your Google API key
        $apiKey = 'AIzaSyA0HjmGzP9rrqNBbpH7B0zwN9Gx9MC4w8w';
        $stops = Tripstop::where('trip_id', $request->trip_id)->get();
        if ($stops->isNotEmpty()) {
            $waypoints = $stops->map(fn($stop) => "{$stop->stop_lat},{$stop->stop_lng}")->implode('|');
        }
        if($bearing <0){
            $url = "https://maps.googleapis.com/maps/api/directions/json?origin={$updatedStartLat},{$updatedStartLng}&destination={$updatedEndLat},{$updatedEndLng}&key={$apiKey}";
        }else{
            $url = "https://maps.googleapis.com/maps/api/directions/json?origin=heading={$bearing}:{$updatedStartLat},{$updatedStartLng}&destination={$updatedEndLat},{$updatedEndLng}&key={$apiKey}";

        }
       if (isset($waypoints)) {
            $url .= "&waypoints=optimize:true|{$waypoints}";
        }
        //\Log::info($url);
        // Fetch data from Google Maps API
        $response = Http::get($url);

        if ($response->successful()) {
            $data = $response->json();
            if($data['routes'] && $data['routes'][0]){
                if (!empty($data['routes'][0]['legs'])) {
                    $steps = $data['routes'][0]['legs'][0]['steps'];
                    $decodedCoordinates = [];
                    $stepSize =150; // Sample every 10th point

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
                        'polyline' => json_encode($polylinePoints),
                        'polyline_encoded' => $encodedPolyline,
                        'distance' => $formattedDistance,
                        'duration'=> $formattedDuration,
                    ]);
                    FuelStation::where('trip_id', $trip->id)->delete();
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
                            'user_id' => $trip->user_id,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                    if(!empty($fuelStations)){
                        FuelStation::insert($fuelStations);
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

                    unset($trip->polyline);
                    unset($trip->polyline_encoded);
                    // Create a separate key for the polyline
                    $responseData = [
                        'trip_id' => (int)$request->trip_id,
                        'trip' => $trip,
                        'fuel_stations' => $result, // Fuel stations with optimal station marked

                        'polyline_paths' => $polylinePoints ?? [],
                        'stops' => $stops,
                        'vehicle' => $vehicle
                    ];

                    return response()->json([
                        'status' => 200,
                        'message' => 'Fuel stations fetched successfully.',
                        'data' => $responseData,
                    ],200);
                }else{
                    return response()->json([
                        'status' => 404,
                        'message' => 'No route found.',
                        'data'=>(object)[]
                    ], 404);
                }


            }else{
                return response()->json([
                    'status' => 500,
                    'message' => 'Failed to fetch data from Google Maps API.',
                    'data'=>(object)[]
                ],500);
            }

        }


    }
    private function getBestForwardRoute($routes, $currentLocation, $userBearing)
{
    $bestRoute = null;
    $bestBearingDifference = 360; // Initialize with maximum possible difference

    foreach ($routes as $route) {
        $legs = $route['legs'];
        $firstStep = $legs[0]['steps'][0];
        $startLocation = $firstStep['start_location'];
        $endLocation = $firstStep['end_location'];

        // Calculate the bearing of the first segment of the route
        $routeBearing = $this->bearingBetweenLocations(
            "{$startLocation['lat']},{$startLocation['lng']}",
            "{$endLocation['lat']},{$endLocation['lng']}"
        );

        // Calculate the difference between the user's bearing and the route's bearing
        $bearingDifference = abs($userBearing - $routeBearing);

        // Normalize the bearing difference to be within 0-180 degrees
        if ($bearingDifference > 180) {
            $bearingDifference = 360 - $bearingDifference;
        }

        // If this route has a smaller bearing difference, it's a better match
        if ($bearingDifference < $bestBearingDifference) {
            $bestBearingDifference = $bearingDifference;
            $bestRoute = $route;
        }
    }

    return $bestRoute;
}

    private function getFirstValidTurn($steps, $currentLocation, $userBearing)
{
    $closestTurn = null;
    $closestDistance = PHP_INT_MAX;

    foreach ($steps as $step) {
        $turnLocation = "{$step['start_location']['lat']},{$step['start_location']['lng']}";
        if ($this->isTurnAhead($currentLocation, $turnLocation, $userBearing)) {
            $distanceToTurn = $this->distanceBetween($currentLocation, $turnLocation);
            if ($distanceToTurn < $closestDistance) {
                $closestTurn = $turnLocation;
                $closestDistance = $distanceToTurn;
            }
        }
    }

    return $closestTurn ?? "{$steps[0]['start_location']['lat']},{$steps[0]['start_location']['lng']}";
}

    private function isTurnAhead($currentLocation, $turnLocation, $userBearing)
{
    $bearingToTurn = $this->bearingBetweenLocations($currentLocation, $turnLocation);
    $bearingDifference = abs($bearingToTurn - $userBearing);

    // Normalize the bearing difference to be within 0-180 degrees
    if ($bearingDifference > 180) {
        $bearingDifference = 360 - $bearingDifference;
    }

    // Consider the turn "ahead" if the bearing difference is less than 45°
    return $bearingDifference < 45;
}

    private function distanceBetween($point1, $point2)
    {
        list($lat1, $lng1) = explode(',', $point1);
        list($lat2, $lng2) = explode(',', $point2);

        $earthRadius = 6371; // Radius of Earth in km
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

        return $earthRadius * $c;
    }

    private function bearingBetweenLocations($point1, $point2)
    {

        list($lat1, $lng1) = explode(',', $point1);
        list($lat2, $lng2) = explode(',', $point2);

        $lat1 = deg2rad($lat1);
        $lng1 = deg2rad($lng1);
        $lat2 = deg2rad($lat2);
        $lng2 = deg2rad($lng2);

        $dLng = $lng2 - $lng1;
        $y = sin($dLng) * cos($lat2);
        $x = cos($lat1) * sin($lat2) - sin($lat1) * cos($lat2) * cos($dLng);
        $bearing = rad2deg(atan2($y, $x));

        return ($bearing + 360) % 360;
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
    private function findGasStations($startLat, $startLng, $endLat, $endLng)
    {
        $client = new Client();
        $radius = 5000; // Search radius of 1000 meters
        $distance = $this->calculateDistance($startLat, $startLng, $endLat, $endLng);

        // Calculate the number of API requests dynamically based on route length
        $maxRequests = max(5, (int)($distance / 5)); // 1 request per 5 km, with a minimum of 5 requests
        $epsilon = 0.001; // Tolerance for RDP simplification (smaller values retain more points)

        $gasStations = [];

        // Generate initial route points (more points for better accuracy)
        $routePoints = $this->getRoutePoints($startLat, $startLng, $endLat, $endLng, 100); // Increase number of points

        // Simplify route points using RDP (optional, but helps to reduce unnecessary points)
        $simplifiedPoints = $this->ramerDouglasPeucker($routePoints, $epsilon);

        // Limit checkpoints based on calculated maxRequests
        $checkpoints = array_slice($simplifiedPoints, 0, $maxRequests);

        // Google Places API URL
        $url = 'https://maps.googleapis.com/maps/api/place/nearbysearch/json';

        // Loop through each checkpoint and find gas stations
        foreach ($checkpoints  as $point) {
            $response = $client->get($url, [
                'query' => [
                    'location' => "{$point['lat']},{$point['lng']}",
                    'radius' => $radius,
                    'type' => 'gas_station',
                    'key' => 'AIzaSyBtQuABE7uPsvBnnkXtCNMt9BpG9hjeDIg'
                ]
            ]);

            $results = json_decode($response->getBody(), true)['results'];
            foreach ($results as $result) {
                $gasStations[] = [
                    'name' => $result['name'],
                    'latitude' => preg_replace('/^(\d+\.\d{4}).*$/', '$1', number_format($result['geometry']['location']['lat'], 10, '.', '')),
                    'longitude' => preg_replace('/^(\-?\d+\.\d{4}).*$/', '$1', number_format($result['geometry']['location']['lng'], 10, '.', '')),
                ];
            }
        }

        // Remove duplicate stations (same station with the same latitude/longitude)
        $uniqueGasStations = collect($gasStations)->unique(function ($station) {
            return $station['name'] . $station['latitude'] . $station['longitude'];
        })->values()->all();

        return $uniqueGasStations;
    }

    private function getRoutePoints($startLat, $startLng, $endLat, $endLng, $numPoints)
    {
        $points = [];
        for ($i = 0; $i <= $numPoints; $i++) {
            $fraction = $i / $numPoints;
            $lat = $startLat + $fraction * ($endLat - $startLat);
            $lng = $startLng + $fraction * ($endLng - $startLng);
            $points[] = ['lat' => $lat, 'lng' => $lng];
        }
        return $points;
    }

    private function ramerDouglasPeucker($points, $epsilon)
    {
        if (count($points) < 3) {
            return $points;
        }

        // Find the point with the maximum distance
        $dmax = 0;
        $index = 0;
        $end = count($points) - 1;

        for ($i = 1; $i < $end; $i++) {
            $d = $this->perpendicularDistance($points[$i], $points[0], $points[$end]);
            if ($d > $dmax) {
                $index = $i;
                $dmax = $d;
            }
        }

        // If max distance is greater than epsilon, recursively simplify
        if ($dmax > $epsilon) {
            $recResults1 = $this->ramerDouglasPeucker(array_slice($points, 0, $index + 1), $epsilon);
            $recResults2 = $this->ramerDouglasPeucker(array_slice($points, $index, $end - $index + 1), $epsilon);

            return array_merge(array_slice($recResults1, 0, -1), $recResults2);
        } else {
            return [$points[0], $points[$end]];
        }
    }

    private function perpendicularDistance($point, $lineStart, $lineEnd)
    {
        $x0 = $point['lat'];
        $y0 = $point['lng'];
        $x1 = $lineStart['lat'];
        $y1 = $lineStart['lng'];
        $x2 = $lineEnd['lat'];
        $y2 = $lineEnd['lng'];

        $numerator = abs(($y2 - $y1) * $x0 - ($x2 - $x1) * $y0 + $x2 * $y1 - $y2 * $x1);
        $denominator = sqrt(pow($y2 - $y1, 2) + pow($x2 - $x1, 2));

        return $denominator == 0 ? 0 : $numerator / $denominator;
    }

    private function calculateDistance($startLat, $startLng, $endLat, $endLng)
    {
        $earthRadius = 6371; // Radius in kilometers

        $dLat = deg2rad($endLat - $startLat);
        $dLng = deg2rad($endLng - $startLng);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($startLat)) * cos(deg2rad($endLat)) *
            sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c; // Distance in kilometers
    }


    public function getTrip($user_id)
    {
        $trip = Trip::where('user_id', $user_id)->where('status', 'active')->orderBy('created_at', 'desc')->first();

        if (!$trip) {
            return response()->json(['status' =>404, 'message' => 'No trip found for this user', 'data' => (object)[]], 404);
        }
        return response()->json([
            'status' => 200,
            'message' => 'Trip retrieved successfully',
            'data' => [
                'id' => $trip->id,
                'start_lat' => $trip->start_lat,
                'start_lng' => $trip->start_lng,
                'end_lat' => $trip->end_lat,
                'end_lng' => $trip->end_lng,
                'stops' => $trip->stops->map(function ($stop) {
                    return [
                        'lat' => $stop->stop_lat,
                        'lng' => $stop->stop_lng,
                    ];
                }),
            ]
        ], 200);
        // return response()->json(['status' => 200, 'message' => 'Trip retrieved successfully', 'data' => $trip]);
    }
    public function deleteTrip(Request $request)
    {
        $trip = Trip::whereId($request->trip_id)->first();
        if (!$trip) {
            return response()->json(['status' => 404, 'message' => 'No trip found for this user', 'data' => (object)[]], 404);
        }
        $trip->delete();
        return response()->json(['status' => 200, 'message' => 'Trip deleted successfully', 'data' => (object)[]], 200);
    }
    public function completeTrip(Request $request){
        $trip = Trip::whereId($request->trip_id)->first();
        if (!$trip) {
            return response()->json(['status' => 404, 'message' => 'No trip found', 'data' => (object)[]], 404);
        }

        $trip->status = $request->status;
        $trip->save();
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
                    if($request->status == "completed"){
                        $message = CloudMessage::new()
                        ->withNotification(Notification::create('Trip Completed', $findDriver->name . 'has completed a trip.'))
                        ->withData([
                            'trip_id' => (string) $trip->id,  // Include trip ID for reference
                            'driver_name' => $findDriver->name, // Driver's name
                            'sound' => 'default',  // This triggers the sound
                        ]);
                    }else{
                        $message = CloudMessage::new()
                        ->withNotification(Notification::create('Trip Cancelled', $findDriver->name . 'has cancelled a trip.'))
                        ->withData([
                            'trip_id' => (string) $trip->id,  // Include trip ID for reference
                            'driver_name' => $findDriver->name, // Driver's name
                            'sound' => 'default',  // This triggers the sound
                        ]);
                    }
                    // Create the notification payload


                    // Send notification to all FCM tokens of the company
                    $response = $messaging->sendMulticast($message, $companyFcmTokens);
                }
                if (!empty($driverFcm)) {
                    $factory = (new Factory)->withServiceAccount(storage_path('app/zeroifta.json'));
                    $messaging = $factory->createMessaging();
                    if($request->status == "completed"){
                        $message = CloudMessage::new()
                            ->withNotification(Notification::create('Trip Completed', 'Trip completed successfully'))
                            ->withData([
                                'sound' => 'default', // This triggers the sound
                            ]);
                            $response = $messaging->sendMulticast($message, $driverFcm);
                            ModelsNotification::create([
                                'user_id' => $findCompany->company_id,
                                'title' => 'Trip Completed',
                                'body' => $findDriver->name . ' has completed a trip.',
                            ]);
                    }else{
                        $message = CloudMessage::new()
                            ->withNotification(Notification::create('Trip Cancelled', 'Trip cancelled successfully'))
                            ->withData([
                                'sound' => 'default', // This triggers the sound
                            ]);
                            $response = $messaging->sendMulticast($message, $driverFcm);
                            ModelsNotification::create([
                                'user_id' => $findCompany->company_id,
                                'title' => 'Trip Cancelled',
                                'body' => $findDriver->name . ' has cancelled a trip.',
                            ]);
                    }

                }
            }
        }
        return response()->json(['status' => 200, 'message' => 'Trip status updated successfully', 'data' => (object)[]], 200);
    }
    function fetchFileDataAndMatchCoordinates($latitude, $longitude)
    {
        $filePath = 'EFSLLCpricing'; // The FTP file name

        // Connect to the FTP disk
        $ftpDisk = Storage::disk('ftp');

        // Check if the file exists
        if (!$ftpDisk->exists($filePath)) {
            return "File not found.";
        }

        // Get the file content
        $fileContent = $ftpDisk->get($filePath);

        // Convert content to an array by splitting lines
        $rows = explode("\n", trim($fileContent));

        // Loop through the lines to find matching coordinates
        foreach ($rows as $line) {
            $row = explode('|', $line); // Split by the pipe character

            // Check if the line contains latitude and longitude data
            // Assuming latitude is at index 8 and longitude is at index 9
            if (isset($row[8], $row[9]) && trim($row[8]) == $latitude && trim($row[9]) == $longitude) {
                return $row; // Return the matching row
            }
        }

        return "No matching coordinates found.";
    }
    public function checkActiveTrip(Request $request)
    {
        $validatedData = $request->validate([
            'trip_id' => 'required|exists:trips,id',
        ]);

        $trip = Trip::find($validatedData['trip_id']);
        $vehicle = Vehicle::where('id',$trip->vehicle_id)->first();
        if (!$trip) {
            return response()->json(['success' => false, 'message' => 'Trip not found'], 404);
        }
        $findVehicle = Vehicle::whereId($trip->vehicle_id)->first();
        return response()->json([
            'success' => true,
            'trip' => [
                'trip_id' => $trip->id,
                'start_lat' => $trip->updated_start_lat,
                'start_lng' => $trip->updated_start_lng,
                'end_lat' => $trip->updated_end_lat,
                'end_lng' => $trip->updated_end_lng,
                'truck_mpg' => $findVehicle->mpg ?? null,
                'fuel_tank_capacity' => $findVehicle->fuel_tank_capacity ?? null,
                'fuel_left' =>$findVehicle->fuel_left ?? 0,
                'reserve_fuel'=>$findVehicle->reserve_fuel ?? 0,
                'polyline_points'=>json_decode($trip->polyline,true),
                'encoded_polyline'=>$trip->polyline_encoded
            ]
        ], 200);
    }

    public function getActiveTrip(Request $request)
    {
        $trip = Trip::select('id', 'user_id', 'start_lat', 'start_lng', 'end_lat', 'end_lng', 'updated_start_lat', 'updated_start_lng', 'updated_end_lat', 'updated_end_lng', 'polyline', 'polyline_encoded','distance','duration')
        ->where('id', $request->trip_id)
        ->first();
        $startLat = $trip->start_lat;
        $startLng = $trip->start_lng;
        $endLat = $trip->end_lat;
        $endLng = $trip->end_lng;
        $updatedStartLat = $trip->updated_start_lat;
        $updatedStartLng = $trip->updated_start_lng;
        $updatedEndLat =$trip->updated_end_lat;
        $updatedEndLng = $trip->updated_end_lng;
        $polylinePoints = json_decode($trip->polyline, true);
        $decodedCoordinates = [];
        $stepSize = 150; // Sample every 3rd point
        foreach ($polylinePoints as $points) {
            $decodedPoints = $this->decodePolyline($points);
            for ($i = 0; $i < count($decodedPoints); $i += $stepSize) {
                $decodedCoordinates[] = $decodedPoints[$i];
            }
        }

        $encodedPolyline = $trip->polyline_encoded;
        $decodedPolyline = $this->decodePolyline($encodedPolyline);
        $finalFilteredPolyline = array_filter($decodedPolyline, function ($coordinate) use ($updatedStartLat, $updatedStartLng, $updatedEndLat, $updatedEndLng) {
            if (!isset($coordinate['lat'], $coordinate['lng'])) {
                return false;
            }

            $distanceFromStart = $this->haversineDistanceFilter($updatedStartLat, $updatedStartLng, $coordinate['lat'], $coordinate['lng']);
            $distanceFromEnd = $this->haversineDistanceFilter($updatedEndLat, $updatedEndLng, $coordinate['lat'], $coordinate['lng']);

            return ($distanceFromStart > 9 && $distanceFromEnd > 9);
        });
        $finalFilteredPolyline = array_values($finalFilteredPolyline);
        $matchingRecords = $this->loadAndParseFTPData($finalFilteredPolyline);
        $currentTrip = Trip::where('id', $trip->id)->first();
        $vehicle_id = DriverVehicle::where('driver_id', $currentTrip->user_id)->first();
        if($vehicle_id){

            $findVehicle = Vehicle::select('id', 'mpg', 'fuel_left', 'reserve_fuel')
            ->where('id',  $vehicle_id->vehicle_id)
            ->first();
            $truckMpg = $findVehicle->mpg;
            $currentFuel = $findVehicle->fuel_left;
            $reserve_fuel = $findVehicle->reserve_fuel ?? 0;

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
            $insertData = [];
            FuelStation::where('trip_id', $trip->id)->delete();
            foreach ($result as  $value) {
                $fuelStations[] = [
                    'name' => $value['fuel_station_name'] ??'N/A',
                    'latitude' => $value['ftpLat'] ?? 0,
                    'longitude' => $value['ftpLng'] ?? 0,
                    'price' => $value['price'] ?? 0,
                    'lastprice' => $value['lastprice'] ?? 0,
                    'discount' => $value['discount'] ?? 0,
                    'ifta_tax' => $value['IFTA_tax'] ?? 0,
                    'is_optimal' => $value['isOptimal'] ?? false,
                    'address' => $value['address'] ?? 'N/A',
                    'gallons_to_buy' => $value['gallons_to_buy'] ?? 0,
                    'trip_id' => $trip->id,
                    'user_id' => $trip->user_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            FuelStation::insert($fuelStations);

        }else{
            return response()->json(['status'=>404,'message'=>'trip not found','data'=>(object)[]],404);
        }
        $stops = Tripstop::where('trip_id', $trip->id)->get();

        $driverVehicle = DriverVehicle::where('driver_id', $trip->user_id)->first();
        if($driverVehicle && $driverVehicle->vehicle_id != null){
            $vehicle = Vehicle::where('id', $driverVehicle->vehicle_id)->first();
            if($vehicle && $vehicle->vehicle_image != null){
                $vehicle->vehicle_image = url('/vehicles/' . $vehicle->vehicle_image);
            }
        }else{
            $vehicle = null;
        }

        unset($trip->vehicle_id);
        unset($trip->polyline);
        unset($trip->polyline_encoded);
        if($trip){
            $trip->distance = $trip->distance;
            $trip->duration = $trip->duration;
            $trip->user_id = (int)$trip->user_id;
            $response = [
                'trip_id' => $trip->id,
                'trip' => $trip,
                'fuel_stations' => $result,

                'polyline_paths'=>$polylinePoints,
                'stops' => $stops,
                'vehicle' => $vehicle
            ];
            return response()->json(['status'=>200,'message'=>'trip found','data'=>$response],200);
        }else{
            return response()->json(['status'=>404,'message'=>'trip not found','data'=>(object)[]],404);
        }
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
    public function tripDetail(Request $request)
    {
        $trip = Trip::where('id', $request->trip_id)->first();
        if($trip){
            $driverVehicle = DriverVehicle::where('driver_id', $trip->user_id)->pluck('vehicle_id')->first();
            $vehicle = Vehicle::where('id', $driverVehicle)->first();

            $pickupState = $this->getAddressFromCoordinates($trip->start_lat, $trip->start_lng);
            $dropoffState = $this->getAddressFromCoordinates($trip->end_lat, $trip->end_lng);
            $pickup = $this->getPickupFromCoordinates($trip->start_lat, $trip->start_lng);
            $dropoff = $this->getPickupFromCoordinates($trip->end_lat, $trip->end_lng);
            $trip->pickup = $pickup;
            $trip->dropoff = $dropoff;
            $trip->pickupState = $pickupState;
            $trip->dropoffState = $dropoffState;
            $trip->vehicle = $vehicle;
            if($trip->vehicle && $trip->vehicle->vehicle_image){
                $trip->vehicle->vehicle_image = url('/vehicles/' . $vehicle->vehicle_image);
            }
            $startLat = $trip->start_lat;
            $startLng = $trip->start_lng;
            $endLat = $trip->end_lat;
            $endLng = $trip->end_lng;
            $apiKey = 'AIzaSyA0HjmGzP9rrqNBbpH7B0zwN9Gx9MC4w8w';
        $url = "https://maps.googleapis.com/maps/api/directions/json?origin={$startLat},{$startLng}&destination={$endLat},{$endLng}&key={$apiKey}";
        $response = Http::get($url);
        if ($response->successful()) {
            $data = $response->json();
            $route = $data['routes'][0];

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
        }
            $trip->distance = $formattedDistance;
            $trip->duration = $formattedDuration;
            return response()->json(['status'=>200,'message'=>'trip found','data'=>$trip],200);
        }else{
            return response()->json(['status'=>404,'message'=>'trip not found','data'=>(object)[]],404);
        }
    }

    private function getAddressFromCoordinates($latitude, $longitude)
    {
        $apiKey = 'AIzaSyA0HjmGzP9rrqNBbpH7B0zwN9Gx9MC4w8w'; // Add your API key in .env
        $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng={$latitude},{$longitude}&key={$apiKey}";

        $response = file_get_contents($url);
        $response = json_decode($response, true);

        if (isset($response['results'][0]['address_components'])) {
            foreach ($response['results'][0]['address_components'] as $component) {
                if (in_array('administrative_area_level_1', $component['types'])) {
                    return $component['long_name']; // State name
                }
            }
        }

        return 'Address not found';
    }
    private function getPickupFromCoordinates($latitude, $longitude)
    {
        $apiKey = 'AIzaSyA0HjmGzP9rrqNBbpH7B0zwN9Gx9MC4w8w'; // Add your API key in .env
        $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng={$latitude},{$longitude}&key={$apiKey}";

        $response = file_get_contents($url);
        $response = json_decode($response, true);

        if (isset($response['results'][0]['formatted_address'])) {
            return $response['results'][0]['formatted_address'];
        }

        return 'Address not found';
    }
    public function storeStop(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'trip_id' => 'required|exists:trips,id',
            'stops' => 'required|array',
            'stops.*.stop_lat' => 'required|string',
            'stops.*.stop_lng' => 'required|string',
            'stops.*.stop_name' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first(),
                'data' => (object) [],
            ],422);
        }

        // Save stops
        $stopsData = array_map(function ($stop) use ($request) {
            return [
                'trip_id' => $request->trip_id,
                'stop_name' => $stop['stop_name'] ?? null,
                'stop_lat' => $stop['stop_lat'],
                'stop_lng' => $stop['stop_lng'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }, $request->stops);

        Tripstop::insert($stopsData);
        $trip = Trip::whereId($request->trip_id)->first();
        // $fuelStations = FuelStation::where('trip_id', $request->trip_id)->get()
        //     ->map(function ($station) {
        //         // Convert the station to an array, keeping all attributes
        //         $data = $station->toArray();

        //         // Add the new keys
        //         $data['ftp_lat'] = $data['latitude'];
        //         $data['ftp_lng'] = $data['longitude'];
        //         $data['fuel_station_name'] = $data['name'];
        //         $data['IFTA_tax'] = floatval(preg_replace('/[^0-9.-]/', '', $station->ifta_tax));
        //         $data['lastprice'] = floatval(preg_replace('/[^0-9.-]/', '', $station->lastprice));
        //         $data['price'] = floatval(preg_replace('/[^0-9.-]/', '', $station->price));
        //         $data['discount'] = $data['discount'] ? (double)$data['discount'] : 0;
        //         $data['gallons_to_buy'] = $data['gallons_to_buy'] ? (double)$data['gallons_to_buy'] :null;
        //         $data['is_optimal'] = $data['is_optimal'] ? (bool)$data['is_optimal'] : false;
        //         // Optionally remove the old keys if not needed
        //         unset($data['latitude'], $data['longitude'],$data['ifta_tax'],$data['name']);
        //     return $data;
        // });
        unset($trip->vehicle_id);
        $startLat = $trip->start_lat;
        $startLng = $trip->start_lng;
        $endLat = $trip->end_lat;
        $endLng = $trip->end_lng;
        $updatedStartLat = $trip->updated_start_lat;
        $updatedStartLng = $trip->updated_start_lng;
        $updatedEndLat =$trip->updated_end_lat;
        $updatedEndLng = $trip->updated_end_lng;
        $apiKey = 'AIzaSyA0HjmGzP9rrqNBbpH7B0zwN9Gx9MC4w8w';
        $stops = Tripstop::where('trip_id', $request->trip_id)->get();
        if ($stops->isNotEmpty()) {
            $waypoints = $stops->map(fn($stop) => "{$stop->stop_lat},{$stop->stop_lng}")->implode('|');
        }
        $url = "https://maps.googleapis.com/maps/api/directions/json?origin={$updatedStartLat},{$updatedStartLng}&destination={$updatedEndLat},{$updatedEndLng}&key={$apiKey}";
        if ($waypoints) {
            $url .= "&waypoints=optimize:true|{$waypoints}";
        }
        $response = Http::get($url);
        if ($response->successful()) {
            $data = $response->json();

            if($data['routes'] && $data['routes'][0]){
                if (!empty($data['routes'][0]['legs'])) {
                    $steps = $data['routes'][0]['legs'][0]['steps'];
                    $decodedCoordinates = [];
                    $stepSize = 150; // Sample every 10th point

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
                        foreach ($leg['steps'] as $step) {
                            $polylinePoints[] = $step['polyline']['points'] ?? null;
                        }
                    }

                    $polylinePoints = array_filter($polylinePoints);
                   // $completePolyline = implode('', $polylinePoints);
                }
                $route = $data['routes'][0] ?? null;
                if ($route) {
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
                    //$matchingRecords = $this->findMatchingRecords($finalFilteredPolyline, $ftpData);
                    $currentTrip = Trip::where('id', $trip->id)->first();
                    $vehicle_id = DriverVehicle::where('driver_id', $currentTrip->user_id)->first();

                    $findVehicle = Vehicle::where('id', $vehicle_id->vehicle_id)->first();
                    $truckMpg = $findVehicle->mpg;
                    $currentFuel = $findVehicle->fuel_left;

                    $reserve_fuel = $findVehicle->reserve_fuel ?? 0;

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
                    //$result = $this->findOptimalFuelStation($startLat, $startLng, $truckMpg, $currentFuel, $matchingRecords, $endLat, $endLng);
                    FuelStation::where('trip_id', $trip->id)->delete();
                    foreach ($result as  $value) {
                        $fuelStations[] = [
                            'name' => $value['fuel_station_name'] ??'N/A',
                            'latitude' => $value['ftpLat'] ?? 0,
                            'longitude' => $value['ftpLng'] ?? 0,
                            'price' => $value['price'] ?? 0,
                            'lastprice' => $value['lastprice'] ?? 0,
                            'discount' => $value['discount'] ?? 0,
                            'ifta_tax' => $value['IFTA_tax'] ?? 0,
                            'is_optimal' => $value['isOptimal'] ?? false,
                            'address' => $value['address'] ?? 'N/A',
                            'gallons_to_buy' => $value['gallons_to_buy'] ?? 0,
                            'trip_id' => $trip->id,
                            'user_id' => $trip->user_id,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                   }
                }
                FuelStation::insert($fuelStations);

            }else{
                return response()->json(['status'=>500,'message'=>'invalid coordinates','data'=>(object)[]],500);
            }
        }
        $trip->update([
            'polyline'=>json_encode($polylinePoints),
            'polyline_encoded'=>$encodedPolyline,
            'duration'=>$formattedDuration,
            'distance'=>$formattedDistance
        ]);
        $vehiclefind = DriverVehicle::where('driver_id', $trip->user_id)->pluck('vehicle_id')->first();
        if($vehiclefind){
            $vehicle = Vehicle::where('id', $vehiclefind)->first();
            if($vehicle && $vehicle->vehicle_image != null){
                $vehicle->vehicle_image = url('/vehicles/' . $vehicle->vehicle_image);
            }
        }else{
            $vehicle =null;

        }


        if($trip){
            $trip->distance = $formattedDistance;
            $trip->duration = $formattedDuration;
            $trip->user_id = (int)$trip->user_id;
            $stops = Tripstop::where('trip_id', $trip->id)->get();
            unset($trip->polyline);
            unset($trip->polyline_encoded);
            $response = [
                'trip_id' => $trip->id,
                'trip' => $trip,
                'fuel_stations' => $result ?? [],

                'polyline_paths' => $polylinePoints ?? [],
                'stops' => $stops,
                'vehicle' => $vehicle
            ];
            return response()->json(['status'=>200,'message'=>'stops added','data'=>$response],200);
        }else{
            return response()->json(['status'=>404,'message'=>'trip not found','data'=>[]],404);
        }

    }
    public function removeStop(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'trip_id' => 'required|exists:trips,id',
            'stop_id' => 'required|exists:tripstops,id',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first(),
                'data' => (object) [],
            ],422);
        }

        $stopId = $request->input('stop_id');
        Tripstop::where('id', $stopId)->delete();
        $trip = Trip::whereId($request->trip_id)->first();
        $fuelStations = FuelStation::where('trip_id', $request->trip_id)->get()
            ->map(function ($station) {
                // Convert the station to an array, keeping all attributes
                $data = $station->toArray();

                // Add the new keys
                $data['ftp_lat'] = $data['latitude'];
                $data['ftp_lng'] = $data['longitude'];
                $data['fuel_station_name'] = $data['name'];
                $data['IFTA_tax'] = floatval(preg_replace('/[^0-9.-]/', '', $station->ifta_tax));
                $data['lastprice'] = floatval(preg_replace('/[^0-9.-]/', '', $station->lastprice));
                $data['price'] = floatval(preg_replace('/[^0-9.-]/', '', $station->price));
                $data['discount'] = $data['discount'] ? (double)$data['discount'] : 0;
                $data['gallons_to_buy'] = $data['gallons_to_buy'] ? (double)$data['gallons_to_buy'] :null;
                $data['is_optimal'] = $data['is_optimal'] ? (bool)$data['is_optimal'] : false;
                // Optionally remove the old keys if not needed
                unset($data['latitude'], $data['longitude'],$data['ifta_tax'],$data['name']);
            return $data;
        });
        unset($trip->vehicle_id);
        $startLat = $trip->start_lat;
        $startLng = $trip->start_lng;
        $endLat = $trip->end_lat;
        $endLng = $trip->end_lng;
        $updatedStartLat = $trip->updated_start_lat;
        $updatedStartLng = $trip->updated_start_lng;
        $updatedEndLat =$trip->updated_end_lat;
        $updatedEndLng = $trip->updated_end_lng;
        $apiKey = 'AIzaSyA0HjmGzP9rrqNBbpH7B0zwN9Gx9MC4w8w';
        $waypoints = '';
        $stops = Tripstop::where('trip_id', $trip->id)->get();
        if ($stops->isNotEmpty()) {
            $waypoints = $stops->map(fn($stop) => "{$stop->stop_lat},{$stop->stop_lng}")->implode('|');
        }
        $url = "https://maps.googleapis.com/maps/api/directions/json?origin={$updatedStartLat},{$updatedStartLng}&destination={$updatedEndLat},{$updatedEndLng}&key={$apiKey}";
        if ($waypoints) {
            $url .= "&waypoints=optimize:true|{$waypoints}";
        }
        $response = Http::get($url);
        if ($response->successful()) {
            $data = $response->json();
            if($data['routes'] && $data['routes'][0]){
                if (!empty($data['routes'][0]['legs'])) {
                    $steps = $data['routes'][0]['legs'][0]['steps'];
                    $decodedCoordinates = [];
                    $stepSize = 150; // Sample every 10th point

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
                        foreach ($leg['steps'] as $step) {
                            $polylinePoints[] = $step['polyline']['points'] ?? null;
                        }
                    }

                    $polylinePoints = array_filter($polylinePoints);
                   // $completePolyline = implode('', $polylinePoints);
                }
                $route = $data['routes'][0] ?? null;
                if ($route) {
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
                    //$matchingRecords = $this->findMatchingRecords($finalFilteredPolyline, $ftpData);
                    $currentTrip = Trip::where('id', $trip->id)->first();
                    $vehicle_id = DriverVehicle::where('driver_id', $currentTrip->user_id)->first();

                    $findVehicle = Vehicle::where('id', $vehicle_id->vehicle_id)->first();
                    $truckMpg = $findVehicle->mpg;
                    $currentFuel = $findVehicle->fuel_left;
                    $fuelStations = [];
                    $reserve_fuel = $findVehicle->reserve_fuel ?? 0;

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
                    //$result = $this->findOptimalFuelStation($startLat, $startLng, $truckMpg, $currentFuel, $matchingRecords, $endLat, $endLng);
                    FuelStation::where('trip_id', $trip->id)->delete();
                    foreach ($result as  $value) {
                        $fuelStations[] = [
                            'name' => $value['fuel_station_name'] ??'N/A',
                            'latitude' => $value['ftpLat'] ?? 0,
                            'longitude' => $value['ftpLng'] ?? 0,
                            'price' => $value['price'] ?? 0,
                            'lastprice' => $value['lastprice'] ?? 0,
                            'discount' => $value['discount'] ?? 0,
                            'ifta_tax' => $value['IFTA_tax'] ?? 0,
                            'is_optimal' => $value['isOptimal'] ?? false,
                            'address' => $value['address'] ?? 'N/A',
                            'gallons_to_buy' => $value['gallons_to_buy'] ?? 0,
                            'trip_id' => $trip->id,
                            'user_id' => $trip->user_id,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                   }
                }
                FuelStation::insert($fuelStations);

            }
        }
        $trip->update([
            'polyline'=>json_encode($polylinePoints),
            'polyline_encoded'=>$encodedPolyline,
            'duration'=>$formattedDuration,
            'distance'=>$formattedDistance
        ]);
        $vehiclefind = DriverVehicle::where('driver_id', $trip->user_id)->pluck('vehicle_id')->first();
        if($vehiclefind){
            $vehicle = Vehicle::where('id', $vehiclefind)->first();
            if($vehicle && $vehicle->vehicle_image != null){
                $vehicle->vehicle_image = url('/vehicles/' . $vehicle->vehicle_image);
            }
        }else{
            $vehicle =null;

        }


        if($trip){
            $trip->distance = $formattedDistance;
            $trip->duration = $formattedDuration;
            $trip->user_id = (int)$trip->user_id;
            $stops = Tripstop::where('trip_id', $trip->id)->get();
            unset($trip->polyline);
            unset($trip->polyline_encoded);
            $response = [
                'trip_id' => $trip->id,
                'trip' => $trip,
                'fuel_stations' => $result ?? [],

                'polyline_paths' => $polylinePoints ?? [],
                'stops' => $stops,
                'vehicle' => $vehicle
            ];
            return response()->json(['status'=>200,'message'=>'stop removed','data'=>$response],200);
        }else{
            return response()->json(['status'=>404,'message'=>'trip not found','data'=>[]],404);
        }

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
    private function findMatchingRecords(array $decodedPolyline, array $ftpData)
{
    $matchingRecords = [];
    $uniqueRecords = []; // To track unique lat,lng combinations

    // Iterate through decoded polyline points
    foreach ($decodedPolyline as $decoded) {
        $lat1 = $decoded['lat'];
        $lng1 = $decoded['lng'];

        // Compare with FTP data points
        foreach ($ftpData as $lat2 => $lngData) {
            foreach ($lngData as $lng2 => $data) {
                $distance = $this->haversineDistance($lat1, $lng1, $lat2, $lng2);

                // Check if within the defined proximity
                if ($distance < 12000) { // Distance is less than 500 meters
                    // Create a unique key for each lat,lng pair
                    $uniqueKey = $lat2 . ',' . $lng2;

                    // Only add if this key hasn't been processed
                    if (!isset($uniqueRecords[$uniqueKey])) {
                        $matchingRecords[] = [
                            'fuel_station_name' => (string) $data['fuel_station_name'],
                            'ftpLat' => (string) $lat2, // Ensure lat/lng are strings for consistency
                            'ftpLng' => (string) $lng2,
                            'lastprice' => (float) $data['lastprice'], // Ensure numeric fields are cast properly
                            'price' => (float) $data['price'],
                            'discount' => isset($data['discount']) ? (float) $data['discount'] : 0.0,
                            'address' => isset($data['address']) ? (string) $data['address'] : 'N/A',
                            'IFTA_tax' => isset($data['IFTA_tax']) ? (float) $data['IFTA_tax'] : 0.0,
                        ];

                        // Mark this lat,lng pair as processed
                        $uniqueRecords[$uniqueKey] = true;
                    }
                }
            }
        }
    }

    return array_values($matchingRecords); // Reindex the array for proper JSON formatting
}
    private function findOptimalFuelStation($startLat, $startLng, $mpg, $currentGallons, $fuelStations, $destinationLat, $destinationLng)
    {
        $optimalStation = collect($fuelStations)->sortBy('price')->first();

        foreach ($fuelStations as &$station) {
            if (
                $station['ftp_lat'] == $optimalStation['ftp_lat'] &&
                $station['ftp_lng'] == $optimalStation['ftp_lng']
            ) {
                // Calculate distance from the optimal station to the destination
                $distanceToDestination = $this->haversineDistance(
                    $station['ftp_lat'],
                    $station['ftp_lng'],
                    $destinationLat,
                    $destinationLng
                );

                // Convert distance to miles and calculate gallons needed
                $distanceInMiles = $distanceToDestination / 1609.34; // Convert meters to miles
                $fuelRequired = $distanceInMiles / $mpg; // Fuel needed in gallons

                // Calculate gallons to buy
                $gallonsToBuy = max(0, $fuelRequired - $currentGallons);
                $station['gallons_to_buy'] = round($gallonsToBuy, 2);
                $station['is_optimal'] = true; // Mark as optimal
            } else {
                // Skip `gallons_to_buy` for non-optimal stations
                $station['gallons_to_buy'] = null;
                $station['is_optimal'] = false; // Mark as non-optimal
            }
        }
        return array_values($fuelStations);
    }
    private function haversineDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6371000; // Earth's radius in meters
        $lat1 = (float) $lat1;
        $lng1 = (float) $lng1;
        $lat2 = (float) $lat2;
        $lng2 = (float) $lng2;
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


    public function getDistance($start, $fuelStation, $polyline)
    {

        $userLocation = ['lat' => $start['latitude'], 'lng' => $start['longitude']];
        $stationLocation = ['lat' => $fuelStation['ftpLat'], 'lng' => $fuelStation['ftpLng']];

        return $this->calculatePolylineDistance($userLocation, $stationLocation, $polyline);
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
function calculateDistance1($lat1, $lng1, $lat2, $lng2) {
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
