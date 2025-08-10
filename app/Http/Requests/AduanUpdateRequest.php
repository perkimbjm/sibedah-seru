<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AduanUpdateRequest extends FormRequest
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
            // Untuk menambah pengaduan lanjutan
            'complain2' => 'nullable|string|max:2000|min:10',
            'complain3' => 'nullable|string|max:2000|min:10',

            // Untuk respon admin - validation dilakukan di controller
            'respon' => 'nullable|string|max:2000',
            'respon2' => 'nullable|string|max:2000',
            'respon3' => 'nullable|string|max:2000',

            // Untuk update status
            'status' => 'nullable|in:pending,process,completed',

            // Untuk polling evaluasi
            'expect' => 'nullable|integer|min:1|max:4',

            // Captcha untuk user yang menambah pengaduan lanjutan
            'captcha' => 'nullable|integer',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'complain2.min' => 'Pengaduan lanjutan minimal 10 karakter.',
            'complain2.max' => 'Pengaduan lanjutan maksimal 2000 karakter.',
            'complain3.min' => 'Pengaduan lanjutan minimal 10 karakter.',
            'complain3.max' => 'Pengaduan lanjutan maksimal 2000 karakter.',
            'respon.max' => 'Tanggapan maksimal 2000 karakter.',
            'respon2.max' => 'Tanggapan maksimal 2000 karakter.',
            'respon3.max' => 'Tanggapan maksimal 2000 karakter.',
            'status.in' => 'Status tidak valid.',
            'expect.integer' => 'Penilaian harus berupa angka.',
            'expect.min' => 'Penilaian minimal 1.',
            'expect.max' => 'Penilaian maksimal 4.',
            'captcha.integer' => 'Captcha harus berupa angka.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Sanitasi input text
        $fields = ['complain2', 'complain3', 'respon', 'respon2', 'respon3'];
        $sanitized = [];

        foreach ($fields as $field) {
            if ($this->has($field) && !is_null($this->get($field))) {
                $sanitized[$field] = strip_tags(trim($this->get($field)));
            }
        }

        if (!empty($sanitized)) {
            $this->merge($sanitized);
        }
    }

    /**
     * Get the validated data from the request.
     */
    public function validated($key = null, $default = null)
    {
        $validated = parent::validated($key, $default);

        // Additional sanitization untuk text fields
        $textFields = ['complain2', 'complain3', 'respon', 'respon2', 'respon3'];

        foreach ($textFields as $field) {
            if (isset($validated[$field])) {
                $validated[$field] = htmlspecialchars($validated[$field], ENT_QUOTES, 'UTF-8');
            }
        }

        return $validated;
    }
}
