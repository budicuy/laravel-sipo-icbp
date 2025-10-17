<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // This migration will fix stok calculation formula and update data consistency
        // New formula: Stok Akhir = Stok Awal + Stok Masuk - Stok Pakai

        // Check if stok_bulanan table exists
        if (!Schema::hasTable('stok_bulanan')) {
            Log::info('stok_bulanan table does not exist, skipping migration');
            return;
        }

        Log::info('Starting migration to fix stok calculation formula');

        // Get all stok bulanan records
        $stokObats = DB::table('stok_bulanan')->get();
        $fixedCount = 0;
        $errorCount = 0;

        foreach ($stokObats as $stok) {
            try {
                // Calculate expected stok akhir using new formula
                $expectedStokAkhir = $stok->stok_awal + $stok->stok_masuk - $stok->stok_pakai;

                // Check if current stok akhir matches expected value
                if ($stok->stok_akhir != $expectedStokAkhir) {
                    // Update stok akhir to correct value
                    DB::table('stok_bulanan')
                        ->where('id_stok_bulanan', $stok->id_stok_bulanan)
                        ->update(['stok_akhir' => $expectedStokAkhir]);

                    $fixedCount++;

                    Log::info('Fixed stok calculation', [
                        'id_stok_bulanan' => $stok->id_stok_bulanan,
                        'id_obat' => $stok->id_obat,
                        'periode' => $stok->periode,
                        'old_stok_akhir' => $stok->stok_akhir,
                        'new_stok_akhir' => $expectedStokAkhir,
                        'stok_awal' => $stok->stok_awal,
                        'stok_pakai' => $stok->stok_pakai,
                        'stok_masuk' => $stok->stok_masuk
                    ]);
                }
            } catch (\Exception $e) {
                $errorCount++;
                Log::error('Error fixing stok calculation', [
                    'id_stok_bulanan' => $stok->id_stok_bulanan,
                    'error' => $e->getMessage()
                ]);
            }
        }

        Log::info('Migration completed', [
            'total_records' => $stokObats->count(),
            'fixed_count' => $fixedCount,
            'error_count' => $errorCount
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // In case we need to rollback, we would restore the previous formula
        // Previous formula: Stok Akhir = Stok Awal - Stok Pakai + Stok Masuk

        // Check if stok_bulanan table exists
        if (!Schema::hasTable('stok_bulanan')) {
            Log::info('stok_bulanan table does not exist, skipping rollback migration');
            return;
        }

        Log::info('Starting rollback migration to restore previous stok calculation formula');

        // Get all stok bulanan records
        $stokObats = DB::table('stok_bulanan')->get();
        $fixedCount = 0;
        $errorCount = 0;

        foreach ($stokObats as $stok) {
            try {
                // Calculate expected stok akhir using previous formula
                $expectedStokAkhir = $stok->stok_awal - $stok->stok_pakai + $stok->stok_masuk;

                // Check if current stok akhir matches expected value
                if ($stok->stok_akhir != $expectedStokAkhir) {
                    // Update stok akhir to correct value
                    DB::table('stok_bulanan')
                        ->where('id_stok_bulanan', $stok->id_stok_bulanan)
                        ->update(['stok_akhir' => $expectedStokAkhir]);

                    $fixedCount++;

                    Log::info('Rolled back stok calculation', [
                        'id_stok_bulanan' => $stok->id_stok_bulanan,
                        'id_obat' => $stok->id_obat,
                        'periode' => $stok->periode,
                        'old_stok_akhir' => $stok->stok_akhir,
                        'new_stok_akhir' => $expectedStokAkhir,
                        'stok_awal' => $stok->stok_awal,
                        'stok_pakai' => $stok->stok_pakai,
                        'stok_masuk' => $stok->stok_masuk
                    ]);
                }
            } catch (\Exception $e) {
                $errorCount++;
                Log::error('Error rolling back stok calculation', [
                    'id_stok_bulanan' => $stok->id_stok_bulanan,
                    'error' => $e->getMessage()
                ]);
            }
        }

        Log::info('Rollback migration completed', [
            'total_records' => $stokObats->count(),
            'fixed_count' => $fixedCount,
            'error_count' => $errorCount
        ]);
    }
};
