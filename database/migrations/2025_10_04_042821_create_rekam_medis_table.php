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
        Schema::create('rekam_medis', function (Blueprint $table) {
            $table->integer('id_rekam', true);
            $table->integer('id_pasien')->index('fk_pasien');
            $table->integer('id_kunjungan')->nullable()->index('fk_rekam_kunjungan');
            $table->integer('id_user')->index('fk_user');
            $table->date('tanggal')->default('CURRENT_DATE');
            $table->enum('terapi', ['Obat', 'Lab', '-'])->nullable()->default('-');
            $table->integer('id_penyakit')->nullable()->index('fk_penyakit_rekam');
            $table->text('keterangan')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->decimal('total_biaya', 10)->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rekam_medis');
    }
};
