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
        // Disable foreign key checks
        Schema::disableForeignKeyConstraints();

        // Drop penyakit_obat terlebih dahulu karena ada foreign key ke penyakit
        Schema::dropIfExists('penyakit_obat');
        Schema::dropIfExists('penyakit');

        // Enable foreign key checks
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate penyakit table
        Schema::create('penyakit', function (Blueprint $table) {
            $table->integer('id_penyakit', true);
            $table->string('nama_penyakit', 100)->unique('nama_penyakit');
            $table->text('deskripsi')->nullable();
        });

        // Recreate penyakit_obat table
        Schema::create('penyakit_obat', function (Blueprint $table) {
            $table->integer('id_penyakit');
            $table->integer('id_obat');

            $table->primary(['id_penyakit', 'id_obat']);

            // Foreign keys
            $table->foreign(['id_obat'], 'fk_obat')->references(['id_obat'])->on('obat')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign(['id_penyakit'], 'fk_penyakit')->references(['id_penyakit'])->on('penyakit')->onUpdate('cascade')->onDelete('cascade');
        });
    }
};
