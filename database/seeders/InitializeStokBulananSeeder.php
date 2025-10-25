<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Obat;
use App\Models\StokBulanan;

class InitializeStokBulananSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Initializing stok bulanan for all obat...');

        // Get current year and month
        $tahun = now()->year;
        $bulan = now()->month;

        // Get all obat
        $obats = Obat::all();

        $createdCount = 0;
        $skippedCount = 0;

        foreach ($obats as $obat) {
            // Check if stok bulanan already exists for this obat
            $existingStok = StokBulanan::where('obat_id', $obat->id_obat)
                                      ->where('tahun', $tahun)
                                      ->where('bulan', $bulan)
                                      ->first();

            if (!$existingStok) {
                // Create new stok bulanan record
                StokBulanan::create([
                    'obat_id' => $obat->id_obat,
                    'tahun' => $tahun,
                    'bulan' => $bulan,
                    'stok_masuk' => 0,
                    'stok_pakai' => 0,
                ]);

                $createdCount++;
                $this->command->info("Created stok bulanan for: {$obat->nama_obat}");
            } else {
                $skippedCount++;
            }
        }

        $this->command->info("Stok bulanan initialization complete!");
        $this->command->info("Created: {$createdCount} records");
        $this->command->info("Skipped: {$skippedCount} records (already exist)");

        // Log the activity
        Log::info('Stok bulanan initialization completed', [
            'created_count' => $createdCount,
            'skipped_count' => $skippedCount,
            'tahun' => $tahun,
            'bulan' => $bulan,
        ]);
    }
}
