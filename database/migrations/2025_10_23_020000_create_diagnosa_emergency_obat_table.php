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
        Schema::create('diagnosa_emergency_obat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_diagnosa_emergency')->constrained('diagnosa_emergency', 'id_diagnosa_emergency')->onDelete('cascade');
            $table->foreignId('id_obat')->constrained('obat', 'id_obat')->onDelete('cascade');
            $table->timestamps();
            
            // Ensure unique combination
            $table->unique(['id_diagnosa_emergency', 'id_obat']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diagnosa_emergency_obat');
    }
};