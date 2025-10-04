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
