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
        Schema::create('pasien', function (Blueprint $table) {
            $table->integer('id_pasien', true);
            $table->integer('id_karyawan')->index('fk_pasien_karyawan');
            $table->string('nama_pasien', 100);
            $table->date('tanggal_lahir')->nullable();
            $table->enum('jenis_kelamin', ['Laki - Laki', 'Perempuan']);
            $table->text('alamat')->nullable();
            $table->date('tanggal_daftar')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->string('no_rm', 30)->nullable()->unique('no_rm');
            $table->char('kode_hubungan', 1)->index('kode_hubungan');
            
            // Foreign keys
            $table->foreign(['id_karyawan'], 'fk_pasien_karyawan')->references(['id_karyawan'])->on('karyawan')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign(['kode_hubungan'], 'fk_pasien_hubungan')->references(['kode_hubungan'])->on('hubungan')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pasien');
    }
};
