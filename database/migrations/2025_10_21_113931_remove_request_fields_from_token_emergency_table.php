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
            // Drop foreign keys
            $table->dropForeign(['requested_by']);
            $table->dropForeign(['request_approved_by']);

            // Drop indexes
            $table->dropIndex(['request_status', 'requested_by']);

            // Drop request-related columns
            $table->dropColumn([
                'requested_by',
                'request_quantity',
                'request_status',
                'request_approved_at',
                'request_approved_by'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('token_emergency', function (Blueprint $table) {
            // Add back request-related columns
            $table->unsignedInteger('requested_by')->nullable()->after('generated_by');
            $table->integer('request_quantity')->default(1)->after('requested_by');
            $table->enum('request_status', ['pending', 'approved', 'rejected'])->nullable()->after('request_quantity');
            $table->timestamp('request_approved_at')->nullable()->after('request_status');
            $table->unsignedInteger('request_approved_by')->nullable()->after('request_approved_at');

            // Add back foreign keys
            $table->foreign('requested_by')->references('id_user')->on('user')->onDelete('set null');
            $table->foreign('request_approved_by')->references('id_user')->on('user')->onDelete('set null');

            // Add back index
            $table->index(['request_status', 'requested_by']);
        });
    }
};
