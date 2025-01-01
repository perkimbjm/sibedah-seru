<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Village;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VillageController extends Controller
{
    public function index(Request $request)
    {
        $query = Village::with(['district' => function ($query) {
            $query->select('id', 'name');
        }]);
        
        // Filter by district if provided
        if ($request->has('district_id')) {
            $query->where('district_id', $request->district_id);
        }

        $villages = $query->select('id', 'name', 'district_id')->get();
        
        return response()->json([
            'success' => true,
            'data' => $villages
        ]);
    }

    public function show($slug)
    {
        $village = Village::with('district')
            ->where('slug', $slug)
            ->firstOrFail();
            
        return response()->json([
            'success' => true,
            'data' => $village
        ]);
    }

    public function geojson(Request $request)
    {
        $query = "
            SELECT jsonb_build_object(
                'type', 'FeatureCollection',
                'features', jsonb_agg(features.feature)
            )
            FROM (
                SELECT jsonb_build_object(
                    'type', 'Feature',
                    'geometry', ST_AsGeoJSON(v.geom)::jsonb,
                    'properties', jsonb_build_object(
                        'id', v.id,
                        'name', v.name,
                        'code', v.code,
                        'slug', v.slug,
                        'district_name', d.name
                    )
                ) AS feature
                FROM villages v
                JOIN districts d ON v.district_id = d.id
        ";

        $conditions = [];
        if ($request->has('district_id')) {
            $conditions[] = "v.district_id = " . intval($request->district_id);
        }

        if ($request->has('village_id')) {
            $conditions[] = "v.id = " . intval($request->village_id);
        }

        if (!empty($conditions)) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }

        $query .= ") features;";

        $geojson = DB::select($query)[0]->jsonb_build_object;

        return response()->json(json_decode($geojson));
    }

    // Method untuk spatial query
    public function findByLocation(Request $request)
    {
        $lat = $request->input('lat');
        $lng = $request->input('lng');

        $village = DB::select("
            SELECT v.id, v.name, v.code, v.slug, d.name as district_name
            FROM villages v
            JOIN districts d ON v.district_id = d.id
            WHERE ST_Contains(v.geom, ST_SetSRID(ST_Point(?, ?), 4326))
        ", [$lng, $lat]);

        return response()->json([
            'success' => true,
            'data' => $village
        ]);
    }

}