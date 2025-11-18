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
        Schema::table('ai_chat_histories', function (Blueprint $table) {
            $table->string('kode_hubungan', 10)->nullable()->after('departemen'); // Kode hubungan (null untuk karyawan)
            $table->string('tipe_pengguna', 20)->default('karyawan')->after('kode_hubungan'); // 'karyawan' atau 'keluarga'

            // Indexes for performance
            $table->index(['tipe_pengguna']);
            $table->index(['kode_hubungan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_chat_histories', function (Blueprint $table) {
            $table->dropIndex(['tipe_pengguna']);
            $table->dropIndex(['kode_hubungan']);
            $table->dropColumn(['kode_hubungan', 'tipe_pengguna']);
        });
    }
};
