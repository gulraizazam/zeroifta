<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class NavigationController extends Controller
{
    public function reroute(Request $request)
    {
        $currentLocation = $request->input('current_location'); // "lat,lng"
        $destination = $request->input('destination'); // "lat,lng"
        $apiKey = 'AIzaSyA0HjmGzP9rrqNBbpH7B0zwN9Gx9MC4w8w'; // Store in .env file
        $bearing = $request->input('bearing'); // "0°"
        // Request Google Directions API with alternative routes
        $response = Http::get("https://maps.googleapis.com/maps/api/directions/json", [
            'origin' => $currentLocation,
            'destination' => $destination,
            'alternatives' => 'true',
            'key' => $apiKey,
        ]);

        $data = $response->json();
        if ($data['status'] !== 'OK') {
            return response()->json(['status' => 400,'message' => 'Unable to fetch routes','data'=>(object)[]], 400);
        }

        $routes = $data['routes'];
        $bestRoute = $this->getBestForwardRoute($routes, $currentLocation,$bearing);

        return response()->json($bestRoute);
    }

    private function getBestForwardRoute($routes, $currentLocation,$bearing)
    {
        $bestRoute = null;
        $bestDistance = PHP_INT_MAX;

        foreach ($routes as $route) {
            $firstTurn = $this->getFirstValidTurn($route['legs'][0]['steps'], $currentLocation,$bearing);
            $distanceToTurn = $this->distanceBetween($currentLocation, $firstTurn);

            // Choose the closest turn that is ahead
            if ($this->isTurnAhead($currentLocation, $firstTurn,$bearing) && $distanceToTurn < $bestDistance) {
                $bestRoute = $route;
                $bestDistance = $distanceToTurn;
            }
        }

        return $bestRoute;
    }

    private function getFirstValidTurn($steps, $currentLocation,$bearing)
    {
        foreach ($steps as $step) {
            $turnLocation = "{$step['start_location']['lat']},{$step['start_location']['lng']}";
            if ($this->isTurnAhead($currentLocation, $turnLocation,$bearing)) {
                return $turnLocation;
            }
        }
        return "{$steps[0]['start_location']['lat']},{$steps[0]['start_location']['lng']}"; // Default to first point
    }

    private function isTurnAhead($currentLocation, $turnLocation,$bearing)
    {
        // Get user bearing (mocked; replace with real user bearing logic if available)
        $userBearing = $bearing ?? 0; // Assume user is moving north (0°)
        $bearingToTurn = $this->bearingBetweenLocations($currentLocation, $turnLocation);

        return abs($bearingToTurn - $userBearing) < 90; // Forward if within 90°
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
}
