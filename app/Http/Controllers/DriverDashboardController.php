<?php

namespace App\Http\Controllers;

use App\Models\CompanyContactUs;
use App\Models\CompanyDriver;
use App\Models\Contactus;
use App\Models\DriverVehicle;
use App\Models\Plan;
use App\Models\Trip;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Stripe\Customer;
use Stripe\Stripe;
use Stripe\Subscription;

class DriverDashboardController extends Controller
{
    public function index(Request $request)
    {
        Stripe::setApiKey('sk_test_51FYXgWJOfbRIs4ne6dmGfFbmR1pKgX5V1CQVQHSSlzjCom2KemJylbslX2ylQ2dpbrvmSBGUQSWt6kXETr1ByRR500fTaO7v7k');
        $start = microtime(true); // Measure execution time

        // Initialize dashboard data
        $dashboardData = [];

        // Fetch vehicle data
        $vehicle = Vehicle::select(
            'id',
            'vehicle_image',
            'vehicle_number',
            'mpg',
            'odometer_reading',
            'fuel_left',
            'fuel_tank_capacity',
            'model',
            'make',
            'make_year',
            'license_plate_number',
            'reserve_fuel'
        )
        ->whereHas('driverVehicle', function ($query) use ($request) {
            $query->where('driver_id', $request->driver_id);
        })
        ->first();
        if ($vehicle) {
            if($vehicle->vehicle_image){
                $vehicle->vehicle_image = url('vehicles/' . $vehicle->vehicle_image);
            }else{
                $vehicle->vehicle_image =null;
            }
           
        }
            $dashboardData['vehicle'] = $vehicle;
        
        

        // Fetch the last 5 trips
        $trips = Trip::select('id', 'user_id', 'start_lat', 'start_lng', 'end_lat', 'end_lng', 'status', 'created_at')
            ->where('user_id', $request->driver_id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Cache addresses and routes
        $addresses = $this->batchGetAddressesFromCoordinates($trips->flatMap(function ($trip) {
            return [
                ['lat' => $trip->start_lat, 'lng' => $trip->start_lng],
                ['lat' => $trip->end_lat, 'lng' => $trip->end_lng],
            ];
        })->unique());

        $routes = $this->batchGetRoutesFromCoordinates($trips);

        // Map trips with pre-fetched data
        $tripData = $trips->map(function ($trip) use ($addresses, $routes) {
            $pickupKey = "{$trip->start_lat},{$trip->start_lng}";
            $dropoffKey = "{$trip->end_lat},{$trip->end_lng}";
            $routeKey = "$pickupKey-$dropoffKey";

            return [
                'id' => $trip->id,
                'start_lat'=>$trip->start_lat,
                'start_lng'=>$trip->start_lng,
                'end_lat'=>$trip->end_lat,
                'end_lng'=>$trip->end_lng,
                'user_id' => $trip->user_id,
                'pickup' => $addresses[$pickupKey] ?? 'Unknown Location',
                'dropoff' => $addresses[$dropoffKey] ?? 'Unknown Location',
                'distance' => $routes[$routeKey]['distance'] ?? null,
                'duration' => $routes[$routeKey]['duration'] ?? null,
                'status' => $trip->status,
                'created_at' => $trip->created_at->format('d M'),
            ];
        });
        $customerId = User::find($request->driver_id);
        if(!empty($customerId->stripe_customer_id)){
            $customer = Customer::retrieve($customerId->stripe_customer_id);
            $subscriptions = Subscription::all([
                'customer' => $customer->id,
                'status' => 'active',
                'limit' => 1,
            ]);
        }
        

            // Check if customer has active subscriptions
           

            

           if(!empty($subscriptions->data)){
            $findPlan = Plan::where('stripe_plan_id',$subscriptions->data[0]->items->data[0]->plan->id)->first();
                
            $subscription = $subscriptions->data[0];
              
            // Extract next billing details
            $nextBillingDate = $subscription->current_period_end;
            $planName = $findPlan->name;
            $amount = $subscription->items->data[0]->plan->amount / 100; // Convert to dollars (if in cents)
            $currency = strtoupper($subscription->items->data[0]->plan->currency);
            $subscriptionDetail = [
                'plan_name' => $planName,
                'amount' => $amount,
                'currency' => $currency,
                'next_billing_date' =>Carbon::createFromTimestamp($nextBillingDate)->format('d M Y'),
            ];
           }else{
            $subscriptionDetail = null;
           }
           

             $dashboardData['subscription'] = $subscriptionDetail;
             $dashboardData['recentTrips'] = $tripData;

        // Return the response
        return response()->json([
            'status' => 200,
            'message' => 'Data Fetched',
            'data' => $dashboardData,
            'execution_time' => microtime(true) - $start // Optional: For debugging
        ]);
    }
    private function batchGetAddressesFromCoordinates($coordinates)
    {
        $cacheKey = 'addresses:' . md5(serialize($coordinates)); // Cache key for results
        $addresses = cache()->remember($cacheKey, 3600, function () use ($coordinates) {
            $results = [];
            foreach ($coordinates as $coordinate) {
                $lat = $coordinate['lat'];
                $lng = $coordinate['lng'];
                $results["$lat,$lng"] = $this->getAddressFromCoordinates($lat, $lng);
            }
            return $results;
        });

        return $addresses;
    }
    private function batchGetAddressesFromCoordinates($coordinates)
    {
        $cacheKey = 'addresses:' . md5(serialize($coordinates)); // Cache key for results
        $addresses = cache()->remember($cacheKey, 3600, function () use ($coordinates) {
            $results = [];
            foreach ($coordinates as $coordinate) {
                $lat = $coordinate['lat'];
                $lng = $coordinate['lng'];
                $results["$lat,$lng"] = $this->getAddressFromCoordinates($lat, $lng);
            }
            return $results;
        });

        return $addresses;
    }
    public function contactus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'driver_id'=>'required|exists:users,id',
            'subject' => 'required',
            'message'=>'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status'=>422,'message' => $validator->errors()->first(),'data'=>(object)[]], 422);
        }
        //$findCompay = CompanyDriver::where('driver_id',$request->driver_id)->first();
        $contact = new CompanyContactUs();
        //$contact->driver_id = $request->driver_id;
        $contact->company_id = $request->driver_id;
        $contact->subject = $request->subject;
        $contact->message = $request->message;
        $contact->save();
        return response()->json(['status'=>200,'message'=>'Request submitted successfully','data'=>$contact],200);
    }
    public function getAddressFromCoordinates($latitude, $longitude)
{
    $apiKey = 'AIzaSyBtQuABE7uPsvBnnkXtCNMt9BpG9hjeDIg'; // Use config for the API key
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
}
