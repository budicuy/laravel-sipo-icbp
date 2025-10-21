<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Kategori;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kategoris = [
            ['kode_kategori' => 'x', 'nama_kategori' => 'guest'],
            ['kode_kategori' => 'y', 'nama_kategori' => 'outsourcing'],
            ['kode_kategori' => 'z', 'nama_kategori' => 'supporting'],
        ];

        foreach ($kategoris as $kategori) {
            Kategori::create($kategori);
        }
    }
}