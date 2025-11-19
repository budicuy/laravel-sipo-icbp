<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KondisiKesehatan;

class KondisiKesehatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kondisiKesehatan = [
            ['nama_kondisi' => 'Hipertensi', 'deskripsi' => '-'],
            ['nama_kondisi' => 'Diabetes', 'deskripsi' => '-'],
        ];

        foreach ($kondisiKesehatan as $kondisi) {
            KondisiKesehatan::create($kondisi);
        }
    }
}