<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessTripStart;
use App\Models\CompanyDriver;
use App\Models\DriverVehicle;
use App\Models\FcmToken;
use App\Models\FuelStation;
use App\Models\Notification as ModelsNotification;
use App\Models\Trip;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class TripController extends Controller
{
    protected $nodeApiUrl;

    public function __construct()
    {
        $this->nodeApiUrl = env('NODE_API_URL', 'https://localhost:3000');
    }

    public function startTrip(Request $request)
    {
        try {
            // Validate request
            $validatedData = $request->validate([
                'user_id' => 'required|exists:users,id',
                'start_lat' => 'required',
                'start_lng' => 'required',
                'end_lat' => 'required',
                'end_lng' => 'required',
                'truck_mpg' => 'required',
                'fuel_tank_capacity' => 'required',
                'total_gallons_present' => 'required',
            ]);

            // Check for existing active trip
            $existingTrip = Trip::where('user_id', $request->user_id)
                                ->where('status', 'active')
                                ->first();

            if ($existingTrip) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Trip already exists for this user',
                    'data' => $existingTrip
                ], 422);
            }

            // Call Node.js API for calculations
            $nodeResponse = Http::post("{$this->nodeApiUrl}/api/trip/calculate", [
                'start_lat' => $request->start_lat,
                'start_lng' => $request->start_lng,
                'end_lat' => $request->end_lat,
                'end_lng' => $request->end_lng,
                'truck_mpg' => $request->truck_mpg,
                'fuel_tank_capacity' => $request->fuel_tank_capacity,
                'total_gallons_present' => $request->total_gallons_present,
                'reserve_fuel' => $request->reserve_fuel
            ]);

            if (!$nodeResponse->successful()) {
                return response()->json([
                    'status' => 500,
                    'message' => 'Failed to calculate trip details',
                    'data' => []
                ], 500);
            }

            $calculationResults = $nodeResponse->json()['data'];

            // Begin database transaction
            \DB::beginTransaction();

            try {
                // Create trip record
                $tripData = array_merge($validatedData, [
                    'status' => 'active',
                    'updated_start_lat' => $request->start_lat,
                    'updated_start_lng' => $request->start_lng,
                    'updated_end_lat' => $request->end_lat,
                    'updated_end_lng' => $request->end_lng,
                    'polyline' => json_encode($calculationResults['route_data']['polyline']),
                    'polyline_encoded' => $calculationResults['route_data']['polyline_encoded'],
                    'distance' => $calculationResults['route_data']['distance'],
                    'duration' => $calculationResults['route_data']['duration'],
                    'start_address' => $calculationResults['route_data']['start_address'],
                    'end_address' => $calculationResults['route_data']['end_address'],
                    'start_city' => $calculationResults['route_data']['start_city'],
                    'start_state' => $calculationResults['route_data']['start_state'],
                    'end_city' => $calculationResults['route_data']['end_city'],
                    'end_state' => $calculationResults['route_data']['end_state'],
                ]);

                $trip = Trip::create($tripData);

                // Update vehicle information
                $driverVehicle = DriverVehicle::where('driver_id', $request->user_id)->first();
                $vehicle = null;

                if ($driverVehicle) {
                    $vehicle = Vehicle::find($driverVehicle->vehicle_id);
                    if ($vehicle) {
                        $vehicle->update([
                            'fuel_left' => $request->total_gallons_present,
                            'mpg' => $request->truck_mpg,
                            'reserve_fuel' => $request->reserve_fuel
                        ]);

                        if ($vehicle->vehicle_image) {
                            $vehicle->vehicle_image = url('/vehicles/' . $vehicle->vehicle_image);
                        }
                    }
                }

                // Save fuel stations
                foreach ($calculationResults['fuel_stations'] as $station) {
                    FuelStation::create([
                        'name' => $station['name'],
                        'latitude' => $station['latitude'],
                        'longitude' => $station['longitude'],
                        'price' => $station['price'],
                        'lastprice' => $station['lastprice'],
                        'discount' => $station['discount'],
                        'ifta_tax' => $station['ifta_tax'],
                        'is_optimal' => $station['is_optimal'],
                        'firstOptimal' => $station['firstOptimal'],
                        'midOptimal' => $station['midOptimal'],
                        'secondOptimal' => $station['secondOptimal'],
                        'address' => $station['address'],
                        'distanceFromStart' => $station['distanceFromStart'],
                        'gallons_to_buy' => $station['gallons_to_buy'],
                        'trip_id' => $trip->id,
                        'user_id' => $request->user_id,
                    ]);
                }

                // Send notifications
                $this->sendTripNotifications($trip);

                \DB::commit();

                // Prepare response
                $responseData = [
                    'trip_id' => $trip->id,
                    'trip' => $trip,
                    'fuel_stations' => $calculationResults['fuel_stations'],
                    'polyline_paths' => $calculationResults['route_data']['polyline'],
                    'stops' => [],
                    'vehicle' => $vehicle
                ];

                return response()->json([
                    'status' => 200,
                    'message' => 'Trip started successfully',
                    'data' => $responseData
                ]);

            } catch (\Exception $e) {
                \DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Failed to start trip: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    protected function sendTripNotifications($trip)
    {
        $driver = $trip->user;
        if (!$driver) return;

        $companyDriver = CompanyDriver::where('driver_id', $driver->id)->first();
        if (!$companyDriver) return;

        $driverFcmTokens = FcmToken::where('user_id', $driver->id)->pluck('token')->toArray();
        $companyFcmTokens = FcmToken::where('user_id', $companyDriver->company_id)
            ->pluck('token')
            ->toArray();

        if (!empty($companyFcmTokens)) {
            $factory = (new Factory)->withServiceAccount(storage_path('app/zeroifta.json'));
            $messaging = $factory->createMessaging();

            // Notify company
            $message = CloudMessage::new()
                ->withNotification(Notification::create('New Trip Started', "{$driver->name} has started a new trip."))
                ->withData([
                    'trip_id' => (string) $trip->id,
                    'driver_name' => $driver->name,
                    'sound' => 'default',
                ]);

            $messaging->sendMulticast($message, $companyFcmTokens);

            // Create notification record
            ModelsNotification::create([
                'user_id' => $companyDriver->company_id,
                'title' => 'New Trip Started',
                'body' => "{$driver->name} has started a new trip.",
            ]);
        }

        // Notify driver
        if (!empty($driverFcmTokens)) {
            $message = CloudMessage::new()
                ->withNotification(Notification::create('Trip Started', 'Your trip has been started successfully'))
                ->withData([
                    'sound' => 'default',
                ]);

            $messaging->sendMulticast($message, $driverFcmTokens);
        }
    }
}
