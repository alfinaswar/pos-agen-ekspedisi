<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Absensi extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'absensis';

    protected $guarded = ['id'];
    public function getUser()
    {
        return $this->belongsTo(User::class, 'Nama');
    }
}
