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
        Schema::table('token_emergency', function (Blueprint $table) {
            $table->unsignedInteger('used_by')->nullable()->after('used_at');

            // Add foreign key
            $table->foreign('used_by')->references('id_user')->on('user')->onDelete('set null');

            // Add index for performance
            $table->index('used_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('token_emergency', function (Blueprint $table) {
            // Drop foreign key
            $table->dropForeign(['used_by']);

            // Drop index
            $table->dropIndex('used_by');

            // Drop column
            $table->dropColumn('used_by');
        });
    }
};
