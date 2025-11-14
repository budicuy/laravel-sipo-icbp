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
        Schema::create('surat_pengantars', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_surat')->unique();
            $table->string('nama_pasien');
            $table->string('nik_karyawan_penanggung_jawab');
            $table->date('tanggal_pengantar');
            $table->jsonb('diagnosa');
            $table->string('catatan')->nullable();
            $table->tinyInteger('lama_istirahat');
            $table->date('tanggal_mulai_istirahat');
            $table->string('petugas_medis');
            $table->string('qrcode_path');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_pengantars');
    }
};
