<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Facility;

class FacilitySeeder extends Seeder
{
    public function run(): void
    {
        Facility::query()->delete();

        $data = [
            [
                'nama' => 'Polrestabes Medan',
                'kategori' => 'keamanan',
                'alamat' => 'Jl. HM. Said No.1',
                'lat' => 3.593630,
                'lng' => 98.681144
            ],
            [
                'nama' => 'Polsek Medan Baru',
                'kategori' => 'keamanan',
                'alamat' => 'Jl. Baru No.14',
                'lat' => 3.585721,
                'lng' => 98.660142
            ],
            [
                'nama' => 'RSUD Dr. Pirngadi',
                'kategori' => 'rumah_sakit',
                'alamat' => 'Jl. Prof. HM. Yamin No.47',
                'lat' => 3.596644,
                'lng' => 98.685325
            ],
            [
                'nama' => 'RSUP H. Adam Malik',
                'kategori' => 'rumah_sakit',
                'alamat' => 'Jl. Bunga Lau No.17',
                'lat' => 3.518600,
                'lng' => 98.607400
            ],
        ];

        foreach ($data as $val) {
            Facility::create($val);
        }
    }
}