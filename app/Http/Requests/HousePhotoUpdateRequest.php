<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HousePhotoUpdateRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'house_id' => ['required', 'integer', 'exists:houses,id'],
            'photo_url' => ['required', 'string'],
            'description' => ['nullable', 'string'],
        ];
    }
}
