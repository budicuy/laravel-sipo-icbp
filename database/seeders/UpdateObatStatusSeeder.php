<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Obat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateObatStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            // Update semua obat yang sudah ada dengan status 'aktif'
            $updated = DB::table('obat')
                ->whereNull('status')
                ->update(['status' => 'aktif']);

            $this->command->info("Updated {$updated} existing obat records with status 'aktif'");
            
            Log::info("Obat status update completed", [
                'updated_records' => $updated
            ]);

        } catch (\Exception $e) {
            $this->command->error('Error updating obat status: ' . $e->getMessage());
            Log::error('Error updating obat status: ' . $e->getMessage());
        }
    }
}