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
            HubunganSeeder::class,
            UserSeeder::class,
            ObatSeeder::class,
            DiagnosaSeeder::class,
            DiagnosaEmergencySeeder::class,
            KaryawanSeeder::class,
            KeluargaSeeder::class,
            HargaObatSeeder::class,
            VendorSeeder::class,
            KategoriSeeder::class,
            ExternalEmployeeSeeder::class,
            DiagnosaEmergencyObatSeeder::class, // Tambahkan seeder untuk pivot table
            StokObatSeeder::class, // Seeder untuk data stok obat
            TokenEmergencySeeder::class, // Seeder untuk token emergency
        ]);

        // Tabel users belum ada, komentari dulu
        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
