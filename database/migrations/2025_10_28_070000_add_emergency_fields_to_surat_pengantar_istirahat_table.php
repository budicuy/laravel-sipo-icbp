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
            if (! Schema::hasColumn('surat_pengantar_istirahat', 'id_emergency')) {
                $table->unsignedBigInteger('id_emergency')->nullable()->after('id_rekam');
            }
            if (! Schema::hasColumn('surat_pengantar_istirahat', 'tipe_rekam_medis')) {
                $table->enum('tipe_rekam_medis', ['regular', 'emergency'])->default('regular')->after('id_emergency');
            }

            // Index untuk performa (hanya jika belum ada)
            if (! $this->indexExists('surat_pengantar_istirahat', 'id_emergency')) {
                $table->index('id_emergency');
            }
            if (! $this->indexExists('surat_pengantar_istirahat', 'tipe_rekam_medis')) {
                $table->index('tipe_rekam_medis');
            }
        });
    }

    /**
     * Check if index exists
     */
    private function indexExists($table, $indexName): bool
    {
        $indexes = \Illuminate\Support\Facades\DB::select('
            SELECT INDEX_NAME
            FROM INFORMATION_SCHEMA.STATISTICS
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = ?
            AND INDEX_NAME = ?
        ', [$table, $indexName]);

        return ! empty($indexes);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_pengantar_istirahat', function (Blueprint $table) {
            $table->dropForeign(['id_emergency']);
            $table->dropIndex(['id_emergency']);
            $table->dropIndex(['tipe_rekam_medis']);
            $table->dropColumn(['id_emergency', 'tipe_rekam_medis']);
        });
    }
};
