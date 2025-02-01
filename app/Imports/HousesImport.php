<?php

namespace App\Imports;

use App\Models\House;
use App\Models\District;
use App\Models\Village;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Str;

class HousesImport implements ToModel, WithHeadingRow, WithValidation
{

    public function model(array $row)
    {
        // Extract data from the row
        $name = strtoupper($row['nama']);
        $nik = strval($row['nik']);
        $address = $row['alamat'];
        $district = ucwords(strtolower($row['kecamatan']));
        $village = ucwords(strtolower($row['desa']));
        $latitude = strval($row['latitude']);
        $longitude = strval($row['longitude']);
        $year = $row['tahun'];
        $type = $row['jenis_bantuan'];
        $source = $row['sumber'];
        $note = $row['catatan'];

        // Generate slug
        $slug = Str::slug($name);

        $district = District::where('name', $district)->first();
        $district_id = $district ? $district->id : null;
        $village = Village::where('name', $village)->first();
        $village_id = $village ? $village->id : null;

        $house = new House([
            'name' => $name,
            'nik' => $nik,
            'address' => $address,
            'district' => strtoupper($district->name),
            'district_id' => $district_id,
            'village_id' => $village_id,
            'lat' => $latitude,
            'lng' => $longitude,
            'year' => $year,
            'type' => $type,
            'source' => $source,
            'note' => $note,
        ]);

            // Save the house to the database
            $house->save();

            // Generate slug with ID prefix
            $slug = $house->id . '-' . Str::slug($house->name);

            // Update the house record with the generated slug
            $house->update(['slug' => $slug]);

            return $house;


    }


    public function rules(): array
    {
        return [
            'nama' => 'required|string',
            'nik' => 'required|size:16|unique:houses,nik',
            'alamat' => 'required|string',
            'kecamatan' => 'required',
            'desa' => 'required',
            'tahun' => 'required',
        ];
    }

    public function headingRow(): int
    {
        return 1;
    }

}
