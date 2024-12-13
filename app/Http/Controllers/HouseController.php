<?php

namespace App\Http\Controllers;

use App\Models\Rtlh;
use App\Models\House;
use App\Models\Village;
use App\Models\District;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\StoreHouseRequest;
use App\Http\Requests\UpdateHouseRequest;
use Symfony\Component\HttpFoundation\Response;

class HouseController extends Controller
{

    public function index(Request $request): View
    {
        abort_if(Gate::denies('house_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        
        $houses = House::select('id', 'name', 'nik', 'address', 'district', 'year', 'type', 'source')->get();

        return view('house.index', compact('houses'));
    }


    public function create()
    {
        abort_if(Gate::denies('house_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $districts = District::select('id','name')->pluck('name', 'id')->prepend("Pilih Kecamatan", '');

        $villages = Village::select('id','name', 'district_id')->pluck('name','id')->prepend("Pilih Kelurahan / Desa", '');

        return view('house.create', compact('districts', 'villages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreHouseRequest $request)
    {
        // Simpan data validasi
        $validated = $request->validated();

        // Buat instance model House baru
        $house = new House();
        $house->name = strtoupper($validated['name']);
        $house->nik = $validated['nik'];
        $house->address = $validated['address'];
        $house->village_id = $validated['village_id'];
        $house->district_id = $validated['district_id'];
        $house->lat = $validated['lat'];
        $house->lng = $validated['lng'];
        $house->year = $validated['year'];
        $house->type = $validated['type'];
        $house->source = $validated['source'];
        $house->note = $validated['note'];

        // Cari nama kecamatan dari district_id
        $district = District::find($validated['district_id']);
        if ($district) {
            $house->district = strtoupper($district->name); // Simpan nama kecamatan
        }

        // Cek apakah ada rtlh_id berdasarkan nik
        $rtlh = Rtlh::where('nik', $house->nik)->first();
        if ($rtlh) {
            $house->rtlh_id = $rtlh->id;
        }

        // Simpan rumah ke database
        $house->save();

        // Buat slug berdasarkan {id}-{name}
        $house->slug = strtolower($house->id . '-' . Str::slug($house->name));
        $house->save();

        // Redirect setelah berhasil menyimpan data
        return redirect()->route('app.houses.index')->with('success', 'Data berhasil ditambahkan.');
    }


    /**
     * Display the specified resource.
     */
    public function show(House $house)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(House $house)
    {
        abort_if(Gate::denies('house_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $district = District::select('id','name')->pluck('name', 'id')->prepend("Pilih Kecamatan", '');

        $villages = Village::select('id','name', 'district_id')->pluck('name','id')->prepend("Pilih Kelurahan / Desa", '');


        $house->load('district', 'village');

        return view('house.edit', compact('house','district', 'villages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateHouseRequest $request, House $house)
    {

        $house->update($request->all());

        return redirect()->route('app.houses.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(House $house)
    {
        //
    }

    public function renov($rtlh_id)
    {
        $house = House::with('rtlh')->findOrFail($rtlh_id);
        return view('houses.renov', compact('house'));
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

    public function checkNIK(Request $request)
    {
        $nik = $request->get('nik');
        
        $house = Rtlh::where('nik', $nik)->first();

        if ($house) {
            return response()->json([
                'exists' => true,
                'house' => [
                    'name' => $house->name,
                    'address' => $house->address,
                    'lat' => $house->lat,
                    'lng' => $house->lng,
                ]
            ]);
        } else {
            return response()->json(['exists' => false]);
        }
    }


    
}
