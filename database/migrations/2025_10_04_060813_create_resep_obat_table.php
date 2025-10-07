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
        Schema::create('resep_obat', function (Blueprint $table) {
            $table->integer('id_resep_obat', true);
            $table->integer('id_rekam');
            $table->integer('id_obat');
            $table->integer('jumlah')->nullable()->default(1);
            $table->decimal('harga_satuan', 10, 2)->nullable()->default(0);
            $table->decimal('subtotal', 10, 2)->nullable()->storedAs('jumlah * harga_satuan');
            $table->text('keterangan')->nullable();
            
            // Foreign keys
            $table->foreign(['id_rekam'], 'resep_obat_ibfk_1')->references(['id_rekam'])->on('rekam_medis')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['id_obat'], 'resep_obat_ibfk_2')->references(['id_obat'])->on('obat')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resep_obat');
    }
};
