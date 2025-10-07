<?php

namespace Database\Factories;

use App\Models\Obat;
use App\Models\JenisObat;
use App\Models\SatuanObat;
use Illuminate\Database\Eloquent\Factories\Factory;

class ObatFactory extends Factory
{
    protected $model = Obat::class;

    public function definition(): array
    {
        $namaObat = [
            'Paracetamol 500mg', 'Amoxicillin 500mg', 'Ibuprofen 400mg',
            'Cetirizine 10mg', 'Omeprazole 20mg', 'Metformin 500mg',
            'Amlodipine 5mg', 'Simvastatin 10mg', 'Aspirin 100mg',
            'Vitamin C 1000mg', 'Zinc 50mg', 'Vitamin B Complex',
            'Antasida Syrup', 'OBH Combi Syrup', 'Salep Hydrocortisone',
            'Tetes Mata Cendo', 'Albothyl Concentrate', 'Betadine Solution',
            'Kasa Steril', 'Perban Elastis', 'Cotton Bud',
        ];

        $keterangan = [
            'Obat pereda nyeri dan penurun demam',
            'Antibiotik untuk infeksi bakteri',
            'Anti-inflamasi non-steroid',
            'Obat alergi dan antihistamin',
            'Obat maag dan GERD',
            'Obat diabetes tipe 2',
            'Obat hipertensi',
            'Obat kolesterol',
            'Pengencer darah',
            'Suplemen vitamin',
            'Obat flu dan batuk',
            'Antiseptik',
            'Alat medis habis pakai',
        ];

        $stokAwal = $this->faker->numberBetween(50, 500);
        $stokMasuk = $this->faker->numberBetween(0, 200);
        $stokKeluar = $this->faker->numberBetween(0, min($stokAwal + $stokMasuk, 100));
        $jumlahPerKemasan = $this->faker->randomElement([1, 2, 4, 6, 10]);
        $hargaPerKemasan = $this->faker->numberBetween(5000, 150000);
        $hargaPerSatuan = $jumlahPerKemasan > 1 ? round($hargaPerKemasan / $jumlahPerKemasan, 2) : $hargaPerKemasan;

        return [
            'nama_obat' => $this->faker->unique()->randomElement($namaObat) . ' - ' . $this->faker->randomNumber(3),
            'keterangan' => $this->faker->randomElement($keterangan),
            'id_jenis_obat' => $this->faker->numberBetween(1, 8), // Sesuai dengan JenisObatSeeder
            'id_satuan' => $this->faker->numberBetween(1, 10), // Sesuai dengan SatuanObatSeeder
            'stok_awal' => $stokAwal,
            'stok_masuk' => $stokMasuk,
            'stok_keluar' => $stokKeluar,
            'stok_akhir' => $stokAwal + $stokMasuk - $stokKeluar, // Auto-calculated
            'jumlah_per_kemasan' => $jumlahPerKemasan,
            'harga_per_satuan' => $hargaPerSatuan,
            'harga_per_kemasan' => $hargaPerKemasan,
            'tanggal_update' => now(),
        ];
    }
}
