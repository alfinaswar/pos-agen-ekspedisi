<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pendaftaran_tenants', function (Blueprint $table) {
            // Hapus kolom BuktiPembayaran (sudah tidak diperlukan)
            if (Schema::hasColumn('pendaftaran_tenants', 'BuktiPembayaran')) {
                $table->dropColumn('BuktiPembayaran');
            }

            // Tambah kolom terkait payment gateway
            $table->string('PaymentStatus')->default('PENDING')->after('Status');
            // PENDING, PAID, EXPIRED, FAILED
            $table->string('DokuInvoiceNumber')->nullable()->after('PaymentStatus');
            $table->string('DokuPaymentUrl')->nullable()->after('DokuInvoiceNumber');
            $table->string('DokuTokenId')->nullable()->after('DokuPaymentUrl');
            $table->string('DokuSessionId')->nullable()->after('DokuTokenId');
            $table->timestamp('PaymentExpiredAt')->nullable()->after('DokuSessionId');
            $table->timestamp('PaidAt')->nullable()->after('PaymentExpiredAt');
            $table->string('PaymentChannel')->nullable()->after('PaidAt');
            // e.g., VIRTUAL_ACCOUNT_BCA, QRIS, etc.
        });
    }

    public function down(): void
    {
        Schema::table('pendaftaran_tenants', function (Blueprint $table) {
            $table->string('BuktiPembayaran')->nullable();
            $table->dropColumn([
                'PaymentStatus',
                'DokuInvoiceNumber',
                'DokuPaymentUrl',
                'DokuTokenId',
                'DokuSessionId',
                'PaymentExpiredAt',
                'PaidAt',
                'PaymentChannel',
            ]);
        });
    }
};
