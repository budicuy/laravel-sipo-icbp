<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartemenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus data lama jika ada
        DB::table('departemen')->delete();

        $departemens = [
            ['nama_departemen' => 'ADM Gen. MGT'],
            ['nama_departemen' => 'ADM HR'],
            ['nama_departemen' => 'MFG Warehouse'],
            ['nama_departemen' => 'MFG Purchasing'],
            ['nama_departemen' => 'MFG Production'],
            ['nama_departemen' => 'MFG QC'],
            ['nama_departemen' => 'MKT Marketing'],
            ['nama_departemen' => 'MFG Technical'],
            ['nama_departemen' => 'ADM Financial Accounting'],
            ['nama_departemen' => 'MFG PPIC'],
            ['nama_departemen' => 'MKT Sales Distributor'],
            ['nama_departemen' => 'MKT Task Force'],
        ];

        DB::table('departemen')->insert($departemens);
    }
}
