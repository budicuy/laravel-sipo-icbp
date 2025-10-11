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
            // Hapus kolom-kolom stok dari tabel obat
            $table->dropColumn(['stok_awal', 'stok_masuk', 'stok_keluar', 'stok_akhir']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('obat', function (Blueprint $table) {
            // Tambah kembali kolom-kolom stok jika rollback
            $table->integer('stok_awal')->nullable()->default(0)->after('id_satuan');
            $table->integer('stok_masuk')->nullable()->default(0)->after('stok_awal');
            $table->integer('stok_keluar')->nullable()->default(0)->after('stok_masuk');
            $table->integer('stok_akhir')->nullable()->default(0)->after('stok_keluar');
        });
    }
};
