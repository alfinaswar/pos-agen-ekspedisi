<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function Up()
    {
        Schema::create('pekerjaan_kurirs', function (Blueprint $table) {
            $table->id();
            $table->date('Tanggal')->nullable();
            $table->time('Jam')->nullable();
            $table->string('Pekerjaan')->nullable();
            $table->string('DariLokasi')->nullable();
            $table->string('Tujuan')->nullable();
            $table->integer('JumlahPaket')->default(0)->nullable();
            $table->string('Durasi')->nullable();
            $table->text('Keterangan')->nullable();
            $table->string('BuktiFoto')->nullable();
            $table->string('IdUser', 200)->nullable();
            $table->string('UserCreate', 200)->nullable();
            $table->string('UserUpdate', 200)->nullable();
            $table->string('UserDelete', 200)->nullable();
            $table->string('Tenant', 200)->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

    }

    public function Down()
    {
        Schema::dropIfExists('PekerjaanKurirs');
    }
};
