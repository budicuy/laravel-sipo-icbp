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
            ['nama_departemen' => 'ADM Gen.Mgt'],
            ['nama_departemen' => 'ADM HR'],
            ['nama_departemen' => 'MFG Warehouse'],
            ['nama_departemen' => 'MFG Purchasing'],
            ['nama_departemen' => 'MFG Production'],
            ['nama_departemen' => 'R&D QC/QA'],
            ['nama_departemen' => 'MKT Sales&Distr'],
            ['nama_departemen' => 'MFG Technical'],
            ['nama_departemen' => 'ADM Fin. & Acct.'],
            ['nama_departemen' => 'MFG PPIC'],
            ['nama_departemen' => 'Outsouching'],
        ];

        DB::table('departemen')->insert($departemens);
    }
}
