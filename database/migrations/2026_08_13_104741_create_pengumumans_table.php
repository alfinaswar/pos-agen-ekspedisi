<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pengumumans', function (Blueprint $table) {
            $table->id();
            $table->string('Judul');
            $table->longtext('Isi');
            $table->enum('Kategori', ['Umum', 'Penting', 'Darurat'])->default('Umum');
            $table->string('TargetDivisi')->nullable();
            $table->string('TargetRole')->nullable();
            $table->string('Tanggal');
            $table->string('Gambar')->nullable();

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
        Schema::dropIfExists('pengumumans');
    }
};
