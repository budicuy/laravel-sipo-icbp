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
        Schema::table('resep_obat', function (Blueprint $table) {
            $table->foreign(['id_rekam'], 'resep_obat_ibfk_1')->references(['id_rekam'])->on('rekam_medis')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['id_obat'], 'resep_obat_ibfk_2')->references(['id_obat'])->on('obat')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resep_obat', function (Blueprint $table) {
            $table->dropForeign('resep_obat_ibfk_1');
            $table->dropForeign('resep_obat_ibfk_2');
        });
    }
};
