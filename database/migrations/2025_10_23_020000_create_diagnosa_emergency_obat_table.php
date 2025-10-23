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
        // Cek apakah tabel sudah ada di database
        if (!Schema::hasTable('diagnosa_emergency_obat')) {
            Schema::create('diagnosa_emergency_obat', function (Blueprint $table) {
                $table->id();

                $table->foreignId('id_diagnosa_emergency')
                    ->constrained('diagnosa_emergency', 'id_diagnosa_emergency')
                    ->onDelete('cascade');

                $table->foreignId('id_obat')
                    ->constrained('obat', 'id_obat')
                    ->onDelete('cascade');

                $table->timestamps();

                // Pastikan kombinasi id_diagnosa_emergency dan id_obat unik
                $table->unique(['id_diagnosa_emergency', 'id_obat']);
            });
        } else {
            // Jika tabel sudah ada, tambahkan pemeriksaan kolom penting
            Schema::table('diagnosa_emergency_obat', function (Blueprint $table) {
                if (!Schema::hasColumn('diagnosa_emergency_obat', 'id_diagnosa_emergency')) {
                    $table->foreignId('id_diagnosa_emergency')
                        ->constrained('diagnosa_emergency', 'id_diagnosa_emergency')
                        ->onDelete('cascade');
                }

                if (!Schema::hasColumn('diagnosa_emergency_obat', 'id_obat')) {
                    $table->foreignId('id_obat')
                        ->constrained('obat', 'id_obat')
                        ->onDelete('cascade');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diagnosa_emergency_obat');
    }
};
