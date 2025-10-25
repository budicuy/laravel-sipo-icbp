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
        Schema::table('harga_obat_per_bulan', function (Blueprint $table) {
            // Change harga_per_satuan from decimal(15, 2) to decimal(20, 3)
            $table->decimal('harga_per_satuan', 20, 3)->default(0)->change();
            
            // Change harga_per_kemasan from decimal(15, 2) to decimal(20, 3)
            $table->decimal('harga_per_kemasan', 20, 3)->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('harga_obat_per_bulan', function (Blueprint $table) {
            // Revert harga_per_satuan back to decimal(15, 2)
            $table->decimal('harga_per_satuan', 15, 2)->default(0)->change();
            
            // Revert harga_per_kemasan back to decimal(15, 2)
            $table->decimal('harga_per_kemasan', 15, 2)->default(0)->change();
        });
    }
};