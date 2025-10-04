<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PenyakitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus data lama jika ada
        DB::table('penyakit')->delete();

        $penyakits = [
            [
                'nama_penyakit' => 'ISPA / PHARINGITIS',
                'deskripsi' => 'Infeksi saluran pernapasan atas yang menyebabkan radang tenggorokan dan batuk.'
            ],
            [
                'nama_penyakit' => 'CEPALGIA',
                'deskripsi' => 'Sakit kepala yang dapat disebabkan oleh berbagai kondisi, termasuk migrain dan tensi tinggi.'
            ],
            [
                'nama_penyakit' => 'DISPEPSIA',
                'deskripsi' => 'Gangguan pencernaan yang menyebabkan rasa tidak nyaman atau nyeri di perut bagian atas.'
            ],
            [
                'nama_penyakit' => 'PENYAKIT PULPA JARINGAN PERCAPIKAL',
                'deskripsi' => 'Infeksi atau peradangan pada jaringan pulpa gigi dan sekitarnya.'
            ],
            [
                'nama_penyakit' => 'PENYAKIT OTOT JARINGAN PENGIKAT',
                'deskripsi' => 'Gangguan pada otot dan jaringan ikat, termasuk nyeri dan peradangan.'
            ],
            [
                'nama_penyakit' => 'HIPERTENSI',
                'deskripsi' => 'Tekanan darah tinggi kronis yang dapat meningkatkan risiko penyakit jantung dan stroke.'
            ],
            [
                'nama_penyakit' => 'DIARE',
                'deskripsi' => 'Buang air besar cair atau lebih sering dari normal, biasanya akibat infeksi atau gangguan pencernaan.'
            ],
            [
                'nama_penyakit' => 'DERMATITIS ALERGIKA',
                'deskripsi' => 'Peradangan kulit akibat reaksi alergi, ditandai gatal, kemerahan, dan ruam.'
            ],
            [
                'nama_penyakit' => 'PENYAKIT MATA LAIN',
                'deskripsi' => 'Berbagai gangguan mata selain konjungtivitis, seperti infeksi atau inflamasi.'
            ],
            [
                'nama_penyakit' => 'FEBRIS',
                'deskripsi' => 'Kondisi demam akibat infeksi atau penyakit lainnya.'
            ],
            [
                'nama_penyakit' => 'OTITIS MEDIA',
                'deskripsi' => 'Infeksi atau peradangan pada telinga tengah, sering terjadi pada anak-anak.'
            ],
            [
                'nama_penyakit' => 'DIABETES MELITUS',
                'deskripsi' => 'Penyakit metabolik dengan kadar gula darah tinggi akibat gangguan insulin.'
            ],
            [
                'nama_penyakit' => 'DM',
                'deskripsi' => 'Singkatan dari Diabetes Mellitus, gangguan gula darah kronis.'
            ],
            [
                'nama_penyakit' => 'KONJUNGTIVITIS',
                'deskripsi' => 'Radang atau infeksi pada konjungtiva mata, biasanya merah dan gatal.'
            ],
            [
                'nama_penyakit' => 'VARICELLA',
                'deskripsi' => 'Penyakit cacar air yang menimbulkan ruam dan lepuhan pada kulit.'
            ],
            [
                'nama_penyakit' => 'HEPATITIS A',
                'deskripsi' => 'Infeksi virus hepatitis A yang menyerang hati, menyebabkan jaundice dan mual.'
            ],
            [
                'nama_penyakit' => 'TBC',
                'deskripsi' => 'Tuberkulosis, infeksi bakteri Mycobacterium tuberculosis yang biasanya menyerang paru-paru.'
            ],
            [
                'nama_penyakit' => 'HERVES',
                'deskripsi' => 'Infeksi virus herpes yang menyebabkan lepuhan pada kulit atau mukosa.'
            ],
            [
                'nama_penyakit' => 'HEPATITIS B',
                'deskripsi' => 'Infeksi virus hepatitis B yang menyerang hati, dapat menjadi kronis.'
            ],
            [
                'nama_penyakit' => 'KOLERA',
                'deskripsi' => 'Infeksi bakteri Vibrio cholerae yang menyebabkan diare berat dan dehidrasi.'
            ],
            [
                'nama_penyakit' => 'CAMPAK RUBELLA',
                'deskripsi' => 'Infeksi virus campak dan rubella yang menyebabkan ruam dan demam.'
            ],
            [
                'nama_penyakit' => 'DIPTERI',
                'deskripsi' => 'Infeksi pada mata atau saluran pernapasan yang disebabkan oleh bakteri dipteri.'
            ],
            [
                'nama_penyakit' => 'PAROTITIS',
                'deskripsi' => 'Peradangan kelenjar parotis, biasanya disebabkan oleh virus mumps.'
            ],
            [
                'nama_penyakit' => 'Asma Bronkial',
                'deskripsi' => 'Penyakit pernapasan kronis akibat peradangan saluran udara.'
            ],
            [
                'nama_penyakit' => 'Pneumonia',
                'deskripsi' => 'Infeksi paru-paru akibat bakteri, virus, atau jamur.'
            ],
            [
                'nama_penyakit' => 'Bronkitis',
                'deskripsi' => 'Peradangan pada bronkus yang menyebabkan batuk berdahak.'
            ],
            [
                'nama_penyakit' => 'Gastritis',
                'deskripsi' => 'Peradangan pada lapisan lambung.'
            ],
            [
                'nama_penyakit' => 'Hipotensi',
                'deskripsi' => 'Tekanan darah rendah yang dapat menyebabkan pusing dan pingsan.'
            ],
            [
                'nama_penyakit' => 'Malaria',
                'deskripsi' => 'Demam meriang'
            ],
            [
                'nama_penyakit' => 'Demam Berdarah Dengue (DBD)',
                'deskripsi' => 'Infeksi virus Dengue yang ditularkan nyamuk Aedes aegypti.'
            ],
            [
                'nama_penyakit' => 'Skabies',
                'deskripsi' => 'Infeksi kulit akibat tungau Sarcoptes scabiei.'
            ],
            [
                'nama_penyakit' => 'Anemia Defisiensi Besi',
                'deskripsi' => 'Kekurangan hemoglobin akibat defisiensi zat besi.'
            ],
            [
                'nama_penyakit' => 'Tonsilitis',
                'deskripsi' => 'Peradangan pada amandel, biasanya disebabkan infeksi bakteri/virus.'
            ],
            [
                'nama_penyakit' => 'Sinusitis',
                'deskripsi' => 'Peradangan pada rongga sinus.'
            ],
            [
                'nama_penyakit' => 'Apendisitis',
                'deskripsi' => 'Radang usus buntu.'
            ],
            [
                'nama_penyakit' => 'Infeksi Saluran Kemih (ISK)',
                'deskripsi' => 'Infeksi pada kandung kemih, ginjal, atau uretra.'
            ],
            [
                'nama_penyakit' => 'Gagal Ginjal Kronis',
                'deskripsi' => 'Penurunan fungsi ginjal secara progresif dan permanen.'
            ],
            [
                'nama_penyakit' => 'Stroke',
                'deskripsi' => 'Gangguan aliran darah otak yang menyebabkan kerusakan jaringan otak.'
            ],
            [
                'nama_penyakit' => 'Epilepsi',
                'deskripsi' => 'Gangguan sistem saraf yang menyebabkan kejang berulang.'
            ],
            [
                'nama_penyakit' => 'Gout Arthritis (Asam Urat)',
                'deskripsi' => 'Radang sendi akibat kristal asam urat.'
            ],
            [
                'nama_penyakit' => 'Obesitas',
                'deskripsi' => 'Kelebihan berat badan dengan indeks massa tubuh tinggi.'
            ],
            [
                'nama_penyakit' => 'Dislipidemia',
                'deskripsi' => 'Gangguan metabolisme lipid yang ditandai kadar kolesterol abnormal.'
            ],
        ];

        DB::table('penyakit')->insert($penyakits);
    }
}
