<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Obat;
use App\Models\SatuanObat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ObatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Path ke file CSV
        $csvPath = database_path('seeders/seeders_master_data/data_obat.csv');

        // Hapus data lama jika ada menggunakan Eloquent
        Obat::query()->delete();

        // Get reference data for satuan obat
        $satuanObats = SatuanObat::pluck('id_satuan', 'nama_satuan')->toArray();

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
                        $namaSatuan = trim($rowData[1] ?? '');
                        $keterangan = trim($rowData[2] ?? '');

                        if (empty($namaObat)) {
                            continue; // Skip empty rows
                        }

                        // Find satuan obat ID
                        $idSatuan = null;
                        if (!empty($namaSatuan)) {
                            // Try exact match first
                            if (isset($satuanObats[$namaSatuan])) {
                                $idSatuan = $satuanObats[$namaSatuan];
                            } else {
                                // Try partial match
                                foreach ($satuanObats as $satuanName => $satuanId) {
                                    if (stripos($satuanName, $namaSatuan) !== false ||
                                        stripos($namaSatuan, $satuanName) !== false) {
                                        $idSatuan = $satuanId;
                                        break;
                                    }
                                }

                                // If still not found, try to match common variations
                                if ($idSatuan === null) {
                                    $namaSatuanLower = strtolower($namaSatuan);
                                    if ($namaSatuanLower === 'tablet' || $namaSatuanLower === 'tab') {
                                        $idSatuan = $satuanObats['Tablet'] ?? null;
                                    } elseif ($namaSatuanLower === 'botol' || $namaSatuanLower === 'btl') {
                                        $idSatuan = $satuanObats['Botol'] ?? null;
                                    } elseif ($namaSatuanLower === 'tube' || $namaSatuanLower === 'salep') {
                                        $idSatuan = $satuanObats['Tube'] ?? null;
                                    } elseif ($namaSatuanLower === 'ampul' || $namaSatuanLower === 'injek') {
                                        $idSatuan = $satuanObats['Ampul'] ?? null;
                                    } elseif ($namaSatuanLower === 'pcs' || $namaSatuanLower === 'piece') {
                                        $idSatuan = $satuanObats['Pcs'] ?? null;
                                    } elseif ($namaSatuanLower === 'roll') {
                                        $idSatuan = $satuanObats['Roll'] ?? null;
                                    }
                                }

                                if ($idSatuan === null) {
                                    $errors[] = "Baris " . ($rowNumber + 1) . ": Satuan '$namaSatuan' tidak ditemukan";
                                    $errorCount++;
                                    continue;
                                }
                            }
                        }

                        // Create obat record
                        Obat::create([
                            'nama_obat' => $namaObat,
                            'keterangan' => $keterangan,
                            'id_satuan' => $idSatuan,
                            'tanggal_update' => now(),
                        ]);

                        $successCount++;
                    }

                    $this->command->info("Import data obat selesai: $successCount data berhasil diproses");
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
            $this->command->error('Error importing obat: ' . $e->getMessage());
            Log::error('Error importing obat: ' . $e->getMessage());
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
