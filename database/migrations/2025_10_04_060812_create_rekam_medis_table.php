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
            $table->unsignedInteger('id_rekam')->autoIncrement();
            $table->unsignedInteger('id_keluarga');
            $table->date('tanggal_periksa');
            $table->unsignedInteger('id_user');
            $table->integer('jumlah_keluhan');
            $table->timestamp('created_at')->nullable()->useCurrent();
            // Foreign keys
            $table->foreign(['id_keluarga'], 'fk_keluarga')->references(['id_keluarga'])->on('keluarga')->onUpdate('cascade')->onDelete('cascade');
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
