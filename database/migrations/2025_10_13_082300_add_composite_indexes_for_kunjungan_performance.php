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
        Schema::disableForeignKeyConstraints();

        // Composite index untuk query utama di halaman kunjungan
        Schema::table('rekam_medis', function (Blueprint $table) {
            if (!Schema::hasIndex('rekam_medis', 'idx_rekam_tanggal_keluarga_user')) {
                $table->index(['tanggal_periksa', 'id_keluarga', 'id_user'], 'idx_rekam_tanggal_keluarga_user');
            }
            if (!Schema::hasIndex('rekam_medis', 'idx_rekam_tanggal_status')) {
                $table->index(['tanggal_periksa', 'status'], 'idx_rekam_tanggal_status');
            }
        });

        // Composite index untuk join dengan keluhan
        Schema::table('keluhan', function (Blueprint $table) {
            if (!Schema::hasIndex('keluhan', 'idx_keluhan_rekam_diagnosa_obat')) {
                $table->index(['id_rekam', 'id_diagnosa', 'id_obat'], 'idx_keluhan_rekam_diagnosa_obat');
            }
        });

        // Composite index untuk join dengan keluarga
        Schema::table('keluarga', function (Blueprint $table) {
            if (!Schema::hasIndex('keluarga', 'idx_keluarga_karyawan_hubungan')) {
                $table->index(['id_karyawan', 'kode_hubungan'], 'idx_keluarga_karyawan_hubungan');
            }
            if (!Schema::hasIndex('keluarga', 'idx_keluarga_nama_no_rm')) {
                $table->index(['nama_keluarga', 'no_rm'], 'idx_keluarga_nama_no_rm');
            }
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        // Drop composite indexes
        Schema::table('rekam_medis', function (Blueprint $table) {
            $table->dropIndex('idx_rekam_tanggal_keluarga_user');
            $table->dropIndex('idx_rekam_tanggal_status');
        });

        Schema::table('keluhan', function (Blueprint $table) {
            $table->dropIndex('idx_keluhan_rekam_diagnosa_obat');
        });

        Schema::table('keluarga', function (Blueprint $table) {
            $table->dropIndex('idx_keluarga_karyawan_hubungan');
            $table->dropIndex('idx_keluarga_nama_no_rm');
        });

        Schema::enableForeignKeyConstraints();
    }
};
