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
        static::creating(function ($transaksi) {
            if (empty($transaksi->KodeTransaksi)) {
                $transaksi->KodeTransaksi = self::generateKodeTransaksi();
            }
        });
    }

    public static function generateKodeTransaksi()
    {
        $year = date('y');
        $month = date('m');
        $prefix = "TRX{$year}{$month}";

        // DB::transaction dipertahankan di sini agar lockForUpdate bekerja,
        // tapi query sudah dioptimalkan.
        return DB::transaction(function () use ($prefix) {
            // 1. Hapus withTrashed() kecuali bisnis logic MEMAKSA menghitung data terhapus
            // 2. Gunakan value() alih-alih first() untuk efisiensi memori & query
            $lastKode = Transaksi::where('KodeTransaksi', 'like', $prefix . '%')
                ->orderByDesc('KodeTransaksi')
                ->lockForUpdate()
                ->value('KodeTransaksi');

            if ($lastKode && preg_match('/^' . preg_quote($prefix, '/') . '(\d{3,})$/', $lastKode, $matches)) {
                $nextNumber = (int) $matches[1] + 1;
            } else {
                $nextNumber = 1;
            }

            // Hapus loop while, karena lockForUpdate sudah menjamin tidak ada duplikat
            return $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        });
    }

    // Relasi ke Ekspedisi (Opsional, jika nanti Anda ubah kolom 'Ekspedisi' menjadi 'ekspedisi_id')
    public function ekspedisi()
    {
        return $this->belongsTo(Ekspedisi::class, 'Ekspedisi');
    }

    public function userFinance()
    {
        return $this->belongsTo(User::class, 'UserFinance');
    }

    public function userCreate()
    {
        return $this->belongsTo(User::class, 'UserCreate');
    }

    public function getDivisi()
    {
        return $this->belongsTo(Divisi::class, 'Divisi');
    }
}
