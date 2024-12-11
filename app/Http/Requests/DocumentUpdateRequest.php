<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DocumentUpdateRequest extends FormRequest
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
            'renovated_house_id' => ['required', 'integer', 'exists:houses,id'],
            'document_url' => ['required', 'string'],
            'description' => ['nullable', 'string'],
        ];
    }
}
