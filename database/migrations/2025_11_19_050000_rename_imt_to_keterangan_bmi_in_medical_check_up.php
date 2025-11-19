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
            // Rename imt column to keterangan_bmi
            $table->renameColumn('imt', 'keterangan_bmi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medical_check_up', function (Blueprint $table) {
            // Rename keterangan_bmi back to imt
            $table->renameColumn('keterangan_bmi', 'imt');
        });
    }
};