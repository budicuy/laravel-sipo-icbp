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
        Schema::table('keluhan', function (Blueprint $table) {
            // Change jumlah_obat from smallInteger to integer to support larger values
            $table->integer('jumlah_obat')->unsigned()->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('keluhan', function (Blueprint $table) {
            // Revert back to smallInteger
            $table->smallInteger('jumlah_obat')->unsigned()->nullable()->change();
        });
    }
};
