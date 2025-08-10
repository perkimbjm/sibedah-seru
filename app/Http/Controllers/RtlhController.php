<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Rtlh;
use App\Models\House;
use App\Models\Village;
use App\Models\District;
use Illuminate\View\View;
use App\Imports\RtlhImport;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\StoreRtlhRequest;
use App\Http\Requests\UpdateRtlhRequest;
use Yajra\DataTables\Facades\DataTables;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Http\Requests\MassDestroyRtlhRequest;
use Symfony\Component\HttpFoundation\Response;
use Maatwebsite\Excel\Facades\Excel;

class RtlhController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('rtlh_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax() || $request->wantsJson()) {
            $query = Rtlh::query()
                ->select([
                    'rtlh.id',
                    'rtlh.name',
                    'rtlh.nik',
                    'rtlh.address',
                    'rtlh.district_id',
                    'districts.name as district_name',
                    'rtlh.people',
                    'rtlh.area',
                    'rtlh.pondasi',
                    'rtlh.kolom_blk',
                    'rtlh.rngk_atap',
                    'rtlh.atap',
                    'rtlh.dinding',
                    'rtlh.lantai',
                    'rtlh.air',
                    'rtlh.jarak_tinja',
                    'rtlh.wc',
                    'rtlh.jenis_wc',
                    'rtlh.tpa_tinja',
                    'rtlh.status_safety',
                    'rtlh.status',
                    'rtlh.is_renov',
                ])
                ->leftJoin('districts', 'rtlh.district_id', '=', 'districts.id');

            return datatables()->of($query)
                ->addColumn('placeholder', '&nbsp;')
                ->filterColumn('district_name', function($query, $keyword) {
                    $query->whereRaw("LOWER(districts.name) ILIKE ?", ["%{$keyword}%"]);
                })
                ->addColumn('status_perbaikan', function($row) {
                    if ($row->is_renov) {
                        $house = \App\Models\House::where('rtlh_id', $row->id)->first();
                        $link = $house ? '<a class="btn btn-xs btn-primary" href="' . route('app.houses.show', $house->id) . '">Lihat Data</a>' : '';
                        return '<span class="badge bg-success">Sudah Diperbaiki</span>' . $link;
                    } else {
                        return '<span class="badge bg-danger">Belum Diperbaiki</span>';
                    }
                })
                ->addColumn('gallery', function ($row) {
                    return '
                    <a href="' . route('app.rutilahu.index', $row->id) . '" class="px-2 py-1 text-gray-500 rounded-lg btn btn-md btn-warning d-inline-block">
                        Foto
                    </a>';
                })
                ->addColumn('action', function($row) {
                    return view('rtlh.partials.action-buttons', compact('row'))->render();
                })
                ->rawColumns(['placeholder','status_perbaikan', 'gallery','action'])
                ->setRowId(function ($row) {
                    return 'row_'.$row->id;
                })
                ->smart(true)
                ->toJson(); // Pastikan response Ajax mengembalikan JSON
        }

        return view('rtlh.index'); // Mengembalikan view saat bukan permintaan Ajax
    }

    public function create()
    {
        abort_if(Gate::denies('rtlh_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $districts = District::select('id','name')->pluck('name', 'id')->prepend("Pilih Kecamatan", '');

        $villages = Village::select('id','name', 'district_id')->pluck('name','id')->prepend("Pilih Kelurahan / Desa", '');

        $kelayakanOptions = ['Layak', 'Kurang Layak', 'Tidak Layak'];
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

        // Data dari verifikasi (jika ada)
        $fromVerifikasi = request('from_verifikasi');
        $verifikasiData = null;

        if ($fromVerifikasi) {
            $verifikasi = \App\Models\Verifikasi::with(['usulan.user', 'usulan.district', 'usulan.village'])->find($fromVerifikasi);
            if ($verifikasi && $verifikasi->isAccepted()) {
                $verifikasiData = [
                    'nik' => $verifikasi->usulan->nik,
                    'kk' => $verifikasi->usulan->nomor_kk,
                    'name' => $verifikasi->usulan->nama,
                    'address' => $verifikasi->usulan->alamat_lengkap,
                    'district_id' => $verifikasi->usulan->district_id,
                    'village_id' => $verifikasi->usulan->village_id,
                    'district_name' => $verifikasi->usulan->district->name ?? '',
                    'lat' => $verifikasi->usulan->latitude,
                    'lng' => $verifikasi->usulan->longitude,
                ];
            }
        }

        return view('rtlh.create', compact('districts', 'villages', 'kelayakanOptions', 'jenisWcOptions', 'airOptions', 'jarakTinjaOptions', 'wcOptions', 'tpaTinjaOptions', 'verifikasiData'));
    }

    public function store(StoreRtlhRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $validatedData['is_renov'] = false;

            $rtlh = Rtlh::create($validatedData);
            $rtlh['name'] = strtoupper($rtlh['name']);
            $rtlh['status_safety'] = strtoupper($rtlh['status_safety']);

            $rtlh['status'] = strtoupper($rtlh['status']);
            $rtlh->slug = strtolower($rtlh->id . '-' . Str::slug($rtlh->name));
            $rtlh->save();

            session(['rtlh.id' => $rtlh->id]);
            return redirect()->route('app.rtlh.index')->with('success', 'Data berhasil ditambahkan.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Gagal menyimpan data RTLH: ' . $e->getMessage()]);
        }
    }


    // // Menampilkan detail satu data RTLH
    public function show(Rtlh $rtlh)
    {
        abort_if(Gate::denies('rtlh_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $rtlh = Rtlh::with([
            'district:id,name',
            'village:id,name',
        ])->findOrFail($rtlh->id);


        $spaceScore = $rtlh->calculateSpaceScore();
        $cleanWaterScore = $rtlh->calculateCleanWaterScore();
        $sanitationScore = $rtlh->calculateSanitationScore();
        $safetyScore = $rtlh->calculateSafetyScore();
        $finalScore = $rtlh->calculateFinalScore();

        return view('rtlh.show', compact('rtlh', 'safetyScore', 'sanitationScore', 'cleanWaterScore', 'spaceScore', 'finalScore'));

    }

    public function edit(Rtlh $rtlh)
    {
        abort_if(Gate::denies('rtlh_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $districts = District::select('id','name')->pluck('name', 'id')->prepend("Pilih Kecamatan", '');

        $villages = Village::select('id','name', 'district_id')->pluck('name','id')->prepend("Pilih Kelurahan / Desa", '');

        $kelayakanOptions = ['Layak', 'Kurang Layak', 'Tidak Layak'];
        $bigOptions = ['LAYAK', 'MENUJU LAYAK', 'KURANG LAYAK', 'TIDAK LAYAK'];
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

        $rtlh->load('district', 'village');

        return view('rtlh.edit', compact('districts', 'villages', 'rtlh', 'kelayakanOptions', 'bigOptions','jenisWcOptions', 'airOptions', 'jarakTinjaOptions', 'wcOptions', 'tpaTinjaOptions'));
    }



    // Mengupdate data RTLH
    public function update(UpdateRtlhRequest $request, $id)
    {
        $rtlh = Rtlh::findOrFail($id);
        $rtlh['name'] = strtoupper($rtlh['name']);

        $rtlh->update($request->all());
        $data['is_renov'] = isset($data['is_renov']) && $data['is_renov'] === '1';

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
        $rtlh->housePhotos()->delete();

        $rtlh->delete();

        return redirect()->route('app.rtlh.index');
    }

    public function massDestroy(MassDestroyRtlhRequest $request)
    {
        Rtlh::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
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

    public function getDesa(Request $request)
    {
        $villages = Village::select('id', 'name')->get();

        return response()->json($villages);
    }

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set header kolom
        $headers = [
            'NAMA',
            'NIK',
            'KK',
            'ALAMAT',
            'PEOPLE',
            'LATITUDE',
            'LONGITUDE',
            'AREA',
            'PONDASI',
            'KOLOM_BLK',
            'RNGK_ATAP',
            'ATAP',
            'DINDING',
            'LANTAI',
            'AIR',
            'JARAK_TINJA',
            'WC',
            'JENIS_WC',
            'TPA_TINJA',
            'STATUS_SAFETY',
            'STATUS',
            'IS_RENOV',
            'KECAMATAN',
            'DESA',
            'CATATAN'
        ];

        // Atur lebar kolom
        foreach (range('A', 'Y') as $column) {
            $sheet->getColumnDimension($column)->setWidth(20);
        }

        // Tulis header
        foreach ($headers as $index => $header) {
            $column = chr(65 + $index); // Konversi ke huruf kolom (A, B, C, etc.)
            $sheet->setCellValue($column . '1', $header);

            // Style header
            $sheet->getStyle($column . '1')->getFont()->setBold(true);
            $sheet->getStyle($column . '1')->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setRGB('CCCCCC');
        }

        // Tambah contoh data
        $exampleData = [
            [
                'Ahmad Muhammad',
                "'6311567890123456",
                '1234567890123456',
                'Dayak Pitap RT. 03',
                4,
                '-2.123456',
                '115.123456',
                100,
                'Layak',
                'Layak',
                'Layak',
                'Layak',
                'Layak',
                'Layak',
                'Layak',
                '≥ 10 Meter',
                'Milik Sendiri',
                'Leher Angsa',
                'Tangki Septik',
                'Layak',
                'Aktif',
                true,
                'Tebing Tinggi',
                'Dayak Pitap',
                'Perlu Perbaikan Koordinat'
            ]
        ];

        // Tulis contoh data
        $row = 2;
        foreach ($exampleData as $data) {
            foreach ($data as $index => $value) {
                $column = chr(65 + $index);
                $sheet->setCellValue($column . $row, $value);
            }
            $row++;
        }

        $writer = new Xlsx($spreadsheet);

        // Set header untuk download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="template_import_rtlh.xlsx"');
        header('Cache-Control: max-age=0');

        // Output file
        $writer->save('php://output');
        exit;
    }

    public function import(Request $request)
    {
        abort_if(Gate::denies('rtlh_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        try {
            Excel::import(new RtlhImport, $request->file('file'));
            return redirect()->route('app.rtlh.index')->with('success', 'Data berhasil diimpor.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal mengimpor data: ' . $e->getMessage()]);
        }
    }

    public function importForm()
    {
        abort_if(Gate::denies('rtlh_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return view('rtlh.import');
    }
}
