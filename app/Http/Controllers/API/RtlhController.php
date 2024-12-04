<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rtlh;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class RtlhController extends Controller
{
   
    public function map(Request $request)
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
                    'geometry', ST_AsGeoJSON(r.geom)::jsonb,
                    'properties', jsonb_build_object(
                        'id', r.id,
                        'name', r.name,
                        'district_name', d.name
                    )
                ) AS feature
                FROM rtlh r
                JOIN districts d ON r.district_id = d.id
        ";

        if ($request->has('district_id')) {
            $query .= " WHERE r.district_id = " . intval($request->district_id);
        }

        $query .= ") features;";

        $geojson = DB::select($query)[0]->jsonb_build_object;

        return response()->json(json_decode($geojson));
    }

    public function index(Request $request)
    {
        $query = Rtlh::selectRaw("name, address, people, area, pondasi, kolom_blk, rngk_atap, atap, dinding, lantai, air, jarak_tinja, wc, jenis_wc, tpa_tinja, status, is_renov, note, district_id, village_id, ST_X(geom::geometry) AS lng, ST_Y(geom::geometry) AS lat")
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

        // Filter berdasarkan status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan dibedah atau tidak
        if ($request->has('is_renov')) {
            $query->where('is_renov', $request->is_renov);
        }

        // Filter berdasarkan kepemilikan wc
        if ($request->has('wc')) {
            $query->where('wc', $request->wc);
        }

        // Filter berdasarkan jenis wc
        if ($request->has('jenis_wc')) {
            $query->where('jenis_wc', $request->jenis_wc);
        }

        // Pencarian berdasarkan nama atau alamat
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'ILIKE', "%{$search}%")
                  ->orWhere('address', 'ILIKE', "%{$search}%");
            });
        }

        $rtlhs = $query->get();

        return response()->json([
            'success' => true,
            'data' => $rtlhs
        ]);
    }


    
    // Method untuk spatial query
    public function findByLocation(Request $request)
    {
        $lat = $request->input('lat');
        $lng = $request->input('lng');
        $radius = $request->input('radius', 200);

        $rtlh = DB::select("
            SELECT r.*, 
                d.name as district_name,
                v.name as village_name,
                ST_Distance(
                    r.geom::geography,
                    ST_SetSRID(ST_Point(?, ?), 4326)::geography
                ) as distance
            FROM rtlh h
            JOIN districts d ON r.district_id = d.id
            JOIN villages v ON r.village_id = v.id
            WHERE ST_DWithin(
                r.geom::geography,
                ST_SetSRID(ST_Point(?, ?), 4326)::geography,
                ?
            )
            ORDER BY distance
        ", [$lng, $lat, $lng, $lat, $radius]);

        return response()->json([
            'success' => true,
            'data' => $rtlh
        ]);
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
    public function getStatus()
    {
        $statuses = Rtlh::select('status')
            ->distinct()
            ->orderBy('status')
            ->pluck('status');

        return response()->json([
            'success' => true,
            'data' => $statuses
        ]);
    }


    // Mendapatkan detail satu rumah
    public function show($slug)
    {
        $rtl = Rtlh::where('slug', $slug)->firstOrFail();
        return response()->json([
            'success' => true,
            'data' => $rtl
        ]);
    }


    // Membuat rumah baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nik' => 'nullable|string|max:255',
            'kk' => 'nullable|string|max:255',
            'address' => 'required|string',
            'people' => 'nullable|integer',
            'lat'=> 'nullable|string',
            'lng'=> 'nullable|string',
            'area'=> 'nullable|float',
            'pondasi'=> 'nullable|string', 
            'kolom_blk'=> 'nullable|string',
            'rngk_atap' => 'nullable|string',
            'atap'=> 'nullable|string',
            'dinding'=> 'nullable|string',
            'lantai'=> 'nullable|string',
            'air'=> 'nullable|string',
            'jarak_tinja'=> 'nullable|string',
            'wc'=> 'nullable|string',
            'jenis_wc' => 'nullable|string',
            'tpa_tinja' => 'nullable|string',
            'status'=> 'nullable|string',
            'is_renov'=> 'nullable|boolean',
            'district_id' => 'nullable|integer',
            'village_id' => 'nullable|integer',
            'note'=> 'nullable|string'
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        $rtl = Rtlh::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Rtlh created successfully',
            'data' => $rtl
        ], 201);
    }

    // Mengupdate rumah
    public function update(Request $request, $slug)
    {
        $rtl = Rtlh::where('slug', $slug)->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nik' => 'nullable|string|max:255',
            'kk' => 'nullable|string|max:255',
            'address' => 'required|string',
            'people' => 'nullable|integer',
            'lat'=> 'nullable|string',
            'lng'=> 'nullable|string',
            'area'=> 'nullable|float',
            'pondasi'=> 'nullable|string', 
            'kolom_blk'=> 'nullable|string',
            'rngk_atap' => 'nullable|string',
            'atap'=> 'nullable|string',
            'dinding'=> 'nullable|string',
            'lantai'=> 'nullable|string',
            'air'=> 'nullable|string',
            'jarak_tinja'=> 'nullable|string',
            'wc'=> 'nullable|string',
            'jenis_wc' => 'nullable|string',
            'tpa_tinja' => 'nullable|string',
            'status'=> 'nullable|string',
            'is_renov'=> 'nullable|boolean',
            'district_id' => 'nullable|integer',
            'village_id' => 'nullable|integer',
            'note'=> 'nullable|string'
        ]);

        if (isset($validated['name'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $rtl->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Rtlh updated successfully',
            'data' => $rtl
        ]);
    }

    // Menghapus rumah
    public function destroy($slug)
    {
        $rtl = Rtlh::where('slug', $slug)->firstOrFail();
        $rtl->delete();

        return response()->json([
            'success' => true,
            'message' => 'Rtlh deleted successfully'
        ]);
    }

}