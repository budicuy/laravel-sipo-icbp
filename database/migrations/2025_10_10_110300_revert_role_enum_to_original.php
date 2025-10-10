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
        // Update new roles back to 'User' first
        DB::statement("UPDATE `user` SET role = 'User' WHERE role IN ('Dokter', 'Perawat', 'Apoteker')");

        // Then modify the enum back to original
        DB::statement("ALTER TABLE `user` MODIFY COLUMN `role` ENUM('Super Admin', 'Admin', 'User') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Re-add the new roles
        DB::statement("ALTER TABLE `user` MODIFY COLUMN `role` ENUM('Super Admin', 'Admin', 'Dokter', 'Perawat', 'Apoteker') NOT NULL");
    }
};
