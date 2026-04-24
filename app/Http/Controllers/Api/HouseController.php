<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\House;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

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

    public function general(Request $request)
    {
        $query = House::selectRaw("id, name, address, year, type, source, slug, district_id, village_id, 
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
                  ->orWhere('id', '=', $search)
                  ->orWhere('address', 'ILIKE', "%{$search}%");
            });
        }

        // Eksekusi query
        $houses = $query->with([
            'renovatedHousePhotos' => function ($query) {
                $query->select('renovated_house_id', 'photo_url')->limit(1);
            },
        ])
        ->get();

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

    
    public function getHouses(Request $request)
    {
        $validated = $request->validate([
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
            'district_id' => 'nullable|array',
            'district_id.*' => 'nullable|integer',
            'village_id' => 'nullable|array',
            'village_id.*' => 'nullable|integer',
            'year' => 'nullable|array',
            'year.*' => 'nullable|integer',
            'source' => 'nullable|array',
            'source.*' => 'nullable|string',
            'type' => 'nullable|array',
            'type.*' => 'nullable|string'
    ]);

        try {
            $query = House::query();

            // Filter berdasarkan radius jika koordinat diberikan
            if (!empty($validated['lat']) && !empty($validated['lng'])) {
                $lat = $validated['lat'];
                $lng = $validated['lng'];

                $query->selectRaw("
                    id, name, address, year, type, district_id, village_id, source, 
                    ST_X(geom::geometry) as lng, ST_Y(geom::geometry) as lat,
                    ST_DistanceSphere(ST_MakePoint(?, ?), geom) AS distance
                ", [$lng, $lat])
                ->orderBy('distance');
            } else {
                $query->selectRaw("
                    id, name, address, year, type, district_id, village_id, source,
                    ST_X(geom::geometry) as lng, ST_Y(geom::geometry) as lat
                ");
            }

            // Terapkan semua filter menggunakan data tervalidasi
            if (!empty($validated['district_id'])) {
                $query->whereIn('district_id', $validated['district_id']);
            }

            if (!empty($validated['village_id'])) {
                $query->whereIn('village_id', $validated['village_id']);
            }

            if (!empty($validated['year'])) {
                $query->whereIn('year', $validated['year']);
            }

            if (!empty($validated['source'])) {
                $query->whereIn('source', $validated['source']);
            }

            if (!empty($validated['type'])) {
                $query->whereIn('type', $validated['type']);
            }

            $houses = $query->with([
                'renovatedHousePhotos' => function ($query) {
                    $query->select('renovated_house_id', 'photo_url')
                    ->where('is_primary', true)
                    ->where('photo_url', 'like', '%progres-100%');
                },
                'district:id,name',
                'village:id,name',
            ])
            ->get();

            return response()->json([
                'success' => true,
                'data' => $houses,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getHousesByRadius(Request $request)
    {
        $validated = $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'radius' => 'nullable|numeric|min:1|max:5', // Radius maksimal 5 km
        ]);

        $lat = $validated['lat'];
        $lng = $validated['lng'];
        $radius = $validated['radius'] ?? 1; // Default 1 km

        try {
            $houses = House::selectRaw("
                id, name, address, year, type,
                ST_DistanceSphere(geom, ST_MakePoint(?, ?)) AS distance
            ", [$lng, $lat])
            ->whereRaw("ST_DistanceSphere(geom, ST_MakePoint(?, ?)) <= ?", [$lng, $lat, $radius * 1000])
            ->orderBy('distance')
            ->get();
            

            return response()->json([
                'success' => true,
                'data' => $houses,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getHousesInBounds(Request $request)
    {
        $validated = $request->validate([
            'south' => 'nullable|numeric',
            'west' => 'nullable|numeric',
            'north' => 'nullable|numeric',
            'east' => 'nullable|numeric',
            'district_id' => 'nullable|array',
            'district_id.*' => 'nullable|integer',
            'village_id' => 'nullable|array',
            'village_id.*' => 'nullable|integer',
            'year' => 'nullable|array',
            'year.*' => 'nullable|integer',
            'source' => 'nullable|array',
            'source.*' => 'nullable|string',
            'type' => 'nullable|array',
            'type.*' => 'nullable|string'
        ]);

        try {
            $query = House::query();

            // Filter bounds menggunakan geom
            $query->whereRaw("ST_Intersects(
                geography(ST_MakeEnvelope(?, ?, ?, ?)),
                geography(geom)
            )", [
                $validated['west'], $validated['south'],
                $validated['east'], $validated['north']
            ]);

            // Filter tambahan
            if ($request->filled('district_id')) {
                $query->whereIn('district_id', $request->district_id);
            }

            if ($request->filled('village_id')) {
                $query->whereIn('village_id', $request->village_id);
            }

            if ($request->filled('year')) {
                $query->whereIn('year', $request->year);
            }

            if ($request->filled('source')) {
                $query->whereIn('source', $request->source);
            }

            if ($request->filled('type')) {
                $query->whereIn('type', $request->type);
            }

            $houses = $query->select('id', 'name', 'address', 'year', 'type', 'district_id', 'village_id', 'source', 'lat', 'lng')
            ->with([
                'renovatedHousePhotos' => function ($query) {
                    $query->select('renovated_house_id', 'photo_url')
                    ->where('is_primary', true)
                        ->where('photo_url', 'like', '%progres-100%');
                },
                'district:id,name',
                'village:id,name',
            ])
            ->get();

                

            return response()->json([
                'success' => true,
                'data' => $houses,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
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

    public function getHouse(Request $request, $id)
    {
        $house = House::find($id);
        if (!$house) {
            return response()->json(['success' => false, 'message' => 'House not found'], 404);
        }
        return $this->formatGeoJSON([$house]);
    }

    protected function formatGeoJSON($houses)
    {
        $features = collect($houses)->map(function ($house) {
            $properties = $house->toArray();
            unset($properties['geom']);
            unset($properties['nik']);
            unset($properties['coordinate']);
            unset($properties['map_popup_content']);
            unset($properties['created_at']);
            unset($properties['updated_at']);
            $properties['renovated_house_photos'] = $house->renovatedHousePhotos()->where('is_primary', true)->pluck('photo_url')->first();
            return [
                'type' => 'Feature',
                'geometry' => [
                    'type' => 'Point',
                    'coordinates' => [(float)$house->lng, (float)$house->lat]
                ],
                'properties' => $properties
            ];
        });

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $features->toArray()
        ]);
    }
}