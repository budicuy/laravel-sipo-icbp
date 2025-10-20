<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Diagnosa;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DiagnosaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Path ke file CSV
        $csvPath = database_path('seeders/seeders_master_data/data_diagnosa.csv');

        // Hapus data lama jika ada menggunakan Eloquent
        Diagnosa::query()->delete();

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
                        $namaDiagnosa = trim($rowData[0] ?? '');
                        $deskripsi = trim($rowData[1] ?? '');

                        if (empty($namaDiagnosa)) {
                            continue; // Skip empty rows
                        }

                        // Create diagnosa record
                        Diagnosa::create([
                            'nama_diagnosa' => $namaDiagnosa,
                            'deskripsi' => $deskripsi,
                        ]);

                        $successCount++;
                    }

                    $this->command->info("Import data diagnosa selesai: $successCount data berhasil diproses");
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
            $this->command->error('Error importing diagnosa: ' . $e->getMessage());
            Log::error('Error importing diagnosa: ' . $e->getMessage());
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
