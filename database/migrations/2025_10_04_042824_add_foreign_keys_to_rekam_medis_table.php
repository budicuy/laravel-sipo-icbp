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
            $table->foreign(['id_pasien'], 'fk_pasien')->references(['id_pasien'])->on('pasien')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign(['id_penyakit'], 'fk_penyakit_rekam')->references(['id_penyakit'])->on('penyakit')->onUpdate('cascade')->onDelete('set null');
            $table->foreign(['id_kunjungan'], 'fk_rekam_kunjungan')->references(['id_kunjungan'])->on('kunjungan')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['id_user'], 'fk_user')->references(['id_user'])->on('user')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rekam_medis', function (Blueprint $table) {
            $table->dropForeign('fk_pasien');
            $table->dropForeign('fk_penyakit_rekam');
            $table->dropForeign('fk_rekam_kunjungan');
            $table->dropForeign('fk_user');
        });
    }
};
