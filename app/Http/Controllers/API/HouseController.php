<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\House;
use Illuminate\Support\Str;

class HouseController extends Controller
{
    public function index(Request $request)
    {
        $query = House::selectRaw("id, name, address, year, type, source, note, district_id, village_id, 
                           ST_X(geom::geometry) AS lng, ST_Y(geom::geometry) AS lat")
        ->with([
            'district:id,name',
            'village:id,name'
        ]);


        // Filter berdasarkan district_id
        if ($request->has('district_id')) {
            $query->where('district_id', $request->district_id);
        }

        // Filter berdasarkan village_id
        if ($request->has('village_id')) {
            $query->where('village_id', $request->village_id);
        }

        // Filter berdasarkan year
        if ($request->has('year')) {
            $query->where('year', $request->year);
        }

        // Filter berdasarkan type
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Pencarian berdasarkan nama atau alamat
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'ILIKE', "%{$search}%")
                  ->orWhere('address', 'ILIKE', "%{$search}%");
            });
        }

        $houses = $query->get();

        return response()->json([
            'success' => true,
            'data' => $houses
        ]);
    }

    public function getToken(Request $request)
    {
        return response()->json(['token' => env('HOUSE')]);
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
                    'geometry', ST_AsGeoJSON(h.geom)::jsonb,
                    'properties', jsonb_build_object(
                        'id', h.id,
                        'name', h.name,
                        'address', h.address,
                        'district', h.district,
                        'year', h.year,
                        'type', h.type,
                        'source', h.source,
                        'slug', h.slug,
                        'district_name', d.name,
                        'village_name', v.name
                    )
                ) AS feature
                FROM houses h
                LEFT JOIN districts d ON h.district_id = d.id
                LEFT JOIN villages v ON h.village_id = v.id
                WHERE ST_IsValid(h.geom)
        ";

        $params = [];

        if ($request->has('district_id')) {
            $query .= " AND h.district_id = ?";
            $params[] = intval($request->district_id);
        }

        if ($request->has('village_id')) {
            $query .= " AND h.village_id = ?";
            $params[] = intval($request->village_id);
        }

        if ($request->has('year')) {
            $query .= " AND h.year = ?";
            $params[] = intval($request->year);
        }

        if ($request->has('type')) {
            $query .= " AND h.type = ?";
            $params[] = $request->type;
        }

        if ($request->has('search')) {
            $query .= " AND (h.name ILIKE ? OR h.address ILIKE ?)";
            $search = "%{$request->search}%";
            $params[] = $search;
            $params[] = $search;
        }

        $query .= ") features;";

        $geojson = DB::select($query, $params);

        if (empty($geojson)) {
            return response()->json(['error' => 'No data found'], 404);
        }

        return response()->json(json_decode($geojson[0]->jsonb_build_object));
    }

    // Method untuk mendapatkan daftar tahun yang tersedia
    public function getYears()
    {
        $years = House::select('year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        return response()->json([
            'success' => true,
            'data' => $years
        ]);
    }

    // Method untuk mendapatkan daftar tipe yang tersedia
    public function getTypes()
    {
        $types = House::select('type')
            ->distinct()
            ->orderBy('type')
            ->pluck('type');

        return response()->json([
            'success' => true,
            'data' => $types
        ]);
    }

    // Method untuk spatial query
    public function findByLocation(Request $request)
    {
        $lat = $request->input('lat');
        $lng = $request->input('lng');
        $radius = $request->input('radius', 200);

        $houses = DB::select("
            SELECT h.*, 
                d.name as district_name,
                v.name as village_name,
                ST_Distance(
                    h.geom::geography,
                    ST_SetSRID(ST_Point(?, ?), 4326)::geography
                ) as distance
            FROM houses h
            JOIN districts d ON h.district_id = d.id
            JOIN villages v ON h.village_id = v.id
            WHERE ST_DWithin(
                h.geom::geography,
                ST_SetSRID(ST_Point(?, ?), 4326)::geography,
                ?
            )
            ORDER BY distance
        ", [$lng, $lat, $lng, $lat, $radius]);

        return response()->json([
            'success' => true,
            'data' => $houses
        ]);
    }


    // Mendapatkan detail satu rumah
    public function show($slug)
    {
        $house = House::where('slug', $slug)->firstOrFail();
        return response()->json([
            'success' => true,
            'data' => $house
        ]);
    }

    // Membuat rumah baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'district' => 'required|string|max:255',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'year' => 'required|integer',
            'type' => 'required|string|max:255',
            'source' => 'required|string|max:255',
            'note' => 'nullable|string'
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        $house = House::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'House created successfully',
            'data' => $house
        ], 201);
    }

    // Mengupdate rumah
    public function update(Request $request, $slug)
    {
        $house = House::where('slug', $slug)->firstOrFail();

        $validated = $request->validate([
            'name' => 'string|max:255',
            'address' => 'string',
            'district' => 'string|max:255',
            'lat' => 'numeric',
            'lng' => 'numeric',
            'year' => 'integer',
            'type' => 'string|max:255',
            'source' => 'string|max:255',
            'note' => 'nullable|string'
        ]);

        if (isset($validated['name'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $house->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'House updated successfully',
            'data' => $house
        ]);
    }

    // Menghapus rumah
    public function destroy($slug)
    {
        $house = House::where('slug', $slug)->firstOrFail();
        $house->delete();

        return response()->json([
            'success' => true,
            'message' => 'House deleted successfully'
        ]);
    }
}