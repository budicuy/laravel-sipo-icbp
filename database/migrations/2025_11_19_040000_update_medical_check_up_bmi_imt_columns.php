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
            // Drop the existing enum columns
            $table->dropColumn('bmi');
            $table->dropColumn('imt');
        });
        
        Schema::table('medical_check_up', function (Blueprint $table) {
            // Add BMI as decimal (for numbers with decimals)
            $table->decimal('bmi', 5, 2)->nullable()->comment('BMI value as decimal number');
            
            // Add IMT as enum with BMI categories
            $table->enum('imt', [
                'Underweight', 
                'Normal', 
                'Overweight', 
                'Obesitas Tk 1', 
                'Obesitas Tk 2', 
                'Obesitas Tk 3'
            ])->nullable()->comment('BMI category/keterangan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medical_check_up', function (Blueprint $table) {
            // Drop the new columns
            $table->dropColumn('bmi');
            $table->dropColumn('imt');
        });
        
        Schema::table('medical_check_up', function (Blueprint $table) {
            // Restore the original enum columns
            $table->enum('bmi', [
                'Underweight', 
                'Normal', 
                'Overweight', 
                'Obesitas Tk 1', 
                'Obesitas Tk 2', 
                'Obesitas Tk 3'
            ])->nullable();
            
            $table->enum('imt', [
                'Kurus', 
                'Normal', 
                'Gemuk', 
                'Obesitas'
            ])->nullable();
        });
    }
};