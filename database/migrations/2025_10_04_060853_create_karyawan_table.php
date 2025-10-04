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
        Schema::create('karyawan', function (Blueprint $table) {
            $table->integer('id_karyawan', true);
            $table->string('nik_karyawan', 16)->unique('nik_karyawan');
            $table->string('nama_karyawan', 100);
            $table->date('tanggal_lahir');
            $table->enum('jenis_kelamin', ['Laki - Laki', 'Perempuan']);
            $table->text('alamat')->nullable();
            $table->string('no_hp', 20)->nullable();
            $table->integer('id_departemen')->index('fk_departemen');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();

            $table->unique(['nik_karyawan'], 'uq_nik_karyawan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('karyawan');
    }
};
