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
        Schema::create('stok_bulanan', function (Blueprint $table) {
            $table->id('id_stok_bulanan');
            $table->unsignedInteger('id_obat');
            $table->string('periode', 7); // Format: MM-YY (08-24)
            $table->integer('stok_awal')->default(0);
            $table->integer('stok_pakai')->default(0);
            $table->integer('stok_akhir')->default(0);
            $table->integer('stok_masuk')->default(0);
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrentOnUpdate()->nullable();

            // Foreign key ke tabel obat
            $table->foreign('id_obat')->references('id_obat')->on('obat')
                  ->onUpdate('cascade')->onDelete('cascade');

            // Unique constraint untuk setiap obat per periode
            $table->unique(['id_obat', 'periode'], 'unique_obat_periode');

            // Index untuk performance
            $table->index('periode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stok_bulanan');
    }
};
