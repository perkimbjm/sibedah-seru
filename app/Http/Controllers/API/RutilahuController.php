<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rtlh;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class RutilahuController extends Controller
{
   
    public function index(Request $request)
        {
            try {
                $query = "
                    SELECT jsonb_build_object(
                        'type', 'FeatureCollection',
                        'features', COALESCE(jsonb_agg(features.feature), '[]'::jsonb)
                    )
                    FROM (
                        SELECT jsonb_build_object(
                            'type', 'Feature',
                            'geometry', ST_AsGeoJSON(r.geom)::jsonb,
                            'properties', jsonb_build_object(
                                'id', r.id,
                                'name', r.name,
                                'address', r.address,
                                'status', r.status,
                                'is_renov', r.is_renov,
                                'slug', r.slug,
                                'district_name', d.name,
                                'village_name', v.name
                            )
                        ) AS feature
                        FROM rtlh r
                        LEFT JOIN districts d ON r.district_id = d.id
                        LEFT JOIN villages v ON r.village_id = v.id
                        WHERE ST_IsValid(r.geom)
                ";

                $params = [];

                if ($request->has('district_id')) {
                    $query .= " AND r.district_id = ?";
                    $params[] = $request->district_id;
                }

                if ($request->has('village_id')) {
                    $query .= " AND r.village_id = ?";
                    $params[] = $request->village_id;
                }

                if ($request->has('search')) {
                    $query .= " AND (r.name ILIKE ? OR r.address ILIKE ?)";
                    $search = "%{$request->search}%";
                    $params[] = $search;
                    $params[] = $search;
                }

                $query .= ") features;";

                Log::info('GeoJSON Query: ' . $query);
                Log::info('GeoJSON Params: ', $params);

                $geojson = DB::select($query, $params);
                
                if (!$geojson) {
                    throw new \Exception('Query returned no results');
                }

                $result = json_decode($geojson[0]->jsonb_build_object);
                
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \Exception('JSON decode error: ' . json_last_error_msg());
                }

                return response()->json($result);

            } catch (\Exception $e) {
                Log::error('Map endpoint error: ' . $e->getMessage());
                return response()->json([
                    'error' => 'Error processing map data',
                    'message' => $e->getMessage()
                ], 500);
            }
        }

}