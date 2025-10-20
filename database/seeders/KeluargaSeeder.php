<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Keluarga;
use App\Models\Karyawan;
use App\Models\Hubungan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KeluargaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Path ke file CSV
        $csvPath = database_path('seeders/seeders_master_data/data_keluarga.csv');

        // Hapus data lama jika ada menggunakan Eloquent
        Keluarga::query()->delete();

        // Get reference data
        $karyawans = Karyawan::pluck('id_karyawan', 'nik_karyawan')->toArray();
        $hubungans = Hubungan::pluck('kode_hubungan', 'hubungan')->toArray();

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

                        // Get data dari kolom (struktur yang benar)
                        $nikKaryawan = trim($rowData[0] ?? '');
                        $namaKeluarga = trim($rowData[1] ?? '');
                        $tanggalLahir = trim($rowData[2] ?? '');
                        $kodeHubunganCSV = trim($rowData[3] ?? '');
                        $namaHubungan = trim($rowData[4] ?? '');
                        $alamat = trim($rowData[5] ?? '');
                        $jenisKelamin = trim($rowData[6] ?? '');
                        $bpjsId = trim($rowData[7] ?? '');

                        if (empty($namaKeluarga)) {
                            continue; // Skip empty rows
                        }

                        // Find karyawan ID
                        $idKaryawan = null;
                        if (!empty($nikKaryawan)) {
                            if (isset($karyawans[$nikKaryawan])) {
                                $idKaryawan = $karyawans[$nikKaryawan];
                            } else {
                                $errors[] = "Baris " . ($rowNumber + 1) . ": Karyawan dengan NIK '$nikKaryawan' tidak ditemukan";
                                $errorCount++;
                                continue;
                            }
                        }

                        // Extract the last character from kode hubungan CSV (e.g., "200032-A" -> "A")
                        $kodeHubungan = null;
                        if (!empty($kodeHubunganCSV)) {
                            $lastChar = substr($kodeHubunganCSV, -1);
                            if (isset($hubungans[$lastChar])) {
                                $kodeHubungan = $lastChar;
                            } else {
                                // Try to find by nama hubungan
                                if (!empty($namaHubungan) && isset($hubungans[$namaHubungan])) {
                                    $kodeHubungan = $hubungans[$namaHubungan];
                                } else {
                                    $errors[] = "Baris " . ($rowNumber + 1) . ": Kode hubungan '$kodeHubunganCSV' tidak ditemukan";
                                    $errorCount++;
                                    continue;
                                }
                            }
                        }

                        // Parse tanggal lahir
                        $tanggalLahirParsed = null;
                        if (!empty($tanggalLahir)) {
                            try {
                                $tanggalLahirParsed = \Carbon\Carbon::createFromFormat('Y-m-d', $tanggalLahir);
                            } catch (\Exception $e) {
                                try {
                                    // Try other formats
                                    $tanggalLahirParsed = \Carbon\Carbon::parse($tanggalLahir);
                                } catch (\Exception $e2) {
                                    // Skip if invalid date
                                    $tanggalLahirParsed = null;
                                }
                            }
                        }

                        // Generate no_rm (nomor rekam medis) if needed
                        $noRm = 'RM-' . date('Ym') . '-' . str_pad($successCount + 1, 4, '0', STR_PAD_LEFT);

                        // Create keluarga record using Eloquent
                        try {
                            Keluarga::create([
                                'id_karyawan' => $idKaryawan,
                                'nama_keluarga' => $namaKeluarga,
                                'tanggal_lahir' => $tanggalLahirParsed,
                                'jenis_kelamin' => $jenisKelamin,
                                'alamat' => $alamat,
                                'tanggal_daftar' => now(),
                                'no_rm' => $noRm,
                                'kode_hubungan' => $kodeHubungan,
                                'bpjs_id' => $bpjsId,
                            ]);
                            $successCount++;
                        } catch (\Exception $e) {
                            $errors[] = "Baris " . ($rowNumber + 1) . ": Error creating record - " . $e->getMessage();
                            $errorCount++;
                        }
                    }

                    $this->command->info("Import data keluarga selesai: $successCount data berhasil diproses");
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
            $this->command->error('Error importing keluarga: ' . $e->getMessage());
            Log::error('Error importing keluarga: ' . $e->getMessage());
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
