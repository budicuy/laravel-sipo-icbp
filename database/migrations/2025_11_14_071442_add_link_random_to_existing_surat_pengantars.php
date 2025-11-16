<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Models\SuratPengantar;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add column if it doesn't exist
        if (!Schema::hasColumn('surat_pengantars', 'link_random')) {
            Schema::table('surat_pengantars', function (Blueprint $table) {
                $table->string('link_random')->nullable()->after('petugas_medis');
            });
        }

        // Update existing records to have unique link_random values
        SuratPengantar::whereNull('link_random')
            ->orWhere('link_random', '')
            ->chunk(100, function ($suratPengantars) {
                foreach ($suratPengantars as $surat) {
                    $surat->update([
                        'link_random' => Str::random(32)
                    ]);
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Optionally remove the column
        if (Schema::hasColumn('surat_pengantars', 'link_random')) {
            Schema::table('surat_pengantars', function (Blueprint $table) {
                $table->dropColumn('link_random');
            });
        }
    }
};
