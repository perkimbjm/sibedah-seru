<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AduanStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Publik bisa mengakses form pengaduan
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|regex:/^[\pL\s\-\.]+$/u',
            'email' => [
                'nullable',
                'email',
                'max:255',
                function ($attribute, $value, $fail) {
                    // Jika user sudah login, email bisa kosong atau menggunakan email user
                    if (Auth::check()) {
                        return;
                    }

                    // Jika email diisi dan user tidak login, cek apakah email sudah terdaftar
                    if (!empty($value)) {
                        $existingUser = User::where('email', $value)->first();
                        if ($existingUser) {
                            $fail('Email yang dimasukkan sudah terdaftar dan anda harus login untuk menggunakannya.');
                        }
                    }
                }
            ],
            'contact' => 'nullable|string|max:20|regex:/^[0-9\+\-\s\(\)]+$/',
            'district_id' => 'required|exists:districts,id',
            'village_id' => 'required|exists:villages,id',
            'complain' => 'required|string|max:2000|min:10',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:3072|dimensions:max_width=4000,max_height=4000',
            'captcha' => Auth::check() ? 'nullable|integer' : 'required|integer', // Untuk user login, captcha opsional
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama wajib diisi.',
            'name.regex' => 'Nama hanya boleh berisi huruf, spasi, tanda hubung, dan titik.',
            'name.max' => 'Nama maksimal 255 karakter.',
            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email maksimal 255 karakter.',
            'contact.regex' => 'Nomor HP hanya boleh berisi angka, tanda plus, tanda hubung, spasi, dan kurung.',
            'contact.max' => 'Nomor HP maksimal 20 karakter.',
            'district_id.required' => 'Kecamatan wajib dipilih.',
            'district_id.exists' => 'Kecamatan yang dipilih tidak valid.',
            'village_id.required' => 'Kelurahan/Desa wajib dipilih.',
            'village_id.exists' => 'Kelurahan/Desa yang dipilih tidak valid.',
            'complain.required' => 'Isi pengaduan wajib diisi.',
            'complain.min' => 'Isi pengaduan minimal 10 karakter.',
            'complain.max' => 'Isi pengaduan maksimal 2000 karakter.',
            'foto.image' => 'File harus berupa gambar.',
            'foto.mimes' => 'Format foto harus jpeg, png, jpg, gif, atau webp.',
            'foto.max' => 'Ukuran foto maksimal 3MB.',
            'foto.dimensions' => 'Dimensi foto maksimal 4000x4000 pixel.',
            'captcha.required' => 'Captcha wajib diisi.',
            'captcha.integer' => 'Captcha harus berupa angka.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => strip_tags(trim($this->name)),
            'email' => strip_tags(trim($this->email)),
            'contact' => strip_tags(trim($this->contact)),
            'complain' => strip_tags(trim($this->complain)),
        ]);
    }

    /**
     * Get the validated data from the request.
     */
    public function validated($key = null, $default = null)
    {
        $validated = parent::validated($key, $default);

        // Additional sanitization
        if (isset($validated['name'])) {
            $validated['name'] = htmlspecialchars($validated['name'], ENT_QUOTES, 'UTF-8');
        }

        if (isset($validated['email'])) {
            $validated['email'] = filter_var($validated['email'], FILTER_SANITIZE_EMAIL);
        }

        if (isset($validated['complain'])) {
            $validated['complain'] = htmlspecialchars($validated['complain'], ENT_QUOTES, 'UTF-8');
        }

        return $validated;
    }
}
