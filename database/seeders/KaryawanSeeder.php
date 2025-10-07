<?php

namespace Database\Seeders;

use App\Models\Karyawan;
use App\Models\Departemen;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KaryawanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure departments exist
        if (Departemen::count() === 0) {
            $this->command->warn('No departments found. Please run DepartemenSeeder first.');
            return;
        }

        $this->command->info('Creating 200 karyawan...');

        // Create 200 karyawan with random data
        Karyawan::factory()->count(200)->create();

        $this->command->info('Successfully created 200 karyawan!');
    }
}
