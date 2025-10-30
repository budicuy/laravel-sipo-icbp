<?php

namespace Database\Seeders;

use App\Models\Diagnosa;
use App\Models\Karyawan;
use App\Models\Keluarga;
use App\Models\Keluhan;
use App\Models\Obat;
use App\Models\RekamMedis;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class RekamMedisRegulerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvFile = database_path('seeders/rekam_medis_reguler/RM_REGULER.csv');

        if (! file_exists($csvFile)) {
            $this->command->error("CSV file not found: {$csvFile}");

            return;
        }

        $this->command->info('Processing CSV file: RM_REGULER.csv');

        // Read CSV file
        $csvData = $this->readCsv($csvFile);

        if (empty($csvData)) {
            $this->command->warn('No data found in CSV file');

            return;
        }

        // Skip header row and process data rows
        $totalRows = count($csvData) - 1; // Exclude header
        $this->command->info("Found {$totalRows} data rows to process");

        $processed = 0;
        $skipped = 0;

        for ($rowNumber = 1; $rowNumber < count($csvData); $rowNumber++) {
            try {
                $result = $this->processDataRow($csvData[$rowNumber], $rowNumber + 1);
                if ($result) {
                    $processed++;
                } else {
                    $skipped++;
                }

                // Progress indicator
                if ($rowNumber % 10 == 0) {
                    $this->command->info("Processed {$rowNumber}/{$totalRows} rows");
                }
            } catch (\Exception $e) {
                $this->command->error("Error processing row {$rowNumber}: ".$e->getMessage());
                Log::error("Error processing CSV row {$rowNumber}: ".$e->getMessage());
                $skipped++;
            }
        }

        $this->command->info("Processing complete. Processed: {$processed}, Skipped: {$skipped}");
    }

    /**
     * Process a single data row from CSV
     */
    private function processDataRow($rowData, $rowNumber)
    {
        // Extract data from CSV columns (semicolon separated)
        $hariTanggal = trim($rowData[0] ?? '');
        $waktu = trim($rowData[1] ?? '');
        $nik = trim($rowData[2] ?? '');
        $namaKaryawan = trim($rowData[3] ?? '');
        $kodeRm = trim($rowData[4] ?? '');
        $namaPasien = trim($rowData[5] ?? '');

        // Diagnosis 1
        $diagnosa1 = trim($rowData[6] ?? '');
        $keluhan1 = trim($rowData[7] ?? '');
        $obat1_1 = trim($rowData[8] ?? '');
        $qty1_1 = trim($rowData[9] ?? '');
        $obat1_2 = trim($rowData[10] ?? '');
        $qty1_2 = trim($rowData[11] ?? '');
        $obat1_3 = trim($rowData[12] ?? '');
        $qty1_3 = trim($rowData[13] ?? '');

        // Diagnosis 2
        $diagnosa2 = trim($rowData[14] ?? '');
        $keluhan2 = trim($rowData[15] ?? '');
        $obat2_1 = trim($rowData[16] ?? '');
        $qty2_1 = trim($rowData[17] ?? '');
        $obat2_2 = trim($rowData[18] ?? '');
        $qty2_2 = trim($rowData[19] ?? '');
        $obat2_3 = trim($rowData[20] ?? '');
        $qty2_3 = trim($rowData[21] ?? '');

        // Diagnosis 3
        $diagnosa3 = trim($rowData[22] ?? '');
        $keluhan3 = trim($rowData[23] ?? '');
        $obat3_1 = trim($rowData[24] ?? '');
        $qty3_1 = trim($rowData[25] ?? '');
        $obat3_2 = trim($rowData[26] ?? '');
        $qty3_2 = trim($rowData[27] ?? '');
        $obat3_3 = trim($rowData[28] ?? '');
        $qty3_3 = trim($rowData[29] ?? '');

        $petugas = trim($rowData[30] ?? '');
        $status = trim($rowData[31] ?? '');

        // Skip empty rows
        if (empty($hariTanggal) || empty($namaKaryawan)) {
            return false;
        }

        // Parse date and time
        $tanggalPeriksa = $this->parseDate($hariTanggal);
        $waktuPeriksa = $this->parseTime($waktu);

        // Find keluarga (patient)
        $keluarga = $this->findKeluarga($nik, $namaKaryawan, $kodeRm, $namaPasien);

        if (! $keluarga) {
            $this->command->warn("Could not find keluarga for: {$namaPasien} (Kode RM: {$kodeRm}, NIK: {$nik})");

            return false;
        }

        // Find user (petugas)
        $user = $this->findUserByName($petugas);

        if (! $user) {
            $this->command->warn("Could not find user: {$petugas}");

            return false;
        }

        // Count total keluhan (diagnoses with medications)
        $jumlahKeluhan = 0;
        if (! empty($diagnosa1) && (! empty($obat1_1) || ! empty($obat1_2) || ! empty($obat1_3))) {
            $jumlahKeluhan++;
        }
        if (! empty($diagnosa2) && (! empty($obat2_1) || ! empty($obat2_2) || ! empty($obat2_3))) {
            $jumlahKeluhan++;
        }
        if (! empty($diagnosa3) && (! empty($obat3_1) || ! empty($obat3_2) || ! empty($obat3_3))) {
            $jumlahKeluhan++;
        }

        if ($jumlahKeluhan == 0) {
            $this->command->warn("No valid diagnoses with medications found for row {$rowNumber}");

            return false;
        }

        // Create rekam medis
        $rekamMedis = RekamMedis::create([
            'id_keluarga' => $keluarga->id_keluarga,
            'tanggal_periksa' => $tanggalPeriksa,
            'waktu_periksa' => $waktuPeriksa,
            'id_user' => $user->id_user,
            'jumlah_keluhan' => $jumlahKeluhan,
            'status' => $status,
        ]);

        // Create keluhan records for each diagnosis
        $this->createKeluhanForDiagnosis($rekamMedis, $diagnosa1, $keluhan1, [
            [$obat1_1, $qty1_1], [$obat1_2, $qty1_2], [$obat1_3, $qty1_3],
        ]);

        $this->createKeluhanForDiagnosis($rekamMedis, $diagnosa2, $keluhan2, [
            [$obat2_1, $qty2_1], [$obat2_2, $qty2_2], [$obat2_3, $qty2_3],
        ]);

        $this->createKeluhanForDiagnosis($rekamMedis, $diagnosa3, $keluhan3, [
            [$obat3_1, $qty3_1], [$obat3_2, $qty3_2], [$obat3_3, $qty3_3],
        ]);

        $this->command->info("Created regular medical record for {$namaPasien} - {$jumlahKeluhan} diagnoses");

        return true;
    }

    /**
     * Find keluarga (patient) by various criteria
     */
    private function findKeluarga($nik, $namaKaryawan, $kodeRm, $namaPasien)
    {
        // First try to find by kode_rm if provided
        if (! empty($kodeRm)) {
            $keluarga = Keluarga::where('no_rm', $kodeRm)->first();
            if ($keluarga) {
                return $keluarga;
            }
        }

        // Try to find karyawan by NIK
        $karyawan = Karyawan::where('nik_karyawan', $nik)->first();

        if ($karyawan) {
            // Find keluarga by karyawan and nama_pasien
            $keluarga = Keluarga::where('id_karyawan', $karyawan->id_karyawan)
                ->where('nama_keluarga', $namaPasien)
                ->first();

            if ($keluarga) {
                return $keluarga;
            }

            // If not found, try to find any keluarga with matching name
            $keluarga = Keluarga::where('nama_keluarga', $namaPasien)->first();
            if ($keluarga) {
                return $keluarga;
            }
        }

        // Last resort: find by nama_pasien only
        return Keluarga::where('nama_keluarga', $namaPasien)->first();
    }

    /**
     * Find user by name
     */
    private function findUserByName($namaPetugas)
    {
        // Try exact match first
        $user = User::where('nama_lengkap', $namaPetugas)->first();

        if ($user) {
            return $user;
        }

        // Try partial match
        return User::where('nama_lengkap', 'like', "%{$namaPetugas}%")->first();
    }

    /**
     * Create keluhan records for a diagnosis
     */
    private function createKeluhanForDiagnosis($rekamMedis, $diagnosa, $keluhan, $obatData)
    {
        if (empty($diagnosa)) {
            return;
        }

        // Find diagnosa
        $diagnosaModel = Diagnosa::where('nama_diagnosa', $diagnosa)->first();

        if (! $diagnosaModel) {
            // Try partial match
            $diagnosaModel = Diagnosa::where('nama_diagnosa', 'like', "%{$diagnosa}%")->first();
        }

        if (! $diagnosaModel) {
            $this->command->warn("Could not find diagnosis: {$diagnosa}");

            return;
        }

        // Check if there are any medications
        $hasMedications = false;
        foreach ($obatData as $obatInfo) {
            if (! empty($obatInfo[0]) && ! empty($obatInfo[1]) && $obatInfo[1] > 0) {
                $hasMedications = true;
                break;
            }
        }

        if (! $hasMedications) {
            return;
        }

        // Create keluhan for each medication
        foreach ($obatData as $obatInfo) {
            $namaObat = trim($obatInfo[0]);
            $qty = (int) trim($obatInfo[1]);

            if (empty($namaObat) || $qty <= 0) {
                continue;
            }

            // Find obat
            $obat = $this->findObatByName($namaObat);

            if (! $obat) {
                $this->command->warn("Could not find medication: {$namaObat}");

                continue;
            }

            // Create keluhan record
            Keluhan::create([
                'id_rekam' => $rekamMedis->id_rekam,
                'id_diagnosa' => $diagnosaModel->id_diagnosa,
                'terapi' => 'Obat',
                'keterangan' => $keluhan,
                'id_obat' => $obat->id_obat,
                'jumlah_obat' => $qty,
                'aturan_pakai' => null,
                'id_keluarga' => $rekamMedis->id_keluarga,
                'id_emergency' => null,
                'id_diagnosa_emergency' => null,
            ]);

            $this->command->info("Created keluhan for medication: {$namaObat} (Qty: {$qty})");
        }
    }

    /**
     * Find obat by name
     */
    private function findObatByName($namaObat)
    {
        // Try exact match first
        $obat = Obat::where('nama_obat', $namaObat)->first();

        if ($obat) {
            return $obat;
        }

        // Try partial match
        return Obat::where('nama_obat', 'like', "%{$namaObat}%")->first();
    }

    /**
     * Parse date from CSV (format: DD/MM/YYYY)
     */
    private function parseDate($dateString)
    {
        try {
            // Handle different date formats
            if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $dateString, $matches)) {
                return Carbon::createFromDate($matches[3], $matches[2], $matches[1]);
            }

            // Try other formats if needed
            return Carbon::parse($dateString);
        } catch (\Exception $e) {
            $this->command->warn("Invalid date format: {$dateString}");

            return Carbon::now();
        }
    }

    /**
     * Parse time from CSV (format: HH:MM:SS)
     */
    private function parseTime($timeString)
    {
        try {
            if (empty($timeString)) {
                return Carbon::now()->format('H:i:s');
            }

            return Carbon::parse($timeString)->format('H:i:s');
        } catch (\Exception $e) {
            $this->command->warn("Invalid time format: {$timeString}");

            return Carbon::now()->format('H:i:s');
        }
    }

    /**
     * Read CSV file
     */
    private function readCsv($filePath)
    {
        $csvData = [];

        if (($handle = fopen($filePath, 'r')) !== false) {
            while (($data = fgetcsv($handle, 1000, ';')) !== false) {
                $csvData[] = $data;
            }
            fclose($handle);
        }

        return $csvData;
    }
}
