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
            // 1. Perbaiki foreign key di tabel rekam_medis_emergency
            echo "Checking existing foreign keys in rekam_medis_emergency...\n";

            // Get column types first
            $rekamMedisColumn = DB::select("
                SELECT DATA_TYPE, COLUMN_TYPE
                FROM INFORMATION_SCHEMA.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME = 'rekam_medis_emergency'
                AND COLUMN_NAME = 'id_external_employee'
            ")[0];

            $externalEmployeeColumn = DB::select("
                SELECT DATA_TYPE, COLUMN_TYPE
                FROM INFORMATION_SCHEMA.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME = 'external_employees'
                AND COLUMN_NAME = 'id_external_employee'
            ")[0];

            echo "rekam_medis_emergency.id_external_employee type: {$rekamMedisColumn->COLUMN_TYPE}\n";
            echo "external_employees.id_external_employee type: {$externalEmployeeColumn->COLUMN_TYPE}\n";

            // Check if types are compatible
            if ($rekamMedisColumn->DATA_TYPE !== $externalEmployeeColumn->DATA_TYPE) {
                echo "Column types are incompatible. Fixing id_external_employee column type...\n";

                // Drop existing foreign keys if any
                $foreignKeys = DB::select("
                    SELECT CONSTRAINT_NAME
                    FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                    WHERE TABLE_SCHEMA = DATABASE()
                    AND TABLE_NAME = 'rekam_medis_emergency'
                    AND REFERENCED_TABLE_NAME = 'external_employees'
                ");

                if (!empty($foreignKeys)) {
                    foreach ($foreignKeys as $foreignKey) {
                        echo "Dropping foreign key: {$foreignKey->CONSTRAINT_NAME}\n";
                        DB::statement("ALTER TABLE rekam_medis_emergency DROP FOREIGN KEY {$foreignKey->CONSTRAINT_NAME}");
                    }
                }

                // Change column type to match external_employees.id (bigint unsigned)
                DB::statement("ALTER TABLE rekam_medis_emergency MODIFY COLUMN id_external_employee BIGINT UNSIGNED");
                echo "Changed id_external_employee column type to BIGINT UNSIGNED\n";
            }

            // Get all foreign keys for rekam_medis_emergency
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME
                FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME = 'rekam_medis_emergency'
                AND REFERENCED_TABLE_NAME = 'external_employees'
            ");

            // Drop existing foreign keys if any
            if (!empty($foreignKeys)) {
                foreach ($foreignKeys as $foreignKey) {
                    echo "Dropping foreign key: {$foreignKey->CONSTRAINT_NAME}\n";
                    DB::statement("ALTER TABLE rekam_medis_emergency DROP FOREIGN KEY {$foreignKey->CONSTRAINT_NAME}");
                }
            }

            // Add correct foreign key
            echo "Adding correct foreign key to external_employees.id\n";
            DB::statement("
                ALTER TABLE rekam_medis_emergency
                ADD CONSTRAINT rekam_medis_emergency_id_external_employee_foreign
                FOREIGN KEY (id_external_employee) REFERENCES external_employees(id_external_employee)
                ON UPDATE CASCADE ON DELETE RESTRICT
            ");

            // 2. Hapus kolom id_keluhan dari tabel rekam_medis_emergency jika ada
            echo "Checking for id_keluhan column in rekam_medis_emergency...\n";
            $columnExists = DB::select("
                SELECT COLUMN_NAME
                FROM INFORMATION_SCHEMA.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME = 'rekam_medis_emergency'
                AND COLUMN_NAME = 'id_keluhan'
            ");

            if (!empty($columnExists)) {
                echo "Dropping id_keluhan column from rekam_medis_emergency\n";

                // Drop foreign key first if exists
                $keluhanForeignKeys = DB::select("
                    SELECT CONSTRAINT_NAME
                    FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                    WHERE TABLE_SCHEMA = DATABASE()
                    AND TABLE_NAME = 'rekam_medis_emergency'
                    AND COLUMN_NAME = 'id_keluhan'
                    AND REFERENCED_TABLE_NAME = 'keluhan'
                ");

                if (!empty($keluhanForeignKeys)) {
                    foreach ($keluhanForeignKeys as $foreignKey) {
                        echo "Dropping foreign key: {$foreignKey->CONSTRAINT_NAME}\n";
                        DB::statement("ALTER TABLE rekam_medis_emergency DROP FOREIGN KEY {$foreignKey->CONSTRAINT_NAME}");
                    }
                }

                // Drop column
                DB::statement("ALTER TABLE rekam_medis_emergency DROP COLUMN id_keluhan");
            }

            // 3. Tambahkan kolom id_emergency ke tabel keluhan dengan tipe data yang sama dengan rekam_medis_emergency
            echo "Checking for id_emergency column in keluhan...\n";
            $emergencyColumnExists = DB::select("
                SELECT COLUMN_NAME, DATA_TYPE, COLUMN_TYPE
                FROM INFORMATION_SCHEMA.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME = 'keluhan'
                AND COLUMN_NAME = 'id_emergency'
            ");

            // Get the type of id_emergency in rekam_medis_emergency
            $emergencyColumnType = DB::select("
                SELECT DATA_TYPE, COLUMN_TYPE
                FROM INFORMATION_SCHEMA.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME = 'rekam_medis_emergency'
                AND COLUMN_NAME = 'id_emergency'
            ")[0];

            echo "rekam_medis_emergency.id_emergency type: {$emergencyColumnType->COLUMN_TYPE}\n";

            if (empty($emergencyColumnExists)) {
                echo "Adding id_emergency column to keluhan with type {$emergencyColumnType->COLUMN_TYPE}\n";
                DB::statement("ALTER TABLE keluhan ADD COLUMN id_emergency {$emergencyColumnType->COLUMN_TYPE} NULL AFTER id_rekam");

                // Add foreign key
                echo "Adding foreign key to rekam_medis_emergency\n";
                DB::statement("
                    ALTER TABLE keluhan
                    ADD CONSTRAINT keluhan_id_emergency_foreign
                    FOREIGN KEY (id_emergency) REFERENCES rekam_medis_emergency(id_emergency)
                    ON UPDATE CASCADE ON DELETE CASCADE
                ");
            } else {
                // Check if column type needs to be updated
                if ($emergencyColumnExists[0]->DATA_TYPE !== $emergencyColumnType->DATA_TYPE) {
                    echo "Updating id_emergency column type to {$emergencyColumnType->COLUMN_TYPE}\n";

                    // Drop foreign key first if exists
                    $keluhanForeignKeys = DB::select("
                        SELECT CONSTRAINT_NAME
                        FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                        WHERE TABLE_SCHEMA = DATABASE()
                        AND TABLE_NAME = 'keluhan'
                        AND COLUMN_NAME = 'id_emergency'
                        AND REFERENCED_TABLE_NAME = 'rekam_medis_emergency'
                    ");

                    if (!empty($keluhanForeignKeys)) {
                        foreach ($keluhanForeignKeys as $foreignKey) {
                            echo "Dropping foreign key: {$foreignKey->CONSTRAINT_NAME}\n";
                            DB::statement("ALTER TABLE keluhan DROP FOREIGN KEY {$foreignKey->CONSTRAINT_NAME}");
                        }
                    }

                    // Update column type to match rekam_medis_emergency.id_emergency
                    DB::statement("ALTER TABLE keluhan MODIFY COLUMN id_emergency {$emergencyColumnType->COLUMN_TYPE} NULL");

                    // Re-add foreign key
                    echo "Re-adding foreign key to rekam_medis_emergency\n";
                    DB::statement("
                        ALTER TABLE keluhan
                        ADD CONSTRAINT keluhan_id_emergency_foreign
                        FOREIGN KEY (id_emergency) REFERENCES rekam_medis_emergency(id_emergency)
                        ON UPDATE CASCADE ON DELETE CASCADE
                    ");
                }
            }

            // 4. Tambahkan kolom id_diagnosa_emergency ke tabel keluhan
            echo "Checking for id_diagnosa_emergency column in keluhan...\n";
            $diagnosaColumnExists = DB::select("
                SELECT COLUMN_NAME
                FROM INFORMATION_SCHEMA.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME = 'keluhan'
                AND COLUMN_NAME = 'id_diagnosa_emergency'
            ");

            if (empty($diagnosaColumnExists)) {
                echo "Adding id_diagnosa_emergency column to keluhan\n";
                DB::statement("ALTER TABLE keluhan ADD COLUMN id_diagnosa_emergency INT UNSIGNED NULL AFTER id_diagnosa");

                // Add foreign key
                echo "Adding foreign key to diagnosa_emergency\n";
                DB::statement("
                    ALTER TABLE keluhan
                    ADD CONSTRAINT keluhan_id_diagnosa_emergency_foreign
                    FOREIGN KEY (id_diagnosa_emergency) REFERENCES diagnosa_emergency(id_diagnosa_emergency)
                    ON UPDATE CASCADE ON DELETE CASCADE
                ");
            }

            // 4. Buat pivot table diagnosa_emergency_obat
            echo "Checking for diagnosa_emergency_obat table...\n";
            $tableExists = DB::select("
                SELECT TABLE_NAME
                FROM INFORMATION_SCHEMA.TABLES
                WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME = 'diagnosa_emergency_obat'
            ");

            if (empty($tableExists)) {
                echo "Creating diagnosa_emergency_obat pivot table\n";
                DB::statement("
                    CREATE TABLE diagnosa_emergency_obat (
                        id_diagnosa_emergency INT UNSIGNED NOT NULL,
                        id_obat INT UNSIGNED NOT NULL,
                        created_at TIMESTAMP NULL DEFAULT NULL,
                        updated_at TIMESTAMP NULL DEFAULT NULL,
                        PRIMARY KEY (id_diagnosa_emergency, id_obat),
                        CONSTRAINT fk_diagnosa_emergency_obat_diagnosa FOREIGN KEY (id_diagnosa_emergency) REFERENCES diagnosa_emergency (id_diagnosa_emergency) ON DELETE CASCADE ON UPDATE CASCADE,
                        CONSTRAINT fk_diagnosa_emergency_obat_obat FOREIGN KEY (id_obat) REFERENCES obat (id_obat) ON DELETE CASCADE ON UPDATE CASCADE
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
                ");
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

            // 1. Drop pivot table
            if (Schema::hasTable('diagnosa_emergency_obat')) {
                Schema::dropIfExists('diagnosa_emergency_obat');
                echo "Dropped diagnosa_emergency_obat table\n";
            }

            // 2. Hapus kolom id_diagnosa_emergency dari tabel keluhan
            if (Schema::hasColumn('keluhan', 'id_diagnosa_emergency')) {
                Schema::table('keluhan', function (Blueprint $table) {
                    $table->dropForeign(['id_diagnosa_emergency']);
                    $table->dropColumn('id_diagnosa_emergency');
                });
                echo "Dropped id_diagnosa_emergency column from keluhan\n";
            }

            // 3. Kembalikan kolom id_keluhan ke tabel rekam_medis_emergency
            if (!Schema::hasColumn('rekam_medis_emergency', 'id_keluhan')) {
                Schema::table('rekam_medis_emergency', function (Blueprint $table) {
                    $table->unsignedInteger('id_keluhan')->nullable()->after('catatan');
                    $table->foreign('id_keluhan')
                          ->references('id_keluhan')
                          ->on('keluhan')
                          ->onUpdate('cascade')
                          ->onDelete('set null');
                });
                echo "Restored id_keluhan column to rekam_medis_emergency\n";
            }

            // 4. Kembalikan foreign key lama di rekam_medis_emergency
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME
                FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME = 'rekam_medis_emergency'
                AND COLUMN_NAME = 'id_external_employee'
                AND REFERENCED_TABLE_NAME = 'external_employees'
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
            echo "Restored original foreign key\n";

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
