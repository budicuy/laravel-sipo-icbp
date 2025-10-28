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
            if (! Schema::hasColumn('surat_pengantar_istirahat', 'id_emergency')) {
                $table->unsignedBigInteger('id_emergency')->nullable()->after('id_rekam');
            }
            if (! Schema::hasColumn('surat_pengantar_istirahat', 'tipe_rekam_medis')) {
                $table->enum('tipe_rekam_medis', ['regular', 'emergency'])->default('regular')->after('id_emergency');
            }

            // Foreign key untuk emergency (hanya jika belum ada)
            if (! Schema::hasColumn('surat_pengantar_istirahat', 'id_emergency')) {
                $table->foreign('id_emergency')->references('id_emergency')->on('rekam_medis_emergency')
                    ->onUpdate('cascade')->onDelete('cascade');
            }

            // Index untuk performa (hanya jika belum ada)
            $table->index('id_emergency');
            $table->index('tipe_rekam_medis');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_pengantar_istirahat', function (Blueprint $table) {
            $table->dropForeign(['id_emergency']);
            $table->dropIndex(['id_emergency']);
            $table->dropIndex(['tipe_rekam_medis']);
            $table->dropColumn(['id_emergency', 'tipe_rekam_medis']);
        });
    }
};
