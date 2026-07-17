<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Ekspedisi extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ekspedisi';

    protected $guarded = ['id'];

    public function getUserCreate()
    {
        return $this->belongsTo(User::class, 'UserCreate');
    }

    // public function Transaksis()
    // {
    //     return $this->hasMany(Transaksi::class, 'EkspedisiId');
    // }
}
