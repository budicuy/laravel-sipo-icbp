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
        Schema::table('obat', function (Blueprint $table) {
            $table->foreign(['id_jenis_obat'], 'fk_jenis_obat')->references(['id_jenis_obat'])->on('jenis_obat')->onUpdate('cascade')->onDelete('set null');
            $table->foreign(['id_satuan'], 'fk_obat_satuan')->references(['id_satuan'])->on('satuan_obat')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('obat', function (Blueprint $table) {
            $table->dropForeign('fk_jenis_obat');
            $table->dropForeign('fk_obat_satuan');
        });
    }
};
