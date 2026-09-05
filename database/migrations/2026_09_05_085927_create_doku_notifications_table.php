<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('doku_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number');
            $table->string('transaction_status')->nullable();
            $table->string('payment_channel')->nullable();
            $table->decimal('amount', 15, 2)->default(0);
            $table->json('raw_payload')->nullable(); // simpan seluruh body notification
            $table->string('signature')->nullable();
            $table->boolean('signature_valid')->default(false);
            $table->timestamps();

            $table->index('invoice_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doku_notifications');
    }
};
