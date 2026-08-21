<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tenant extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tenants';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];


    public function GetRemainingDays()
    {
        if (!$this->TanggalAkhirSubscription) {
            return null;
        }

        return Carbon::now()->diffInDays($this->TanggalAkhirSubscription, false);
    }
    public function IsSubscriptionExpired()
    {
        if (!$this->TanggalAkhirSubscription) {
            return false;
        }

        return Carbon::parse($this->TanggalAkhirSubscription)->isPast();
    }
    public function IsSubscriptionExpiringSoon($Days = 7)
    {
        if (!$this->TanggalAkhirSubscription) {
            return false;
        }

        if ($this->StatusSubscription !== 'Aktif') {
            return false;
        }

        $RemainingDays = Carbon::now()->diffInDays($this->TanggalAkhirSubscription, false);

        // Return true jika sisa hari antara 0 sampai $Days
        return $RemainingDays >= 0 && $RemainingDays <= $Days;
    }
    /**
     * Boot method for the model.
     * Generate 'Kode' otomatis saat data diinput.
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($tenant) {
            if (empty($tenant->Kode)) {
                $tenant->Kode = 'TEN-' . str_pad($tenant->id, 4, '0', STR_PAD_LEFT);
                $tenant->save();
            }
        });

    }
}
