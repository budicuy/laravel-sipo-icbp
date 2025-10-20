<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Karyawan;
use App\Models\Departemen;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KaryawanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Path ke file CSV
        $csvPath = database_path('seeders/seeders_master_data/data_karyawan.csv');

        // Hapus data lama jika ada menggunakan Eloquent
        Karyawan::query()->delete();

        // Get reference data for departemen
        $departemens = Departemen::pluck('id_departemen', 'nama_departemen')->toArray();

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
                        $nik = trim($rowData[0] ?? '');
                        $nama = trim($rowData[1] ?? '');
                        $tanggalLahir = trim($rowData[2] ?? '');
                        $jenisKelamin = trim($rowData[3] ?? '');
                        $alamat = trim($rowData[4] ?? '');
                        $noHp = trim($rowData[5] ?? '');
                        $namaDepartemen = trim($rowData[6] ?? '');
                        $email = trim($rowData[7] ?? '');
                        $bpjsId = trim($rowData[8] ?? '');

                        if (empty($nama)) {
                            continue; // Skip empty rows
                        }

                        // Find departemen ID
                        $idDepartemen = null;
                        if (!empty($namaDepartemen)) {
                            // Try exact match first
                            if (isset($departemens[$namaDepartemen])) {
                                $idDepartemen = $departemens[$namaDepartemen];
                            } else {
                                // Try partial match
                                foreach ($departemens as $deptName => $deptId) {
                                    if (stripos($deptName, $namaDepartemen) !== false ||
                                        stripos($namaDepartemen, $deptName) !== false) {
                                        $idDepartemen = $deptId;
                                        break;
                                    }
                                }

                                if ($idDepartemen === null) {
                                    $errors[] = "Baris " . ($rowNumber + 1) . ": Departemen '$namaDepartemen' tidak ditemukan";
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
                                    $errors[] = "Baris " . ($rowNumber + 1) . ": Format tanggal lahir tidak valid: $tanggalLahir";
                                    $errorCount++;
                                    continue;
                                }
                            }
                        }

                        // Create karyawan record
                        Karyawan::create([
                            'nik_karyawan' => $nik,
                            'nama_karyawan' => $nama,
                            'tanggal_lahir' => $tanggalLahirParsed,
                            'jenis_kelamin' => $jenisKelamin,
                            'alamat' => $alamat,
                            'no_hp' => $noHp,
                            'id_departemen' => $idDepartemen,
                            'email' => $email,
                            'bpjs_id' => $bpjsId,
                        ]);

                        $successCount++;
                    }

                    $this->command->info("Import data karyawan selesai: $successCount data berhasil diproses");
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
            $this->command->error('Error importing karyawan: ' . $e->getMessage());
            Log::error('Error importing karyawan: ' . $e->getMessage());
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
