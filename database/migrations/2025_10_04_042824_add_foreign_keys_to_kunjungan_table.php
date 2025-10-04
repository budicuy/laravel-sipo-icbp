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
        Schema::table('kunjungan', function (Blueprint $table) {
            $table->foreign(['id_pasien'], 'fk_kunjungan_pasien')->references(['id_pasien'])->on('pasien')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kunjungan', function (Blueprint $table) {
            $table->dropForeign('fk_kunjungan_pasien');
        });
    }
};
