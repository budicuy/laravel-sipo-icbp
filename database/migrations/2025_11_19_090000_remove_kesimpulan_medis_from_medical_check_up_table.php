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
            $table->dropColumn('kesimpulan_medis');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medical_check_up', function (Blueprint $table) {
            $table->text('kesimpulan_medis')->nullable()->after('dikeluarkan_oleh');
        });
    }
};