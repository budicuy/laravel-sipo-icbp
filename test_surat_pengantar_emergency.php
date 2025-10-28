<?php

require_once __DIR__.'/vendor/autoload.php';

use App\Models\ExternalEmployee;
use App\Models\RekamMedisEmergency;
use App\Models\SuratPengantarIstirahat;

// Bootstrap Laravel
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Testing Surat Pengantar Istirahat dengan Emergency ===\n\n";

// Test 1: Cek apakah model SuratPengantarIstirahat memiliki relasi emergency
echo "Test 1: Memeriksa relasi emergency pada model SuratPengantarIstirahat\n";
$surat = new SuratPengantarIstirahat;
if (method_exists($surat, 'rekamMedisEmergency')) {
    echo "✓ Relasi rekamMedisEmergency ada\n";
} else {
    echo "✗ Relasi rekamMedisEmergency tidak ada\n";
}

// Test 2: Cek apakah ada data emergency yang bisa digunakan
echo "\nTest 2: Memeriksa data RekamMedisEmergency\n";
$emergencyData = RekamMedisEmergency::with('externalEmployee')->limit(3)->get();
if ($emergencyData->count() > 0) {
    echo "✓ Ditemukan {$emergencyData->count()} data RekamMedisEmergency\n";
    foreach ($emergencyData as $data) {
        echo "  - ID: {$data->id_emergency}, Nama: ".($data->externalEmployee->nama ?? 'N/A').', Perusahaan: '.($data->externalEmployee->perusahaan ?? 'N/A')."\n";
    }
} else {
    echo "✗ Tidak ada data RekamMedisEmergency\n";
}

// Test 3: Cek apakah ada data external employee
echo "\nTest 3: Memeriksa data ExternalEmployee\n";
$externalEmployees = ExternalEmployee::limit(3)->get();
if ($externalEmployees->count() > 0) {
    echo "✓ Ditemukan {$externalEmployees->count()} data ExternalEmployee\n";
    foreach ($externalEmployees as $employee) {
        echo "  - ID: {$employee->id}, Nama: {$employee->nama_employee}, NIK: {$employee->nik_employee}\n";
    }
} else {
    echo "✗ Tidak ada data ExternalEmployee\n";
}

// Test 4: Cek apakah controller memiliki method yang diperlukan
echo "\nTest 4: Memeriksa method pada SuratPengantarIstirahatController\n";
$controller = new App\Http\Controllers\SuratPengantarIstirahatController;
$methods = ['searchRekamMedisEmergency', 'getRekamMedisEmergencyDetail'];
foreach ($methods as $method) {
    if (method_exists($controller, $method)) {
        echo "✓ Method {$method} ada\n";
    } else {
        echo "✗ Method {$method} tidak ada\n";
    }
}

// Test 5: Cek apakah ada route untuk emergency
echo "\nTest 5: Memeriksa route untuk emergency\n";
$routeCollection = app('router')->getRoutes();
$hasEmergencyRoute = false;
foreach ($routeCollection as $route) {
    if (strpos($route->uri(), 'surat-pengantar-istirahat/get-rekam-medis-emergency-detail') !== false) {
        $hasEmergencyRoute = true;
        break;
    }
}
if ($hasEmergencyRoute) {
    echo "✓ Route untuk emergency ditemukan\n";
} else {
    echo "✗ Route untuk emergency tidak ditemukan\n";
}

// Test 6: Cek apakah view create.blade.php ada
echo "\nTest 6: Memeriksa view create.blade.php\n";
$viewPath = __DIR__.'/resources/views/surat-pengantar-istirahat/create.blade.php';
if (file_exists($viewPath)) {
    echo "✓ View create.blade.php ada\n";
    $content = file_get_contents($viewPath);
    if (strpos($content, 'tipe_pasien') !== false) {
        echo "✓ View create.blade.php memiliki opsi tipe_pasien\n";
    } else {
        echo "✗ View create.blade.php tidak memiliki opsi tipe_pasien\n";
    }
} else {
    echo "✗ View create.blade.php tidak ada\n";
}

// Test 7: Cek apakah view edit.blade.php ada
echo "\nTest 7: Memeriksa view edit.blade.php\n";
$viewPath = __DIR__.'/resources/views/surat-pengantar-istirahat/edit.blade.php';
if (file_exists($viewPath)) {
    echo "✓ View edit.blade.php ada\n";
    $content = file_get_contents($viewPath);
    if (strpos($content, 'tipe_pasien') !== false) {
        echo "✓ View edit.blade.php memiliki pengecekan tipe_pasien\n";
    } else {
        echo "✗ View edit.blade.php tidak memiliki pengecekan tipe_pasien\n";
    }
} else {
    echo "✗ View edit.blade.php tidak ada\n";
}

// Test 8: Cek apakah view cetak.blade.php ada
echo "\nTest 8: Memeriksa view cetak.blade.php\n";
$viewPath = __DIR__.'/resources/views/surat-pengantar-istirahat/cetak.blade.php';
if (file_exists($viewPath)) {
    echo "✓ View cetak.blade.php ada\n";
    $content = file_get_contents($viewPath);
    if (strpos($content, 'tipe_pasien') !== false) {
        echo "✓ View cetak.blade.php memiliki pengecekan tipe_pasien\n";
    } else {
        echo "✗ View cetak.blade.php tidak memiliki pengecekan tipe_pasien\n";
    }
} else {
    echo "✗ View cetak.blade.php tidak ada\n";
}

// Test 9: Cek apakah view index.blade.php ada
echo "\nTest 9: Memeriksa view index.blade.php\n";
$viewPath = __DIR__.'/resources/views/surat-pengantar-istirahat/index.blade.php';
if (file_exists($viewPath)) {
    echo "✓ View index.blade.php ada\n";
    $content = file_get_contents($viewPath);
    if (strpos($content, 'tipe_pasien') !== false) {
        echo "✓ View index.blade.php memiliki pengecekan tipe_pasien\n";
    } else {
        echo "✗ View index.blade.php tidak memiliki pengecekan tipe_pasien\n";
    }
} else {
    echo "✗ View index.blade.php tidak ada\n";
}

// Test 10: Cek struktur tabel surat_pengantar_istirahat
echo "\nTest 10: Memeriksa struktur tabel surat_pengantar_istirahat\n";
$columns = DB::select('SHOW COLUMNS FROM surat_pengantar_istirahat');
$requiredColumns = ['id_emergency', 'tipe_rekam_medis'];
foreach ($requiredColumns as $column) {
    $found = false;
    foreach ($columns as $col) {
        if ($col->Field == $column) {
            $found = true;
            break;
        }
    }
    if ($found) {
        echo "✓ Kolom {$column} ada\n";
    } else {
        echo "✗ Kolom {$column} tidak ada\n";
    }
}

echo "\n=== Testing Selesai ===\n";
