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
            $table->integer('id_rekam')->index('id_rekam');
            $table->integer('id_obat')->index('id_obat');
            $table->integer('jumlah')->nullable()->default(1);
            $table->decimal('harga_satuan', 10)->nullable()->default(0);
            $table->decimal('subtotal', 10)->nullable()->storedAs('`jumlah` * `harga_satuan`');
            $table->text('keterangan')->nullable();
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
