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
            ['nama_kondisi' => 'Anemia', 'deskripsi' => '-'],
            ['nama_kondisi' => 'Arcus Senilis', 'deskripsi' => '-'],
            ['nama_kondisi' => 'Arthritis', 'deskripsi' => '-'],
            ['nama_kondisi' => 'Asam Urat', 'deskripsi' => '-'],
            ['nama_kondisi' => 'Asma', 'deskripsi' => '-'],
            ['nama_kondisi' => 'Bakteriuria', 'deskripsi' => '-'],
            ['nama_kondisi' => 'Bronchiectasis', 'deskripsi' => '-'],
            ['nama_kondisi' => 'Buta warna partial', 'deskripsi' => '-'],
            ['nama_kondisi' => 'Buta warna Total', 'deskripsi' => '-'],
            ['nama_kondisi' => 'Cardiomegali', 'deskripsi' => '-'],
            ['nama_kondisi' => 'Chest Pain', 'deskripsi' => '-'],
            ['nama_kondisi' => 'Dermatitis', 'deskripsi' => '-'],
            ['nama_kondisi' => 'Diabetes Melitus', 'deskripsi' => '-'],
            ['nama_kondisi' => 'Dislipidemia', 'deskripsi' => '-'],
            ['nama_kondisi' => 'Dispepsia', 'deskripsi' => '-'],
            ['nama_kondisi' => 'Eksim', 'deskripsi' => '-'],
            ['nama_kondisi' => 'Fibroadenoma', 'deskripsi' => '-'],
            ['nama_kondisi' => 'Fractur Left Clavicle', 'deskripsi' => '-'],
            ['nama_kondisi' => 'Gangguan Faal Paru', 'deskripsi' => '-'],
            ['nama_kondisi' => 'Gangguan pendengaraan ringan', 'deskripsi' => '-'],
            ['nama_kondisi' => 'Gangguan Refraksi Mata', 'deskripsi' => '-'],
            ['nama_kondisi' => 'Ganglion regio Left Wrist', 'deskripsi' => '-'],
            ['nama_kondisi' => 'Glukosuria', 'deskripsi' => '-'],
            ['nama_kondisi' => 'Heart Prominent', 'deskripsi' => '-'],
            ['nama_kondisi' => 'Hematuria', 'deskripsi' => '-'],
            ['nama_kondisi' => 'Hemoroid', 'deskripsi' => '-'],
            ['nama_kondisi' => 'Hemoroid Grade I', 'deskripsi' => '-'],
            ['nama_kondisi' => 'Hemoroid Grade II', 'deskripsi' => '-'],
            ['nama_kondisi' => 'Hemoroid Grade III', 'deskripsi' => '-'],
            ['nama_kondisi' => 'Hemoroid Grade IV', 'deskripsi' => '-'],
            ['nama_kondisi' => 'Hiperglikemia', 'deskripsi' => '-'],
            ['nama_kondisi' => 'Hiperkolesterolemia', 'deskripsi' => '-'],
            ['nama_kondisi' => 'Hipertensi Grade I', 'deskripsi' => '-'],
            ['nama_kondisi' => 'Hipertensi Grade II', 'deskripsi' => '-'],
            ['nama_kondisi' => 'Hipertensi Heart Disease (HHD)', 'deskripsi' => '-'],
            ['nama_kondisi' => 'Hipertensi terkontrol', 'deskripsi' => '-'],
            ['nama_kondisi' => 'Hipertiroid', 'deskripsi' => '-'],
            ['nama_kondisi' => 'Hipertrigliseridemia', 'deskripsi' => '-'],
            ['nama_kondisi' => 'Hipertrofi Tonsil', 'deskripsi' => '-'],
            ['nama_kondisi' => 'Hiperuricemia', 'deskripsi' => '-'],
            ['nama_kondisi' => 'Hipotensi', 'deskripsi' => '-'],
            ['nama_kondisi' => 'ISK', 'deskripsi' => '-'],
            ['nama_kondisi' => 'Iskemia Anterolateral', 'deskripsi' => '-'],
            ['nama_kondisi' => 'Keloid', 'deskripsi' => '-'],
            ['nama_kondisi' => 'Kristaluria', 'deskripsi' => '-'],
            ['nama_kondisi' => 'Leukositosis', 'deskripsi' => '-'],
            ['nama_kondisi' => 'Leukosituria', 'deskripsi' => '-'],
            ['nama_kondisi' => 'Lipoma regio', 'deskripsi' => '-'],
            ['nama_kondisi' => 'Low Back Pain (LBP)', 'deskripsi' => '-'],
            ['nama_kondisi' => 'OMI Inferior', 'deskripsi' => '-'],
            ['nama_kondisi' => 'OMSK (Otitis Media Supuratif Kronik)', 'deskripsi' => '-'],
            ['nama_kondisi' => 'Papiloma regio', 'deskripsi' => '-'],
            ['nama_kondisi' => 'Pleuritis', 'deskripsi' => '-'],
            ['nama_kondisi' => 'Pre Hipertensi', 'deskripsi' => '-'],
            ['nama_kondisi' => 'Prominent Heart', 'deskripsi' => '-'],
            ['nama_kondisi' => 'Proteinuria', 'deskripsi' => '-'],
            ['nama_kondisi' => 'Psoriasis', 'deskripsi' => '-'],
            ['nama_kondisi' => 'Pterygium ODS', 'deskripsi' => '-'],
            ['nama_kondisi' => 'Rhinitis', 'deskripsi' => '-'],
            ['nama_kondisi' => 'Scoliosis Thoracalis', 'deskripsi' => '-'],
            ['nama_kondisi' => 'Septum Deviasi', 'deskripsi' => '-'],
            ['nama_kondisi' => 'Serumen telinga', 'deskripsi' => '-'],
            ['nama_kondisi' => 'SGOT & SGPT', 'deskripsi' => '-'],
            ['nama_kondisi' => 'Soft Tissue Tumor (STT) regio', 'deskripsi' => '-'],
            ['nama_kondisi' => 'Tinea Corporis', 'deskripsi' => '-'],
            ['nama_kondisi' => 'Trombositopenia', 'deskripsi' => '-'],
            ['nama_kondisi' => 'Tumor regio axilla', 'deskripsi' => '-'],
            ['nama_kondisi' => 'Tumor regio Humerus Dekstra', 'deskripsi' => '-'],
            ['nama_kondisi' => 'Tumor Thyroid', 'deskripsi' => '-'],
            ['nama_kondisi' => 'Urobilinogenuria', 'deskripsi' => '-'],
            ['nama_kondisi' => 'Varises', 'deskripsi' => '-'],
        ];

        foreach ($kondisiKesehatan as $kondisi) {
            KondisiKesehatan::create($kondisi);
        }
    }
}