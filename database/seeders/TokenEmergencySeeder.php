<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TokenEmergency;

class TokenEmergencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Generate 10 token emergency awal untuk testing
        TokenEmergency::generateMultipleTokens(10, 6);

        $this->command->info('10 token emergency berhasil digenerate!');
    }
}
