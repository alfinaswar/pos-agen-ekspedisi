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
        Schema::create('transaksis', function (Blueprint $table) {
            $table->id();
            $table->text('KodeTransaksi')->nullable();
            $table->dateTime('Tanggal')->nullable();
            $table->string('Ekspedisi')->nullable();
            $table->string('NoResi')->nullable();
            $table->enum('Metode', ['Tunai', 'Non-Tunai', 'COD'])->nullable()->default('Tunai');
            $table->decimal('Pendapatan', 15, 2)->nullable()->default(0.00);
            $table->decimal('Diskon', 15, 2)->nullable()->default(0.00);
            $table->decimal('PendapatanBersih', 15, 2)->nullable()->default(0.00);
            $table->text('KodeBayar')->nullable()->comment('Diisi jika Metode Non-Tunai');
            $table->string('BuktiBayar')->nullable()->comment('Upload bukti pembayaran jika metode non-tunai');
            $table->text('Keterangan')->nullable();
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
        Schema::dropIfExists('transaksis');
    }
};
