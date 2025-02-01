<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($this->user()->id),
            ],
            'phone' => [
                'nullable', 'string', Rule::unique('users')->ignore($this->user()->id),
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
            'name.required' => 'Bidang nama harus diisi.',
            'name.max' => 'Nama tidak boleh melebihi 255 karakter.',
            'email.required' => 'Bidang email harus diisi.',
            'email.email' => 'Silakan masukkan alamat email yang valid.',
            'email.unique' => 'Email ini sudah digunakan.',
            'phone.unique' => 'Nomor Telepon ini sudah digunakan.',
        ];
    }
}
