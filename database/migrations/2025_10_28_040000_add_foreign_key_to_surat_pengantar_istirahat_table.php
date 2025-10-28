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
        Schema::table('surat_pengantar_istirahat', function (Blueprint $table) {
            // First, modify the column type to match the referenced column
            $table->bigInteger('id_emergency')->unsigned()->nullable()->change();

            // Then add foreign key for id_emergency
            $table->foreign('id_emergency')->references('id_emergency')->on('rekam_medis_emergency')
                ->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_pengantar_istirahat', function (Blueprint $table) {
            // Drop foreign key for id_emergency
            $table->dropForeign(['id_emergency']);
        });
    }
};
