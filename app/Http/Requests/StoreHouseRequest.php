<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;

class StoreHouseRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'nik' => ['nullable', 'string', 'max:16'],
            'address' => ['nullable', 'string'],
            'lat' => ['required', 'string'],
            'lng' => ['required', 'string'],
            'year' => ['required', 'integer'],
            'type' => ['required', 'string'],
            'rtlh_id' => ['nullable', 'integer', 'exists:rtlh,id'],
            'district_id' => ['required', 'integer', 'exists:districts,id'],
            'village_id' => ['required', 'integer', 'exists:villages,id'],
            'source' => ['nullable','string'],
            'note' => ['nullable', 'string'],
        ];
    }
}
