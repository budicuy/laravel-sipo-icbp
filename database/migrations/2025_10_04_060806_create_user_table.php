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
        Schema::create('user', function (Blueprint $table) {
            $table->unsignedInteger('id_user')->autoIncrement();
            $table->string('username', 50)->unique('username');
            $table->string('password');
            $table->string('nama_lengkap', 100);
            $table->enum('role', ['Super Admin', 'Admin', 'User']);
            $table->timestamp('created_at')->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user');
    }
};
