<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUsulanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Semua user yang login bisa membuat usulan
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:255',
            'nik' => [
                'required',
                'string',
                'size:16',
                'regex:/^[0-9]+$/',
                Rule::unique('usulan', 'nik')->ignore($this->usulan),
            ],
            'nomor_kk' => [
                'required',
                'string',
                'size:16',
                'regex:/^[0-9]+$/',
                Rule::unique('usulan', 'nomor_kk')->ignore($this->usulan),
            ],
            'nomor_hp' => [
                'nullable',
                'string',
                'regex:/^(\+628|628|08)[0-9]{8,11}$/',
            ],
            'email' => [
                'nullable',
                'string',
                'email',
                'max:255',
            ],
            'district_id' => 'required|exists:districts,id',
            'village_id' => 'required|exists:villages,id',
            'alamat_lengkap' => 'required|string|max:1000',
            'jenis_usulan' => 'required|in:RTLH,Rumah Korban Bencana',
            'foto_rumah' => [
                'nullable',
                'file',
                'image',
                'mimes:jpeg,jpg,png,webp',
                'max:2048', // 2MB max
            ],
            'latitude' => [
                'nullable',
                'numeric',
                'between:-90,90',
            ],
            'longitude' => [
                'nullable',
                'numeric',
                'between:-180,180',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'nama.required' => 'Nama wajib diisi.',
            'nama.max' => 'Nama maksimal 255 karakter.',

            'nik.required' => 'NIK wajib diisi.',
            'nik.size' => 'NIK harus tepat 16 digit.',
            'nik.regex' => 'NIK hanya boleh berisi angka.',
            'nik.unique' => 'NIK sudah terdaftar dalam sistem.',

            'nomor_kk.required' => 'Nomor KK wajib diisi.',
            'nomor_kk.size' => 'Nomor KK harus tepat 16 digit.',
            'nomor_kk.regex' => 'Nomor KK hanya boleh berisi angka.',
            'nomor_kk.unique' => 'Nomor KK sudah terdaftar dalam sistem.',

            'nomor_hp.regex' => 'Nomor HP harus diawali dengan +628, 628, atau 08 dan diikuti 8-11 digit angka.',

            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email maksimal 255 karakter.',

            'district_id.required' => 'Kecamatan wajib dipilih.',
            'district_id.exists' => 'Kecamatan yang dipilih tidak valid.',

            'village_id.required' => 'Kelurahan/Desa wajib dipilih.',
            'village_id.exists' => 'Kelurahan/Desa yang dipilih tidak valid.',

            'alamat_lengkap.required' => 'Alamat lengkap wajib diisi.',
            'alamat_lengkap.max' => 'Alamat lengkap maksimal 1000 karakter.',

            'jenis_usulan.required' => 'Jenis usulan wajib dipilih.',
            'jenis_usulan.in' => 'Jenis usulan yang dipilih tidak valid.',

            'foto_rumah.file' => 'File foto rumah tidak valid.',
            'foto_rumah.image' => 'File harus berupa gambar.',
            'foto_rumah.mimes' => 'Format gambar harus JPEG, JPG, PNG, atau WEBP.',
            'foto_rumah.max' => 'Ukuran file foto maksimal 2MB.',

            'latitude.numeric' => 'Latitude harus berupa angka.',
            'latitude.between' => 'Latitude harus antara -90 sampai 90.',

            'longitude.numeric' => 'Longitude harus berupa angka.',
            'longitude.between' => 'Longitude harus antara -180 sampai 180.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes(): array
    {
        return [
            'nama' => 'nama',
            'nik' => 'NIK',
            'nomor_kk' => 'nomor KK',
            'nomor_hp' => 'nomor HP',
            'email' => 'email',
            'district_id' => 'kecamatan',
            'village_id' => 'kelurahan/desa',
            'alamat_lengkap' => 'alamat lengkap',
            'jenis_usulan' => 'jenis usulan',
            'foto_rumah' => 'foto rumah',
            'latitude' => 'latitude',
            'longitude' => 'longitude',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Validasi tambahan untuk memastikan village_id sesuai dengan district_id
            if ($this->district_id && $this->village_id) {
                $village = \App\Models\Village::where('id', $this->village_id)
                    ->where('district_id', $this->district_id)
                    ->first();

                if (!$village) {
                    $validator->errors()->add('village_id', 'Kelurahan/Desa yang dipilih tidak sesuai dengan Kecamatan.');
                }
            }

            // Validasi koordinat jika keduanya diisi
            if ($this->latitude && $this->longitude) {
                // Validasi untuk koordinat di Indonesia (sekitar)
                if ($this->latitude < -11 || $this->latitude > 6 ||
                    $this->longitude < 95 || $this->longitude > 141) {
                    $validator->errors()->add('latitude', 'Koordinat yang dimasukkan tidak berada di wilayah Indonesia.');
                }
            }
        });
    }
}
