<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TagihanPembayaran extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tagihan_pembayarans';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    protected $casts = [
        'TanggalJatuhTempo'          => 'date',
        'TanggalPembayaran'          => 'date',
        'BerlakuHingga'              => 'date',
        'PaymentExpiredAt'           => 'datetime',
        'PaidAt'                     => 'datetime',
        'VerifPada'                  => 'datetime',
        'JumlahTagihan'              => 'integer',
        'TanggalJoin'                => 'datetime',
        'TanggalMulaiSubscription'   => 'datetime', // ← WAJIB ADA
        'TanggalAkhirSubscription'   => 'datetime', // ← WAJIB ADA
    ];

    // Nomor tagihan akan di-generate otomatis dengan format: INV26(TAHUN)(BULAN)(NO URUT)
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Generate nomor tagihan jika belum ada
            if (empty($model->NomorTagihan)) {
                $tahun = now()->format('Y');
                $bulan = now()->format('m');

                // Cari nomor urut terakhir pada tahun dan bulan yang sama
                $last = self::whereYear('created_at', $tahun)
                    ->whereMonth('created_at', $bulan)
                    ->orderBy('NomorTagihan', 'desc')
                    ->first();

                // Ambil no urut dari nomor tagihan terakhir (jika ada)
                if ($last && preg_match('/INV26' . $tahun . $bulan . '(\d+)$/', $last->NomorTagihan, $match)) {
                    $nextUrut = str_pad(((int) $match[1]) + 1, 4, '0', STR_PAD_LEFT);
                } else {
                    $nextUrut = '0001';
                }

                // Format: INV26{TAHUN}{BULAN}{NO_URUT}
                $model->NomorTagihan = 'INV26' . $tahun . $bulan . $nextUrut;
            }
        });
    }

    public function Tenant()
    {
        return $this->hasOne(Tenant::class, 'Kode', 'KodeTenant');
    }
    public function Paket()
    {
        return $this->belongsTo(MasterPaketHarga::class, 'Paket', 'id');
    }
    public function ScopeBelumLunas($Query)
    {
        return $Query->whereIn('StatusPembayaran', ['Belum Bayar', 'Terlambat']);
    }
}
