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
            $table->enum('Status', ['Y', 'N', 'N/A'])
                ->nullable()
                ->default('N/A')
                ->after('IdUser');
            $table->text('Catatan')
                ->nullable()
                ->after('Status');
            $table->dateTime('DicekPada')
                ->nullable()
                ->after('Catatan');
            $table->string('UserVerif')
                ->nullable()
                ->comment('User ID yang melakukan pengecekan')
                ->after('DicekPada');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pekerjaan_kurirs', function (Blueprint $table) {
            //
        });
    }
};
