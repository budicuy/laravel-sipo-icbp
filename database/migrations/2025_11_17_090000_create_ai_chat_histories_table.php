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
        Schema::create('ai_chat_histories', function (Blueprint $table) {
            $table->id();
            $table->string('nik', 20)->index(); // NIK karyawan
            $table->string('nama_karyawan', 100); // Nama lengkap karyawan
            $table->string('departemen', 100)->nullable(); // Departemen karyawan
            $table->integer('login_count')->default(1); // Jumlah kali login
            $table->timestamp('last_login_at'); // Waktu terakhir login
            $table->timestamp('last_ai_chat_access_at')->nullable(); // Waktu terakhir akses AI chat
            $table->integer('ai_chat_access_count')->default(0); // Jumlah kali akses AI chat
            $table->timestamps(); // created_at, updated_at

            // Indexes for performance
            $table->index(['nik', 'last_login_at']);
            $table->index(['last_ai_chat_access_at']);
            $table->index(['ai_chat_access_count'], 'ai_chat_access_count_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_chat_histories');
    }
};
