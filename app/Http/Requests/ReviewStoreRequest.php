<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReviewStoreRequest extends FormRequest
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
            'name' => ['required', 'string'],
            'comment' => ['nullable', 'string'],
            'rating' => ['required', 'integer'],
        ];
    }
}
