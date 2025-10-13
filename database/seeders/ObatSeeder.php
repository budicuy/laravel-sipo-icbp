<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Obat;

class ObatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus data lama jika ada menggunakan Eloquent
        Obat::query()->delete();

        $obats = [
            [
                'nama_obat' => 'Amlodipin 5mg',
                'keterangan' => 'Obat hipertensi',
                'id_jenis_obat' => 1,
                'id_satuan' => 4,
                'jumlah_per_kemasan' => 10,
                'harga_per_satuan' => 1200.00,
                'harga_per_kemasan' => 12000.00
            ],
            [
                'nama_obat' => 'Amlodipin 10mg',
                'keterangan' => 'Obat hipertensi',
                'id_jenis_obat' => 1,
                'id_satuan' => 4,
                'jumlah_per_kemasan' => 10,
                'harga_per_satuan' => 1500.00,
                'harga_per_kemasan' => 15000.00
            ],
            [
                'nama_obat' => 'Amoxicilin',
                'keterangan' => 'Antibiotik',
                'id_jenis_obat' => 1,
                'id_satuan' => 4,
                'jumlah_per_kemasan' => 10,
                'harga_per_satuan' => 800.00,
                'harga_per_kemasan' => 8000.00
            ],
            [
                'nama_obat' => 'Antasid tab',
                'keterangan' => 'Obat maag',
                'id_jenis_obat' => 1,
                'id_satuan' => 4,
                'jumlah_per_kemasan' => 10,
                'harga_per_satuan' => 500.00,
                'harga_per_kemasan' => 5000.00
            ],
            [
                'nama_obat' => 'Antasid Sy',
                'keterangan' => 'Syrup obat maag',
                'id_jenis_obat' => 3,
                'id_satuan' => 3,
                'jumlah_per_kemasan' => 1,
                'harga_per_satuan' => 7000.00,
                'harga_per_kemasan' => 7000.00
            ],
            [
                'nama_obat' => 'Asmef',
                'keterangan' => 'Obat asma',
                'id_jenis_obat' => 7,
                'id_satuan' => 4,
                'jumlah_per_kemasan' => 1,
                'harga_per_satuan' => 6000.00,
                'harga_per_kemasan' => 6000.00
            ],
            [
                'nama_obat' => 'Piroxicamp',
                'keterangan' => 'Obat antiinflamasi',
                'id_jenis_obat' => 1,
                'id_satuan' => 4,
                'jumlah_per_kemasan' => 10,
                'harga_per_satuan' => 1000.00,
                'harga_per_kemasan' => 10000.00
            ],
            [
                'nama_obat' => 'Dexa',
                'keterangan' => 'Kortikosteroid',
                'id_jenis_obat' => 1,
                'id_satuan' => 4,
                'jumlah_per_kemasan' => 10,
                'harga_per_satuan' => 500.00,
                'harga_per_kemasan' => 5000.00
            ],
            [
                'nama_obat' => 'Methyl',
                'keterangan' => 'Obat antiinflamasi',
                'id_jenis_obat' => 1,
                'id_satuan' => 4,
                'jumlah_per_kemasan' => 10,
                'harga_per_satuan' => 1200.00,
                'harga_per_kemasan' => 12000.00
            ],
            [
                'nama_obat' => 'Flucodex. du',
                'keterangan' => 'Obat flu',
                'id_jenis_obat' => 1,
                'id_satuan' => 4,
                'jumlah_per_kemasan' => 10,
                'harga_per_satuan' => 1000.00,
                'harga_per_kemasan' => 10000.00
            ],
            [
                'nama_obat' => 'Microgynon',
                'keterangan' => 'Kontrasepsi oral',
                'id_jenis_obat' => 1,
                'id_satuan' => 4,
                'jumlah_per_kemasan' => 10,
                'harga_per_satuan' => 1500.00,
                'harga_per_kemasan' => 15000.00
            ],
            [
                'nama_obat' => 'Nadic',
                'keterangan' => 'Antibiotik',
                'id_jenis_obat' => 1,
                'id_satuan' => 4,
                'jumlah_per_kemasan' => 10,
                'harga_per_satuan' => 800.00,
                'harga_per_kemasan' => 8000.00
            ],
            [
                'nama_obat' => 'Paracetamol',
                'keterangan' => 'Obat analgesik dan antipiretik',
                'id_jenis_obat' => 1,
                'id_satuan' => 4,
                'jumlah_per_kemasan' => 10,
                'harga_per_satuan' => 400.00,
                'harga_per_kemasan' => 4000.00
            ],
            [
                'nama_obat' => 'Ranitidin',
                'keterangan' => 'Obat maag',
                'id_jenis_obat' => 1,
                'id_satuan' => 4,
                'jumlah_per_kemasan' => 10,
                'harga_per_satuan' => 600.00,
                'harga_per_kemasan' => 6000.00
            ],
            [
                'nama_obat' => 'Paracetamol Sy',
                'keterangan' => 'Syrup anak',
                'id_jenis_obat' => 3,
                'id_satuan' => 3,
                'jumlah_per_kemasan' => 1,
                'harga_per_satuan' => 7000.00,
                'harga_per_kemasan' => 7000.00
            ],
            [
                'nama_obat' => 'Salbutamol 4mg',
                'keterangan' => 'Obat inhaler/asthma',
                'id_jenis_obat' => 7,
                'id_satuan' => 6,
                'jumlah_per_kemasan' => 1,
                'harga_per_satuan' => 9000.00,
                'harga_per_kemasan' => 9000.00
            ],
            [
                'nama_obat' => 'Slopma / Dermi',
                'keterangan' => 'Salep kulit',
                'id_jenis_obat' => 4,
                'id_satuan' => 5,
                'jumlah_per_kemasan' => 1,
                'harga_per_satuan' => 20000.00,
                'harga_per_kemasan' => 20000.00
            ],
            [
                'nama_obat' => 'Simvastatin',
                'keterangan' => 'Obat kolesterol',
                'id_jenis_obat' => 1,
                'id_satuan' => 4,
                'jumlah_per_kemasan' => 10,
                'harga_per_satuan' => 1200.00,
                'harga_per_kemasan' => 12000.00
            ],
            [
                'nama_obat' => 'Vit C',
                'keterangan' => 'Suplemen vitamin',
                'id_jenis_obat' => 1,
                'id_satuan' => 4,
                'jumlah_per_kemasan' => 10,
                'harga_per_satuan' => 800.00,
                'harga_per_kemasan' => 8000.00
            ],
            [
                'nama_obat' => 'Neurodex/Neuropyron',
                'keterangan' => 'Suplemen saraf',
                'id_jenis_obat' => 1,
                'id_satuan' => 4,
                'jumlah_per_kemasan' => 10,
                'harga_per_satuan' => 1000.00,
                'harga_per_kemasan' => 10000.00
            ],
            [
                'nama_obat' => 'Tm. Insto',
                'keterangan' => 'Tetes mata',
                'id_jenis_obat' => 5,
                'id_satuan' => 3,
                'jumlah_per_kemasan' => 1,
                'harga_per_satuan' => 18000.00,
                'harga_per_kemasan' => 18000.00
            ],
            [
                'nama_obat' => 'Tm. Genoint',
                'keterangan' => 'Tetes mata',
                'id_jenis_obat' => 5,
                'id_satuan' => 6,
                'jumlah_per_kemasan' => 1,
                'harga_per_satuan' => 15000.00,
                'harga_per_kemasan' => 15000.00
            ],
            [
                'nama_obat' => 'T. Telinga',
                'keterangan' => 'Tetes telinga',
                'id_jenis_obat' => 6,
                'id_satuan' => 3,
                'jumlah_per_kemasan' => 1,
                'harga_per_satuan' => 20000.00,
                'harga_per_kemasan' => 20000.00
            ],
            [
                'nama_obat' => 'S.K Miconazole',
                'keterangan' => 'Salep jamur ringan',
                'id_jenis_obat' => 4,
                'id_satuan' => 5,
                'jumlah_per_kemasan' => 1,
                'harga_per_satuan' => 12000.00,
                'harga_per_kemasan' => 12000.00
            ],
            [
                'nama_obat' => 'S.K Hydro',
                'keterangan' => 'Salep kulit',
                'id_jenis_obat' => 4,
                'id_satuan' => 5,
                'jumlah_per_kemasan' => 1,
                'harga_per_satuan' => 10000.00,
                'harga_per_kemasan' => 10000.00
            ],
            [
                'nama_obat' => 'S.K Gentamicin / Genoint',
                'keterangan' => 'Salep antibiotik',
                'id_jenis_obat' => 4,
                'id_satuan' => 5,
                'jumlah_per_kemasan' => 1,
                'harga_per_satuan' => 12000.00,
                'harga_per_kemasan' => 12000.00
            ],
            [
                'nama_obat' => 'Octenilin / Bioplasington',
                'keterangan' => 'Salep luka',
                'id_jenis_obat' => 4,
                'id_satuan' => 5,
                'jumlah_per_kemasan' => 1,
                'harga_per_satuan' => 25000.00,
                'harga_per_kemasan' => 25000.00
            ],
            [
                'nama_obat' => 'Attapulgite',
                'keterangan' => 'Obat diare',
                'id_jenis_obat' => 1,
                'id_satuan' => 1,
                'jumlah_per_kemasan' => 10,
                'harga_per_satuan' => 500.00,
                'harga_per_kemasan' => 5000.00
            ],
            [
                'nama_obat' => 'Diatabs',
                'keterangan' => 'Obat diare',
                'id_jenis_obat' => 1,
                'id_satuan' => 1,
                'jumlah_per_kemasan' => 10,
                'harga_per_satuan' => 700.00,
                'harga_per_kemasan' => 7000.00
            ],
            [
                'nama_obat' => 'Stic A.U',
                'keterangan' => 'Tetes mata',
                'id_jenis_obat' => 5,
                'id_satuan' => 4,
                'jumlah_per_kemasan' => 1,
                'harga_per_satuan' => 15000.00,
                'harga_per_kemasan' => 15000.00
            ],
            [
                'nama_obat' => 'Stic Cho',
                'keterangan' => 'Tetes mata',
                'id_jenis_obat' => 5,
                'id_satuan' => 4,
                'jumlah_per_kemasan' => 1,
                'harga_per_satuan' => 15000.00,
                'harga_per_kemasan' => 15000.00
            ],
            [
                'nama_obat' => 'Panadol Extra',
                'keterangan' => 'Obat nyeri',
                'id_jenis_obat' => 1,
                'id_satuan' => 1,
                'jumlah_per_kemasan' => 10,
                'harga_per_satuan' => 1200.00,
                'harga_per_kemasan' => 12000.00
            ],
            [
                'nama_obat' => 'Panadol Biru',
                'keterangan' => 'Obat nyeri',
                'id_jenis_obat' => 1,
                'id_satuan' => 1,
                'jumlah_per_kemasan' => 10,
                'harga_per_satuan' => 1000.00,
                'harga_per_kemasan' => 10000.00
            ],
            [
                'nama_obat' => 'Triclofem KBS 3Bln',
                'keterangan' => 'Kontrasepsi',
                'id_jenis_obat' => 1,
                'id_satuan' => 1,
                'jumlah_per_kemasan' => 10,
                'harga_per_satuan' => 3000.00,
                'harga_per_kemasan' => 30000.00
            ],
            [
                'nama_obat' => 'Spuite 3 cc',
                'keterangan' => 'Alat suntik',
                'id_jenis_obat' => 8,
                'id_satuan' => 6,
                'jumlah_per_kemasan' => 1,
                'harga_per_satuan' => 2000.00,
                'harga_per_kemasan' => 2000.00
            ],
            [
                'nama_obat' => 'Dobrizole / lansoprazole',
                'keterangan' => 'Obat maag',
                'id_jenis_obat' => 1,
                'id_satuan' => 1,
                'jumlah_per_kemasan' => 10,
                'harga_per_satuan' => 1500.00,
                'harga_per_kemasan' => 15000.00
            ],
            [
                'nama_obat' => 'Ternix hijau',
                'keterangan' => 'Salep kulit',
                'id_jenis_obat' => 4,
                'id_satuan' => 5,
                'jumlah_per_kemasan' => 1,
                'harga_per_satuan' => 10000.00,
                'harga_per_kemasan' => 10000.00
            ],
            [
                'nama_obat' => 'Ternix merah / coparcetin sy',
                'keterangan' => 'Salep kulit',
                'id_jenis_obat' => 4,
                'id_satuan' => 5,
                'jumlah_per_kemasan' => 1,
                'harga_per_satuan' => 12000.00,
                'harga_per_kemasan' => 12000.00
            ],
            [
                'nama_obat' => 'Hansaplast',
                'keterangan' => 'Plester luka',
                'id_jenis_obat' => 8,
                'id_satuan' => 6,
                'jumlah_per_kemasan' => 1,
                'harga_per_satuan' => 5000.00,
                'harga_per_kemasan' => 5000.00
            ],
            [
                'nama_obat' => 'Allopurinol',
                'keterangan' => 'Obat asam urat',
                'id_jenis_obat' => 1,
                'id_satuan' => 4,
                'jumlah_per_kemasan' => 10,
                'harga_per_satuan' => 800.00,
                'harga_per_kemasan' => 8000.00
            ],
            [
                'nama_obat' => 'Ambroxol',
                'keterangan' => 'Obat batuk',
                'id_jenis_obat' => 1,
                'id_satuan' => 4,
                'jumlah_per_kemasan' => 10,
                'harga_per_satuan' => 800.00,
                'harga_per_kemasan' => 8000.00
            ],
            [
                'nama_obat' => 'CTM',
                'keterangan' => 'Obat alergi',
                'id_jenis_obat' => 1,
                'id_satuan' => 1,
                'jumlah_per_kemasan' => 10,
                'harga_per_satuan' => 300.00,
                'harga_per_kemasan' => 3000.00
            ],
            [
                'nama_obat' => 'Cipropluxacin',
                'keterangan' => 'Antibiotik',
                'id_jenis_obat' => 1,
                'id_satuan' => 1,
                'jumlah_per_kemasan' => 10,
                'harga_per_satuan' => 1800.00,
                'harga_per_kemasan' => 18000.00
            ],
            [
                'nama_obat' => 'Cefixme',
                'keterangan' => 'Antibiotik',
                'id_jenis_obat' => 1,
                'id_satuan' => 1,
                'jumlah_per_kemasan' => 10,
                'harga_per_satuan' => 2000.00,
                'harga_per_kemasan' => 20000.00
            ],
            [
                'nama_obat' => 'Cepadroxile',
                'keterangan' => 'Antibiotik',
                'id_jenis_obat' => 1,
                'id_satuan' => 1,
                'jumlah_per_kemasan' => 10,
                'harga_per_satuan' => 1500.00,
                'harga_per_kemasan' => 15000.00
            ],
            [
                'nama_obat' => 'Metforment',
                'keterangan' => 'Obat diabetes',
                'id_jenis_obat' => 1,
                'id_satuan' => 1,
                'jumlah_per_kemasan' => 10,
                'harga_per_satuan' => 1200.00,
                'harga_per_kemasan' => 12000.00
            ],
            [
                'nama_obat' => 'Ambeven',
                'keterangan' => 'Obat batuk',
                'id_jenis_obat' => 1,
                'id_satuan' => 4,
                'jumlah_per_kemasan' => 10,
                'harga_per_satuan' => 1000.00,
                'harga_per_kemasan' => 10000.00
            ],
            [
                'nama_obat' => 'Ventasal / salbu mg',
                'keterangan' => 'Obat asma',
                'id_jenis_obat' => 7,
                'id_satuan' => 6,
                'jumlah_per_kemasan' => 1,
                'harga_per_satuan' => 8000.00,
                'harga_per_kemasan' => 8000.00
            ],
            [
                'nama_obat' => 'Kassa Steril',
                'keterangan' => 'Alat kesehatan',
                'id_jenis_obat' => 8,
                'id_satuan' => 6,
                'jumlah_per_kemasan' => 1,
                'harga_per_satuan' => 5000.00,
                'harga_per_kemasan' => 5000.00
            ],
            [
                'nama_obat' => 'Salep mata Genoint',
                'keterangan' => 'Salep mata antibiotik',
                'id_jenis_obat' => 4,
                'id_satuan' => 5,
                'jumlah_per_kemasan' => 1,
                'harga_per_satuan' => 12000.00,
                'harga_per_kemasan' => 12000.00
            ],
            [
                'nama_obat' => 'Grantusif',
                'keterangan' => 'Obat flu',
                'id_jenis_obat' => 1,
                'id_satuan' => 1,
                'jumlah_per_kemasan' => 10,
                'harga_per_satuan' => 1000.00,
                'harga_per_kemasan' => 10000.00
            ],
            [
                'nama_obat' => 'CETIRIZINE',
                'keterangan' => 'Antihistamin',
                'id_jenis_obat' => 1,
                'id_satuan' => 1,
                'jumlah_per_kemasan' => 10,
                'harga_per_satuan' => 800.00,
                'harga_per_kemasan' => 8000.00
            ],
        ];

        // Using Eloquent insert method for better compatibility
        // Process in chunks to avoid memory issues with large datasets
        foreach (array_chunk($obats, 100) as $chunk) {
            Obat::insert($chunk);
        }
    }
}
