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
        Schema::table('medical_check_up', function (Blueprint $table) {
            $table->unsignedInteger('id_kondisi_kesehatan')->nullable()->after('keterangan_bmi');
            
            // Add foreign key constraint
            $table->foreign('id_kondisi_kesehatan')->references('id')->on('kondisi_kesehatan')->onDelete('set null');
            
            // Add index for performance
            $table->index('id_kondisi_kesehatan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medical_check_up', function (Blueprint $table) {
            // Drop foreign key constraint first
            $table->dropForeign(['id_kondisi_kesehatan']);
            
            // Drop the column
            $table->dropColumn('id_kondisi_kesehatan');
        });
    }
};