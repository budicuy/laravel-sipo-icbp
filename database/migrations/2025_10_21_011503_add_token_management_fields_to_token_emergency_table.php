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
            // Update status enum to include expired
            $table->enum('status', ['available', 'used', 'expired'])->default('available')->change();

            // Add token management fields
            $table->unsignedInteger('generated_by')->nullable()->after('used_at');
            $table->unsignedInteger('requested_by')->nullable()->after('generated_by');
            $table->integer('request_quantity')->default(1)->after('requested_by');
            $table->enum('request_status', ['pending', 'approved', 'rejected'])->nullable()->after('request_quantity');
            $table->timestamp('request_approved_at')->nullable()->after('request_status');
            $table->unsignedInteger('request_approved_by')->nullable()->after('request_approved_at');
            $table->text('notes')->nullable()->after('request_approved_by');

            // Add foreign keys
            $table->foreign('generated_by')->references('id_user')->on('user')->onDelete('set null');
            $table->foreign('requested_by')->references('id_user')->on('user')->onDelete('set null');
            $table->foreign('request_approved_by')->references('id_user')->on('user')->onDelete('set null');

            // Add indexes for performance
            $table->index(['status', 'id_user']);
            $table->index(['request_status', 'requested_by']);
            $table->index('generated_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('token_emergency', function (Blueprint $table) {
            // Drop foreign keys
            $table->dropForeign(['generated_by']);
            $table->dropForeign(['requested_by']);
            $table->dropForeign(['request_approved_by']);

            // Drop indexes
            $table->dropIndex(['status', 'id_user']);
            $table->dropIndex(['request_status', 'requested_by']);
            $table->dropIndex(['generated_by']);

            // Drop columns
            $table->dropColumn([
                'generated_by',
                'requested_by',
                'request_quantity',
                'request_status',
                'request_approved_at',
                'request_approved_by',
                'notes'
            ]);

            // Revert status enum
            $table->enum('status', ['available', 'used'])->default('available')->change();
        });
    }
};
