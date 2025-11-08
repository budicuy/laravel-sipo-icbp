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
        Schema::table('obat', function (Blueprint $table) {
            // Tambahkan kolom status dengan default 'aktif'
            $table->enum('status', ['aktif', 'non-aktif'])->default('aktif')->after('lokasi');
            
            // Tambahkan index untuk performa query filter
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('obat', function (Blueprint $table) {
            // Hapus index terlebih dahulu
            $table->dropIndex(['status']);
            
            // Hapus kolom status
            $table->dropColumn('status');
        });
    }
};