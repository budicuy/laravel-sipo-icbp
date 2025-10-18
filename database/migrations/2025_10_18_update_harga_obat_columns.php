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
            // Increase the precision and scale of harga columns to handle larger values
            $table->decimal('harga_per_satuan', 20, 2)->default(0)->change();
            $table->decimal('harga_per_kemasan', 20, 2)->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('harga_obat_per_bulan', function (Blueprint $table) {
            $table->decimal('harga_per_satuan', 15, 2)->default(0)->change();
            $table->decimal('harga_per_kemasan', 15, 2)->default(0)->change();
        });
    }
};
