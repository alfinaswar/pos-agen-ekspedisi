<?php

namespace App\Models;

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
