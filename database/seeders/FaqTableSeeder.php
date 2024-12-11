<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class FaqTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faqs = [
            ['question' => 'Siapa saja yang bisa diusulkan untuk Program Bedah Rumah ?', 'answer' => 'Bantuan bedah rumah dapat diterima oleh individu berkeluarga yang memenuhi kriteria kelayakan yang telah ditetapkan.', 'order' => 1],
            ['question' => 'Apa saja kriteria kelayakan penerima bantuan bedah rumah ?', 'answer' => 'Kriteria ini termasuk memiliki rumah yang rusak atau tidak layak huni, serta tidak memiliki kemampuan untuk memperbaikinya sendiri.', 'order' => 2],
            ['question' => 'Bagaimana mekanisme pendataan dan seleksi penerima bantuan ?', 'answer' => 'Proses seleksi penerima bantuan bedah rumah dilakukan oleh tim survei dari Dinas PUPR Perkim Balangan dan Perangkat Desa', 'order' => 3],
            ['question' => 'Bagaimana cara mendaftar untuk mendapatkan bantuan bedah rumah ?', 'answer' => 'Untuk mengajukan bantuan bedah rumah, Anda dapat mengisi formulir pengajuan yang tersedia di situs web kami atau menghubungi kantor Dinas PUPR Perkim Kab. Balangan.', 'order' => 4],
            ['question' => 'Bagaimana jika saya belum memiliki sertifikat tanah ?', 'answer' => 'Persyaratan calon penerima bantuan adalah memilki alas hak yang sah. Jika ada kendala atau legalitas yang meragukan dapat berkoordinasi dengan Lurah/Kepala Desa', 'order' => 5],
            ['question' => 'Siapa yang akan mengerjakan perbaikan rumah ?', 'answer' => 'Perbaikan rumah akan dilakukan secara swakelola.', 'order' => 6],
            ['question' => 'Apakah data pada aplikasi ini dapat diakses oleh publik, dan apakah ada batasan akses tertentu?', 'answer' => 'Data pada aplikasi ini sebagain dapat diakses oleh publik sesuai aturan dan kewenangan Pejabat Pengelola Informasi Data. Untuk data yang bersifat pribadi maka kami mengupayakan untuk Perlindunagn Data Pribadi. Untuk data yang sifatnya khusus, Anda memerlukan bersurat', 'order' => 7]
        ];

        Faq::insert($faqs);
    }
}
