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
        Schema::create('surat_rekomendasi_medis', function (Blueprint $table) {
            $table->unsignedInteger('id')->autoIncrement();
            $table->unsignedInteger('id_karyawan');
            $table->unsignedInteger('id_keluarga')->nullable();
            $table->date('tanggal');
            $table->string('penerbit_surat');
            $table->text('catatan_medis')->nullable();
            $table->string('file_path');
            $table->string('file_name');
            $table->integer('file_size');
            $table->string('mime_type');
            $table->unsignedInteger('created_by')->nullable();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('id_karyawan')->references('id_karyawan')->on('karyawan')->onDelete('cascade');
            $table->foreign('id_keluarga')->references('id_keluarga')->on('keluarga')->onDelete('cascade');
            $table->foreign('created_by')->references('id_user')->on('user')->onDelete('set null');
            
            // Indexes for performance
            $table->index('id_karyawan');
            $table->index('id_keluarga');
            $table->index('tanggal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_rekomendasi_medis');
    }
};
