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
        Schema::table('absensis', function (Blueprint $table) {
            $table->enum('StatusVerif', ['Y', 'N', 'N/A'])->default('N/A')->after('SelesaiLembur');
            $table->text('Catatan')->nullable()->after('Status');
            $table->string('UserLeader')->nullable()->after('Catatan'); // Untuk mencatat siapa yang approve
            $table->timestamp('DisetujuiPada')->nullable()->after('UserLeader');
        });
    }

    public function down()
    {
        Schema::table('absensis', function (Blueprint $table) {
            $table->dropColumn(['Status', 'Catatan', 'UserLeader', 'DisetujuiPada']);
        });
    }
};
