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
        // 1. Perbaiki foreign key di tabel rekam_medis_emergency dengan cara manual
        try {
            // Cek nama foreign key yang ada
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'rekam_medis_emergency' 
                AND COLUMN_NAME = 'id_external_employee' 
                AND REFERENCED_TABLE_NAME IS NOT NULL
            ");
            
            // Drop existing foreign key jika ada
            if (!empty($foreignKeys)) {
                foreach ($foreignKeys as $foreignKey) {
                    DB::statement("ALTER TABLE rekam_medis_emergency DROP FOREIGN KEY {$foreignKey->CONSTRAINT_NAME}");
                }
            }
            
            // Add correct foreign key reference to external_employees.id
            DB::statement("
                ALTER TABLE rekam_medis_emergency 
                ADD CONSTRAINT rekam_medis_emergency_id_external_employee_foreign 
                FOREIGN KEY (id_external_employee) REFERENCES external_employees(id) 
                ON UPDATE CASCADE ON DELETE RESTRICT
            ");
        } catch (\Exception $e) {
            // Log error tapi lanjutkan
            echo "Warning: Failed to update foreign key for rekam_medis_emergency: " . $e->getMessage() . "\n";
        }

        // 2. Tambahkan kolom id_emergency ke tabel keluhan
        try {
            // Cek apakah kolom sudah ada
            $columnExists = DB::select("
                SELECT COLUMN_NAME 
                FROM INFORMATION_SCHEMA.COLUMNS 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'keluhan' 
                AND COLUMN_NAME = 'id_emergency'
            ");
            
            if (empty($columnExists)) {
                // Tambahkan kolom
                DB::statement("ALTER TABLE keluhan ADD COLUMN id_emergency INT UNSIGNED NULL AFTER id_rekam");
                
                // Tambahkan foreign key
                DB::statement("
                    ALTER TABLE keluhan 
                    ADD CONSTRAINT keluhan_id_emergency_foreign 
                    FOREIGN KEY (id_emergency) REFERENCES rekam_medis_emergency(id_emergency) 
                    ON UPDATE CASCADE ON DELETE CASCADE
                ");
            }
        } catch (\Exception $e) {
            echo "Warning: Failed to add id_emergency column to keluhan: " . $e->getMessage() . "\n";
        }

        // 3. Buat pivot table diagnosa_emergency_obat
        try {
            // Cek apakah tabel sudah ada
            $tableExists = DB::select("
                SELECT TABLE_NAME 
                FROM INFORMATION_SCHEMA.TABLES 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'diagnosa_emergency_obat'
            ");
            
            if (empty($tableExists)) {
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
        } catch (\Exception $e) {
            echo "Warning: Failed to create diagnosa_emergency_obat table: " . $e->getMessage() . "\n";
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Drop pivot table
        try {
            Schema::dropIfExists('diagnosa_emergency_obat');
        } catch (\Exception $e) {
            echo "Warning: Failed to drop diagnosa_emergency_obat table: " . $e->getMessage() . "\n";
        }

        // 2. Hapus kolom id_emergency dari tabel keluhan
        try {
            // Cek apakah foreign key ada
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'keluhan' 
                AND COLUMN_NAME = 'id_emergency' 
                AND REFERENCED_TABLE_NAME IS NOT NULL
            ");
            
            if (!empty($foreignKeys)) {
                foreach ($foreignKeys as $foreignKey) {
                    DB::statement("ALTER TABLE keluhan DROP FOREIGN KEY {$foreignKey->CONSTRAINT_NAME}");
                }
            }
            
            // Cek apakah kolom ada
            $columnExists = DB::select("
                SELECT COLUMN_NAME 
                FROM INFORMATION_SCHEMA.COLUMNS 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'keluhan' 
                AND COLUMN_NAME = 'id_emergency'
            ");
            
            if (!empty($columnExists)) {
                DB::statement("ALTER TABLE keluhan DROP COLUMN id_emergency");
            }
        } catch (\Exception $e) {
            echo "Warning: Failed to drop id_emergency column from keluhan: " . $e->getMessage() . "\n";
        }

        // 3. Kembalikan foreign key lama di rekam_medis_emergency
        try {
            // Cek apakah foreign key ada
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'rekam_medis_emergency' 
                AND COLUMN_NAME = 'id_external_employee' 
                AND REFERENCED_TABLE_NAME IS NOT NULL
            ");
            
            if (!empty($foreignKeys)) {
                foreach ($foreignKeys as $foreignKey) {
                    DB::statement("ALTER TABLE rekam_medis_emergency DROP FOREIGN KEY {$foreignKey->CONSTRAINT_NAME}");
                }
            }
            
            // Kembalikan ke foreign key lama (salah)
            DB::statement("
                ALTER TABLE rekam_medis_emergency 
                ADD CONSTRAINT rekam_medis_emergency_id_external_employee_foreign 
                FOREIGN KEY (id_external_employee) REFERENCES external_employees(id_external_employee) 
                ON UPDATE CASCADE ON DELETE RESTRICT
            ");
        } catch (\Exception $e) {
            echo "Warning: Failed to restore original foreign key: " . $e->getMessage() . "\n";
        }
    }
};