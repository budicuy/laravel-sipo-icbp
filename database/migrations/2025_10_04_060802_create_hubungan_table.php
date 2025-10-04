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
        Schema::create('hubungan', function (Blueprint $table) {
            $table->char('kode_hubungan', 1)->index('kode_hubungan');
            $table->string('hubungan', 20)->nullable();

            $table->primary(['kode_hubungan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hubungan');
    }
};
