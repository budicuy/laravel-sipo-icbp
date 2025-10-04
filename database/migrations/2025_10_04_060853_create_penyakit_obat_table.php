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
        Schema::create('penyakit_obat', function (Blueprint $table) {
            $table->integer('id_penyakit');
            $table->integer('id_obat')->index('fk_obat');

            $table->primary(['id_penyakit', 'id_obat']);

            // Foreign keys
            $table->foreign(['id_obat'], 'fk_obat')->references(['id_obat'])->on('obat')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign(['id_penyakit'], 'fk_penyakit')->references(['id_penyakit'])->on('penyakit')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penyakit_obat');
    }
};
