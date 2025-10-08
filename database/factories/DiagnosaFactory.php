<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Diagnosa>
 */
class DiagnosaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $diagnosaList = [
            ['nama' => 'Demam Berdarah Dengue (DBD)', 'deskripsi' => 'Infeksi virus dengue yang ditularkan melalui gigitan nyamuk Aedes aegypti dengan gejala demam tinggi, nyeri sendi, dan penurunan trombosit.'],
            ['nama' => 'Hipertensi', 'deskripsi' => 'Kondisi tekanan darah tinggi yang dapat meningkatkan risiko penyakit jantung dan stroke.'],
            ['nama' => 'Diabetes Mellitus Tipe 2', 'deskripsi' => 'Penyakit metabolik yang ditandai dengan kadar gula darah tinggi akibat resistensi insulin.'],
            ['nama' => 'ISPA (Infeksi Saluran Pernapasan Akut)', 'deskripsi' => 'Infeksi pada saluran pernapasan atas yang menyebabkan batuk, pilek, dan demam.'],
            ['nama' => 'Gastritis', 'deskripsi' => 'Peradangan pada lapisan lambung yang menyebabkan nyeri ulu hati, mual, dan muntah.'],
            ['nama' => 'Diare Akut', 'deskripsi' => 'Kondisi buang air besar cair lebih dari 3 kali sehari, biasanya disebabkan infeksi atau keracunan makanan.'],
            ['nama' => 'Tifus (Demam Tifoid)', 'deskripsi' => 'Infeksi bakteri Salmonella typhi yang menyebabkan demam tinggi berkepanjangan.'],
            ['nama' => 'Malaria', 'deskripsi' => 'Penyakit infeksi parasit yang ditularkan melalui gigitan nyamuk Anopheles dengan gejala demam menggigil.'],
            ['nama' => 'Asma Bronkial', 'deskripsi' => 'Penyakit kronis pada saluran napas yang menyebabkan sesak napas dan mengi.'],
            ['nama' => 'Tuberkulosis (TB) Paru', 'deskripsi' => 'Infeksi bakteri Mycobacterium tuberculosis pada paru-paru yang menular melalui udara.'],
            ['nama' => 'Dispepsia', 'deskripsi' => 'Gangguan pencernaan dengan gejala nyeri atau tidak nyaman di perut bagian atas.'],
            ['nama' => 'Vertigo', 'deskripsi' => 'Sensasi pusing berputar yang sering disertai mual dan gangguan keseimbangan.'],
            ['nama' => 'Migrain', 'deskripsi' => 'Nyeri kepala berdenyut hebat yang biasanya pada satu sisi kepala.'],
            ['nama' => 'Konjungtivitis', 'deskripsi' => 'Peradangan pada selaput bening yang melapisi kelopak mata dan bola mata.'],
            ['nama' => 'Dermatitis', 'deskripsi' => 'Peradangan kulit yang menyebabkan ruam, gatal, dan kemerahan.'],
            ['nama' => 'Faringitis', 'deskripsi' => 'Peradangan pada tenggorokan yang menyebabkan sakit saat menelan.'],
            ['nama' => 'Sinusitis', 'deskripsi' => 'Peradangan pada sinus yang menyebabkan hidung tersumbat dan nyeri wajah.'],
            ['nama' => 'Anemia', 'deskripsi' => 'Kondisi kekurangan sel darah merah atau hemoglobin yang menyebabkan lemas dan pucat.'],
            ['nama' => 'Osteoartritis', 'deskripsi' => 'Peradangan sendi akibat kerusakan tulang rawan yang menyebabkan nyeri dan kaku sendi.'],
            ['nama' => 'Infeksi Saluran Kemih', 'deskripsi' => 'Infeksi bakteri pada saluran kemih yang menyebabkan nyeri saat buang air kecil.'],
            ['nama' => 'Pneumonia', 'deskripsi' => 'Infeksi pada paru-paru yang menyebabkan batuk berdahak, demam, dan sesak napas.'],
            ['nama' => 'Bronkitis', 'deskripsi' => 'Peradangan pada saluran bronkus yang menyebabkan batuk berdahak.'],
            ['nama' => 'Hepatitis A', 'deskripsi' => 'Infeksi virus pada hati yang menyebabkan kuning, mual, dan lelah.'],
            ['nama' => 'Chikungunya', 'deskripsi' => 'Infeksi virus yang ditularkan nyamuk dengan gejala demam dan nyeri sendi hebat.'],
            ['nama' => 'COVID-19', 'deskripsi' => 'Penyakit infeksi virus SARS-CoV-2 yang menyerang sistem pernapasan.'],
            ['nama' => 'Gout (Asam Urat)', 'deskripsi' => 'Peradangan sendi akibat penumpukan kristal asam urat.'],
            ['nama' => 'Kolesterol Tinggi', 'deskripsi' => 'Kadar lemak dalam darah yang tinggi meningkatkan risiko penyakit jantung.'],
            ['nama' => 'Insomnia', 'deskripsi' => 'Gangguan tidur yang menyebabkan kesulitan tidur atau tidur tidak nyenyak.'],
            ['nama' => 'Anxiety Disorder', 'deskripsi' => 'Gangguan kecemasan yang berlebihan dan mengganggu aktivitas sehari-hari.'],
            ['nama' => 'Scabies', 'deskripsi' => 'Penyakit kulit menular yang disebabkan tungau dengan gejala gatal hebat.'],
        ];

        $selected = $this->faker->randomElement($diagnosaList);

        return [
            'nama_diagnosa' => $selected['nama'],
            'deskripsi' => $selected['deskripsi'],
        ];
    }
}
