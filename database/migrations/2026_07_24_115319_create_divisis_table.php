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
        Schema::create('divisis', function (Blueprint $table) {
            $table->id();
            $table->string('Nama', 200);
            $table->text('Keterangan')->nullable();
            $table->string('UserCreate', 200)->nullable();
            $table->string('UserUpdate', 200)->nullable();
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
        Schema::dropIfExists('divisis');
    }
};
