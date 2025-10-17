<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Hubungan;

class HubunganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus data lama jika ada menggunakan Eloquent
        Hubungan::query()->delete();

        $hubungans = [
            ['kode_hubungan' => 'A', 'hubungan' => 'Karyawan'],
            ['kode_hubungan' => 'B', 'hubungan' => 'Spouse'],
            ['kode_hubungan' => 'C', 'hubungan' => 'Anak 1'],
            ['kode_hubungan' => 'D', 'hubungan' => 'Anak 2'],
            ['kode_hubungan' => 'E', 'hubungan' => 'Anak 3'],
        ];

        // Using Eloquent insert method for better compatibility
        Hubungan::insert($hubungans);
    }
}
