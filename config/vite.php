<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Build Path
    |--------------------------------------------------------------------------
    |
    | Path tempat output file build Vite disimpan. Biasanya "public/build".
    |
    */
    'build_path' => 'build',
    'manifest_path' => 'build/manifest.json',

    /*
    |--------------------------------------------------------------------------
    | Asset URL
    |--------------------------------------------------------------------------
    |
    | URL yang digunakan untuk memuat aset Vite. Secara default, akan mengambil
    | dari environment variable ASSET_URL. Anda bisa mengatur URL CDN di sini
    | jika diperlukan.
    |
    */
    'asset_url' => env('ASSET_URL', ''),
];
