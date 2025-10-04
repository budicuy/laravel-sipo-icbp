<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SatuanObatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus data lama jika ada
        DB::table('satuan_obat')->delete();

        $satuanObats = [
            ['nama_satuan' => 'Tablet'],
            ['nama_satuan' => 'Strip'],
            ['nama_satuan' => 'Botol'],
            ['nama_satuan' => 'Botol (Tetes)'],
            ['nama_satuan' => 'Tube'],
            ['nama_satuan' => 'Ampul'],
            ['nama_satuan' => 'Sachet'],
            ['nama_satuan' => 'Pcs'],
        ];

        DB::table('satuan_obat')->insert($satuanObats);
    }
}
