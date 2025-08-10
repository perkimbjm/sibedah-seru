<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateVerifikasiRequest extends FormRequest
{
    public function authorize(): bool
    {
        $verifikasi = $this->route('verifikasi');
        return $verifikasi->canBeEditedBy(\Illuminate\Support\Facades\Auth::user());


    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'kesesuaian_tata_ruang' => 'nullable|boolean',
            'tidak_dalam_sengketa' => 'nullable|boolean',
            'memiliki_alas_hak' => 'nullable|boolean',
            'satu_satunya_rumah' => 'nullable|boolean',
            'belum_pernah_bantuan' => 'nullable|boolean',
            'berpenghasilan_rendah' => 'nullable|boolean',
            'hasil_verifikasi' => 'nullable|in:diterima,belum_memenuhi_syarat',
            'catatan_verifikator' => 'nullable|string|max:1000',
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
            // Karena hasil_verifikasi sekarang ditentukan otomatis di controller berdasarkan kriteria,
            // tidak perlu validasi manual lagi di sini
            // Validasi ini dipindahkan ke controller logic
        });
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'hasil_verifikasi.in' => 'Hasil verifikasi yang dipilih tidak valid.',
            'catatan_verifikator.max' => 'Catatan verifikator maksimal 1000 karakter.',
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
            'kesesuaian_tata_ruang' => 'kesesuaian tata ruang',
            'tidak_dalam_sengketa' => 'tidak dalam sengketa',
            'memiliki_alas_hak' => 'memiliki alas hak',
            'satu_satunya_rumah' => 'satu-satunya rumah',
            'belum_pernah_bantuan' => 'belum pernah bantuan',
            'berpenghasilan_rendah' => 'berpenghasilan rendah',
            'hasil_verifikasi' => 'hasil verifikasi',
            'catatan_verifikator' => 'catatan verifikator',
        ];
    }
}
