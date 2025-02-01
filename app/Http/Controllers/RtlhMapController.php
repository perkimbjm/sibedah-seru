<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Rtlh;
use App\Models\Village;
use App\Models\District;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\StoreRtlhRequest;

class RtlhMapController extends Controller
{
    public function index(Request $request): View
    {
        $districts = District::select('id','name')->pluck('name', 'id')->prepend("Pilih Kecamatan", '');

        $villages = Village::select('id','name', 'district_id')->pluck('name','id')->prepend("Pilih Kelurahan / Desa", '');

        $kelayakanOptions = ['Layak', 'Kurang Layak', 'Tidak Layak'];
        $kelayakanOptions2 = ['LAYAK', 'MENUJU LAYAK', 'KURANG LAYAK', 'TIDAK LAYAK'];
        $airOptions = [
            'Air Kemasan',
            'Pamsimas',
            'Mata Air',
            'PDAM',
            'Air Hujan',
            'Sungai',
            'Sumur',
            'Lainnya',
        ];

        $jarakTinjaOptions = ['≥ 10 Meter', '≤ 10 Meter'];

        $wcOptions = ['Tidak Ada', 'Bersama', 'Milik Sendiri'];

        $jenisWcOptions = ['Tidak Ada', 'Plengsengan', 'Leher Angsa', 'Cemplung/Cubluk'];

        $tpaTinjaOptions = ['Tangki Septik', 'Lubang Tanah', 'IPAL', 'Kolam/Sawah/Sungai/Danau/Laut', 'Pantai/Tanah Lapang/Kebun'];

        return view('rtlh.map', compact('districts', 'villages', 'kelayakanOptions', 'kelayakanOptions2', 'jenisWcOptions', 'airOptions', 'jarakTinjaOptions', 'wcOptions', 'tpaTinjaOptions'));
    }

    public function store(StoreRtlhRequest $request, Rtlh $rtlh)
    {
        abort_if(Gate::denies('rtlh_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        try {
            $validated = $request->validated();

            // Buat instance Rtlh baru
            $rtlh = new Rtlh();

            // Isi data
            $rtlh->fill($validated);
            $rtlh->name = strtoupper($validated['name']);

            // Simpan dulu untuk mendapatkan ID
            $rtlh->save();

            // Update slug setelah mendapat ID
            $rtlh->slug = strtolower($rtlh->id . '-' . Str::slug($rtlh->name));
            $rtlh->save();

            // Jika request AJAX
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data berhasil disimpan',
                    'data' => $rtlh
                ]);
            }

            // Jika request normal
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil ditambahkan.',
                'data' => $rtlh
            ]);

        } catch (Exception $e) {
            // Log error untuk debugging
            Log::error('Error saving rtlh: ' . $e->getMessage());

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
        abort_if(Gate::denies('rtlh_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $kelayakanOptions = ['Layak', 'Kurang Layak', 'Tidak Layak'];
        $kelayakanOptions2 = ['LAYAK', 'MENUJU LAYAK', 'KURANG LAYAK', 'TIDAK LAYAK'];
        $airOptions = [
            'Air Kemasan',
            'Pamsimas',
            'Mata Air',
            'PDAM',
            'Air Hujan',
            'Sungai',
            'Sumur',
            'Lainnya',
        ];

        $jarakTinjaOptions = ['≥ 10 Meter', '≤ 10 Meter'];

        $wcOptions = ['Tidak Ada', 'Bersama', 'Milik Sendiri'];

        $jenisWcOptions = ['Tidak Ada', 'Plengsengan', 'Leher Angsa', 'Cemplung/Cubluk'];

        $tpaTinjaOptions = ['Tangki Septik', 'Lubang Tanah', 'IPAL', 'Kolam/Sawah/Sungai/Danau/Laut', 'Pantai/Tanah Lapang/Kebun'];

        $rtlh = Rtlh::findOrFail($request->id);
        $rtlh->load('district', 'village');

        // Ambil data villages dengan format yang sesuai
        $villages = Village::select('id','name')
            ->orderBy('name')
            ->pluck('name', 'id')
            ->prepend("Pilih Kelurahan / Desa", '');

        $districts = District::select('id','name')
            ->pluck('name', 'id')
            ->prepend("Pilih Kecamatan", '');

        return view('rtlh.partials.sidebar-edit', compact('rtlh', 'districts', 'villages', 'kelayakanOptions', 'kelayakanOptions2', 'jenisWcOptions', 'airOptions', 'jarakTinjaOptions', 'wcOptions', 'tpaTinjaOptions'));
    }

    public function update(Request $request)
    {
        try {
            $rtlh = Rtlh::findOrFail($request->id);
            $rtlh->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diperbarui.',
                'data' => $rtlh
            ]);
        } catch (Exception $e) {
            Log::error('Error updating rtlh: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui data: ' . $e->getMessage()
            ], 422);
        }
    }

    public function getRtlhData(Request $request)
    {
        abort_if(Gate::denies('rtlh_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $rtlh = Rtlh::with(['district', 'village'])->findOrFail($request->id);

        return response()->json([
            'id' => $rtlh->id,
            'nik' => $rtlh->nik,
            'kk' => $rtlh->nik,
            'name' => $rtlh->name,
            'address' => $rtlh->address,
            'village_id' => $rtlh->village_id,
            'district_id' => $rtlh->district_id,
            'district' => [
                'name' => $rtlh->name,
                'id' => $rtlh->district_id
            ],
            'people' => $rtlh->people,
            'area' => $rtlh->area,
            'lat' => $rtlh->lat,
            'lng' => $rtlh->lng,
            'pondasi' => $rtlh->pondasi,
            'kolom_blk' => $rtlh->kolom_blk,
            'rngk_atap' => $rtlh->rngk_atap,
            'atap' => $rtlh->atap,
            'dinding' => $rtlh->dinding,
            'lantai' => $rtlh->lantai,
            'air' => $rtlh->air,
            'jarak_tinja' => $rtlh->jarak_tinja,
            'wc' => $rtlh->wc,
            'jenis_wc' => $rtlh->jenis_wc,
            'tpa_tinja' => $rtlh->tpa_tinja,
            'status_safety' => $rtlh->status_safety,
            'status' => $rtlh->status,
            'is_renov' => $rtlh->is_renov,
            'note' => $rtlh->note,
        ]);
    }
}
