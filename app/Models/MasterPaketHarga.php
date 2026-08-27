<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterPaketHarga extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'master_paket_hargas';

   /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
   protected $guarded = ['id'];

    protected $Casts = [
        'Harga' => 'decimal:2',
        'Fitur' => 'array',
    ];

    public function ScopeAktif($Query)
    {
        return $Query->where('Status', 'Aktif');
    }
}
