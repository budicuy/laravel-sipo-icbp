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
            // Hapus kolom-kolom harga dari tabel obat
            $table->dropColumn(['jumlah_per_kemasan', 'harga_per_satuan', 'harga_per_kemasan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('obat', function (Blueprint $table) {
            // Tambah kembali kolom-kolom harga jika rollback
            $table->integer('jumlah_per_kemasan')->default(1)->after('id_satuan');
            $table->decimal('harga_per_satuan', 15, 2)->default(0)->after('jumlah_per_kemasan');
            $table->decimal('harga_per_kemasan', 15, 2)->default(0)->after('harga_per_satuan');
        });
    }
};
