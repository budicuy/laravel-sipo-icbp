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
        Schema::create('surat_pengantar_istirahat', function (Blueprint $table) {
            $table->unsignedInteger('id_surat')->autoIncrement();
            $table->unsignedInteger('id_rekam');
            $table->unsignedInteger('id_keluarga');
            $table->date('tanggal_surat');
            $table->integer('lama_istirahat');
            $table->date('tanggal_mulai_istirahat');
            $table->date('tanggal_selesai_istirahat');
            $table->text('diagnosa_utama');
            $table->text('keterangan_tambahan')->nullable();
            $table->unsignedInteger('id_dokter');
            $table->string('nomor_surat')->unique();
            $table->timestamps();

            // Foreign keys
            $table->foreign('id_rekam')->references('id_rekam')->on('rekam_medis')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('id_keluarga')->references('id_keluarga')->on('keluarga')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('id_dokter')->references('id_user')->on('user')
                ->onUpdate('cascade')->onDelete('restrict');

            // Indexes
            $table->index('tanggal_surat');
            $table->index('nomor_surat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_pengantar_istirahat');
    }
};
