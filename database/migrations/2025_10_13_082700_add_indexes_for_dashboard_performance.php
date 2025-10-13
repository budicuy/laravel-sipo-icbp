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

        // Indexes untuk dashboard query optimization
        Schema::table('rekam_medis', function (Blueprint $table) {
            // Index untuk query tanggal (basic index sudah ada dari migrasi sebelumnya)
            if (!Schema::hasIndex('rekam_medis', 'idx_rekam_tanggal')) {
                $table->index('tanggal_periksa', 'idx_rekam_tanggal');
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

        // Drop indexes
        Schema::table('rekam_medis', function (Blueprint $table) {
            $table->dropIndex('idx_rekam_tanggal');
        });

        Schema::enableForeignKeyConstraints();
    }
};
