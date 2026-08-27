<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function Up()
    {
        Schema::create('master_paket_hargas', function (Blueprint $table) {
            $table->id();

            // Informasi Paket
            $table->string('NamaPaket');
            $table->string('KodePaket', 50)->unique();
            $table->text('Deskripsi')->nullable();

            // Harga & Durasi
            $table->decimal('Harga', 15, 2);
            $table->integer('DurasiBulan')->default(1); // Dalam bulan

            // Fitur (Disimpan sebagai JSON Array)
            $table->json('Fitur')->nullable();

            // Status
            $table->enum('Status', ['Aktif', 'Nonaktif'])->default('Aktif');

            // Audit Trail (Sesuai style kamu)
            $table->string('UserCreate', 200)->nullable();
            $table->string('UserUpdate', 200)->nullable();
            $table->string('UserDelete', 200)->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function Down()
    {
        Schema::dropIfExists('master_paket_hargas');
    }
};
