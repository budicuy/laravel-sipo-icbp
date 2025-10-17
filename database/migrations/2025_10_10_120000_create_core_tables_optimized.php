<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Disable foreign key checks
        Schema::disableForeignKeyConstraints();

        // === TABEL MASTER/REFERENSI ===

        // Tabel departemen
        Schema::create('departemen', function (Blueprint $table) {
            $table->unsignedInteger('id_departemen')->autoIncrement();
            $table->string('nama_departemen', 100);
            $table->timestamp('created_at')->nullable()->useCurrent();
        });

        // Tabel hubungan
        Schema::create('hubungan', function (Blueprint $table) {
            $table->char('kode_hubungan', 1)->primary();
            $table->string('hubungan', 20)->nullable();
        });

        // Tabel jenis_obat
        Schema::create('jenis_obat', function (Blueprint $table) {
            $table->unsignedInteger('id_jenis_obat')->autoIncrement();
            $table->string('nama_jenis_obat', 50)->unique();
            $table->timestamp('created_at')->nullable()->useCurrent();
        });

        // Tabel satuan_obat
        Schema::create('satuan_obat', function (Blueprint $table) {
            $table->unsignedInteger('id_satuan')->autoIncrement();
            $table->string('nama_satuan', 50)->unique();
            $table->timestamp('created_at')->nullable()->useCurrent();
        });

        // Tabel diagnosa
        Schema::create('diagnosa', function (Blueprint $table) {
            $table->unsignedInteger('id_diagnosa')->autoIncrement();
            $table->string('nama_diagnosa', 100);
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });

        // === TABEL USER & KARYAWAN ===

        // Tabel user
        Schema::create('user', function (Blueprint $table) {
            $table->unsignedInteger('id_user')->autoIncrement();
            $table->string('username', 50)->unique();
            $table->string('password');
            $table->string('nama_lengkap', 100);
            $table->enum('role', ['Super Admin', 'Admin', 'User']);
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('last_login')->nullable();
            $table->boolean('is_active')->default(1);
        });

        // Tabel karyawan
        Schema::create('karyawan', function (Blueprint $table) {
            $table->unsignedInteger('id_karyawan')->autoIncrement();
            $table->string('nik_karyawan', 15)->unique();
            $table->string('nama_karyawan', 100);
            $table->date('tanggal_lahir');
            $table->enum('jenis_kelamin', ['Laki - Laki', 'Perempuan']);
            $table->text('alamat')->nullable();
            $table->string('no_hp', 20)->nullable();
            $table->unsignedInteger('id_departemen');
            $table->string('foto', 255)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('bpjs_id', 50)->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();

            // Foreign key
            $table->foreign('id_departemen')->references('id_departemen')->on('departemen')
                  ->onUpdate('cascade')->onDelete('restrict');
        });

        // === TABEL KELUARGA & KUNJUNGAN ===

        // Tabel keluarga
        Schema::create('keluarga', function (Blueprint $table) {
            $table->unsignedInteger('id_keluarga')->autoIncrement();
            $table->unsignedInteger('id_karyawan');
            $table->string('nama_keluarga', 100);
            $table->date('tanggal_lahir')->nullable();
            $table->enum('jenis_kelamin', ['Laki - Laki', 'Perempuan']);
            $table->text('alamat')->nullable();
            $table->date('tanggal_daftar')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->string('no_rm', 30)->nullable()->unique();
            $table->char('kode_hubungan', 1);
            $table->string('bpjs_id', 50)->nullable();

            // Foreign keys
            $table->foreign('id_karyawan')->references('id_karyawan')->on('karyawan')
                  ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('kode_hubungan')->references('kode_hubungan')->on('hubungan')
                  ->onUpdate('cascade')->onDelete('restrict');
        });

        // Tabel kunjungan
        Schema::create('kunjungan', function (Blueprint $table) {
            $table->unsignedInteger('id_kunjungan')->autoIncrement();
            $table->unsignedInteger('id_keluarga');
            $table->string('kode_transaksi', 50);
            $table->date('tanggal_kunjungan')->useCurrent();
            $table->timestamp('created_at')->nullable()->useCurrent();

            // Foreign key
            $table->foreign('id_keluarga')->references('id_keluarga')->on('keluarga')
                  ->onUpdate('cascade')->onDelete('cascade');
        });

        // === TABEL OBAT ===

        // Tabel obat
        Schema::create('obat', function (Blueprint $table) {
            $table->unsignedInteger('id_obat')->autoIncrement();
            $table->string('nama_obat', 100)->unique();
            $table->text('keterangan')->nullable();
            $table->unsignedInteger('id_jenis_obat')->nullable();
            $table->unsignedInteger('id_satuan')->nullable();
            $table->integer('stok_awal')->nullable()->default(0);
            $table->integer('stok_masuk')->nullable()->default(0);
            $table->integer('stok_keluar')->nullable()->default(0);
            $table->integer('stok_akhir')->nullable()->default(0);
            $table->integer('jumlah_per_kemasan')->default(1);
            $table->decimal('harga_per_satuan', 15)->default(0);
            $table->decimal('harga_per_kemasan', 15);
            $table->dateTime('tanggal_update')->nullable()->useCurrent();

            // Foreign keys
            $table->foreign('id_jenis_obat')->references('id_jenis_obat')->on('jenis_obat')
                  ->onUpdate('cascade')->onDelete('set null');
            $table->foreign('id_satuan')->references('id_satuan')->on('satuan_obat')
                  ->onUpdate('cascade')->onDelete('restrict');
        });

        // === TABEL MEDIS ===

        // Tabel rekam_medis
        Schema::create('rekam_medis', function (Blueprint $table) {
            $table->unsignedInteger('id_rekam')->autoIncrement();
            $table->unsignedInteger('id_keluarga');
            $table->date('tanggal_periksa');
            $table->unsignedInteger('id_user');
            $table->integer('jumlah_keluhan');
            $table->enum('status', ['On Progress', 'Close'])->default('On Progress');
            $table->timestamp('created_at')->nullable()->useCurrent();

            // Foreign keys
            $table->foreign('id_keluarga')->references('id_keluarga')->on('keluarga')
                  ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('id_user')->references('id_user')->on('user')
                  ->onUpdate('cascade')->onDelete('cascade');
        });

        // Tabel keluhan
        Schema::create('keluhan', function (Blueprint $table) {
            $table->unsignedInteger('id_keluhan')->autoIncrement();
            $table->unsignedInteger('id_rekam');
            $table->unsignedInteger('id_diagnosa');
            $table->enum('terapi', ['Obat', 'Lab', 'Istirahat']);
            $table->text('keterangan')->nullable();
            $table->unsignedInteger('id_obat')->nullable();
            $table->integer('jumlah_obat')->unsigned()->nullable();
            $table->text('aturan_pakai')->nullable();
            $table->unsignedInteger('id_keluarga');
            $table->timestamp('created_at')->useCurrent();

            // Foreign keys
            $table->foreign('id_rekam')->references('id_rekam')->on('rekam_medis')
                  ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('id_diagnosa')->references('id_diagnosa')->on('diagnosa')
                  ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('id_obat')->references('id_obat')->on('obat')
                  ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('id_keluarga')->references('id_keluarga')->on('keluarga')
                  ->onUpdate('cascade')->onDelete('cascade');
        });

        // Tabel diagnosa_obat (pivot table)
        Schema::create('diagnosa_obat', function (Blueprint $table) {
            $table->unsignedInteger('id_diagnosa');
            $table->unsignedInteger('id_obat');

            $table->primary(['id_diagnosa', 'id_obat']);

            // Foreign keys
            $table->foreign('id_diagnosa')->references('id_diagnosa')->on('diagnosa')
                  ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('id_obat')->references('id_obat')->on('obat')
                  ->onUpdate('cascade')->onDelete('cascade');
        });

        // Re-enable foreign key checks
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        // Drop tables in reverse order to avoid foreign key constraints
        Schema::dropIfExists('diagnosa_obat');
        Schema::dropIfExists('keluhan');
        Schema::dropIfExists('rekam_medis');
        Schema::dropIfExists('obat');
        Schema::dropIfExists('kunjungan');
        Schema::dropIfExists('keluarga');
        Schema::dropIfExists('karyawan');
        Schema::dropIfExists('user');
        Schema::dropIfExists('diagnosa');
        Schema::dropIfExists('satuan_obat');
        Schema::dropIfExists('jenis_obat');
        Schema::dropIfExists('hubungan');
        Schema::dropIfExists('departemen');

        Schema::enableForeignKeyConstraints();
    }
};
