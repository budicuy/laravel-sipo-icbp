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
            KondisiKesehatanSeeder::class, // Seeder untuk kondisi kesehatan
            DiagnosaEmergencyObatSeeder::class, // Tambahkan seeder untuk pivot table
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
            PostSeeder::class,
            ExternalEmployeeSeeder::class,
            TokenEmergencySeeder::class, // Seeder untuk token emergency
            RekamMedisEmergencyFromCSVSeeder::class, // Seeder untuk data emergency dari CSV
            RekamMedisRegulerSeeder::class, // Seeder untuk data rekam medis reguler dari CSV
            MedicalCheckUpSeeder::class, // Seeder untuk data medical check up dari CSV
        ]);

        // Tabel users belum ada, komentari dulu
        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
