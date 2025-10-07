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
        Schema::create('obat', function (Blueprint $table) {
            $table->integer('id_obat', true);
            $table->string('nama_obat', 100)->unique('nama_obat');
            $table->text('keterangan')->nullable();
            $table->integer('id_jenis_obat')->nullable();
            $table->integer('id_satuan')->nullable();
            $table->integer('stok_awal')->nullable()->default(0);
            $table->integer('stok_masuk')->nullable()->default(0);
            $table->integer('stok_keluar')->nullable()->default(0);
            $table->integer('stok_akhir')->nullable()->default(0);
            $table->integer('jumlah_per_kemasan')->default(1);
            $table->decimal('harga_per_satuan', 15)->default(0);
            $table->decimal('harga_per_kemasan', 15);
            $table->dateTime('tanggal_update')->nullable()->useCurrent();

            // Foreign keys
            $table->foreign(['id_jenis_obat'], 'fk_jenis_obat')->references(['id_jenis_obat'])->on('jenis_obat')->onUpdate('cascade')->onDelete('set null');
            $table->foreign(['id_satuan'], 'fk_obat_satuan')->references(['id_satuan'])->on('satuan_obat')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('obat');
    }
};
