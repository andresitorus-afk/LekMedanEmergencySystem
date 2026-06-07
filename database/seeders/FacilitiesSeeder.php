<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FacilitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Bersihkan data lama terlebih dahulu agar tidak terjadi duplikat jika dijalankan ulang
        DB::table('facilities')->truncate();

        $data = [
            [
                'nama' => 'RS Umum Pusat Adam Malik',
                'kategori' => 'rumah_sakit',
                'alamat' => 'Jl. Bunga Lau No.17, Medan Tuntungan',
                'lat' => 3.52120000,
                'lng' => 98.61830000,
                'telepon' => '061-8360143',
            ],
            [
                'nama' => 'Rumah Sakit Universitas Sumatera Utara (USU)',
                'kategori' => 'rumah_sakit',
                'alamat' => 'Jl. Dr. Mansyur No.66, Medan Baru',
                'lat' => 3.56530000,
                'lng' => 98.65650000,
                'telepon' => '061-8218926',
            ],
            [
                'nama' => 'RSUD Dr. Pirngadi',
                'kategori' => 'rumah_sakit',
                'alamat' => 'Jl. Prof. HM. Yamin Sh No.47, Perintis, Medan Timur',
                'lat' => 3.59640000,
                'lng' => 98.68450000,
                'telepon' => '061-4532263',
            ],
            [
                'nama' => 'Polsek Medan Baru',
                'kategori' => 'keamanan',
                'alamat' => 'Jl. Nibung Utama No.1, Medan Petisah',
                'lat' => 3.58910000,
                'lng' => 98.66310000,
                'telepon' => '061-4523141',
            ],
            [
                'nama' => 'Polsek Medan Kota',
                'kategori' => 'keamanan',
                'alamat' => 'Jl. Stadion No.1, Amaliun, Medan Kota',
                'lat' => 3.57520000,
                'lng' => 98.68810000,
                'telepon' => '061-7366770',
            ],
            [
                'nama' => 'Polsek Medan Sunggal',
                'kategori' => 'keamanan',
                'alamat' => 'Jl. T.B. Simatupang No.240, Sunggal, Medan Sunggal',
                'lat' => 3.56270000,
                'lng' => 98.61190000,
                'telepon' => '061-8451515',
            ],
            [
                'nama' => 'RS Umum Daerah Dr. Pirngadi',
                'kategori' => 'rumah_sakit',
                'alamat' => 'Jl. Prof. HM. Yamin Sh No.47, Perintis, Medan Timur',
                'lat' => 3.59640000,
                'lng' => 98.68450000,
                'telepon' => '061-4532263',
            ],
            [
                'nama' => 'RS Umum Siloam Dhirga Surya',
                'kategori' => 'rumah_sakit',
                'alamat' => 'Jl. Imam Bonjol No.6, Petisah Tengah, Medan Petisah',
                'lat' => 3.58490000,
                'lng' => 98.67380000,
                'telepon' => '061-88881900',
            ],
            [
                'nama' => 'RS Umum Columbia Asia Medan',
                'kategori' => 'rumah_sakit',
                'alamat' => 'Jl. Listrik No.2A, Petisah Tengah, Medan Petisah',
                'lat' => 3.58610000,
                'lng' => 98.67160000,
                'telepon' => '061-4566368',
            ],
            [
                'nama' => 'RS Umum Mitra Medika Amplas',
                'kategori' => 'rumah_sakit',
                'alamat' => 'Jl. Sisingamangaraja No.11, Harjosari I, Medan Amplas',
                'lat' => 3.54110000,
                'lng' => 98.71140000,
                'telepon' => '061-42733111',
            ],
            [
                'nama' => 'RS Umum Hermina Medan',
                'kategori' => 'rumah_sakit',
                'alamat' => 'Jl. Asrama No.7, Sei Sikambing C II, Medan Helvetia',
                'lat' => 3.59820000,
                'lng' => 98.62510000,
                'telepon' => '061-80862525',
            ],
            [
                'nama' => 'RS Umum Murni Teguh Memorial Hospital',
                'kategori' => 'rumah_sakit',
                'alamat' => 'Jl. Jawa No.2, Gang Buntu, Medan Timur',
                'lat' => 3.59220000,
                'lng' => 98.68110000,
                'telepon' => '061-80501888',
            ],
            [
                'nama' => 'Polsek Medan Helvetia',
                'kategori' => 'keamanan',
                'alamat' => 'Jl. Matahari Raya No.99, Helvetia Tengah, Medan Helvetia',
                'lat' => 3.60540000,
                'lng' => 98.63220000,
                'telepon' => '061-8453224',
            ],
            [
                'nama' => 'Polsek Medan Barat',
                'kategori' => 'keamanan',
                'alamat' => 'Jl. Barraya No.4, Silalas, Medan Barat',
                'lat' => 3.60210000,
                'lng' => 98.67280000,
                'telepon' => '061-6614776',
            ],
            [
                'nama' => 'Polsek Medan Amplas',
                'kategori' => 'keamanan',
                'alamat' => 'Jl. Panglima Denai No.12, Amplas, Medan Amplas',
                'lat' => 3.54040000,
                'lng' => 98.71970000,
                'telepon' => '061-7870817',
            ],
            [
                'nama' => 'Polsek Patumbak',
                'kategori' => 'keamanan',
                'alamat' => 'Jl. Dr. Cipto No.1, Anggrung, Medan Polonia',
                'lat' => 3.56840000,
                'lng' => 98.68140000,
                'telepon' => '061-4513324',
            ]

        ];

        foreach ($data as $item) {
            DB::table('facilities')->insert([
                'nama' => $item['nama'],
                'kategori' => $item['kategori'],
                'alamat' => $item['alamat'],
                'lat' => $item['lat'],
                'lng' => $item['lng'],
                'telepon' => $item['telepon'],
                
                // OTOMATISASI POSTGIS: Mengubah data angka menjadi biner spasial POINT SRID 4326
                'geom' => DB::raw("ST_SetSRID(ST_MakePoint({$item['lng']}, {$item['lat']}), 4326)"),
                
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}