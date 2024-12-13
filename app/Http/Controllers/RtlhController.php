<?php

namespace App\Http\Controllers;

use App\Models\Rtlh;
use App\Models\House;
use App\Models\Village;
use App\Models\District;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\StoreRtlhRequest;
use App\Http\Requests\UpdateRtlhRequest;
use Symfony\Component\HttpFoundation\Response;

class RtlhController extends Controller
{
    public function index(Request $request): View
    {
        abort_if(Gate::denies('rtlh_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $rtlhs = Rtlh::with([
            'districts:id,name',
            'villages:id,name'
        ])->select(
            'id', 'name', 'nik', 'kk', 'address', 'people', 'area', 'pondasi', 
            'kolom_blk', 'rngk_atap', 'atap', 'dinding', 'lantai', 'air', 
            'jarak_tinja', 'wc', 'jenis_wc', 'tpa_tinja', 'status_safety', 'status', 'is_renov', 
            'district_id', 'village_id', 'note'
        )->get();

        return view('rtlh.index', compact('rtlhs'));
    }

    public function create()
    {
        abort_if(Gate::denies('rtlh_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $districts = District::select('id','name')->pluck('name', 'id')->prepend("Pilih Kecamatan", '');

        $villages = Village::select('id','name', 'district_id')->pluck('name','id')->prepend("Pilih Kelurahan / Desa", '');

        $kelayakanOptions = ['Layak', 'Menuju Layak', 'Agak Layak', 'Kurang Layak', 'Tidak Layak'];
        $airOptions = [
            'Leher Angsa',
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


        return view('rtlh.create', compact('districts', 'villages', 'kelayakanOptions', 'jenisWcOptions', 'airOptions', 'jarakTinjaOptions', 'wcOptions', 'tpaTinjaOptions'));
    }

    public function store(StoreRtlhRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['is_renov'] = false;

        $rtlh = Rtlh::create($validatedData);

        $rtlh['status'] = strtoupper($rtlh['status']);
        $rtlh->slug = strtolower($rtlh->id . '-' . Str::slug($rtlh->name));
        $rtlh->save();

        $request->session()->flash('rtlh.id', $rtlh->id);
        return redirect()->route('app.rtlh.index')->with('success', 'Data berhasil ditambahkan.');
    }
    

    // // Menampilkan detail satu data RTLH
    public function show($id)
    {
        abort_if(Gate::denies('rtlh_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $rtlh = Rtlh::with('house')->findOrFail($id);
        return response()->json($rtlh);
    }

    public function edit(Rtlh $rtlh)
    {
        abort_if(Gate::denies('rtlh_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $districts = District::select('id','name')->pluck('name', 'id')->prepend("Pilih Kecamatan", '');

        $villages = Village::select('id','name', 'district_id')->pluck('name','id')->prepend("Pilih Kelurahan / Desa", '');

        $kelayakanOptions = ['Layak', 'Menuju Layak', 'Agak Layak', 'Kurang Layak', 'Tidak Layak'];
        $bigOptions = ['LAYAK', 'MENUJU LAYAK', 'AGAK LAYAK', 'KURANG LAYAK', 'TIDAK LAYAK'];
        $airOptions = [
            'Leher Angsa',
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

        $rtlh->load('districts', 'villages');

        return view('rtlh.edit', compact('districts', 'villages', 'rtlh', 'kelayakanOptions', 'bigOptions','jenisWcOptions', 'airOptions', 'jarakTinjaOptions', 'wcOptions', 'tpaTinjaOptions'));
    }

    

    // Mengupdate data RTLH
    public function update(UpdateRtlhRequest $request, $id, Rtlh $rtlh)
    {
        $rtlh = Rtlh::findOrFail($id);

        $rtlh->update($request->all());

        if ($request->has('house')) {
            $rtlh->house()->updateOrCreate([], $request->input('house'));
        }

        return redirect()->route('app.rtlh.index');

    }


    // Menghapus data RTLH
    public function destroy($id)
    {
        abort_if(Gate::denies('rtlh_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $rtlh = Rtlh::findOrFail($id);

        // Hapus relasi house jika ada
        $rtlh->house()->delete();

        $rtlh->delete();

        return redirect()->route('app.rtlh.index');
    }

    public function getKecamatan(Request $request)
    {
        $village_id = $request->village_id;
        $village = Village::where('id', $village_id)->first();

        if (!$village) {
            // Jika kelurahan/desa tidak ditemukan
            return response()->json(['error' => 'Village not found'], 404);
        }

        // Memastikan relasi dengan district dimuat
        if (!$village->relationLoaded('district')) {
            $village->load('district');
        }

        // Ambil data district dari relasi
        $district = $village->district;

        if (!$district) {
            // Jika district tidak ditemukan
            return response()->json(['error' => 'District not found'], 404);
        }

        // Kembalikan data district sebagai JSON
        return response()->json([
            'district_name' => $district->name, 
            'district_id' => $district->id
        ]);
    }
}
