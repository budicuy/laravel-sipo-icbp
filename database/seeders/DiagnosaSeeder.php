<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Diagnosa;
use App\Models\Obat;

class DiagnosaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil semua ID obat yang tersedia
        $obatIds = Obat::pluck('id_obat')->toArray();

        if (empty($obatIds)) {
            $this->command->warn('Tidak ada data obat. Pastikan ObatSeeder sudah dijalankan terlebih dahulu.');
            return;
        }

        $this->command->info('Membuat 200 data diagnosa dengan relasi obat...');

        // Buat 200 data diagnosa menggunakan factory
        Diagnosa::factory(200)->create()->each(function ($diagnosa) use ($obatIds) {
            // Setiap diagnosa akan memiliki 1-5 obat rekomendasi secara random
            $jumlahObat = rand(1, 5);
            $selectedObatIds = collect($obatIds)->random(min($jumlahObat, count($obatIds)))->toArray();

            // Attach obat ke diagnosa
            $diagnosa->obats()->attach($selectedObatIds);
        });

        $this->command->info('Berhasil membuat 200 data diagnosa dengan relasi obat.');
    }
}
