<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ExternalEmployee;
use App\Models\Vendor;
use App\Models\Kategori;
use Illuminate\Support\Facades\DB;

class ExternalEmployeeSeeder extends Seeder
{
    public function run()
    {
        // ✅ Nonaktifkan foreign key check sementara (agar bisa truncate)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('external_employees')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Path ke file CSV
        $csvFile = database_path('seeders/employee/Data_Master_Employee.csv');
        
        if (!file_exists($csvFile)) {
            $this->command->error("CSV file not found: {$csvFile}");
            return;
        }

        $file = fopen($csvFile, 'r');
        fgetcsv($file, 1000, ';'); // lewati header CSV

        while (($data = fgetcsv($file, 1000, ';')) !== FALSE) {
            // Lewati baris kosong
            if (empty(trim($data[0]))) continue;

            // Ambil data dari CSV
            $nikEmployee = trim($data[0]);
            $namaEmployee = trim($data[1]);
            $kodeRm = trim($data[2]);
            $tanggalLahir = trim($data[3]);
            $jenisKelamin = trim($data[4]);
            $alamat = trim($data[5]);
            $noHp = trim($data[6]);
            $namaVendor = trim($data[7]);
            $noKtp = trim($data[8]);
            $bpjsId = trim($data[9]);
            $kategori = trim($data[10]);

            if (empty($nikEmployee) || empty($namaEmployee) || empty($kodeRm)) continue;

            // 🔹 Cari atau buat vendor
            $vendor = null;
            if (!empty($namaVendor)) {
                $vendor = Vendor::firstOrCreate(['nama_vendor' => $namaVendor]);
            }

            // 🔹 Proses kategori
            $kategoriModel = null;
            if (!empty($kategori)) {
                if (preg_match('/^([xyz])\s*-\s*(.+)$/i', $kategori, $matches)) {
                    $kodeKategori = strtolower($matches[1]);
                    $namaKategori = $matches[2];
                    $kategoriModel = Kategori::firstOrCreate([
                        'kode_kategori' => $kodeKategori
                    ], [
                        'nama_kategori' => $namaKategori
                    ]);
                } elseif (preg_match('/^([xyz])\s*-\s*F/i', $kategori, $matches)) {
                    $kodeKategori = strtolower($matches[1]);
                    $kategoriModel = Kategori::firstOrCreate([
                        'kode_kategori' => $kodeKategori
                    ], [
                        'nama_kategori' => ''
                    ]);
                }
            }

            // 🔹 Buat data External Employee
            ExternalEmployee::create([
                'nik_employee' => $nikEmployee,
                'nama_employee' => $namaEmployee,
                'kode_rm' => $kodeRm,
                'tanggal_lahir' => !empty($tanggalLahir) ? date('Y-m-d', strtotime($tanggalLahir)) : null,
                'jenis_kelamin' => $jenisKelamin,
                'alamat' => $alamat,
                'no_hp' => $noHp,
                'id_vendor' => $vendor ? $vendor->id_vendor : null,
                'no_ktp' => !empty($noKtp) ? $noKtp : null,
                'bpjs_id' => !empty($bpjsId) ? $bpjsId : null,
                'id_kategori' => $kategoriModel ? $kategoriModel->id_kategori : null,
                'status' => 'aktif'
            ]);
        }

        fclose($file);

        $this->command->info('✅ External Employee data seeded successfully!');
    }
}
