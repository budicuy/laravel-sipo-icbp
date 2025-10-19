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
        Schema::create('rekam_medis_emergency', function (Blueprint $table) {
            $table->id('id_emergency');
            $table->string('nik_pasien', 16); // Harus angka, maksimal 16 digit, minimal 1
            $table->string('nama_pasien');
            $table->string('no_rm'); // NIK + Kode hubungan (contoh: 123123-F)
            $table->string('hubungan')->default('Emergency'); // Default Emergency
            $table->enum('jenis_kelamin', ['L', 'P']); // L/P
            $table->date('tanggal_periksa');
            $table->enum('status_rekam_medis', ['On Progress', 'Close'])->default('On Progress'); // Default On Progress
            $table->text('diagnosa')->nullable();
            $table->text('keluhan'); // Wajib
            $table->text('catatan')->nullable(); // Opsional
            $table->unsignedInteger('id_user'); // ID user yang membuat rekam medis
            $table->timestamps();
            
            // Foreign key
            $table->foreign('id_user')->references('id_user')->on('user')
                  ->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rekam_medis_emergency');
    }
};