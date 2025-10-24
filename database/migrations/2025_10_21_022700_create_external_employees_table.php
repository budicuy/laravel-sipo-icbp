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
        Schema::create('external_employees', function (Blueprint $table) {
            $table->id();
            $table->string('nik_employee', 20)->unique();
            $table->string('nama_employee');
            $table->string('kode_rm');
            $table->date('tanggal_lahir');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->text('alamat');
            $table->string('no_hp', 15);
            $table->unsignedBigInteger('id_vendor');
            $table->string('no_ktp', 20)->nullable();
            $table->string('bpjs_id', 20)->nullable();
            $table->unsignedBigInteger('id_kategori');
            $table->string('foto')->nullable();
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamps();

            $table->foreign('id_vendor')->references('id_vendor')->on('vendors')->onDelete('cascade');
            $table->foreign('id_kategori')->references('id_kategori')->on('kategoris')->onDelete('cascade');
            
            $table->index('nik_employee');
            $table->index('nama_employee');
            $table->index('id_vendor');
            $table->index('id_kategori');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('external_employees');
    }
};