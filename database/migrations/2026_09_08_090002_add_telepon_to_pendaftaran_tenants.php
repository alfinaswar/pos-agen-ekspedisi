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
        Schema::table('pendaftaran_tenants', function (Blueprint $table) {
            $table->string('Telepon', 100)->nullable()->after('Email');
            $table->string('TeleponPIC', 100)->nullable()->after('EmailPIC');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pendaftaran_tenants', function (Blueprint $table) {
            //
        });
    }
};
