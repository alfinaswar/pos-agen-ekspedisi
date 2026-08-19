<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pendaftaran_tenants', function (Blueprint $table) {
            $table->id();
            $table->string('Kode')->nullable();
            $table->string('Nama')->nullable();
            $table->string('Email')->nullable();
            $table->string('Alamat')->nullable();
            $table->string('NamaPIC')->nullable();
            $table->string('EmailPIC')->nullable();
            $table->string('AlamatPIC')->nullable();
            $table->string('BuktiPembayaran')->nullable();
            $table->enum('Status', ['Y', 'N','N/A'])->nullable();
            $table->text('CatatanVerifikasi')->nullable();
            $table->dateTime('VerifPada')->nullable();
            $table->string('VerifOleh')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pendaftaran_tenants');
    }
};
