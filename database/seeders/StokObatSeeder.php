<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Obat;
use App\Models\StokObat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StokObatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Path ke file CSV
        $csvPath = database_path('seeders/DATA OBAT PERBULAN.csv');

        // Get reference data
        $obats = Obat::pluck('id_obat', 'nama_obat')->toArray();

        // Start transaction
        DB::beginTransaction();

        try {
            // Create initial stok data for all obat
            $this->createInitialStok($obats);

            // If CSV file exists, process it
            if (file_exists($csvPath)) {
                // Baca file CSV
                $csvData = $this->readCsv($csvPath);

                if (!empty($csvData)) {
                    // Parse header untuk mendapatkan periode
                    $headerRow = $csvData[1]; // Row 2 contains headers
                    $periodes = [];

                    // Ekstrak periode dari header (format: MM-YY)
                    for ($i = 5; $i < count($headerRow); $i += 4) {
                        if (isset($headerRow[$i]) && preg_match('/(\d{2}-\d{2})/', $headerRow[$i], $matches)) {
                            $periodes[] = $matches[1];
                        }
                    }

                    $this->command->info('Ditemukan ' . count($periodes) . ' periode: ' . implode(', ', $periodes));

                    $successCount = 0;
                    $errorCount = 0;
                    $errors = [];

                    // Process data rows (skip first 2 header rows)
                    for ($rowNumber = 2; $rowNumber < count($csvData); $rowNumber++) {
                        $rowData = $csvData[$rowNumber];

                        // Get nama obat dari kolom B (index 1)
                        $namaObat = trim($rowData[1] ?? '');

                        if (empty($namaObat)) {
                            continue; // Skip empty rows
                        }

                        // Check if obat exists
                        if (!isset($obats[$namaObat])) {
                            $errors[] = "Baris " . ($rowNumber + 1) . ": Obat '$namaObat' tidak ditemukan di database";
                            $errorCount++;
                            continue;
                        }

                        $idObat = $obats[$namaObat];

                        // Process each periode
                        $colIndex = 5; // Start from column F (index 5)
                        foreach ($periodes as $periode) {
                            if ($colIndex + 3 < count($rowData)) {
                                $stokAwal = $this->parseStokValue($rowData[$colIndex] ?? 0);
                                $stokPakai = $this->parseStokValue($rowData[$colIndex + 1] ?? 0);
                                $stokAkhir = $this->parseStokValue($rowData[$colIndex + 2] ?? 0);
                                $stokMasuk = $this->parseStokValue($rowData[$colIndex + 3] ?? 0);

                                // Insert or update stok obat
                                StokObat::updateOrCreate(
                                    [
                                        'id_obat' => $idObat,
                                        'periode' => $periode,
                                    ],
                                    [
                                        'stok_awal' => $stokAwal,
                                        'stok_pakai' => $stokPakai,
                                        'stok_akhir' => $stokAkhir,
                                        'stok_masuk' => $stokMasuk,
                                    ]
                                );
                            }
                            $colIndex += 4;
                        }
                        $successCount++;
                    }

                    $this->command->info("Import stok bulanan selesai: $successCount data berhasil diproses");
                    if ($errorCount > 0) {
                        $this->command->warn("$errorCount data gagal diproses");
                        foreach (array_slice($errors, 0, 5) as $error) {
                            $this->command->warn($error);
                        }
                        if (count($errors) > 5) {
                            $this->command->warn('... dan ' . (count($errors) - 5) . ' error lainnya');
                        }
                    }
                }
            } else {
                $this->command->warn('File CSV tidak ditemukan: ' . $csvPath . ', hanya membuat data stok awal');
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Error importing stok bulanan: ' . $e->getMessage());
            Log::error('Error importing stok bulanan: ' . $e->getMessage());
        }
    }

    /**
     * Create initial stok data for all obat
     */
    private function createInitialStok($obats)
    {
        // Current period in MM-YY format
        $currentPeriod = date('m-y');

        // Initial stok data from ObatSeeder
        $initialStokData = [
            'Amlodipin 5mg' => ['stok_awal' => 100, 'stok_pakai' => 0, 'stok_akhir' => 100, 'stok_masuk' => 0],
            'Amlodipin 10mg' => ['stok_awal' => 100, 'stok_pakai' => 0, 'stok_akhir' => 100, 'stok_masuk' => 0],
            'Amoxicilin' => ['stok_awal' => 100, 'stok_pakai' => 0, 'stok_akhir' => 100, 'stok_masuk' => 0],
            'Antasid tab' => ['stok_awal' => 100, 'stok_pakai' => 0, 'stok_akhir' => 100, 'stok_masuk' => 0],
            'Antasid Sy' => ['stok_awal' => 100, 'stok_pakai' => 0, 'stok_akhir' => 100, 'stok_masuk' => 0],
            'Asmef' => ['stok_awal' => 100, 'stok_pakai' => 0, 'stok_akhir' => 100, 'stok_masuk' => 0],
            'Piroxicamp' => ['stok_awal' => 100, 'stok_pakai' => 12, 'stok_akhir' => 88, 'stok_masuk' => 0],
            'Dexa' => ['stok_awal' => 100, 'stok_pakai' => 0, 'stok_akhir' => 100, 'stok_masuk' => 0],
            'Methyl' => ['stok_awal' => 100, 'stok_pakai' => 0, 'stok_akhir' => 100, 'stok_masuk' => 0],
            'Flucodex. du' => ['stok_awal' => 100, 'stok_pakai' => 0, 'stok_akhir' => 100, 'stok_masuk' => 0],
            'Microgynon' => ['stok_awal' => 100, 'stok_pakai' => 0, 'stok_akhir' => 100, 'stok_masuk' => 0],
            'Nadic' => ['stok_awal' => 100, 'stok_pakai' => 0, 'stok_akhir' => 100, 'stok_masuk' => 0],
            'Paracetamol' => ['stok_awal' => 100, 'stok_pakai' => 12, 'stok_akhir' => 88, 'stok_masuk' => 0],
            'Ranitidin' => ['stok_awal' => 100, 'stok_pakai' => 0, 'stok_akhir' => 100, 'stok_masuk' => 0],
            'Paracetamol Sy' => ['stok_awal' => 100, 'stok_pakai' => 0, 'stok_akhir' => 100, 'stok_masuk' => 0],
            'Salbutamol 4mg' => ['stok_awal' => 100, 'stok_pakai' => 0, 'stok_akhir' => 100, 'stok_masuk' => 0],
            'Slopma / Dermi' => ['stok_awal' => 100, 'stok_pakai' => 0, 'stok_akhir' => 100, 'stok_masuk' => 0],
            'Simvastatin' => ['stok_awal' => 100, 'stok_pakai' => 0, 'stok_akhir' => 100, 'stok_masuk' => 0],
            'Vit C' => ['stok_awal' => 100, 'stok_pakai' => 4, 'stok_akhir' => 96, 'stok_masuk' => 0],
            'Neurodex/Neuropyron' => ['stok_awal' => 100, 'stok_pakai' => 0, 'stok_akhir' => 100, 'stok_masuk' => 0],
            'Tm. Insto' => ['stok_awal' => 100, 'stok_pakai' => 0, 'stok_akhir' => 100, 'stok_masuk' => 0],
            'Tm. Genoint' => ['stok_awal' => 100, 'stok_pakai' => 0, 'stok_akhir' => 100, 'stok_masuk' => 0],
            'T. Telinga' => ['stok_awal' => 100, 'stok_pakai' => 0, 'stok_akhir' => 100, 'stok_masuk' => 0],
            'S.K Miconazole' => ['stok_awal' => 100, 'stok_pakai' => 0, 'stok_akhir' => 100, 'stok_masuk' => 0],
            'S.K Hydro' => ['stok_awal' => 100, 'stok_pakai' => 0, 'stok_akhir' => 100, 'stok_masuk' => 0],
            'S.K Gentamicin / Genoint' => ['stok_awal' => 100, 'stok_pakai' => 0, 'stok_akhir' => 100, 'stok_masuk' => 0],
            'Octenilin / Bioplasington' => ['stok_awal' => 100, 'stok_pakai' => 0, 'stok_akhir' => 100, 'stok_masuk' => 0],
            'Attapulgite' => ['stok_awal' => 100, 'stok_pakai' => 0, 'stok_akhir' => 100, 'stok_masuk' => 0],
            'Diatabs' => ['stok_awal' => 100, 'stok_pakai' => 0, 'stok_akhir' => 100, 'stok_masuk' => 0],
            'Stic A.U' => ['stok_awal' => 100, 'stok_pakai' => 0, 'stok_akhir' => 100, 'stok_masuk' => 0],
            'Stic Cho' => ['stok_awal' => 100, 'stok_pakai' => 0, 'stok_akhir' => 100, 'stok_masuk' => 0],
            'Panadol Extra' => ['stok_awal' => 100, 'stok_pakai' => 0, 'stok_akhir' => 100, 'stok_masuk' => 0],
            'Panadol Biru' => ['stok_awal' => 100, 'stok_pakai' => 0, 'stok_akhir' => 100, 'stok_masuk' => 0],
            'Triclofem KBS 3Bln' => ['stok_awal' => 100, 'stok_pakai' => 0, 'stok_akhir' => 100, 'stok_masuk' => 0],
            'Spuite 3 cc' => ['stok_awal' => 100, 'stok_pakai' => 0, 'stok_akhir' => 100, 'stok_masuk' => 0],
            'Dobrizole / lansoprazole' => ['stok_awal' => 100, 'stok_pakai' => 0, 'stok_akhir' => 100, 'stok_masuk' => 0],
            'Ternix hijau' => ['stok_awal' => 100, 'stok_pakai' => 0, 'stok_akhir' => 100, 'stok_masuk' => 0],
            'Ternix merah / coparcetin sy' => ['stok_awal' => 100, 'stok_pakai' => 0, 'stok_akhir' => 100, 'stok_masuk' => 0],
            'Hansaplast' => ['stok_awal' => 100, 'stok_pakai' => 0, 'stok_akhir' => 100, 'stok_masuk' => 0],
            'Allopurinol' => ['stok_awal' => 100, 'stok_pakai' => 0, 'stok_akhir' => 200, 'stok_masuk' => 100],
            'Ambroxol' => ['stok_awal' => 100, 'stok_pakai' => 0, 'stok_akhir' => 100, 'stok_masuk' => 0],
            'CTM' => ['stok_awal' => 100, 'stok_pakai' => 0, 'stok_akhir' => 100, 'stok_masuk' => 0],
            'Cipropluxacin' => ['stok_awal' => 100, 'stok_pakai' => 0, 'stok_akhir' => 100, 'stok_masuk' => 0],
            'Cefixme' => ['stok_awal' => 100, 'stok_pakai' => 0, 'stok_akhir' => 100, 'stok_masuk' => 0],
            'Cepadroxile' => ['stok_awal' => 100, 'stok_pakai' => 0, 'stok_akhir' => 100, 'stok_masuk' => 0],
            'Metforment' => ['stok_awal' => 100, 'stok_pakai' => 0, 'stok_akhir' => 100, 'stok_masuk' => 0],
            'Ambeven' => ['stok_awal' => 100, 'stok_pakai' => 0, 'stok_akhir' => 200, 'stok_masuk' => 100],
            'Ventasal / salbu mg' => ['stok_awal' => 100, 'stok_pakai' => 0, 'stok_akhir' => 100, 'stok_masuk' => 0],
            'Kassa Steril' => ['stok_awal' => 100, 'stok_pakai' => 0, 'stok_akhir' => 100, 'stok_masuk' => 0],
            'Salep mata Genoint' => ['stok_awal' => 100, 'stok_pakai' => 0, 'stok_akhir' => 100, 'stok_masuk' => 0],
            'Grantusif' => ['stok_awal' => 100, 'stok_pakai' => 0, 'stok_akhir' => 100, 'stok_masuk' => 0],
            'CETIRIZINE' => ['stok_awal' => 100, 'stok_pakai' => 0, 'stok_akhir' => 100, 'stok_masuk' => 0],
        ];

        $createdCount = 0;
        foreach ($obats as $namaObat => $idObat) {
            if (isset($initialStokData[$namaObat])) {
                $stokData = $initialStokData[$namaObat];

                // Create stok obat record
                StokObat::updateOrCreate(
                    [
                        'id_obat' => $idObat,
                        'periode' => $currentPeriod,
                    ],
                    [
                        'stok_awal' => $stokData['stok_awal'],
                        'stok_pakai' => $stokData['stok_pakai'],
                        'stok_akhir' => $stokData['stok_akhir'],
                        'stok_masuk' => $stokData['stok_masuk'],
                    ]
                );
                $createdCount++;
            }
        }

        $this->command->info("Created initial stok data for $createdCount obat in period $currentPeriod");
    }

    /**
     * Read CSV file
     */
    private function readCsv($filePath)
    {
        $csvData = [];

        if (($handle = fopen($filePath, 'r')) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ';')) !== FALSE) {
                $csvData[] = $data;
            }
            fclose($handle);
        }

        return $csvData;
    }

    /**
     * Parse nilai stok dari CSV
     */
    private function parseStokValue($value)
    {
        // Handle nilai dengan tanda kurung (60) = -60
        if (is_string($value) && preg_match('/^\((\d+)\)$/', $value, $matches)) {
            return -(int)$matches[1];
        }

        // Handle nilai dengan titik atau koma
        $value = str_replace(['.', ','], '', $value);

        // Handle nilai "-" atau kosong
        if ($value === '-' || $value === '' || $value === null) {
            return 0;
        }

        return (int)$value;
    }
}
