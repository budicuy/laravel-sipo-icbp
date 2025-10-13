<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\JenisObat;
use Illuminate\Support\Facades\DB;

class JenisObatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus data lama jika ada menggunakan Eloquent
        // Disable foreign key checks temporarily (MariaDB/MySQL syntax)
        JenisObat::query()->delete();

        $jenisObats = [
            ['id_jenis_obat' => 1, 'nama_jenis_obat' => 'Tablet'],
            ['id_jenis_obat' => 2, 'nama_jenis_obat' => 'Kapsul'],
            ['id_jenis_obat' => 3, 'nama_jenis_obat' => 'Sirup'],
            ['id_jenis_obat' => 4, 'nama_jenis_obat' => 'Salep / Krim'],
            ['id_jenis_obat' => 5, 'nama_jenis_obat' => 'Tetes Mata'],
            ['id_jenis_obat' => 6, 'nama_jenis_obat' => 'Tetes Telinga'],
            ['id_jenis_obat' => 7, 'nama_jenis_obat' => 'Inhaler / Suntik / Cairan'],
            ['id_jenis_obat' => 8, 'nama_jenis_obat' => 'Alat Medis / Lainnya'],
        ];

        // Using Eloquent insert method for better compatibility
        JenisObat::insert($jenisObats);
    }
}

