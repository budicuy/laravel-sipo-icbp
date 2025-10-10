<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SatuanObat;

class SatuanObatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus data lama jika ada menggunakan Eloquent
        SatuanObat::query()->delete();

        $satuanObats = [
            ['nama_satuan' => 'Satuan Std'],
            ['nama_satuan' => 'Ampul'],
            ['nama_satuan' => 'Botol'],
            ['nama_satuan' => 'Tablet'],
            ['nama_satuan' => 'Pcs'],
            ['nama_satuan' => 'Tube'],
        ];

        // Using Eloquent insert method for better compatibility
        SatuanObat::insert($satuanObats);
    }
}
