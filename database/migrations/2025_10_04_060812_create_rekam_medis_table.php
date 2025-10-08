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
        Schema::create('rekam_medis', function (Blueprint $table) {
            $table->integer('id_rekam', true);
            $table->integer('id_keluarga');
            $table->integer('id_kunjungan')->nullable();
            $table->integer('id_user');
            $table->date('tanggal')->default(DB::raw('CURRENT_DATE'));
            $table->enum('terapi', ['Obat', 'Lab', '-'])->nullable()->default('-');
            $table->text('keterangan')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->decimal('total_biaya', 10)->nullable()->default(0);

            // Foreign keys
            $table->foreign(['id_keluarga'], 'fk_keluarga')->references(['id_keluarga'])->on('keluarga')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign(['id_kunjungan'], 'fk_rekam_kunjungan')->references(['id_kunjungan'])->on('kunjungan')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['id_user'], 'fk_user')->references(['id_user'])->on('user')->onUpdate('cascade')->onDelete('cascade');
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
