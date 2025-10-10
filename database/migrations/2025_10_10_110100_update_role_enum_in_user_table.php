<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update existing 'User' role to 'Admin' first to avoid data truncation
        DB::statement("UPDATE `user` SET role = 'Admin' WHERE role = 'User'");

        // Then modify the enum
        DB::statement("ALTER TABLE `user` MODIFY COLUMN `role` ENUM('Super Admin', 'Admin', 'Dokter', 'Perawat', 'Apoteker') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // First update new roles back to 'User' or 'Admin'
        DB::statement("UPDATE `user` SET role = 'User' WHERE role IN ('Dokter', 'Perawat', 'Apoteker')");

        // Then modify the enum back
        DB::statement("ALTER TABLE `user` MODIFY COLUMN `role` ENUM('Super Admin', 'Admin', 'User') NOT NULL");
    }
};
