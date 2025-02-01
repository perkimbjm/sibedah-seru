<?php

namespace App\Http\Controllers\Api;

use App\Models\Rtlh;
use App\Models\Village;
use App\Models\District;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;


class RtlhController extends Controller
{
   
    public function index(Request $request)
    {
        $query = Rtlh::selectRaw("id, name, address, people, area, pondasi, kolom_blk, rngk_atap, atap, dinding, lantai, air, jarak_tinja, wc, jenis_wc, tpa_tinja, status_safety, status, is_renov, note, district_id, village_id, ST_X(geom::geometry) AS lng, ST_Y(geom::geometry) AS lat")
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
            'area'=> 'nullable|numeric',
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
            'area'=> 'nullable|numeric',
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


    public function getRtlh(Request $request)
    {
        $validated = $request->validate([
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
            'district_id' => 'nullable|array',
            'district_id.*' => 'nullable|integer',
            'village_id' => 'nullable|array',
            'village_id.*' => 'nullable|integer',
            'area'=> 'nullable|numeric',
            'air'=> 'nullable|array',
            'air.*'=> 'nullable|string',
            'jarak_tinja'=> 'nullable|string',
            'wc'=> 'nullable|array',
            'wc.*'=> 'nullable|string',
            'jenis_wc' => 'nullable|array',
            'jenis_wc.*' => 'nullable|string',
            'tpa_tinja' => 'nullable|array',
            'tpa_tinja.*' => 'nullable|string',
            'status_safety' => 'nullable|array',
            'status_safety.*' => 'nullable|string',
            'status'=> 'nullable|array',
            'status.*'=> 'nullable|string',
        ]);

        try {
            // Definisikan $query
            $query = Rtlh::query();

            // Filter berdasarkan radius jika koordinat dan radius diberikan
            if (!empty($validated['lat']) && !empty($validated['lng'])) {
                $lat = $validated['lat'];
                $lng = $validated['lng'];

                
                $query->selectRaw("
                    id, name, address, people, area, pondasi, kolom_blk, rngk_atap, atap, dinding, lantai, air, jarak_tinja, wc, jenis_wc, tpa_tinja, status_safety, status, is_renov, note, district_id, village_id, ST_X(geom::geometry) AS lng, ST_Y(geom::geometry) AS lat,
                    ST_DistanceSphere(ST_MakePoint(?, ?), ST_MakePoint(lng, lat)) AS distance
                ", [$lng, $lat])
                ->orderBy('distance');
            } else {
                // Jika tidak ada lat dan lng, ambil semua data
                $query->select('id', 'name', 'address', 'people', 'area', 'pondasi', 'kolom_blk', 'rngk_atap', 'atap', 'dinding', 'lantai', 'air', 'jarak_tinja', 'wc', 'jenis_wc', 'tpa_tinja', 'status_safety', 'status', 'is_renov', 'note', 'district_id', 'village_id', 'lat', 'lng');
            }

            // Terapkan semua filter menggunakan data tervalidasi
            if (!empty($validated['district_id'])) {
                if (is_array($validated['district_id'])) {
                    $query->whereIn('district_id', $validated['district_id']);
                } else {
                    $query->where('district_id', $validated['district_id']);
                }
            }
            if (!empty($validated['village_id'])) {
                if (is_array($validated['village_id'])) {
                    $query->whereIn('village_id', $validated['village_id']);
                } else {
                    $query->where('village_id', $validated['village_id']);
                }
            }

            if (!empty($validated['area'])) {
                $query->where('area', $validated['area']);
            }

            if (!empty($validated['air'])) {
                $query->whereIn('air', $validated['air']);
            }

            if (!empty($validated['jarak_tinja'])) {
                $query->where('jarak_tinja', $validated['jarak_tinja']);
            }

            if (!empty($validated['wc'])) {
                $query->whereIn('wc', $validated['wc']);
            }

            if (!empty($validated['jenis_wc'])) {
                $query->whereIn('jenis_wc', $validated['jenis_wc']);
            }

            if (!empty($validated['tpa_tinja'])) {
                $query->whereIn('tpa_tinja', $validated['tpa_tinja']);
            }

            if (!empty($validated['status_safety'])) {
                $query->whereIn('status_safety', $validated['status_safety']);
            }

            if (!empty($validated['status'])) {
                $query->whereIn('status', $validated['status']);
            }

            // Jika terdapat radius, filter berdasarkan radius
            if (!empty($validated['lat']) && !empty($validated['lng'])) {
                $query->havingRaw("distance <=?", [$validated['area'] * 1000]);
            }

            // Eksekusi query
            $houses = $query->with([
                'housePhotos' => function ($query) {
                    $query->select('house_id', 'photo_url')->limit(1);
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

    public function getDistricts()
    {
        try {
            $districts = District::select('id', 'name')
                ->orderBy('name')
                ->get();
                
            return response()->json($districts);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching districts: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function getVillages() {
        $villages = Village::all(['id', 'name']);
        return response()->json($villages);
    }
    
    public function getVillagesByDistrict($districtId) {
        $villages = Village::where('district_id', $districtId)
                          ->get(['id', 'name']);
        return response()->json($villages);
    }

    public function searchRTLH($identifier)  
    {  
        // Cari data berdasarkan NIK atau KK  
        $rtlhData = DB::table('rtlh')
            ->select('nik', 'kk', 'name', 'address', 'is_renov')  
            ->where('nik', $identifier)  
            ->orWhere('kk', $identifier)  
            ->first();  
  
        if ($rtlhData) {  
            // Ubah nilai is_renov menjadi string "sudah dibedah rumah" jika true  
            $rtlhData->is_renov = $rtlhData->is_renov ? "sudah dibedah rumah" : "belum dibedah rumah";  
  
            return response()->json($rtlhData);  
        } else {  
            return response()->json(['message' => 'Data tidak ditemukan'], 404);  
        }  
    }  

}