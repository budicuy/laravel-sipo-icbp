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
        Schema::create('stok_bulanans', function (Blueprint $table) {
            $table->id('id');
            $table->unsignedInteger('obat_id');
            $table->year('tahun');
            $table->tinyInteger('bulan'); // 1-12
            $table->integer('stok_masuk')->default(0);
            $table->integer('stok_pakai')->default(0);
            $table->timestamps();

            // Foreign key ke tabel obat
            $table->foreign('obat_id')->references('id_obat')->on('obat')
                  ->onUpdate('cascade')->onDelete('cascade');

            // Unique key untuk kombinasi (obat_id, tahun, bulan)
            $table->unique(['obat_id', 'tahun', 'bulan'], 'unique_obat_tahun_bulan');

            // Index untuk performance
            $table->index('tahun');
            $table->index('bulan');
            $table->index(['tahun', 'bulan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stok_bulanans');
    }
};
