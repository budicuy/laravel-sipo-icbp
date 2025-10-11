<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Obat;
use App\Models\StokBulanan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StokBulananSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Path ke file CSV
        $csvPath = database_path('seeders/DATA OBAT PERBULAN.csv');

        if (!file_exists($csvPath)) {
            $this->command->error('File CSV tidak ditemukan: ' . $csvPath);
            return;
        }

        // Baca file CSV
        $csvData = $this->readCsv($csvPath);

        if (empty($csvData)) {
            $this->command->error('File CSV kosong atau tidak dapat dibaca');
            return;
        }

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

        // Get reference data
        $obats = Obat::pluck('id_obat', 'nama_obat')->toArray();

        $successCount = 0;
        $errorCount = 0;
        $errors = [];

        // Start transaction
        DB::beginTransaction();

        try {
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

                        // Insert or update stok bulanan
                        StokBulanan::updateOrCreate(
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

            DB::commit();

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

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Error importing stok bulanan: ' . $e->getMessage());
            Log::error('Error importing stok bulanan: ' . $e->getMessage());
        }
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
