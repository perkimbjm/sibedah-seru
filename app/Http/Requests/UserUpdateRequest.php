<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        return Gate::allows('user_edit');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'google_id' => ['nullable', 'string'],
            'name' => ['required', 'string'],
            'email' => ['required', 'email'],
            'avatar' => ['nullable', 'string'],
            'password' => ['nullable', 'string'],
            'role_id' => ['required', 'integer', 'exists:roles,id'],
            'phone' => ['nullable', 'string'],
            'profile_photo_path' => ['nullable','image', 'mimes:jpeg,jpg,png,webp']

        ];
    }
}
