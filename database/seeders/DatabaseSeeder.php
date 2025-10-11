<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seeder untuk master data
        $this->call([
            DepartemenSeeder::class,
            SatuanObatSeeder::class,
            JenisObatSeeder::class,
            ObatSeeder::class,
            StokBulananSeeder::class, // Import data stok bulanan dari CSV
            HubunganSeeder::class,
            UserSeeder::class,
            KaryawanSeeder::class, // Seed 200 karyawan
            KeluargaSeeder::class, // Seed 200 keluarga
            DiagnosaSeeder::class, // Seed 200 diagnosa dengan relasi obat
        ]);


        // Tabel users belum ada, komentari dulu
        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
