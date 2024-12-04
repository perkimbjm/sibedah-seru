<?php

namespace App\Http\Controllers;

use App\Models\Rtlh;
use App\Models\House;
use Illuminate\Http\Request;

class RtlhController extends Controller
{
    // Menampilkan daftar data RTLH
    // public function index()
    // {
    //     $rtlhs = Rtlh::with('house')->where('is_renov', false)->get();
    //     return response()->json($rtlhs);
    // }

    // // Menampilkan detail satu data RTLH
    // public function show($id)
    // {
    //     $rtlh = Rtlh::with('house')->findOrFail($id);
    //     return response()->json($rtlh);
    // }

    // // Menyimpan data RTLH baru
    // public function store(Request $request)
    // {
    //     $validated = $request->validate([
    //         'name' => 'required|string|max:255',
    //         'nik' => 'nullable|string|max:255',
    //         'kk' => 'nullable|string|max:255',
    //         'address' => 'required|string',
    //         'people' => 'nullable|integer',
    //         'lat'=> 'nullable|string',
    //         'lng'=> 'nullable|string',
    //         'area'=> 'nullable|float',
    //         'pondasi'=> 'nullable|string', 
    //         'kolom_blk'=> 'nullable|string',
    //         'rngk_atap' => 'nullable|string',
    //         'atap'=> 'nullable|string',
    //         'dinding'=> 'nullable|string',
    //         'lantai'=> 'nullable|string',
    //         'air'=> 'nullable|string',
    //         'jarak_tinja'=> 'nullable|string',
    //         'wc'=> 'nullable|string',
    //         'jenis_wc' => 'nullable|string',
    //         'tpa_tinja' => 'nullable|string',
    //         'status'=> 'nullable|string',
    //         'is_renov'=> 'nullable|boolean',
    //         'district_id' => 'nullable|integer',
    //         'village_id' => 'nullable|integer',
    //         'note'=> 'nullable|string',
            
    //     ]);

    //     $rtlh = Rtlh::create($validated);

    //     if ($request->has('rtlh')) {
    //         $rtlh->rtlh()->create($request->input('rtlh'));
    //     }

    //     return response()->json($rtlh, 201);
    // }

    // // Mengupdate data RTLH
    // public function update(Request $request, $id)
    // {
    //     $rtlh = Rtlh::findOrFail($id);

    //     $validated = $request->validate([
    //         'name' => 'required|string|max:255',
    //         'nik' => 'nullable|string|max:255',
    //         'kk' => 'nullable|string|max:255',
    //         'address' => 'required|string',
    //         'people' => 'nullable|integer',
    //         'lat'=> 'nullable|string',
    //         'lng'=> 'nullable|string',
    //         'area'=> 'nullable|float',
    //         'pondasi'=> 'nullable|string', 
    //         'kolom_blk'=> 'nullable|string',
    //         'rngk_atap' => 'nullable|string',
    //         'atap'=> 'nullable|string',
    //         'dinding'=> 'nullable|string',
    //         'lantai'=> 'nullable|string',
    //         'air'=> 'nullable|string',
    //         'jarak_tinja'=> 'nullable|string',
    //         'wc'=> 'nullable|string',
    //         'jenis_wc' => 'nullable|string',
    //         'tpa_tinja' => 'nullable|string',
    //         'status'=> 'nullable|string',
    //         'is_renov'=> 'nullable|boolean',
    //         'district_id' => 'nullable|integer',
    //         'village_id' => 'nullable|integer',
    //         'note'=> 'nullable|string',
    //     ]);

    //     $rtlh->update($validated);

    //     if ($request->has('house')) {
    //         $rtlh->house()->updateOrCreate([], $request->input('house'));
    //     }

    //     return response()->json($rtlh);
    // }

    // // Menghapus data RTLH
    // public function destroy($id)
    // {
    //     $rtlh = Rtlh::findOrFail($id);

    //     // Hapus relasi house jika ada
    //     $rtlh->house()->delete();

    //     $rtlh->delete();

    //     return response()->json(['message' => 'Data berhasil dihapus.']);
    // }
}
