<?php

namespace Database\Seeders;

use App\Models\Karyawan;
use App\Models\MedicalCheckUp;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MedicalCheckUpSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvPath = database_path('seeders/medical_checkup/Medical_Checkup.csv');
        
        if (!file_exists($csvPath)) {
            $this->command->error("File CSV tidak ditemukan: {$csvPath}");
            return;
        }

        $csvData = $this->readCsv($csvPath);
        
        if (empty($csvData)) {
            $this->command->error("File CSV kosong atau tidak dapat dibaca");
            return;
        }

        $this->command->info('Memulai proses seeding data Medical Check Up...');
        $this->command->info('Total data yang akan diproses: ' . (count($csvData) - 1)); // -1 untuk header

        // Disable foreign key checks temporarily
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        $processed = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($csvData as $index => $row) {
            // Skip header row
            if ($index === 0) {
                continue;
            }

            try {
                $result = $this->processRow($row);
                
                if ($result === 'processed') {
                    $processed++;
                } elseif ($result === 'skipped') {
                    $skipped++;
                }
            } catch (\Exception $e) {
                $errors++;
                Log::error("Error processing row {$index}: " . $e->getMessage());
                $this->command->line("Error pada baris {$index}: " . $e->getMessage());
            }

            // Progress indicator
            if ($index % 50 === 0) {
                $this->command->line("Memproses baris {$index}...");
            }
        }

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('Seeding Medical Check Up selesai!');
        $this->command->info("Data berhasil diproses: {$processed}");
        $this->command->info("Data dilewati: {$skipped}");
        $this->command->info("Error: {$errors}");
    }

    /**
     * Read CSV file and return array of data
     */
    private function readCsv(string $filePath): array
    {
        $csv = array_map(function ($line) {
            return str_getcsv($line, ';');
        }, file($filePath));

        return $csv;
    }

    /**
     * Process a single row from CSV
     */
    private function processRow(array $row): string
    {
        // Extract data from CSV row
        $tahun = $row[0] ?? null;
        $tanggalStr = $row[1] ?? null;
        $namaKaryawan = $row[2] ?? null;
        $dikeluarkanOleh = $row[3] ?? null;
        $bmiAngka = $row[4] ?? null;
        $bmiCategory = $row[5] ?? null;
        $statusKesehatan = $row[11] ?? null;

        // Skip if essential data is missing
        if (empty($namaKaryawan) || empty($tahun)) {
            return 'skipped';
        }

        // Find karyawan by name
        $karyawan = Karyawan::where('nama_karyawan', 'like', '%' . trim($namaKaryawan) . '%')->first();
        
        if (!$karyawan) {
            Log::warning("Karyawan tidak ditemukan: {$namaKaryawan}");
            return 'skipped';
        }

        // Parse tanggal (format DD/MM/YYYY to Y-m-d)
        $tanggal = null;
        if (!empty($tanggalStr)) {
            $tanggalParts = explode('/', trim($tanggalStr));
            if (count($tanggalParts) === 3) {
                $tanggal = "{$tanggalParts[2]}-{$tanggalParts[1]}-{$tanggalParts[0]}";
            }
        }

        // Normalize BMI category
        $bmiCategory = $this->normalizeBmiCategory($bmiCategory);

        // Prepare data for insertion
        $medicalCheckUpData = [
            'id_karyawan' => $karyawan->id_karyawan,
            'id_keluarga' => null, // Not available in CSV
            'periode' => $tahun,
            'tanggal' => $tanggal,
            'dikeluarkan_oleh' => $dikeluarkanOleh,
            'bmi' => $this->convertBmiToDecimal($bmiAngka),
            'keterangan_bmi' => $bmiCategory,
            'id_kondisi_kesehatan' => null, // Not available in CSV
            'catatan' => $this->normalizeCatatan($statusKesehatan),
            'file_path' => null,
            'file_name' => null,
            'file_size' => null,
            'mime_type' => null,
            'id_user' => null, // Not available in CSV
            'created_at' => now(),
            'updated_at' => now(),
        ];

        // Insert or update the record
        MedicalCheckUp::updateOrCreate(
            [
                'id_karyawan' => $karyawan->id_karyawan,
                'periode' => $tahun,
                'tanggal' => $tanggal,
            ],
            $medicalCheckUpData
        );

        return 'processed';
    }

    /**
     * Normalize BMI category to match database enum values
     */
    private function normalizeBmiCategory(?string $bmiCategory): ?string
    {
        if (empty($bmiCategory)) {
            return null;
        }

        $category = trim($bmiCategory);
        
        // Map CSV values to database enum values
        $mapping = [
            'Underweight' => 'Underweight',
            'Nomal' => 'Normal',
            'Normal' => 'Normal',
            'Overweight' => 'Overweight',
            'Obesitas Tk 1' => 'Obesitas Tk 1',
            'Obesitas Tk 2' => 'Obesitas Tk 2',
            'Obesitas Tk 3' => 'Obesitas Tk 3',
        ];

        return $mapping[$category] ?? null;
    }

    /**
     * Convert BMI string to decimal
     */
    private function convertBmiToDecimal(?string $bmiAngka): ?float
    {
        if (empty($bmiAngka)) {
            return null;
        }

        // Replace comma with dot for decimal conversion
        $bmiValue = str_replace(',', '.', trim($bmiAngka));
        
        return is_numeric($bmiValue) ? (float) $bmiValue : null;
    }

    /**
     * Normalize catatan to match database enum values
     */
    private function normalizeCatatan(?string $statusKesehatan): ?string
    {
        if (empty($statusKesehatan)) {
            return null;
        }

        $status = trim($statusKesehatan);
        
        // Map CSV values to database enum values
        $mapping = [
            'Fit' => 'Fit',
            'Fit dengan Catatan' => 'Fit dengan Catatan',
            'Fit dalam Pengawasan' => 'Fit dalam Pengawasan',
        ];

        return $mapping[$status] ?? null;
    }
}