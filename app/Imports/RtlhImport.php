<?php

namespace App\Imports;

use App\Models\Rtlh;
use App\Models\District;
use App\Models\Village;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Str;

class RtlhImport implements ToModel, WithHeadingRow, WithValidation
{

    public function model(array $row)
    {
        // Extract data from the row
        $name = strtoupper($row['nama']);
        $nik = strval($row['nik']);
        $kk = strval($row['kk']);
        $address = $row['alamat'];
        $people = intval($row['jumlah_penghuni']);
        $latitude = strval($row['latitude']);
        $longitude = strval($row['longitude']);
        $area = floatval($row['luas_bangunan_rumah']);
        $pondasi = !empty($row['pondasi']) ? ucwords(strtolower($row['pondasi'])) : "Kurang Layak";
        $kolom_blk = !empty($row['kolom_atau_balok']) ? ucwords(strtolower($row['kolom_atau_balok'])) : "Kurang Layak";
        $rngk_atap = !empty($row['rangka_atap']) ? ucwords(strtolower($row['rangka_atap'])) : "Kurang Layak";
        $atap = !empty($row['atap']) ? ucwords(strtolower($row['atap'])) : "Kurang Layak";
        $dinding = !empty($row['dinding']) ? ucwords(strtolower($row['dinding'])) : "Kurang Layak";
        $lantai = !empty($row['lantai']) ? ucwords(strtolower($row['lantai'])) : "Kurang Layak";
        $air = $row['sumber_air_bersih'];
        $jarak_tinja = !empty($row['jarak_ke_pembuang_tinja']) ? $row['jarak_ke_pembuang_tinja'] : "≥ 10 Meter";
        $wc = !empty($row['fasilitas_bab']) ? $row['fasilitas_bab'] : "Milik Sendiri";
        $jenis_wc = !empty($row['jenis']) ? $row['jenis'] : "Leher Angsa";
        $tpa_tinja = $row['tpa_tinja'];
        $status_safety = strtoupper($row['penilaian_keselamatan_bangunan']);
        $status = strtoupper($row['penilaian_keselamatan_bangunan_dan_sanitasi']);
        $district = ucwords(strtolower($row['kecamatan']));
        $village = ucwords(strtolower($row['desa']));
        $note = $row['catatan'];

        // Generate slug
        $slug = Str::slug($name);

        $district = District::where('name', $district)->first();
        $district_id = $district ? $district->id : null;
        $village = Village::where('name', $village)->first();
        $village_id = $village ? $village->id : null;

        $rtlh = new Rtlh([
            'name' => $name,
            'nik' => $nik,
            'kk' => $kk,
            'address' => $address,
            'people' => $people,
            'lat' => $latitude,
            'lng' => $longitude,
            'area' => $area,
            'pondasi' => $pondasi,
            'kolom_blk' => $kolom_blk,
            'rngk_atap' => $rngk_atap,
            'atap' => $atap,
            'dinding' => $dinding,
            'lantai' => $lantai,
            'air' => $air,
            'jarak_tinja' => $jarak_tinja,
            'wc' => $wc,
            'jenis_wc' => $jenis_wc,
            'tpa_tinja' => $tpa_tinja,
            'status_safety' => $status_safety,
            'status' => $status,
            'district_id' => $district_id,
            'village_id' => $village_id,
            'is_renov' => false,
            'note' => $note,
        ]);

        // Save the rtlh to the database
        $rtlh->save();

        $slug = $rtlh->id . '-' . Str::slug($rtlh->name);

        $rtlh->update(['slug' => $slug]);

        return $rtlh;
    }


    public function rules(): array
    {
        return [
            'nama' => 'required|string',
            'nik' => 'required|size:16|unique:rtlh,nik',
            'kk' => 'required|unique:rtlh,kk',
            'latitude' => 'required',
            'longitude' => 'required',
            'kecamatan' => 'required',
            'desa' => 'required',
        ];
    }

    public function headingRow(): int
    {
        return 1;
    }

}
