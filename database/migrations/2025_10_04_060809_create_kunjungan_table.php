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
        Schema::create('kunjungan', function (Blueprint $table) {
            $table->integer('id_kunjungan', true);
            $table->integer('id_keluarga')->index('fk_kunjungan_keluarga');
            $table->string('kode_transaksi', 50);
            $table->date('tanggal_kunjungan')->default(DB::raw('CURRENT_DATE'));
            $table->timestamp('created_at')->nullable()->useCurrent();

            // Foreign keys
            $table->foreign(['id_keluarga'], 'fk_kunjungan_keluarga')->references(['id_keluarga'])->on('keluarga')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kunjungan');
    }
};
