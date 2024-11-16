<?php

namespace Database\Seeders;

use App\Models\Gallery;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GalleriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('galleries')->insert([
            [
                'title' => 'Pemandangan Alam',
                'slug' => 'pemandangan-alam',
                'image' => 'https://via.placeholder.com/600x400',
                'description' => 'Gambaran indah pemandangan alam yang menakjubkan.',
                'location' => 'Gunung Bromo',
                'latitude' => '-7.9421',
                'longitude' => '112.9536',
                'user_id' => 1, // Pastikan user dengan ID ini ada di tabel users
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Matahari Terbenam',
                'slug' => 'matahari-terbenam',
                'image' => 'https://via.placeholder.com/600x400',
                'description' => 'Pesona matahari terbenam di pantai.',
                'location' => 'Pantai Kuta',
                'latitude' => '-8.4095',
                'longitude' => '115.1889',
                'user_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Kota Jakarta di Malam Hari',
                'slug' => 'kota-jakarta-malam-hari',
                'image' => 'https://via.placeholder.com/600x400',
                'description' => 'Keindahan Jakarta saat malam hari.',
                'location' => 'Jakarta',
                'latitude' => '-6.2088',
                'longitude' => '106.8456',
                'user_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Keindahan Alam',
                'slug' => 'keindahan-alam',
                'image' => 'https://via.placeholder.com/600x400',
                'description' => 'Alam yang asri dan damai.',
                'location' => 'Danau Toba',
                'latitude' => '2.2114',
                'longitude' => '98.9637',
                'user_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Bunga-bunga Musim Semi',
                'slug' => 'bunga-musim-semi',
                'image' => 'https://via.placeholder.com/600x400',
                'description' => 'Kecantikan bunga-bunga bermekaran.',
                'location' => 'Kebun Raya Bogor',
                'latitude' => '-6.5902',
                'longitude' => '106.7627',
                'user_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
