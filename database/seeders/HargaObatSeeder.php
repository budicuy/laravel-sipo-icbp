<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HargaObatPerBulan;
use App\Models\Obat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HargaObatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Path ke file CSV
        $csvPath = database_path('seeders/manajemen_obat/harga_obat.csv');

        // Hapus data lama jika ada menggunakan Eloquent
        HargaObatPerBulan::query()->delete();

        // Get reference data for obat
        $obats = Obat::pluck('id_obat', 'nama_obat')->toArray();

        // Start transaction
        DB::beginTransaction();

        try {
            if (file_exists($csvPath)) {
                // Baca file CSV
                $csvData = $this->readCsv($csvPath);

                if (!empty($csvData)) {
                    $successCount = 0;
                    $errorCount = 0;
                    $errors = [];

                    // Process data rows (skip header row)
                    for ($rowNumber = 1; $rowNumber < count($csvData); $rowNumber++) {
                        $rowData = $csvData[$rowNumber];

                        // Get data dari kolom
                        $namaObat = trim($rowData[0] ?? '');
                        $hargaObatStr = trim($rowData[1] ?? '');
                        $periode = trim($rowData[2] ?? '');

                        if (empty($namaObat)) {
                            continue; // Skip empty rows
                        }

                        // Find obat ID
                        $idObat = null;
                        if (isset($obats[$namaObat])) {
                            $idObat = $obats[$namaObat];
                        } else {
                            // Try partial match
                            foreach ($obats as $obatName => $obatId) {
                                if (stripos($obatName, $namaObat) !== false ||
                                    stripos($namaObat, $obatName) !== false) {
                                    $idObat = $obatId;
                                    break;
                                }
                            }

                            if ($idObat === null) {
                                $errors[] = "Baris " . ($rowNumber + 1) . ": Obat '$namaObat' tidak ditemukan";
                                $errorCount++;
                                continue;
                            }
                        }

                        // Parse harga obat (remove quotes and thousand separators)
                        $hargaObat = 0;
                        if (!empty($hargaObatStr) && $hargaObatStr !== '-') {
                            // Remove quotes and thousand separators
                            $hargaObatStr = str_replace('"', '', $hargaObatStr);
                            $hargaObatStr = str_replace('.', '', $hargaObatStr);
                            $hargaObatStr = str_replace(',', '.', $hargaObatStr);

                            // Convert to float
                            $hargaObat = (float) $hargaObatStr;
                        }

                        // Validate periode format (MM-YY)
                        if (!empty($periode) && !preg_match('/^\d{2}-\d{2}$/', $periode)) {
                            $errors[] = "Baris " . ($rowNumber + 1) . ": Format periode '$periode' tidak valid. Gunakan format MM-YY.";
                            $errorCount++;
                            continue;
                        }

                        // Create harga obat record
                        try {
                            HargaObatPerBulan::create([
                                'id_obat' => $idObat,
                                'periode' => $periode,
                                'jumlah_per_kemasan' => 1, // Default to 1
                                'harga_per_satuan' => $hargaObat,
                                'harga_per_kemasan' => $hargaObat,
                            ]);
                            $successCount++;
                        } catch (\Exception $e) {
                            $errors[] = "Baris " . ($rowNumber + 1) . ": Error creating record - " . $e->getMessage();
                            $errorCount++;
                        }
                    }

                    $this->command->info("Import data harga obat selesai: $successCount data berhasil diproses");
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
                $this->command->error('File CSV tidak ditemukan: ' . $csvPath);
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Error importing harga obat: ' . $e->getMessage());
            Log::error('Error importing harga obat: ' . $e->getMessage());
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
}
