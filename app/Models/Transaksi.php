<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Transaksi extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'transaksis';

    protected $guarded = ['id'];

    /**
     * The "booted" method of the model.
     * Digunakan untuk menjalankan logika otomatis saat event tertentu terjadi.
     */
    protected static function booted()
    {
        // Event 'creating' berjalan tepat SEBELUM data disimpan ke database
        static::creating(function ($transaksi) {
            // Hanya generate kode baru jika field KodeTransaksi masih kosong/null
            if (empty($transaksi->KodeTransaksi)) {
                $transaksi->KodeTransaksi = self::generateKodeTransaksi();
            }
        });
    }

    /**
     * Fungsi generate kode transaksi otomatis.
     * Format: TRX + YY + MM + XXX (Contoh: TRX2607001)
     */
    public static function generateKodeTransaksi()
    {
        $year = date('y');   // 2 digit tahun (misal: 26)
        $month = date('m');  // 2 digit bulan (misal: 07)
        $prefix = "TRX{$year}{$month}";


        return DB::transaction(function () use ($prefix) {


            $lastTransaksi = Transaksi::withTrashed()
                ->where('KodeTransaksi', 'like', $prefix . '%')
                ->orderByDesc('KodeTransaksi')
                ->lockForUpdate()
                ->first();

            if ($lastTransaksi && preg_match('/^' . preg_quote($prefix, '/') . '(\d{3,})$/', $lastTransaksi->KodeTransaksi, $matches)) {
                $nextNumber = (int) $matches[1] + 1;
            } else {
                $nextNumber = 1;
            }

            $newKode = $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);


            while (Transaksi::withTrashed()->where('KodeTransaksi', $newKode)->exists()) {
                $nextNumber++;
                $newKode = $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
            }

            return $newKode;
        });
    }

    // Relasi ke Ekspedisi (Opsional, jika nanti Anda ubah kolom 'Ekspedisi' menjadi 'ekspedisi_id')
    public function ekspedisi()
    {
        return $this->belongsTo(Ekspedisi::class, 'Ekspedisi');
    }
}
