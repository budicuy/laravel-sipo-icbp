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
        Schema::create('diagnosa_obat', function (Blueprint $table) {
            $table->integer('id_diagnosa');
            $table->integer('id_obat');

            $table->primary(['id_diagnosa', 'id_obat']);

            // Foreign keys
            $table->foreign(['id_diagnosa'], 'fk_diagnosa_obat_diagnosa')
                  ->references(['id_diagnosa'])
                  ->on('diagnosa')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');

            $table->foreign(['id_obat'], 'fk_diagnosa_obat_obat')
                  ->references(['id_obat'])
                  ->on('obat')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diagnosa_obat');
    }
};
