<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Menambahkan kolom setelah no_hp (sesuaikan jika urutan kolom Anda berbeda)
            $table->string('foto_profil')->nullable()->after('no_hp');
            $table->string('foto_ktp')->nullable()->after('foto_profil');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['foto_profil', 'foto_ktp']);
        });
    }
};
