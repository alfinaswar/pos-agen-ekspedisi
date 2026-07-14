<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ekspedisi;
use App\Models\User;

class EkspedisiSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first(); // Ambil user pertama sebagai creator

        $ekspedisi = [
            ['NamaEkspedisi' => 'J&T Express', 'Deskripsi' => 'J&T Express Indonesia'],
            ['NamaEkspedisi' => 'J&T Cargo', 'Deskripsi' => 'J&T Cargo Indonesia'],
            ['NamaEkspedisi' => 'JNE Express', 'Deskripsi' => 'JNE Express Indonesia'],
            ['NamaEkspedisi' => 'Lion Parcel', 'Deskripsi' => 'Lion Parcel Indonesia'],
            ['NamaEkspedisi' => 'Wahana Express', 'Deskripsi' => 'Wahana Express Indonesia'],
            ['NamaEkspedisi' => 'Tiki', 'Deskripsi' => 'Tiki Indonesia'],
            ['NamaEkspedisi' => 'Si Cepat', 'Deskripsi' => 'Si Cepat Express Indonesia'],
            ['NamaEkspedisi' => 'Pos Indonesia', 'Deskripsi' => 'Pos Indonesia'],
            ['NamaEkspedisi' => 'Jasa Packing', 'Deskripsi' => 'Layanan Jasa Packing'],
            ['NamaEkspedisi' => 'Lain2', 'Deskripsi' => 'Ekspedisi Lainnya'],
        ];

        foreach ($ekspedisi as $data) {
            Ekspedisi::create([
                'NamaEkspedisi' => $data['NamaEkspedisi'],
                'Deskripsi' => $data['Deskripsi'],
                'UserCreate' => $user->id,
            ]);
        }
    }
}
