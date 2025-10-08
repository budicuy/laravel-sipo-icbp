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
        Schema::create('jenis_obat', function (Blueprint $table) {
            $table->unsignedInteger('id_jenis_obat')->autoIncrement();
            $table->string('nama_jenis_obat', 50)->unique('nama_jenis_obat');
            $table->timestamp('created_at')->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jenis_obat');
    }
};
