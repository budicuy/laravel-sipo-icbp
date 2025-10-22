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
            // Check if the column exists and is NOT NULL
            $columnInfo = DB::select("
                SELECT COLUMN_NAME, IS_NULLABLE
                FROM INFORMATION_SCHEMA.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME = 'keluhan'
                AND COLUMN_NAME = 'id_keluarga'
            ");

            if (!empty($columnInfo) && $columnInfo[0]->IS_NULLABLE === 'NO') {
                echo "Making id_keluarga column nullable in keluhan table...\n";

                // Drop foreign key if exists
                $foreignKeys = DB::select("
                    SELECT CONSTRAINT_NAME
                    FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                    WHERE TABLE_SCHEMA = DATABASE()
                    AND TABLE_NAME = 'keluhan'
                    AND COLUMN_NAME = 'id_keluarga'
                    AND REFERENCED_TABLE_NAME = 'keluarga'
                ");

                if (!empty($foreignKeys)) {
                    foreach ($foreignKeys as $foreignKey) {
                        echo "Dropping foreign key: {$foreignKey->CONSTRAINT_NAME}\n";
                        DB::statement("ALTER TABLE keluhan DROP FOREIGN KEY {$foreignKey->CONSTRAINT_NAME}");
                    }
                }

                // Make column nullable
                DB::statement("ALTER TABLE keluhan MODIFY COLUMN id_keluarga INT UNSIGNED NULL");
                echo "Made id_keluarga column nullable\n";

                // Re-add foreign key with ON DELETE SET NULL
                echo "Re-adding foreign key to keluarga with ON DELETE SET NULL\n";
                DB::statement("
                    ALTER TABLE keluhan
                    ADD CONSTRAINT keluhan_id_keluarga_foreign
                    FOREIGN KEY (id_keluarga) REFERENCES keluarga(id_keluarga)
                    ON UPDATE CASCADE ON DELETE SET NULL
                ");

                echo "Successfully made id_keluarga column nullable in keluhan table\n";
            } else {
                echo "id_keluarga column is already nullable or doesn't exist\n";
            }
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

            // Drop foreign key if exists
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME
                FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME = 'keluhan'
                AND COLUMN_NAME = 'id_keluarga'
                AND REFERENCED_TABLE_NAME = 'keluarga'
            ");

            if (!empty($foreignKeys)) {
                foreach ($foreignKeys as $foreignKey) {
                    echo "Dropping foreign key: {$foreignKey->CONSTRAINT_NAME}\n";
                    DB::statement("ALTER TABLE keluhan DROP FOREIGN KEY {$foreignKey->CONSTRAINT_NAME}");
                }
            }

            // Make column NOT NULL again
            DB::statement("ALTER TABLE keluhan MODIFY COLUMN id_keluarga INT UNSIGNED NOT NULL");
            echo "Made id_keluarga column NOT NULL again\n";

            // Re-add foreign key with original behavior
            echo "Re-adding foreign key to keluarga\n";
            DB::statement("
                ALTER TABLE keluhan
                ADD CONSTRAINT keluhan_id_keluarga_foreign
                FOREIGN KEY (id_keluarga) REFERENCES keluarga(id_keluarga)
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