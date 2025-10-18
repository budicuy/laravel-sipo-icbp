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
        Schema::table('rekam_medis', function (Blueprint $table) {
            // Add waktu field (time) for recording the time of examination
            $table->time('waktu_periksa')->nullable()->after('tanggal_periksa');
        });

        Schema::table('rekam_medis_emergency', function (Blueprint $table) {
            // Add waktu field (time) for recording the time of examination
            $table->time('waktu_periksa')->nullable()->after('tanggal_periksa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rekam_medis', function (Blueprint $table) {
            $table->dropColumn('waktu_periksa');
        });

        Schema::table('rekam_medis_emergency', function (Blueprint $table) {
            $table->dropColumn('waktu_periksa');
        });
    }
};
