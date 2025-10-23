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
        Schema::disableForeignKeyConstraints();

        // 1. Perbaiki foreign key di tabel rekam_medis_emergency
        Schema::table('rekam_medis_emergency', function (Blueprint $table) {
            // Cek dan drop foreign key lama jika ada
            try {
                $table->dropForeign(['id_external_employee']);
            } catch (\Exception $e) {
                // Foreign key tidak ada atau sudah di-drop, lanjutkan
            }
            
            // Add correct foreign key reference to external_employees.id
            $table->foreign('id_external_employee')
                  ->references('id')
                  ->on('external_employees')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');
        });

        // 2. Hapus kolom id_keluhan dari tabel rekam_medis_emergency jika ada
        if (Schema::hasColumn('rekam_medis_emergency', 'id_keluhan')) {
            Schema::table('rekam_medis_emergency', function (Blueprint $table) {
                // Drop foreign key first if exists
                try {
                    $table->dropForeign(['id_keluhan']);
                } catch (\Exception $e) {
                    // Foreign key tidak ada, lanjutkan
                }
                
                // Drop column
                $table->dropColumn('id_keluhan');
            });
        }

        // 3. Tambahkan kolom id_diagnosa_emergency ke tabel keluhan
        Schema::table('keluhan', function (Blueprint $table) {
            // Cek apakah kolom sudah ada
            if (!Schema::hasColumn('keluhan', 'id_diagnosa_emergency')) {
                // Tambahkan kolom id_diagnosa_emergency yang nullable
                $table->unsignedInteger('id_diagnosa_emergency')->nullable()->after('id_diagnosa');
                
                // Tambahkan foreign key ke diagnosa_emergency
                $table->foreign('id_diagnosa_emergency')
                      ->references('id_diagnosa_emergency')
                      ->on('diagnosa_emergency')
                      ->onUpdate('cascade')
                      ->onDelete('cascade');
            }
        });

        // 4. Buat pivot table diagnosa_emergency_obat untuk relasi many-to-many
        if (!Schema::hasTable('diagnosa_emergency_obat')) {
            Schema::create('diagnosa_emergency_obat', function (Blueprint $table) {
                $table->unsignedInteger('id_diagnosa_emergency');
                $table->unsignedInteger('id_obat');
                
                // Composite primary key
                $table->primary(['id_diagnosa_emergency', 'id_obat']);
                
                // Foreign keys
                $table->foreign('id_diagnosa_emergency')
                      ->references('id_diagnosa_emergency')
                      ->on('diagnosa_emergency')
                      ->onUpdate('cascade')
                      ->onDelete('cascade');
                      
                $table->foreign('id_obat')
                      ->references('id_obat')
                      ->on('obat')
                      ->onUpdate('cascade')
                      ->onDelete('cascade');
                
                // Tambahkan timestamps untuk tracking
                $table->timestamps();
            });
        }

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        // 1. Drop pivot table
        Schema::dropIfExists('diagnosa_emergency_obat');

        // 2. Hapus kolom id_diagnosa_emergency dari tabel keluhan
        Schema::table('keluhan', function (Blueprint $table) {
            // Cek apakah foreign key ada sebelum di-drop
            try {
                $table->dropForeign(['id_diagnosa_emergency']);
            } catch (\Exception $e) {
                // Foreign key tidak ada, lanjutkan
            }
            
            // Cek apakah kolom ada sebelum di-drop
            if (Schema::hasColumn('keluhan', 'id_diagnosa_emergency')) {
                $table->dropColumn('id_diagnosa_emergency');
            }
        });

        // 3. Kembalikan kolom id_keluhan ke tabel rekam_medis_emergency
        Schema::table('rekam_medis_emergency', function (Blueprint $table) {
            // Cek apakah kolom sudah ada
            if (!Schema::hasColumn('rekam_medis_emergency', 'id_keluhan')) {
                // Tambahkan kolom id_keluhan yang nullable
                $table->unsignedInteger('id_keluhan')->nullable()->after('catatan');
                
                // Tambahkan foreign key ke keluhan
                $table->foreign('id_keluhan')
                      ->references('id_keluhan')
                      ->on('keluhan')
                      ->onUpdate('cascade')
                      ->onDelete('set null');
            }
        });

        // 4. Kembalikan foreign key lama di rekam_medis_emergency
        Schema::table('rekam_medis_emergency', function (Blueprint $table) {
            // Cek apakah foreign key ada sebelum di-drop
            try {
                $table->dropForeign(['id_external_employee']);
            } catch (\Exception $e) {
                // Foreign key tidak ada, lanjutkan
            }
            
            // Kembalikan ke foreign key lama (salah)
            $table->foreign('id_external_employee')
                  ->references('id_external_employee')
                  ->on('external_employees')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');
        });

        Schema::enableForeignKeyConstraints();
    }
};