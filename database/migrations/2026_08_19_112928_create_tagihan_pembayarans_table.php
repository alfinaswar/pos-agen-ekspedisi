<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function Up()
    {
        Schema::create('tagihan_pembayarans', function (Blueprint $Table) {
            $Table->id();

            // Relasi ke Tenant
            $Table->string('TenantId');
            // Informasi Tagihan
            $Table->string('NomorTagihan')->unique();
            $Table->string('PeriodeBulan', 50); // Contoh: "Oktober 2023" atau "2023-10"
            $Table->date('TanggalJatuhTempo');
            $Table->decimal('JumlahTagihan', 15, 2);

            // Status & Pembayaran
            $Table->enum('StatusPembayaran', ['Belum Bayar', 'Lunas', 'Terlambat'])->default('Belum Bayar');
            $Table->date('TanggalPembayaran')->nullable();
            $Table->date('BerlakuHingga')->nullable();
            $Table->string('BuktiPembayaran')->nullable();
            $Table->text('Catatan')->nullable();
            //verif
            $Table->enum('Status', ['Y', 'N', 'N/A'])->nullable();
            $Table->text('CatatanVerifikasi')->nullable();
            $Table->dateTime('VerifPada')->nullable();
            $Table->string('VerifOleh')->nullable();
            // Audit Trail (Sesuai style kamu)
            $Table->string('UserCreate', 200)->nullable();
            $Table->string('UserUpdate', 200)->nullable();
            $Table->string('UserDelete', 200)->nullable();

            $Table->softDeletes();
            $Table->timestamps();

            // Indexes untuk performa
            $Table->index('TenantId');
            $Table->index('StatusPembayaran');
            $Table->index('TanggalJatuhTempo');
        });
    }

    public function Down()
    {
        Schema::dropIfExists('tagihan_pembayarans');
    }
};
