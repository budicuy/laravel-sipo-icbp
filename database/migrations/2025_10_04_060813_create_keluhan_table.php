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
        Schema::create('keluhan', function (Blueprint $table) {
            $table->unsignedInteger('id_keluhan')->autoIncrement();
            $table->unsignedInteger('id_rekam');
            $table->unsignedInteger('id_diagnosa');
            $table->enum('terapi', ['Obat', 'Lab', 'Istirahat']);
            $table->text('keterangan')->nullable();
            $table->unsignedInteger('id_obat')->nullable();
            $table->smallInteger('jumlah_obat')->unsigned()->nullable();
            $table->text('aturan_pakai')->nullable();
            $table->smallInteger('waktu_pakai')->nullable();
            $table->unsignedInteger('id_keluarga');
            $table->timestamp('created_at')->useCurrent();

            // Foreign keys
            $table->foreign(['id_rekam'], 'fk_keluhan_rekam')->references(['id_rekam'])->on('rekam_medis')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign(['id_diagnosa'], 'fk_keluhan_diagnosa')->references(['id_diagnosa'])->on('diagnosa')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign(['id_obat'], 'fk_keluhan_obat')->references(['id_obat'])->on('obat')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign(['id_keluarga'], 'fk_keluhan_keluarga')->references(['id_keluarga'])->on('keluarga')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */

    public function down(): void
    {
        Schema::dropIfExists('keluhan');
    }
};
