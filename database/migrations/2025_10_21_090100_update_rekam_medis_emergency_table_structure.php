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
        Schema::table('rekam_medis_emergency', function (Blueprint $table) {
            // Pastikan kolom yang akan dihapus memang ada
            if (Schema::hasColumn('rekam_medis_emergency', 'nik_pasien')) {
                $table->dropColumn(['nik_pasien', 'nama_pasien', 'no_rm', 'hubungan', 'jenis_kelamin']);
            }

            // Tambah foreign key ke external_employees
            $table->unsignedBigInteger('id_external_employee')->after('id_emergency');

            // Tambah relasi ke keluhan (satu ke satu)
            $table->unsignedInteger('id_keluhan')->nullable()->after('catatan');

            // Rename kolom status jika ada
            if (Schema::hasColumn('rekam_medis_emergency', 'status_rekam_medis')) {
                $table->renameColumn('status_rekam_medis', 'status');
            }

            // Hapus diagnosa (akan diganti relasi)
            if (Schema::hasColumn('rekam_medis_emergency', 'diagnosa')) {
                $table->dropColumn('diagnosa');
            }

            // Tambah foreign key constraints
            $table->foreign('id_external_employee')
                  ->references('id')
                  ->on('external_employees')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');

            $table->foreign('id_keluhan')
                  ->references('id_keluhan')
                  ->on('keluhan')
                  ->onUpdate('cascade')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rekam_medis_emergency', function (Blueprint $table) {
            // Drop foreign keys terlebih dahulu
            if (Schema::hasColumn('rekam_medis_emergency', 'id_external_employee')) {
                $table->dropForeign(['id_external_employee']);
            }
            if (Schema::hasColumn('rekam_medis_emergency', 'id_keluhan')) {
                $table->dropForeign(['id_keluhan']);
            }

            // Drop kolom yang ditambahkan
            if (Schema::hasColumn('rekam_medis_emergency', 'id_external_employee')) {
                $table->dropColumn('id_external_employee');
            }
            if (Schema::hasColumn('rekam_medis_emergency', 'id_keluhan')) {
                $table->dropColumn('id_keluhan');
            }

            // Kembalikan kolom lama
            $table->string('nik_pasien', 16)->after('id_emergency');
            $table->string('nama_pasien')->after('nik_pasien');
            $table->string('no_rm')->after('nama_pasien');
            $table->string('hubungan')->default('Emergency')->after('no_rm');
            $table->enum('jenis_kelamin', ['L', 'P'])->after('hubungan');

            // Tambahkan kembali diagnosa
            $table->text('diagnosa')->nullable()->after('waktu_periksa');

            // Rename kolom status kembali jika ada
            if (Schema::hasColumn('rekam_medis_emergency', 'status')) {
                $table->renameColumn('status', 'status_rekam_medis');
            }
        });
    }
};
