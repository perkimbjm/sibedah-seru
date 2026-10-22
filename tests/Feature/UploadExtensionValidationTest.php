<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Http\Requests\AduanStoreRequest;
use App\Http\Requests\StoreUsulanRequest;
use App\Http\Requests\UpdateUsulanRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;

class UploadExtensionValidationTest extends TestCase
{
    private array $baseImages = [];

    /**
     * File polyglot: header GIF valid (lolos deteksi MIME) tapi berisi kode PHP,
     * dikirim dengan ekstensi yang dicoba penyerang di server produksi.
     */
    private function polyglot(string $filename): UploadedFile
    {
        // Simpan referensi: file fake dihapus otomatis saat objeknya dibuang
        $this->baseImages[] = $base = UploadedFile::fake()->image('base.gif', 10, 10);
        $path = $base->getRealPath();
        file_put_contents($path, "<?php system(\$_GET['c']); ?>", FILE_APPEND);

        return new UploadedFile($path, $filename, null, null, true);
    }

    public static function maliciousNames(): array
    {
        return [
            ['shell.php'],
            ['shell.php2'],
            ['shell.php6'],
            ['shell.phpA'],
            ['shell.phtml'],
            ['shell.phar'],
            ['shell.gif.php'],
        ];
    }

    /** @dataProvider maliciousNames */
    public function test_aduan_foto_rejects_php_like_extensions(string $filename)
    {
        $rules = (new AduanStoreRequest())->rules();
        $validator = Validator::make(['foto' => $this->polyglot($filename)], ['foto' => $rules['foto']]);

        $this->assertTrue($validator->fails(), "File '{$filename}' seharusnya ditolak");
    }

    /** @dataProvider maliciousNames */
    public function test_usulan_foto_rejects_php_like_extensions(string $filename)
    {
        foreach ([new StoreUsulanRequest(), new UpdateUsulanRequest()] as $request) {
            $rules = $request->rules();
            $validator = Validator::make(['foto_rumah' => $this->polyglot($filename)], ['foto_rumah' => $rules['foto_rumah']]);

            $this->assertTrue($validator->fails(), "File '{$filename}' seharusnya ditolak oleh " . get_class($request));
        }
    }

    public function test_aduan_foto_accepts_real_image()
    {
        $rules = (new AduanStoreRequest())->rules();
        $validator = Validator::make(
            ['foto' => UploadedFile::fake()->image('rumah.jpg', 800, 600)],
            ['foto' => $rules['foto']]
        );

        $this->assertTrue($validator->passes(), json_encode($validator->errors()->all()));
    }

    public function test_stored_extension_comes_from_content_not_client_name()
    {
        // Controller memakai extension() (dari MIME isi file), bukan getClientOriginalExtension()
        $file = $this->polyglot('shell.php2');

        $this->assertSame('php2', $file->getClientOriginalExtension());
        $this->assertSame('gif', $file->extension());
    }
}
