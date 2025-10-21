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
        // Truncate table first for clean seeding
        DB::table('external_employees')->truncate();

        // Get the CSV file path
        $csvFile = database_path('seeders/employee/Data_Master_Employee.csv');
        
        if (!file_exists($csvFile)) {
            $this->command->error("CSV file not found: {$csvFile}");
            return;
        }

        // Open the CSV file
        $file = fopen($csvFile, 'r');
        
        // Skip header
        fgetcsv($file, 1000, ';');
        
        // Process each row
        while (($data = fgetcsv($file, 1000, ';')) !== FALSE) {
            // Skip empty rows
            if (empty(trim($data[0]))) {
                continue;
            }

            // Extract data from CSV
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
            
            // Skip if required fields are empty
            if (empty($nikEmployee) || empty($namaEmployee) || empty($kodeRm)) {
                continue;
            }
            
            // Find or create vendor
            $vendor = Vendor::where('nama_vendor', $namaVendor)->first();
            if (!$vendor && !empty($namaVendor)) {
                $vendor = Vendor::create(['nama_vendor' => $namaVendor]);
            }
            
            // Process kategori
            $kategoriModel = null;
            if (!empty($kategori)) {
                // Extract kode kategori from format like "Y - Outsourcing"
                if (preg_match('/^([xyz])\s*-\s*(.+)$/i', $kategori, $matches)) {
                    $kodeKategori = strtolower($matches[1]);
                    $namaKategori = $matches[2];
                    
                    $kategoriModel = Kategori::where('kode_kategori', $kodeKategori)->first();
                    if (!$kategoriModel) {
                        $kategoriModel = Kategori::create([
                            'kode_kategori' => $kodeKategori,
                            'nama_kategori' => $namaKategori
                        ]);
                    }
                } elseif (preg_match('/^([xyz])\s*-\s*F/i', $kategori, $matches)) {
                    $kodeKategori = strtolower($matches[1]);
                    $namaKategori = '';
                    
                    $kategoriModel = Kategori::where('kode_kategori', $kodeKategori)->first();
                    if (!$kategoriModel) {
                        $kategoriModel = Kategori::create([
                            'kode_kategori' => $kodeKategori,
                            'nama_kategori' => $namaKategori
                        ]);
                    }
                }
            }
            
            // Prepare data for insertion
            $insertData = [
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
            ];
            
            // Create external employee
            ExternalEmployee::create($insertData);
        }
        
        fclose($file);
        
        $this->command->info('External Employee data seeded successfully!');
    }
}