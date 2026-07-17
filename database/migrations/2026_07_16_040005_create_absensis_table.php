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
        Schema::create('absensis', function (Blueprint $table) {
            $table->id();
            $table->string('Nama', 100);
            $table->string('Divisi', 100);
            $table->string('NoHp', 20);
            $table->date('Tanggal');
            $table->time('JamHadir')->nullable();
            $table->time('JamPulang')->nullable();
            $table->string('MulaiLembur', 50)->nullable()->comment('Contoh: 2 Jam, 30 Menit');
            $table->string('SelesaiLembur', 50)->nullable()->comment('Contoh: 2 Jam, 30 Menit');
            $table->enum('Status', ['H', 'I', 'S', 'TK'])->default('H')->comment('H: Hadir, I: Izin, S: Sakit, TK: Tanpa Keterangan');
            $table->enum('Lembur', ['Y', 'N'])->nullable()->default('N');
            $table->string('UserCreate', 200)->nullable();
            $table->string('UserUpdate', 200)->nullable();
            $table->string('UserDelete', 200)->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensis');
    }
};
