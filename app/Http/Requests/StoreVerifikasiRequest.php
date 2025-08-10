<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;

class StoreVerifikasiRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if (!Gate::allows('usulan_verify')) {
            return false;
        }

        $user = Auth::user();
        $usulan = $this->route('usulan');

        // Jika user tidak login atau usulan tidak ditemukan
        if (!$user || !$usulan) {
            return false;
        }

        // Cek apakah usulan bisa diverifikasi dan masih pending
        return $usulan->canBeVerifiedBy($user) && $usulan->isPending();
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
            'hasil_verifikasi' => 'required|in:diterima,ditolak',
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
            // Jika hasil verifikasi adalah 'diterima', pastikan semua kriteria tercentang
            if ($this->hasil_verifikasi === 'diterima') {
                $kriteria = [
                    'kesesuaian_tata_ruang',
                    'tidak_dalam_sengketa',
                    'memiliki_alas_hak',
                    'satu_satunya_rumah',
                    'belum_pernah_bantuan',
                    'berpenghasilan_rendah'
                ];

                $uncheckedCriteria = [];
                foreach ($kriteria as $k) {
                    if (!$this->input($k)) {
                        $uncheckedCriteria[] = $this->attributes()[$k] ?? $k;
                    }
                }

                if (!empty($uncheckedCriteria)) {
                    $validator->errors()->add('hasil_verifikasi',
                        'Untuk memilih "Diterima", semua kriteria verifikasi harus tercentang. Kriteria yang belum tercentang: ' .
                        implode(', ', $uncheckedCriteria)
                    );
                }
            }
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
            'hasil_verifikasi.required' => 'Hasil verifikasi wajib dipilih.',
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
