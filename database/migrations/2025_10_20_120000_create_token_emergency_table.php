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
        Schema::create('token_emergency', function (Blueprint $table) {
            $table->id('id_token');
            $table->string('token', 6); // Token 4-6 digit
            $table->enum('status', ['available', 'used'])->default('available'); // Status token
            $table->unsignedInteger('id_user')->nullable(); // User yang menggunakan token
            $table->timestamp('used_at')->nullable(); // Waktu token digunakan
            $table->timestamps();

            // Foreign key
            $table->foreign('id_user')->references('id_user')->on('user')
                  ->onUpdate('cascade')->onDelete('set null');

            // Index untuk performance
            $table->index('token');
            $table->index('status');
            $table->unique('token'); // Token harus unik
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('token_emergency');
    }
};
