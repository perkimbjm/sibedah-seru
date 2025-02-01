<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Rtlh;
use App\Models\House;
use App\Models\Village;
use App\Models\District;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\StoreHouseRequest;


class HouseMapController extends Controller
{
    public function index(Request $request): View
    {
        $districts = District::select('id','name')->pluck('name', 'id')->prepend("Pilih Kecamatan", '');

        $villages = Village::select('id','name', 'district_id')->pluck('name','id')->prepend("Pilih Kelurahan / Desa", '');

        return view('house.map', compact('districts', 'villages'));
    }

    public function store(StoreHouseRequest $request, House $house)
    {
        abort_if(Gate::denies('house_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        try {
            $validated = $request->validated();

            // Buat instance House baru
            $house = new House();

            // Isi data
            $house->fill($validated);
            $house->name = strtoupper($validated['name']);

            // Cari nama kecamatan
            $district = District::findOrFail($validated['district_id']);
            $house->district = strtoupper($district->name);

            // Cek RTLH
            if ($rtlh = Rtlh::where('nik', $house->nik)->first()) {
                $house->rtlh_id = $rtlh->id;
            }

            // Simpan dulu untuk mendapatkan ID
            $house->save();

            // Update slug setelah mendapat ID
            $house->slug = strtolower($house->id . '-' . Str::slug($house->name));
            $house->save();

            // Jika request AJAX
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data berhasil disimpan',
                    'data' => $house
                ]);
            }

            // Jika request normal
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil ditambahkan.',
                'data' => $house
            ]);

        } catch (Exception $e) {
            // Log error untuk debugging
            Log::error('Error saving house: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menyimpan data: ' . $e->getMessage()
                ], 422);
            }

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data: ' . $e->getMessage()
            ], 422);
        }
    }

    public function edit(Request $request)
    {
        abort_if(Gate::denies('house_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $house = House::findOrFail($request->id);
        $house->load('district', 'village');

        // Ambil data villages dengan format yang sesuai
        $villages = Village::select('id','name')
            ->orderBy('name')
            ->pluck('name', 'id')
            ->prepend("Pilih Kelurahan / Desa", '');

        $districts = District::select('id','name')
            ->pluck('name', 'id')
            ->prepend("Pilih Kecamatan", '');

        return view('components.sidebar-edit', compact('house', 'districts', 'villages'));
    }

    public function update(Request $request)
    {
        try {
            $house = House::findOrFail($request->id);
            $house->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diperbarui.',
                'data' => $house
            ]);
        } catch (Exception $e) {
            Log::error('Error updating house: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui data: ' . $e->getMessage()
            ], 422);
        }
    }

    public function getHouseData(Request $request)
    {
        abort_if(Gate::denies('house_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $house = House::with(['district', 'village'])->findOrFail($request->id);

        return response()->json([
            'id' => $house->id,
            'nik' => $house->nik,
            'name' => $house->name,
            'address' => $house->address,
            'village_id' => $house->village_id,
            'district' => [
                'name' => $house->district,
                'id' => $house->district_id
            ],
            'district_id' => $house->district_id,
            'year' => $house->year,
            'type' => $house->type,
            'source' => $house->source,
            'lat' => $house->lat,
            'lng' => $house->lng,
            'note' => $house->note
        ]);
    }

}
