<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function Up()
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();

            // Informasi Tenant
            $table->string('Nama');
            $table->string('Kode', 50)->unique();
            $table->string('Email', 100)->nullable();
            $table->string('Telepon', 50)->nullable();
            $table->text('Alamat')->nullable();

            // Kontak Person / PIC Tenant
            $table->string('NamaPIC', 100)->nullable()->comment('Nama PIC/Kontak Person utama');
            $table->string('EmailPIC', 100)->nullable();
            $table->string('TeleponPIC', 50)->nullable();
            $table->text('AlamatPIC')->nullable();
            // Join & Referal
            $table->date('TanggalJoin');
            $table->string('KodeReferal', 50)->unique()->nullable();
            // Subscription
            $table->enum('StatusSubscription', ['Aktif', 'Nonaktif', 'Expired'])->default('Aktif');
            $table->date('TanggalMulaiSubscription')->nullable();
            $table->date('TanggalAkhirSubscription')->nullable();

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
        Schema::dropIfExists('tenants');
    }
};
