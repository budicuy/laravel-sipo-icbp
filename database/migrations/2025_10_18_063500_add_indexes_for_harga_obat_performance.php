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
        Schema::table('harga_obat_per_bulan', function (Blueprint $table) {
            // Index untuk optimasi query fallback harga
            $table->index(['id_obat', 'periode'], 'idx_harga_obat_periode_fallback');

            // Index untuk sorting periode (desc) saat mencari harga terakhir
            $table->index('periode', 'idx_harga_periode_desc');

            // Composite index untuk query validasi continuity
            $table->index(['id_obat', 'periode'], 'idx_harga_obat_periode_validation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('harga_obat_per_bulan', function (Blueprint $table) {
            $table->dropIndex('idx_harga_obat_periode_fallback');
            $table->dropIndex('idx_harga_periode_desc');
            $table->dropIndex('idx_harga_obat_periode_validation');
        });
    }
};
