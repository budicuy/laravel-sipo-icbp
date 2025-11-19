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
        Schema::create('medical_check_up_kondisi_kesehatan', function (Blueprint $table) {
            $table->unsignedInteger('id')->autoIncrement();
            $table->unsignedInteger('medical_check_up_id');
            $table->unsignedInteger('kondisi_kesehatan_id');
            
            // Audit fields
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('medical_check_up_id')->references('id')->on('medical_check_up')->onDelete('cascade');
            $table->foreign('kondisi_kesehatan_id')->references('id')->on('kondisi_kesehatan')->onDelete('cascade');
            
            // Unique constraint to prevent duplicate entries
            $table->unique(['medical_check_up_id', 'kondisi_kesehatan_id'], 'mcu_kondisi_unique');
            
            // Indexes for performance
            $table->index('medical_check_up_id');
            $table->index('kondisi_kesehatan_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_check_up_kondisi_kesehatan');
    }
};