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
            // Hapus kolom yang tidak diperlukan (hanya yang masih ada)
            if (Schema::hasColumn('obat', 'id_jenis_obat')) {
                $table->dropForeign(['id_jenis_obat']);
                $table->dropColumn('id_jenis_obat');
            }

            if (Schema::hasColumn('obat', 'stok_awal')) {
                $table->dropColumn('stok_awal');
            }

            if (Schema::hasColumn('obat', 'stok_masuk')) {
                $table->dropColumn('stok_masuk');
            }

            if (Schema::hasColumn('obat', 'stok_keluar')) {
                $table->dropColumn('stok_keluar');
            }

            if (Schema::hasColumn('obat', 'stok_akhir')) {
                $table->dropColumn('stok_akhir');
            }

            if (Schema::hasColumn('obat', 'jumlah_per_kemasan')) {
                $table->dropColumn('jumlah_per_kemasan');
            }

            if (Schema::hasColumn('obat', 'harga_per_satuan')) {
                $table->dropColumn('harga_per_satuan');
            }

            if (Schema::hasColumn('obat', 'harga_per_kemasan')) {
                $table->dropColumn('harga_per_kemasan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('obat', function (Blueprint $table) {
            // Tambah kembali kolom yang dihapus
            $table->unsignedInteger('id_jenis_obat')->nullable();
            $table->integer('stok_awal')->nullable()->default(0);
            $table->integer('stok_masuk')->nullable()->default(0);
            $table->integer('stok_keluar')->nullable()->default(0);
            $table->integer('stok_akhir')->nullable()->default(0);
            $table->integer('jumlah_per_kemasan')->default(1);
            $table->decimal('harga_per_satuan', 15)->default(0);
            $table->decimal('harga_per_kemasan', 15);

            // Tambah kembali foreign key
            $table->foreign('id_jenis_obat')->references('id_jenis_obat')->on('jenis_obat')
                  ->onUpdate('cascade')->onDelete('set null');
        });
    }
};
