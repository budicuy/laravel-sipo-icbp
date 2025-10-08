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
            $table->unsignedInteger('id_karyawan')->autoIncrement();
            $table->string('nik_karyawan', 16)->unique('nik_karyawan');
            $table->string('nama_karyawan', 100);
            $table->date('tanggal_lahir');
            $table->enum('jenis_kelamin', ['Laki - Laki', 'Perempuan']);
            $table->text('alamat')->nullable();
            $table->string('no_hp', 20)->nullable();
            $table->unsignedInteger('id_departemen');
            $table->string('foto', 255)->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();

            $table->unique(['nik_karyawan'], 'uq_nik_karyawan');

            // Foreign keys
            $table->foreign(['id_departemen'], 'fk_departemen')->references(['id_departemen'])->on('departemen')->onUpdate('cascade')->onDelete('restrict');
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
