<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRtlhRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule,array<mixed>,string>
     */
    public function rules(): array
    {
        return [
            'name'=> ['required','string'],
            'nik'=> ['nullable','string','max:16'],
            'kk'=> ['nullable','string'],
            'address'=> ['required','string'],
            'people'=> ['nullable','integer'],
            'lat'=> ['nullable','string'],
            'lng'=> ['nullable','string'],
            'area'=> ['nullable','numeric'],
            'pondasi'=> ['nullable','string'],
            'kolom_blk'=> ['nullable','string'],
            'rngk_atap'=> ['nullable','string'],
            'atap'=> ['nullable','string'],
            'dinding'=> ['nullable','string'],
            'lantai'=> ['nullable','string'],
            'air'=> ['nullable','string'],
            'jarak_tinja'=> ['nullable','string'],
            'wc'=> ['nullable','string'],
            'jenis_wc'=> ['nullable','string'],
            'tpa_tinja'=> ['nullable','string'],
            'status_safety'=> ['nullable','string'],
            'status'=> ['nullable','string'],
            'is_renov'=> ['nullable','boolean'],
            'district_id'=> ['nullable','integer','exists:districts,id'],
            'village_id'=> ['nullable','integer','exists:villages,id'],
            'note'=> ['nullable','string'],
        ];
    }
}
