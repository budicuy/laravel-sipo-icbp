<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\RekamMedisEmergency;
use App\Models\ExternalEmployee;
use App\Models\User;
use App\Models\DiagnosaEmergency;
use App\Models\Obat;
use App\Models\Keluhan;
use App\Models\StokObat;
use App\Models\HargaObatPerBulan;
use Carbon\Carbon;

class RekamMedisEmergencyFromCSVSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // List of CSV files to process
        $csvFiles = [
            database_path('seeders/seeders_rekam_medis/AGT-RM Tunggal Emergency.csv'),
            database_path('seeders/seeders_rekam_medis/SEPT-RM Tunggal Emergency.csv'),
        ];

        foreach ($csvFiles as $csvFile) {
            if (file_exists($csvFile)) {
                $this->processCsvFile($csvFile);
            } else {
                $this->command->warn("CSV file not found: {$csvFile}");
            }
        }
    }

    /**
     * Process a single CSV file
     */
    private function processCsvFile($csvPath)
    {
        $this->command->info("Processing CSV file: " . basename($csvPath));
        
        // Read CSV file
        $csvData = $this->readCsv($csvPath);
        
        if (empty($csvData)) {
            $this->command->warn("No data found in CSV file: " . basename($csvPath));
            return;
        }

        // Skip header row and process data rows
        for ($rowNumber = 1; $rowNumber < count($csvData); $rowNumber++) {
            $rowData = $csvData[$rowNumber];
            
            try {
                $this->processDataRow($rowData, $rowNumber + 1);
            } catch (\Exception $e) {
                $this->command->error("Error processing row {$rowNumber}: " . $e->getMessage());
                Log::error("Error processing CSV row {$rowNumber}: " . $e->getMessage());
            }
        }
    }

    /**
     * Process a single data row from CSV
     */
    private function processDataRow($rowData, $rowNumber)
    {
        // Extract data from CSV columns
        $hariTanggal = trim($rowData[0] ?? '');
        $waktu = trim($rowData[1] ?? '');
        $nik = trim($rowData[2] ?? '');
        $namaKaryawan = trim($rowData[3] ?? '');
        $kodeRm = trim($rowData[4] ?? '');
        $namaPasien = trim($rowData[5] ?? '');
        $keluhan = trim($rowData[6] ?? '');
        $diagnosa = trim($rowData[7] ?? '');
        $obat1 = trim($rowData[8] ?? '');
        $qty1 = trim($rowData[9] ?? '');
        $obat2 = trim($rowData[10] ?? '');
        $qty2 = trim($rowData[11] ?? '');
        $petugas = trim($rowData[12] ?? '');
        $status = trim($rowData[13] ?? '');
        $department = trim($rowData[14] ?? '');
        $kategori = trim($rowData[15] ?? '');

        // Skip empty rows
        if (empty($hariTanggal) || empty($namaKaryawan)) {
            return;
        }

        // Parse date and time
        $tanggalPeriksa = $this->parseDate($hariTanggal);
        $waktuPeriksa = $this->parseTime($waktu);

        // Find or create external employee
        $externalEmployee = $this->findOrCreateExternalEmployee($nik, $namaKaryawan, $kodeRm);
        
        if (!$externalEmployee) {
            $this->command->warn("Could not find or create external employee: {$namaKaryawan} (NIK: {$nik})");
            return;
        }

        // Find user (petugas)
        $user = $this->findUserByName($petugas);
        
        if (!$user) {
            $this->command->warn("Could not find user: {$petugas}");
            return;
        }

        // Find diagnosis
        $diagnosaEmergency = $this->findDiagnosaEmergency($diagnosa);
        
        if (!$diagnosaEmergency) {
            $this->command->warn("Could not find diagnosis: {$diagnosa}");
            return;
        }

        // Create emergency medical record
        $rekamMedisEmergency = RekamMedisEmergency::create([
            'id_external_employee' => $externalEmployee->id,
            'tanggal_periksa' => $tanggalPeriksa,
            'waktu_periksa' => $waktuPeriksa,
            'status' => $status === 'Emergency' ? 'On Progress' : 'Close',
            'keluhan' => $keluhan,
            'catatan' => "Department: {$department}, Kategori: {$kategori}",
            'id_user' => $user->id_user,
        ]);

        // Create keluhan records for each medication
        $this->createKeluhanWithObat($rekamMedisEmergency, $diagnosaEmergency->id_diagnosa_emergency, $obat1, $qty1, $keluhan);
        $this->createKeluhanWithObat($rekamMedisEmergency, $diagnosaEmergency->id_diagnosa_emergency, $obat2, $qty2, $keluhan);

        $this->command->info("Created emergency record for {$namaKaryawan} - {$diagnosa}");
    }

    /**
     * Find or create external employee
     */
    private function findOrCreateExternalEmployee($nik, $namaKaryawan, $kodeRm)
    {
        // Try to find by NIK first
        $externalEmployee = ExternalEmployee::where('nik_employee', $nik)->first();
        
        if (!$externalEmployee) {
            // Try to find by name
            $externalEmployee = ExternalEmployee::where('nama_employee', $namaKaryawan)->first();
        }
        
        if (!$externalEmployee) {
            // Try to find by kode RM
            $externalEmployee = ExternalEmployee::where('kode_rm', $kodeRm)->first();
        }
        
        return $externalEmployee;
    }

    /**
     * Find user by name
     */
    private function findUserByName($namaLengkap)
    {
        return User::where('nama_lengkap', $namaLengkap)->first();
    }

    /**
     * Find diagnosis emergency by name
     */
    private function findDiagnosaEmergency($namaDiagnosa)
    {
        return DiagnosaEmergency::where('nama_diagnosa_emergency', $namaDiagnosa)->first();
    }

    /**
     * Create keluhan record with medication and reduce stock
     */
    private function createKeluhanWithObat($rekamMedisEmergency, $idDiagnosaEmergency, $namaObat, $qty, $keluhanText)
    {
        // Skip if no medication or quantity
        if (empty($namaObat) || empty($qty) || $qty === '-' || $qty === '0') {
            return;
        }

        // Clean medication name
        $namaObat = trim($namaObat);
        $qty = (int) trim($qty);

        // Find medication
        $obat = Obat::where('nama_obat', $namaObat)->first();
        
        if (!$obat) {
            $this->command->warn("Medication not found: {$namaObat}");
            return;
        }

        // Get current period for stock management
        $currentPeriode = Carbon::now()->format('m-y');

        // Create harga obat record if not exists for this period
        $this->createHargaObatIfNotExists($obat->id_obat, $currentPeriode);

        // Create keluhan record
        $keluhan = Keluhan::create([
            'id_emergency' => $rekamMedisEmergency->id_emergency,
            'id_rekam' => null,
            'id_diagnosa' => null,
            'id_diagnosa_emergency' => $idDiagnosaEmergency,
            'id_keluarga' => null,
            'id_obat' => $obat->id_obat,
            'jumlah_obat' => $qty,
            'terapi' => 'Obat',
            'keterangan' => $keluhanText,
            'aturan_pakai' => null,
        ]);

        // Reduce stock
        $this->reduceStock($obat->id_obat, $qty, $currentPeriode, $rekamMedisEmergency->id_emergency);

        $this->command->info("Created keluhan for medication: {$namaObat} (Qty: {$qty})");
    }

    /**
     * Create harga obat record if not exists for this period
     */
    private function createHargaObatIfNotExists($idObat, $periode)
    {
        // Check if harga obat already exists for this period
        $existingHarga = HargaObatPerBulan::where('id_obat', $idObat)
                                          ->where('periode', $periode)
                                          ->first();

        if (!$existingHarga) {
            // Get the latest harga for this obat to use as reference
            $latestHarga = HargaObatPerBulan::where('id_obat', $idObat)
                                                ->orderByRaw("SUBSTRING(periode, 4, 2) DESC, SUBSTRING(periode, 1, 2) DESC")
                                                ->first();

            if ($latestHarga) {
                // Create new harga record for current period with same price
                HargaObatPerBulan::create([
                    'id_obat' => $idObat,
                    'periode' => $periode,
                    'jumlah_per_kemasan' => $latestHarga->jumlah_per_kemasan,
                    'harga_per_satuan' => $latestHarga->harga_per_satuan,
                    'harga_per_kemasan' => $latestHarga->harga_per_kemasan,
                ]);
            }
        }
    }

    /**
     * Reduce medication stock
     */
    private function reduceStock($idObat, $jumlah, $periode, $idEmergency)
    {
        try {
            // Find or create stock record for current period
            $stokObat = StokObat::firstOrCreate(
                [
                    'id_obat' => $idObat,
                    'periode' => $periode,
                ],
                [
                    'stok_awal' => StokObat::getStokAkhirBulanSebelumnya($idObat, $periode),
                    'stok_masuk' => 0,
                    'stok_pakai' => 0,
                    'stok_akhir' => 0,
                    'keterangan' => "Emergency record #{$idEmergency}",
                ]
            );

            // Update stock usage
            $stokObat->stok_pakai += $jumlah;
            
            // Recalculate ending stock
            $stokObat->stok_akhir = StokObat::hitungStokAkhir(
                $stokObat->stok_awal,
                $stokObat->stok_pakai,
                $stokObat->stok_masuk
            );
            
            $stokObat->save();

            $this->command->info("Reduced stock for medication ID {$idObat} by {$jumlah} units");
        } catch (\Exception $e) {
            $this->command->error("Error reducing stock: " . $e->getMessage());
            Log::error("Error reducing stock for medication {$idObat}: " . $e->getMessage());
        }
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

        if (($handle = fopen($filePath, 'r')) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ';')) !== FALSE) {
                $csvData[] = $data;
            }
            fclose($handle);
        }

        return $csvData;
    }
}