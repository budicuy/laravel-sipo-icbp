<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DiagnosaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('diagnosa')->insert([
            [
                'nama_diagnosa' => 'Demam Berdarah Dengue (DBD)',
                'deskripsi' => 'Penyakit infeksi virus dengue yang ditularkan melalui gigitan nyamuk Aedes aegypti. Gejala utama meliputi demam tinggi mendadak, nyeri otot dan sendi, sakit kepala, mual, muntah, dan munculnya ruam atau bintik merah pada kulit.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_diagnosa' => 'Hipertensi',
                'deskripsi' => 'Kondisi tekanan darah tinggi yang persisten. Dapat menyebabkan komplikasi serius seperti penyakit jantung, stroke, dan gagal ginjal jika tidak dikelola dengan baik.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_diagnosa' => 'Diabetes Mellitus Tipe 2',
                'deskripsi' => 'Gangguan metabolik yang ditandai dengan kadar gula darah tinggi akibat resistensi insulin atau produksi insulin yang tidak mencukupi. Memerlukan manajemen diet, olahraga, dan pengobatan.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_diagnosa' => 'ISPA (Infeksi Saluran Pernapasan Akut)',
                'deskripsi' => 'Infeksi pada saluran pernapasan yang dapat disebabkan oleh virus atau bakteri. Gejala meliputi batuk, pilek, demam ringan, dan sakit tenggorokan.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_diagnosa' => 'Gastritis',
                'deskripsi' => 'Peradangan pada dinding lambung yang menyebabkan nyeri atau rasa tidak nyaman di perut bagian atas, mual, kembung, dan kadang muntah.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
