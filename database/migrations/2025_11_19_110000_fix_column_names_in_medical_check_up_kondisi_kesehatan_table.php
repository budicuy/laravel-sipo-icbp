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
        Schema::table('medical_check_up_kondisi_kesehatan', function (Blueprint $table) {
            $table->renameColumn('medical_check_up_id', 'id_medical_check_up');
            $table->renameColumn('kondisi_kesehatan_id', 'id_kondisi_kesehatan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medical_check_up_kondisi_kesehatan', function (Blueprint $table) {
            $table->renameColumn('id_medical_check_up', 'medical_check_up_id');
            $table->renameColumn('id_kondisi_kesehatan', 'kondisi_kesehatan_id');
        });
    }
};