<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SatuanObat;
use Illuminate\Support\Facades\DB;

class SatuanObatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus data lama jika ada menggunakan Eloquent
        // Disable foreign key checks temporarily (MariaDB/MySQL syntax)
        SatuanObat::query()->delete();

        $satuanObats = [
            ['id_satuan' => 1, 'nama_satuan' => 'Satuan Std'],
            ['id_satuan' => 2, 'nama_satuan' => 'Ampul'],
            ['id_satuan' => 3, 'nama_satuan' => 'Botol'],
            ['id_satuan' => 4, 'nama_satuan' => 'Tablet'],
            ['id_satuan' => 5, 'nama_satuan' => 'Pcs'],
            ['id_satuan' => 6, 'nama_satuan' => 'Tube'],
        ];

        // Using Eloquent insert method for better compatibility
        SatuanObat::insert($satuanObats);
    }
}
