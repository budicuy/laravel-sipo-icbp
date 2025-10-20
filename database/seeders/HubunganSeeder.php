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
            // Basic hubungan codes
            ['kode_hubungan' => 'A', 'hubungan' => 'Karyawan'],
            ['kode_hubungan' => 'B', 'hubungan' => 'Spouse'],
            ['kode_hubungan' => 'C', 'hubungan' => 'Anak 1'],
            ['kode_hubungan' => 'D', 'hubungan' => 'Anak 2'],
            ['kode_hubungan' => 'E', 'hubungan' => 'Anak 3'],

            // Extended hubungan codes for family members
            ['kode_hubungan' => 'F', 'hubungan' => 'Anak 4'],
            ['kode_hubungan' => 'G', 'hubungan' => 'Anak 5'],
            ['kode_hubungan' => 'H', 'hubungan' => 'Ayah'],
            ['kode_hubungan' => 'I', 'hubungan' => 'Ibu'],
            ['kode_hubungan' => 'J', 'hubungan' => 'Kakek'],
            ['kode_hubungan' => 'K', 'hubungan' => 'Nenek'],
            ['kode_hubungan' => 'L', 'hubungan' => 'Paman'],
            ['kode_hubungan' => 'M', 'hubungan' => 'Bibi'],
            ['kode_hubungan' => 'N', 'hubungan' => 'Keponakan'],
            ['kode_hubungan' => 'O', 'hubungan' => 'Sepupu'],

            // Extended hubungan codes with family member type
            ['kode_hubungan' => 'P', 'hubungan' => 'Mertua'],
            ['kode_hubungan' => 'Q', 'hubungan' => 'Menantu'],
            ['kode_hubungan' => 'R', 'hubungan' => 'Ipar'],
            ['kode_hubungan' => 'S', 'hubungan' => 'Cucu'],
            ['kode_hubungan' => 'T', 'hubungan' => 'Saudara Kandung'],
            ['kode_hubungan' => 'U', 'hubungan' => 'Saudara Tiri'],
            ['kode_hubungan' => 'V', 'hubungan' => 'Saudara Angkat'],
        ];

        // Using Eloquent insert method for better compatibility
        Hubungan::insert($hubungans);
    }
}
