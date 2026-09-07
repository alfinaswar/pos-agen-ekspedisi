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
        Schema::table('pekerjaan_kurirs', function (Blueprint $table) {
            $table->string('KodeTenant', 100)->default('TEN-0001')->nullable()->after('UserVerif');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pekerjaan_kuris', function (Blueprint $table) {
            //
        });
    }
};
