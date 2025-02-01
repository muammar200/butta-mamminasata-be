<?php

namespace App\Http\Controllers\Api\Web;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\PropertyResource;
use Illuminate\Support\Facades\Validator;

class LocationController extends Controller
{
    public function index(Request $request)
    {
        // Ambil latitude & longitude dari request
        $latitude = $request->latitude;
        $longitude = $request->longitude;
        $radius = $request->radius ?? 10; // Default 10 km

        // Query menggunakan Haversine Formula
        $locations = Category::with('properties')->selectRaw(
            "
                *, 
                (6371 * acos(cos(radians(?)) * cos(radians(latitude)) 
                * cos(radians(longitude) - radians(?)) 
                + sin(radians(?)) * sin(radians(latitude)))) AS distance",
            [$latitude, $longitude, $latitude]
        )
            ->having("distance", "<", $radius) // Filter berdasarkan radius (dalam km)
            ->orderBy("distance", "asc") // Urutkan berdasarkan jarak
            ->get();

        // return response()->json($locations);

        if ($locations) {
            //return success with Api Resource
            return new PropertyResource(true, 'Data Property Terdekat Berhasil Ditampilkan!', $locations);
        }

        //return failed with Api Resource
        return new PropertyResource(
            false,
            'Tidak ada Data',
            null
        );
    }
}
