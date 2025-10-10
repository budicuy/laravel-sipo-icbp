<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Departemen;

class DepartemenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus data lama jika ada menggunakan Eloquent
        Departemen::query()->delete();

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

        // Using Eloquent insert method for better compatibility
        Departemen::insert($departemens);
    }
}
