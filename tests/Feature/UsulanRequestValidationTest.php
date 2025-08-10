<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Http\Requests\StoreUsulanRequest;
use App\Http\Requests\UpdateUsulanRequest;
use Illuminate\Support\Facades\Validator;

class UsulanRequestValidationTest extends TestCase
{
    /** @test */
    public function test_nomor_hp_validation_with_different_formats()
    {
        $validNumbers = [
            '+6281234567890',    // +628 + 10 digit = total 14 karakter
            '6281234567890',     // 628 + 10 digit = total 13 karakter
            '081234567890',      // 08 + 10 digit = total 12 karakter
            '+62812345678',      // +628 + 8 digit (minimal)
            '+628123456789',     // +628 + 9 digit
            '+62812345678901',   // +628 + 11 digit (maksimal)
            '62812345678',       // 628 + 8 digit (minimal)
            '62812345678901',    // 628 + 11 digit (maksimal) - FIXED dari 13 digit ke 11 digit
            '0812345678',        // 08 + 8 digit (minimal)
            '0812345678901',     // 08 + 11 digit (maksimal) - FIXED dari 12 digit ke 11 digit
        ];

        $invalidNumbers = [
            '1234567890',        // Tidak diawali dengan format yang benar
            '+621234567890',     // Bukan 628 setelah +62
            '621234567890',      // Bukan 628 setelah 62
            '01234567890',       // Bukan 08 setelah 0
            '+6281234567',       // +628 + 7 digit (kurang dari 8 digit minimal)
            '+628123456789012',  // +628 + 12 digit (lebih dari 11 digit maksimal)
            '628123456789012',   // 628 + 12 digit (lebih dari 11 digit maksimal)
            '081234567890123',   // 08 + 12 digit (lebih dari 11 digit maksimal)
            '+628abcd12345',     // Mengandung huruf
            '',                  // String kosong - tapi ini nullable jadi OK
        ];

        // Test valid numbers
        foreach ($validNumbers as $number) {
            $rules = (new StoreUsulanRequest())->rules();
            $validator = Validator::make(['nomor_hp' => $number], ['nomor_hp' => $rules['nomor_hp']]);

            $this->assertTrue(
                $validator->passes(),
                "Nomor HP '{$number}' seharusnya valid tapi gagal validasi. Errors: " . json_encode($validator->errors()->all())
            );
        }

        // Test invalid numbers
        foreach ($invalidNumbers as $number) {
            if ($number === '') continue; // Skip empty string karena nullable

            $rules = (new StoreUsulanRequest())->rules();
            $validator = Validator::make(['nomor_hp' => $number], ['nomor_hp' => $rules['nomor_hp']]);

            $this->assertTrue(
                $validator->fails(),
                "Nomor HP '{$number}' seharusnya tidak valid tapi lolos validasi"
            );
        }
    }

    /** @test */
    public function test_nomor_hp_nullable_validation()
    {
        $rules = (new StoreUsulanRequest())->rules();

        // Test null value
        $validator = Validator::make(['nomor_hp' => null], ['nomor_hp' => $rules['nomor_hp']]);
        $this->assertTrue($validator->passes(), 'Nomor HP null seharusnya valid karena nullable');

        // Test empty string
        $validator = Validator::make(['nomor_hp' => ''], ['nomor_hp' => $rules['nomor_hp']]);
        $this->assertTrue($validator->passes(), 'Nomor HP empty string seharusnya valid karena nullable');

        // Test missing field
        $validator = Validator::make([], ['nomor_hp' => $rules['nomor_hp']]);
        $this->assertTrue($validator->passes(), 'Missing nomor HP field seharusnya valid karena nullable');
    }

    /** @test */
    public function test_error_messages_for_invalid_nomor_hp()
    {
        $request = new StoreUsulanRequest();
        $rules = $request->rules();
        $messages = $request->messages();

        $validator = Validator::make(
            ['nomor_hp' => '1234567890'],
            ['nomor_hp' => $rules['nomor_hp']],
            $messages
        );

        $this->assertTrue($validator->fails());
        $this->assertStringContainsString(
            'Nomor HP harus diawali dengan +628, 628, atau 08',
            $validator->errors()->first('nomor_hp')
        );
    }
}
