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
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        try {
            // 1. Periksa apakah kolom id_emergency ada di tabel keluhan
            $columnExists = DB::select("
                SELECT COLUMN_NAME, DATA_TYPE, COLUMN_TYPE
                FROM INFORMATION_SCHEMA.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME = 'keluhan'
                AND COLUMN_NAME = 'id_emergency'
            ");

            if (!empty($columnExists)) {
                $currentType = $columnExists[0]->DATA_TYPE;
                echo "Current id_emergency type in keluhan: {$columnExists[0]->COLUMN_TYPE}\n";

                // 2. Periksa tipe data id_emergency di tabel rekam_medis_emergency
                $emergencyColumnType = DB::select("
                    SELECT DATA_TYPE, COLUMN_TYPE
                    FROM INFORMATION_SCHEMA.COLUMNS
                    WHERE TABLE_SCHEMA = DATABASE()
                    AND TABLE_NAME = 'rekam_medis_emergency'
                    AND COLUMN_NAME = 'id_emergency'
                ")[0];

                echo "rekam_medis_emergency.id_emergency type: {$emergencyColumnType->COLUMN_TYPE}\n";

                // 3. Jika tipe data tidak sama, perbaiki
                if ($currentType !== $emergencyColumnType->DATA_TYPE) {
                    echo "Fixing id_emergency column type in keluhan table...\n";

                    // 3.1. Hapus foreign key jika ada
                    $foreignKeys = DB::select("
                        SELECT CONSTRAINT_NAME
                        FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                        WHERE TABLE_SCHEMA = DATABASE()
                        AND TABLE_NAME = 'keluhan'
                        AND COLUMN_NAME = 'id_emergency'
                        AND REFERENCED_TABLE_NAME = 'rekam_medis_emergency'
                    ");

                    if (!empty($foreignKeys)) {
                        foreach ($foreignKeys as $foreignKey) {
                            echo "Dropping foreign key: {$foreignKey->CONSTRAINT_NAME}\n";
                            DB::statement("ALTER TABLE keluhan DROP FOREIGN KEY {$foreignKey->CONSTRAINT_NAME}");
                        }
                    }

                    // 3.2. Ubah tipe data kolom
                    DB::statement("ALTER TABLE keluhan MODIFY COLUMN id_emergency {$emergencyColumnType->COLUMN_TYPE} NULL");
                    echo "Changed id_emergency column type to {$emergencyColumnType->COLUMN_TYPE}\n";

                    // 3.3. Tambahkan kembali foreign key
                    echo "Re-adding foreign key to rekam_medis_emergency\n";
                    DB::statement("
                        ALTER TABLE keluhan
                        ADD CONSTRAINT keluhan_id_emergency_foreign
                        FOREIGN KEY (id_emergency) REFERENCES rekam_medis_emergency(id_emergency)
                        ON UPDATE CASCADE ON DELETE CASCADE
                    ");

                    echo "Successfully fixed id_emergency column type in keluhan table\n";
                } else {
                    echo "id_emergency column type in keluhan is already correct\n";
                }
            } else {
                // Jika kolom tidak ada, tambahkan dengan tipe data yang benar
                echo "id_emergency column not found in keluhan table. Adding it...\n";

                // Dapatkan tipe data dari tabel rekam_medis_emergency
                $emergencyColumnType = DB::select("
                    SELECT DATA_TYPE, COLUMN_TYPE
                    FROM INFORMATION_SCHEMA.COLUMNS
                    WHERE TABLE_SCHEMA = DATABASE()
                    AND TABLE_NAME = 'rekam_medis_emergency'
                    AND COLUMN_NAME = 'id_emergency'
                ")[0];

                echo "Adding id_emergency column to keluhan with type {$emergencyColumnType->COLUMN_TYPE}\n";
                DB::statement("ALTER TABLE keluhan ADD COLUMN id_emergency {$emergencyColumnType->COLUMN_TYPE} NULL AFTER id_rekam");

                // Tambahkan foreign key
                echo "Adding foreign key to rekam_medis_emergency\n";
                DB::statement("
                    ALTER TABLE keluhan
                    ADD CONSTRAINT keluhan_id_emergency_foreign
                    FOREIGN KEY (id_emergency) REFERENCES rekam_medis_emergency(id_emergency)
                    ON UPDATE CASCADE ON DELETE CASCADE
                ");

                echo "Successfully added id_emergency column to keluhan table\n";
            }

            echo "Migration completed successfully!\n";
        } catch (\Exception $e) {
            echo "Error during migration: " . $e->getMessage() . "\n";
            throw $e;
        } finally {
            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        try {
            echo "Rolling back changes...\n";

            // Hapus foreign key jika ada
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME
                FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME = 'keluhan'
                AND COLUMN_NAME = 'id_emergency'
                AND REFERENCED_TABLE_NAME = 'rekam_medis_emergency'
            ");

            if (!empty($foreignKeys)) {
                foreach ($foreignKeys as $foreignKey) {
                    echo "Dropping foreign key: {$foreignKey->CONSTRAINT_NAME}\n";
                    DB::statement("ALTER TABLE keluhan DROP FOREIGN KEY {$foreignKey->CONSTRAINT_NAME}");
                }
            }

            // Ubah kembali tipe data ke int unsigned
            DB::statement("ALTER TABLE keluhan MODIFY COLUMN id_emergency INT UNSIGNED NULL");
            echo "Changed id_emergency column type back to INT UNSIGNED\n";

            // Tambahkan kembali foreign key
            echo "Re-adding foreign key to rekam_medis_emergency\n";
            DB::statement("
                ALTER TABLE keluhan
                ADD CONSTRAINT keluhan_id_emergency_foreign
                FOREIGN KEY (id_emergency) REFERENCES rekam_medis_emergency(id_emergency)
                ON UPDATE CASCADE ON DELETE CASCADE
            ");

            echo "Rollback completed successfully!\n";
        } catch (\Exception $e) {
            echo "Error during rollback: " . $e->getMessage() . "\n";
            throw $e;
        } finally {
            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }
    }
};