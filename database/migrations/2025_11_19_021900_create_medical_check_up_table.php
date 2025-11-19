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
        Schema::create('medical_check_up', function (Blueprint $table) {
            $table->unsignedInteger('id')->autoIncrement();
            $table->unsignedInteger('id_karyawan');
            $table->unsignedInteger('id_keluarga')->nullable();
            
            // Periode (tahun)
            $table->year('periode');
            
            // Tanggal (DD-MM-YYYY) - disimpan sebagai date
            $table->date('tanggal');
            
            // Dikeluarkan oleh (string)
            $table->string('dikeluarkan_oleh', 100);
            
            // Kesimpulan Medis (TEXT)
            $table->text('kesimpulan_medis')->nullable();
            
            // BMI (string, dropdown)
            $table->enum('bmi', [
                'Underweight', 
                'Normal', 
                'Overweight', 
                'Obesitas Tk 1', 
                'Obesitas Tk 2', 
                'Obesitas Tk 3'
            ])->nullable();
            
            // IMT (String dropdown) - IMT Index Massa Tubuh
            $table->enum('imt', [
                'Kurus', 
                'Normal', 
                'Gemuk', 
                'Obesitas'
            ])->nullable();
            
            // Rekomendasi (TEXT untuk sementara)
            $table->text('rekomendasi')->nullable();
            
            // File upload fields (mirip surat_rekomendasi_medis)
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->integer('file_size')->nullable();
            $table->string('mime_type')->nullable();
            
            // Audit fields
            $table->unsignedInteger('id_user')->nullable();
            $table->timestamps();
            
            // Foreign keys - menggunakan tipe data yang sama dengan tabel utama
            $table->foreign('id_karyawan')->references('id_karyawan')->on('karyawan')->onDelete('cascade');
            $table->foreign('id_keluarga')->references('id_keluarga')->on('keluarga')->onDelete('cascade');
            $table->foreign('id_user')->references('id_user')->on('user')->onDelete('set null');
            
            // Indexes for performance
            $table->index('id_karyawan');
            $table->index('id_keluarga');
            $table->index('periode');
            $table->index('tanggal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_check_up');
    }
};