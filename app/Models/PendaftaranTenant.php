<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PendaftaranTenant extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pendaftaran_tenants';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public function getPaket()
    {
        return $this->belongsTo(MasterPaketHarga::class, 'Paket');
    }

    /**
     * Generate a new registration code with format REG00000001
     *
     * @return string
     */
    public static function generateKode()
    {
        // Counting all including soft deleted records
        $count = self::withTrashed()->count() + 1;
        return 'REG' . str_pad($count, 8, '0', STR_PAD_LEFT);
    }

    /**
     * Boot the model and assign Kode automatically if not set.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($pendaftaran) {
            if (empty($pendaftaran->Kode)) {
                $pendaftaran->Kode = self::generateKode();
            }
        });
    }
}
