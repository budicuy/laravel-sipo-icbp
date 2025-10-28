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
        Schema::table('surat_pengantar_istirahat', function (Blueprint $table) {
            // Add id_emergency column if it doesn't exist
            if (! Schema::hasColumn('surat_pengantar_istirahat', 'id_emergency')) {
                $table->unsignedBigInteger('id_emergency')->nullable()->after('id_rekam');
            } else {
                // Modify the column type to match the referenced column
                $table->bigInteger('id_emergency')->unsigned()->nullable()->change();
            }

            // Add foreign key for id_emergency (only if not already exists)
            if (! $this->foreignExists('surat_pengantar_istirahat', 'id_emergency', 'rekam_medis_emergency')) {
                $table->foreign('id_emergency')->references('id_emergency')->on('rekam_medis_emergency')
                    ->onUpdate('cascade')->onDelete('cascade');
            }
        });
    }

    /**
     * Check if foreign key exists
     */
    private function foreignExists($table, $column, $referencedTable): bool
    {
        $foreignKeys = \Illuminate\Support\Facades\DB::select('
            SELECT CONSTRAINT_NAME
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = ?
            AND COLUMN_NAME = ?
            AND REFERENCED_TABLE_NAME = ?
        ', [$table, $column, $referencedTable]);

        return ! empty($foreignKeys);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_pengantar_istirahat', function (Blueprint $table) {
            // Drop foreign key for id_emergency
            $table->dropForeign(['id_emergency']);
        });
    }
};
