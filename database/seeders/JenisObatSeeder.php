<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JenisObatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus data lama jika ada
        DB::table('jenis_obat')->delete();

        $jenisObats = [
            ['id_jenis_obat' => 1, 'nama_jenis' => 'Tablet'],
            ['id_jenis_obat' => 2, 'nama_jenis' => 'Kapsul'],
            ['id_jenis_obat' => 3, 'nama_jenis' => 'Sirup'],
            ['id_jenis_obat' => 4, 'nama_jenis' => 'Salep / Krim'],
            ['id_jenis_obat' => 5, 'nama_jenis' => 'Tetes Mata'],
            ['id_jenis_obat' => 6, 'nama_jenis' => 'Tetes Telinga'],
            ['id_jenis_obat' => 7, 'nama_jenis' => 'Inhaler / Suntik / Cairan'],
            ['id_jenis_obat' => 8, 'nama_jenis' => 'Alat Medis / Lainnya'],
        ];

        DB::table('jenis_obat')->insert($jenisObats);
    }
}
