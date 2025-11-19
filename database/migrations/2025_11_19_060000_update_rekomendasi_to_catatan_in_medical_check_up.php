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
            // Drop the existing rekomendasi column (text)
            $table->dropColumn('rekomendasi');
        });
        
        Schema::table('medical_check_up', function (Blueprint $table) {
            // Add catatan as enum with the specified options
            $table->enum('catatan', [
                'Fit',
                'Fit dengan Catatan', 
                'Fit dalam Pengawasan'
            ])->nullable()->comment('Catatan medical check up');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medical_check_up', function (Blueprint $table) {
            // Drop the new catatan column
            $table->dropColumn('catatan');
        });
        
        Schema::table('medical_check_up', function (Blueprint $table) {
            // Restore the original rekomendasi column as text
            $table->text('rekomendasi')->nullable();
        });
    }
};