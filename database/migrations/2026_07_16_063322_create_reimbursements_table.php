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
        Schema::create('reimbursements', function (Blueprint $table) {
            $table->id();
            $table->date('Tanggal')->nullable();
            $table->string('Nama', 100)->nullable();
            $table->text('Item')->nullable();
            $table->decimal('Nominal', 15, 2)->default(0)->nullable();
            $table->enum('Status', ['Menunggu', 'Ditolak', 'Dibayar'])->default('Menunggu')->nullable();
            $table->string('BuktiUpload')->nullable()->comment('File bukti pembayaran/struk');
            $table->string('OwnerUpdate', 200)->nullable()->comment('Nama Owner yang mengubah status');
            $table->string('UserCreate', 200)->nullable();
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
        Schema::dropIfExists('reimbursements');
    }
};
