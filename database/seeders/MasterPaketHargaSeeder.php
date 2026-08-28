<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MasterPaketHargaSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        $user = 'system'; // atau bisa diganti dengan user yang sesuai

        $packages = [
            [
                'NamaPaket' => 'Paket Bulanan',
                'KodePaket' => 'PAKET-BULANAN',
                'Deskripsi' => 'Paket berlangganan bulanan dengan promo spesial 50% untuk 3 bulan pertama',
                'Harga' => 125000,
                'DurasiBulan' => 1,
                'Fitur' => json_encode([
                    'Rekap transaksi & verifikasi',
                    'Multi-admin & multi-outlet',
                    'Laporan harian & bulanan',
                    'Absensi, reimburse, pekerjaan kurir'
                ], JSON_UNESCAPED_UNICODE),
                'Status' => 'Aktif',
                'UserCreate' => $user,
                'UserUpdate' => $user,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'NamaPaket' => 'Paket Tahunan',
                'KodePaket' => 'PAKET-TAHUNAN',
                'Deskripsi' => 'Paket berlangganan tahunan - Paling Hemat! Hemat Rp1.240.000 per tahun (setara Rp104.000/bulan)',
                'Harga' => 1250000,
                'DurasiBulan' => 12,
                'Fitur' => json_encode([
                    'Rekap transaksi & verifikasi',
                    'Multi-admin & multi-outlet',
                    'Laporan harian & bulanan',
                    'Absensi, reimburse, pekerjaan kurir'
                ], JSON_UNESCAPED_UNICODE),
                'Status' => 'Aktif',
                'UserCreate' => $user,
                'UserUpdate' => $user,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('master_paket_hargas')->insert($packages);
    }
}
