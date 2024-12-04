<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\District;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DistrictController extends Controller
{
    public function index()
    {
        $districts = District::all();
        return response()->json([
            'success' => true,
            'data' => $districts
        ]);
    }

    public function show($slug)
    {
        $district = District::where('slug', $slug)->firstOrFail();
        return response()->json([
            'success' => true,
            'data' => $district
        ]);
    }

    public function geojson()
    {
        $geojson = DB::select("
            SELECT jsonb_build_object(
                'type', 'FeatureCollection',
                'features', jsonb_agg(features.feature)
            )
            FROM (
                SELECT jsonb_build_object(
                    'type', 'Feature',
                    'geometry', ST_AsGeoJSON(geom)::jsonb,
                    'properties', jsonb_build_object(
                        'id', id,
                        'name', name,
                        'code', code,
                        'slug', slug
                    )
                ) AS feature
                FROM districts
            ) features;
        ")[0]->jsonb_build_object;

        return response()->json(json_decode($geojson));
    }

    // Method untuk spatial query
    public function findByLocation(Request $request)
    {
        $lat = $request->input('lat');
        $lng = $request->input('lng');

        $district = DB::select("
            SELECT id, name, code, slug
            FROM districts
            WHERE ST_Contains(geom, ST_SetSRID(ST_Point(?, ?), 4326))
        ", [$lng, $lat]);

        return response()->json([
            'success' => true,
            'data' => $district
        ]);
    }
}