<?php

require_once __DIR__.'/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\RekamMedis;
use App\Models\SuratPengantarIstirahat;

echo '=== Testing Surat Pengantar Istirahat ==='.PHP_EOL.PHP_EOL;

// Test 1: Generate nomor surat
echo '1. Testing generate nomor surat:'.PHP_EOL;
try {
    $nomorSurat = SuratPengantarIstirahat::generateNomorSurat();
    echo '   ✓ Nomor Surat: '.$nomorSurat.PHP_EOL;
} catch (Exception $e) {
    echo '   ✗ Error: '.$e->getMessage().PHP_EOL;
}
echo PHP_EOL;

// Test 2: Cek relasi model
echo '2. Testing relasi model:'.PHP_EOL;
try {
    $rekamMedis = RekamMedis::with(['keluarga.karyawan.departemen'])
        ->where('status', 'On Progress')
        ->first();

    if ($rekamMedis) {
        echo '   ✓ ID Rekam: '.$rekamMedis->id_rekam.PHP_EOL;
        echo '   ✓ Nama Pasien: '.$rekamMedis->keluarga->nama_keluarga.PHP_EOL;
        echo '   ✓ NIK Karyawan: '.($rekamMedis->keluarga->karyawan->nik_karyawan ?? 'Tidak ada NIK').PHP_EOL;
        echo '   ✓ Nama Karyawan: '.($rekamMedis->keluarga->karyawan->nama_karyawan ?? 'External').PHP_EOL;
        echo '   ✓ Departemen: '.($rekamMedis->keluarga->karyawan->departemen->nama_departemen ?? 'Tidak ada departemen').PHP_EOL;
    } else {
        echo '   ⚠ Tidak ada rekam medis dengan status On Progress'.PHP_EOL;

        // Coba cari rekam medis apa saja
        $anyRekamMedis = RekamMedis::with(['keluarga.karyawan.departemen'])->first();
        if ($anyRekamMedis) {
            echo '   ⚠ Menampilkan rekam medis pertama yang ditemukan:'.PHP_EOL;
            echo '   ✓ ID Rekam: '.$anyRekamMedis->id_rekam.PHP_EOL;
            echo '   ✓ Status: '.$anyRekamMedis->status.PHP_EOL;
            echo '   ✓ Nama Pasien: '.$anyRekamMedis->keluarga->nama_keluarga.PHP_EOL;
            echo '   ✓ NIK Karyawan: '.($anyRekamMedis->keluarga->karyawan->nik_karyawan ?? 'Tidak ada NIK').PHP_EOL;
            echo '   ✓ Nama Karyawan: '.($anyRekamMedis->keluarga->karyawan->nama_karyawan ?? 'External').PHP_EOL;
            echo '   ✓ Departemen: '.($anyRekamMedis->keluarga->karyawan->departemen->nama_departemen ?? 'Tidak ada departemen').PHP_EOL;
        } else {
            echo '   ⚠ Tidak ada rekam medis sama sekali'.PHP_EOL;
        }
    }
} catch (Exception $e) {
    echo '   ✗ Error: '.$e->getMessage().PHP_EOL;
}
echo PHP_EOL;

// Test 3: Test scope pencarian
echo '3. Testing scope pencarian:'.PHP_EOL;
try {
    $searchResults = SuratPengantarIstirahat::searchByNikOrName('test')->get();
    echo '   ✓ Scope searchByNikOrName berfungsi, ditemukan '.$searchResults->count().' hasil'.PHP_EOL;
} catch (Exception $e) {
    echo '   ✗ Error: '.$e->getMessage().PHP_EOL;
}
echo PHP_EOL;

// Test 4: Test scope dengan rekam medis on progress
echo '4. Testing scope WithRekamMedisOnProgress:'.PHP_EOL;
try {
    $onProgressResults = SuratPengantarIstirahat::withRekamMedisOnProgress()->get();
    echo '   ✓ Scope withRekamMedisOnProgress berfungsi, ditemukan '.$onProgressResults->count().' hasil'.PHP_EOL;
} catch (Exception $e) {
    echo '   ✗ Error: '.$e->getMessage().PHP_EOL;
}
echo PHP_EOL;

// Test 5: Test accessor
echo '5. Testing accessor:'.PHP_EOL;
try {
    $anySurat = SuratPengantarIstirahat::first();
    if ($anySurat) {
        echo '   ✓ Accessor nik_karyawan: '.($anySurat->nik_karyawan ?? 'null').PHP_EOL;
        echo '   ✓ Accessor nama_karyawan: '.($anySurat->nama_karyawan ?? 'null').PHP_EOL;
        echo '   ✓ Accessor nama_pasien: '.($anySurat->nama_pasien ?? 'null').PHP_EOL;
        echo '   ✓ Accessor departemen: '.($anySurat->departemen ?? 'null').PHP_EOL;
    } else {
        echo '   ⚠ Tidak ada data surat untuk testing accessor'.PHP_EOL;
    }
} catch (Exception $e) {
    echo '   ✗ Error: '.$e->getMessage().PHP_EOL;
}
echo PHP_EOL;

echo '=== Testing Selesai ==='.PHP_EOL;
