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

        // Indexes for rekam_medis table - check if exists first
        Schema::table('rekam_medis', function (Blueprint $table) {
            if (!Schema::hasIndex('rekam_medis', 'idx_rekam_keluarga_tanggal')) {
                $table->index(['id_keluarga', 'tanggal_periksa'], 'idx_rekam_keluarga_tanggal');
            }
            if (!Schema::hasIndex('rekam_medis', 'idx_rekam_tanggal')) {
                $table->index(['tanggal_periksa'], 'idx_rekam_tanggal');
            }
            if (!Schema::hasIndex('rekam_medis', 'idx_rekam_status')) {
                $table->index(['status'], 'idx_rekam_status');
            }
            if (!Schema::hasIndex('rekam_medis', 'idx_rekam_user')) {
                $table->index(['id_user'], 'idx_rekam_user');
            }
        });

        // Indexes for keluhan table - check if exists first
        Schema::table('keluhan', function (Blueprint $table) {
            if (!Schema::hasIndex('keluhan', 'idx_keluhan_rekam')) {
                $table->index(['id_rekam'], 'idx_keluhan_rekam');
            }
            if (!Schema::hasIndex('keluhan', 'idx_keluhan_diagnosa')) {
                $table->index(['id_diagnosa'], 'idx_keluhan_diagnosa');
            }
            if (!Schema::hasIndex('keluhan', 'idx_keluhan_obat')) {
                $table->index(['id_obat'], 'idx_keluhan_obat');
            }
            if (!Schema::hasIndex('keluhan', 'idx_keluhan_keluarga')) {
                $table->index(['id_keluarga'], 'idx_keluhan_keluarga');
            }
            if (!Schema::hasIndex('keluhan', 'idx_keluhan_terapi')) {
                $table->index(['terapi'], 'idx_keluhan_terapi');
            }
        });

        // Indexes for kunjungan table - check if exists first
        Schema::table('kunjungan', function (Blueprint $table) {
            if (!Schema::hasIndex('kunjungan', 'idx_kunjungan_keluarga_tanggal')) {
                $table->index(['id_keluarga', 'tanggal_kunjungan'], 'idx_kunjungan_keluarga_tanggal');
            }
            if (!Schema::hasIndex('kunjungan', 'idx_kunjungan_tanggal')) {
                $table->index(['tanggal_kunjungan'], 'idx_kunjungan_tanggal');
            }
            if (!Schema::hasIndex('kunjungan', 'idx_kunjungan_kode')) {
                $table->index(['kode_transaksi'], 'idx_kunjungan_kode');
            }
        });

        // Indexes for keluarga table - check if exists first
        Schema::table('keluarga', function (Blueprint $table) {
            if (!Schema::hasIndex('keluarga', 'idx_keluarga_karyawan')) {
                $table->index(['id_karyawan'], 'idx_keluarga_karyawan');
            }
            if (!Schema::hasIndex('keluarga', 'idx_keluarga_hubungan')) {
                $table->index(['kode_hubungan'], 'idx_keluarga_hubungan');
            }
            if (!Schema::hasIndex('keluarga', 'idx_keluarga_no_rm')) {
                $table->index(['no_rm'], 'idx_keluarga_no_rm');
            }
            if (!Schema::hasIndex('keluarga', 'idx_keluarga_bpjs')) {
                $table->index(['bpjs_id'], 'idx_keluarga_bpjs');
            }
            if (!Schema::hasIndex('keluarga', 'idx_keluarga_nama')) {
                $table->index(['nama_keluarga'], 'idx_keluarga_nama');
            }
        });

        // Indexes for karyawan table - check if exists first
        Schema::table('karyawan', function (Blueprint $table) {
            if (!Schema::hasIndex('karyawan', 'idx_karyawan_nik')) {
                $table->index(['nik_karyawan'], 'idx_karyawan_nik');
            }
            if (!Schema::hasIndex('karyawan', 'idx_karyawan_nama')) {
                $table->index(['nama_karyawan'], 'idx_karyawan_nama');
            }
            if (!Schema::hasIndex('karyawan', 'idx_karyawan_departemen')) {
                $table->index(['id_departemen'], 'idx_karyawan_departemen');
            }
            if (!Schema::hasIndex('karyawan', 'idx_karyawan_email')) {
                $table->index(['email'], 'idx_karyawan_email');
            }
        });

        // Indexes for obat table - check if exists first
        Schema::table('obat', function (Blueprint $table) {
            if (!Schema::hasIndex('obat', 'idx_obat_nama')) {
                $table->index(['nama_obat'], 'idx_obat_nama');
            }
            if (!Schema::hasIndex('obat', 'idx_obat_jenis')) {
                $table->index(['id_jenis_obat'], 'idx_obat_jenis');
            }
            if (!Schema::hasIndex('obat', 'idx_obat_satuan')) {
                $table->index(['id_satuan'], 'idx_obat_satuan');
            }
            if (!Schema::hasIndex('obat', 'idx_obat_tanggal_update')) {
                $table->index(['tanggal_update'], 'idx_obat_tanggal_update');
            }
        });

        // Indexes for diagnosa table - check if exists first
        Schema::table('diagnosa', function (Blueprint $table) {
            if (!Schema::hasIndex('diagnosa', 'idx_diagnosa_nama')) {
                $table->index(['nama_diagnosa'], 'idx_diagnosa_nama');
            }
        });

        // Composite index for diagnosa_obat table - check if exists first
        Schema::table('diagnosa_obat', function (Blueprint $table) {
            if (!Schema::hasIndex('diagnosa_obat', 'idx_diagnosa_obat_obat')) {
                $table->index(['id_obat'], 'idx_diagnosa_obat_obat');
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

        // Drop indexes from rekam_medis table
        Schema::table('rekam_medis', function (Blueprint $table) {
            $table->dropIndex('idx_rekam_keluarga_tanggal');
            $table->dropIndex('idx_rekam_tanggal');
            $table->dropIndex('idx_rekam_status');
            $table->dropIndex('idx_rekam_user');
        });

        // Drop indexes from keluhan table
        Schema::table('keluhan', function (Blueprint $table) {
            $table->dropIndex('idx_keluhan_rekam');
            $table->dropIndex('idx_keluhan_diagnosa');
            $table->dropIndex('idx_keluhan_obat');
            $table->dropIndex('idx_keluhan_keluarga');
            $table->dropIndex('idx_keluhan_terapi');
        });

        // Drop indexes from kunjungan table
        Schema::table('kunjungan', function (Blueprint $table) {
            $table->dropIndex('idx_kunjungan_keluarga_tanggal');
            $table->dropIndex('idx_kunjungan_tanggal');
            $table->dropIndex('idx_kunjungan_kode');
        });

        // Drop indexes from keluarga table
        Schema::table('keluarga', function (Blueprint $table) {
            $table->dropIndex('idx_keluarga_karyawan');
            $table->dropIndex('idx_keluarga_hubungan');
            $table->dropIndex('idx_keluarga_no_rm');
            $table->dropIndex('idx_keluarga_bpjs');
            $table->dropIndex('idx_keluarga_nama');
        });

        // Drop indexes from karyawan table
        Schema::table('karyawan', function (Blueprint $table) {
            $table->dropIndex('idx_karyawan_nik');
            $table->dropIndex('idx_karyawan_nama');
            $table->dropIndex('idx_karyawan_departemen');
            $table->dropIndex('idx_karyawan_email');
        });

        // Drop indexes from obat table
        Schema::table('obat', function (Blueprint $table) {
            $table->dropIndex('idx_obat_nama');
            $table->dropIndex('idx_obat_jenis');
            $table->dropIndex('idx_obat_satuan');
            $table->dropIndex('idx_obat_tanggal_update');
        });

        // Drop indexes from diagnosa table
        Schema::table('diagnosa', function (Blueprint $table) {
            $table->dropIndex('idx_diagnosa_nama');
        });

        // Drop composite index from diagnosa_obat table
        Schema::table('diagnosa_obat', function (Blueprint $table) {
            $table->dropIndex('idx_diagnosa_obat_obat');
        });

        Schema::enableForeignKeyConstraints();
    }
};
