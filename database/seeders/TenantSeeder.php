<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Tenant;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat 1 Tenant saja sesuai permintaan
        Tenant::create([
            'Nama' => 'Anggit Yuda Pradana',
            'Kode' => 'TEN-0001',
            'Email' => 'anggityudapradana@gmail.com',
            'Telepon' => '08985326712',
            'Alamat' => 'Alamat Default Anggit Yuda Pradana',
            'NamaPIC' => 'Anggit Yuda Pradana',
            'EmailPIC' => 'anggityudapradana@gmail.com',
            'TeleponPIC' => '08985326712',
            'AlamatPIC' => 'Alamat Default Anggit Yuda Pradana',
            'TanggalJoin' => Carbon::now()->format('Y-m-d'),
            'KodeReferal' => 'REF-ANGGITYUDA',
            'StatusSubscription' => 'Aktif',
            'TanggalMulaiSubscription' => Carbon::now()->format('Y-m-d'),
            'TanggalAkhirSubscription' => Carbon::now()->addYear()->format('Y-m-d'),
            'UserCreate' => 'System',
        ]);
    }
}
