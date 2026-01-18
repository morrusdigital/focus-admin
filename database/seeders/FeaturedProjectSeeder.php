<?php

namespace Database\Seeders;

use App\Models\FeaturedProject;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FeaturedProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        FeaturedProject::truncate();

        FeaturedProject::insert([
            [
                'title' => 'Ruang Direksi HM Sampoerna',
                'scope' => 'Finishing',
                'description' => 'Pekerjaan infrastruktur menyeluruh untuk kawasan resort',
                'image' => '/images/hero/sampoerna.webp',
                'size' => '> 15.000 m²',
            ],
            [
                'title' => 'Konstruksi Baja',
                'scope' => 'Design and Build',
                'description' => 'Pekerjaan struktur baja untuk fasilitas industri',
                'image' => 'https://mandorpro.id/wp-content/uploads/2024/07/harga-borongan-baja-1.webp',
                'size' => '± 5.000 m²',
            ],
            [
                'title' => 'Proyek PEMKOT Surabaya',
                'scope' => 'Infrastructure',
                'description' => 'Paket pekerjaan infrastruktur untuk Pemerintah Kota Surabaya',
                'image' => 'https://asiacon.co.id/wp-content/uploads/2024/12/Efisiensi-Biaya-dengan-Menggunakan-U-Ditch-Beton-dalam-Proyek-Konstruksi-1_11zon-1.jpg',
                'size' => '± 12.000 m²',
            ],
            [
                'title' => 'Icon Mall Gresik',
                'scope' => 'Finishing',
                'description' => 'Pekerjaan finishing area komersial pusat perbelanjaan',
                'image' => 'https://www.rumah123.com/seo-cms/assets/large_Perpaduan_Konsep_Natural_dan_Futuristik_285437ee5c/large_Perpaduan_Konsep_Natural_dan_Futuristik_285437ee5c.png',
                'size' => '± 8.000 m²',
            ],
            [
                'title' => 'PT. Hertz Flavors Makmur Indonesia',
                'scope' => 'Design & Build • Acoustic Installation • Finishing',
                'description' => 'Pekerjaan rancang bangun fasilitas kantor',
                'image' => 'https://tobaccoreporter.com/wp-content/uploads/2023/11/OUTSIDE-FACTORY.jpg',
                'size' => '± 6.000 m²',
            ],
        ]);
    }
}
