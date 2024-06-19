<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GeofencingController extends Controller
{
    public function index()
    {
        return view('geofencing.index');
    }
    public function checkGeofence(Request $request)
    {
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');

        $geofence = [
            'latitude' => 37.7749,
            'longitude' => -122.4194,
            'radius' => 0.01, // Radius in degrees (adjust as needed)
        ];

        $isInsideGeofence = $this->isInsideGeofence($latitude, $longitude, $geofence);

        return response()->json(['inside_geofence' => $isInsideGeofence]);
    }
    private function isInsideGeofence($latitude, $longitude, $geofence)
    {
        $distance = $this->calculateDistance($latitude, $longitude, $geofence['latitude'], $geofence['longitude']);

        return $distance <= $geofence['radius'];
    }
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Earth radius in kilometers

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a =
            sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        $distance = $earthRadius * $c;

        return $distance;
    }
}
