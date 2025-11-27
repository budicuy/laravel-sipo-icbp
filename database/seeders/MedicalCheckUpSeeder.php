<?php

namespace Database\Seeders;

use App\Models\Karyawan;
use App\Models\MedicalCheckUp;
use App\Models\KondisiKesehatan;
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
        $kondisiKesehatanMap = $this->buildKondisiKesehatanMap();

        foreach ($csvData as $index => $row) {
            // Skip header row
            if ($index === 0) {
                continue;
            }

            try {
                $result = $this->processRow($row, $kondisiKesehatanMap);
                
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
     * Build a map of kondisi kesehatan names to IDs
     */
    private function buildKondisiKesehatanMap(): array
    {
        $kondisiKesehatan = KondisiKesehatan::all();
        $map = [];
        
        foreach ($kondisiKesehatan as $kondisi) {
            $map[strtolower(trim($kondisi->nama_kondisi))] = $kondisi->id;
        }
        
        return $map;
    }

    /**
     * Process a single row from CSV
     */
    private function processRow(array $row, array $kondisiKesehatanMap): string
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
            'id_kondisi_kesehatan' => null, // Will be handled via pivot table
            'catatan' => $this->normalizeCatatan($statusKesehatan),
            'file_path' => null,
            'file_name' => null,
            'file_size' => null,
            'mime_type' => null,
            'id_user' => null, // Not available in CSV
            'created_at' => now(),
            'updated_at' => now(),
        ];

        // Check if record exists first
        $existingRecord = MedicalCheckUp::where('id_karyawan', $karyawan->id_karyawan)
            ->where('periode', $tahun)
            ->where('tanggal', $tanggal)
            ->first();
            
        if ($existingRecord) {
            // Update existing record
            $existingRecord->update($medicalCheckUpData);
            $medicalCheckUp = $existingRecord;
        } else {
            // Create new record
            $medicalCheckUp = new MedicalCheckUp($medicalCheckUpData);
            $medicalCheckUp->save();
        }

        // Process gangguan kesehatan (columns 6-10)
        if ($medicalCheckUp->id_medical_check_up) {
            $this->processGangguanKesehatan($row, $medicalCheckUp->id_medical_check_up, $kondisiKesehatanMap);
        }

        return 'processed';
    }

    /**
     * Process gangguan kesehatan and insert into pivot table
     */
    private function processGangguanKesehatan(array $row, $medicalCheckUpId, array $kondisiKesehatanMap): void
    {
        // Extract gangguan kesehatan from columns 6-10
        $gangguanKesehatan = [];
        
        for ($i = 6; $i <= 10; $i++) {
            if (!empty($row[$i]) && trim($row[$i]) !== '-') {
                $gangguanKesehatan[] = trim($row[$i]);
            }
        }

        // Clear existing relations for this medical check up
        DB::table('medical_check_up_kondisi_kesehatan')
            ->where('id_medical_check_up', $medicalCheckUpId)
            ->delete();

        // Insert new relations
        foreach ($gangguanKesehatan as $gangguan) {
            $gangguanLower = strtolower($gangguan);
            
            // Try to find exact match first
            if (isset($kondisiKesehatanMap[$gangguanLower])) {
                $kondisiId = $kondisiKesehatanMap[$gangguanLower];
                
                // Check if relation already exists to avoid duplicate
                $existingRelation = DB::table('medical_check_up_kondisi_kesehatan')
                    ->where('id_medical_check_up', $medicalCheckUpId)
                    ->where('id_kondisi_kesehatan', $kondisiId)
                    ->first();
                
                if (!$existingRelation) {
                    DB::table('medical_check_up_kondisi_kesehatan')->insert([
                        'id_medical_check_up' => $medicalCheckUpId,
                        'id_kondisi_kesehatan' => $kondisiId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            } else {
                // Try partial match
                foreach ($kondisiKesehatanMap as $name => $id) {
                    if (strpos($name, $gangguanLower) !== false || strpos($gangguanLower, $name) !== false) {
                        // Check if relation already exists to avoid duplicate
                        $existingRelation = DB::table('medical_check_up_kondisi_kesehatan')
                            ->where('id_medical_check_up', $medicalCheckUpId)
                            ->where('id_kondisi_kesehatan', $id)
                            ->first();
                        
                        if (!$existingRelation) {
                            DB::table('medical_check_up_kondisi_kesehatan')->insert([
                                'id_medical_check_up' => $medicalCheckUpId,
                                'id_kondisi_kesehatan' => $id,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                        break;
                    }
                }
            }
        }
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