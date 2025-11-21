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
        Schema::table('medical_check_up_kondisi_kesehatan', function (Blueprint $table) {
            // Add composite index for better query performance
            $table->index(['id_medical_check_up', 'id_kondisi_kesehatan'], 'mck_medical_check_up_kondisi_index');
            
            // Add individual indexes if not already present
            if (!Schema::hasIndex('medical_check_up_kondisi_kesehatan', 'mck_medical_check_up_index')) {
                $table->index('id_medical_check_up', 'mck_medical_check_up_index');
            }
            
            if (!Schema::hasIndex('medical_check_up_kondisi_kesehatan', 'mck_kondisi_kesehatan_index')) {
                $table->index('id_kondisi_kesehatan', 'mck_kondisi_kesehatan_index');
            }
        });
        
        // Add indexes to medical_check_up table for better performance
        Schema::table('medical_check_up', function (Blueprint $table) {
            // Composite index for employee queries
            $table->index(['id_karyawan', 'tanggal'], 'mc_karyawan_tanggal_index');
            
            // Index for period filtering
            if (!Schema::hasIndex('medical_check_up', 'mc_periode_index')) {
                $table->index('periode', 'mc_periode_index');
            }
            
            // Index for date ordering
            if (!Schema::hasIndex('medical_check_up', 'mc_tanggal_index')) {
                $table->index('tanggal', 'mc_tanggal_index');
            }
        });
        
        // Add indexes to kondisi_kesehatan table for better performance
        Schema::table('kondisi_kesehatan', function (Blueprint $table) {
            // Index for name searches
            if (!Schema::hasIndex('kondisi_kesehatan', 'kk_nama_kondisi_index')) {
                $table->index('nama_kondisi', 'kk_nama_kondisi_index');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medical_check_up_kondisi_kesehatan', function (Blueprint $table) {
            // Drop composite index
            $table->dropIndex('mck_medical_check_up_kondisi_index');
            
            // Drop individual indexes if they exist
            if (Schema::hasIndex('medical_check_up_kondisi_kesehatan', 'mck_medical_check_up_index')) {
                $table->dropIndex('mck_medical_check_up_index');
            }
            
            if (Schema::hasIndex('medical_check_up_kondisi_kesehatan', 'mck_kondisi_kesehatan_index')) {
                $table->dropIndex('mck_kondisi_kesehatan_index');
            }
        });
        
        Schema::table('medical_check_up', function (Blueprint $table) {
            // Drop composite index
            $table->dropIndex('mc_karyawan_tanggal_index');
            
            // Drop individual indexes if they exist
            if (Schema::hasIndex('medical_check_up', 'mc_periode_index')) {
                $table->dropIndex('mc_periode_index');
            }
            
            if (Schema::hasIndex('medical_check_up', 'mc_tanggal_index')) {
                $table->dropIndex('mc_tanggal_index');
            }
        });
        
        Schema::table('kondisi_kesehatan', function (Blueprint $table) {
            // Drop index if it exists
            if (Schema::hasIndex('kondisi_kesehatan', 'kk_nama_kondisi_index')) {
                $table->dropIndex('kk_nama_kondisi_index');
            }
        });
    }
};