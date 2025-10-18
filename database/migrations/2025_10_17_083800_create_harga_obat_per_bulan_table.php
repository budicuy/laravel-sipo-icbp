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
        Schema::create('harga_obat_per_bulan', function (Blueprint $table) {
            $table->id('id_harga_obat');
            $table->unsignedInteger('id_obat');
            $table->string('periode', 7); // Format: MM-YY (08-24)
            $table->integer('jumlah_per_kemasan')->default(1);
            $table->decimal('harga_per_satuan', 15, 2)->default(0);
            $table->decimal('harga_per_kemasan', 15, 2)->default(0);
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrentOnUpdate()->nullable();

            // Foreign key ke tabel obat
            $table->foreign('id_obat')->references('id_obat')->on('obat')
                  ->onUpdate('cascade')->onDelete('cascade');

            // Unique constraint untuk setiap obat per periode
            $table->unique(['id_obat', 'periode'], 'unique_obat_periode_harga');

            // Index untuk performance
            $table->index('periode');
            $table->index(['id_obat', 'periode']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('harga_obat_per_bulan');
    }
};
