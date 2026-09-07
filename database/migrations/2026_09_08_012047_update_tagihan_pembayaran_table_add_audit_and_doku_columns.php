<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tagihan_pembayarans', function (Blueprint $table) {
            // Kolom DOKU Payment Gateway
            $table->string('PaymentStatus')->default('PENDING')->after('StatusPembayaran');
            // PENDING, PAID, FAILED, EXPIRED
            $table->string('DokuInvoiceNumber')->nullable()->after('PaymentStatus');
            $table->string('DokuPaymentUrl')->nullable()->after('DokuInvoiceNumber');
            $table->string('DokuTokenId')->nullable()->after('DokuPaymentUrl');
            $table->string('DokuSessionId')->nullable()->after('DokuTokenId');
            $table->timestamp('PaymentExpiredAt')->nullable()->after('DokuSessionId');
            $table->timestamp('PaidAt')->nullable()->after('PaymentExpiredAt');
            $table->string('PaymentChannel')->nullable()->after('PaidAt');
        });
    }

    public function down(): void
    {
        Schema::table('tagihan_pembayaran', function (Blueprint $table) {
            $table->dropSoftDeletes();
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
