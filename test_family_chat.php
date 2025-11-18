<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\AIChatHistory;
use App\Models\Karyawan;
use App\Models\Keluarga;

echo "=== TESTING AI CHAT HISTORY FOR FAMILY MEMBERS ===\n\n";

// Test 1: Check if karyawan exists
echo "1. Checking karyawan 1200730...\n";
$karyawan = Karyawan::where('nik_karyawan', '1200730')->first();
if ($karyawan) {
    echo "✅ Karyawan found: " . $karyawan->nama_karyawan . "\n";
} else {
    echo "❌ Karyawan not found\n";
    exit(1);
}

// Test 2: Check if keluarga exists
echo "\n2. Checking keluarga dengan kode B...\n";
$keluarga = Keluarga::where('id_karyawan', $karyawan->id_karyawan)
    ->where('kode_hubungan', 'B')
    ->first();

if ($keluarga) {
    echo "✅ Keluarga found: " . $keluarga->nama_keluarga . " (kode: " . $keluarga->kode_hubungan . ")\n";
} else {
    echo "❌ Keluarga not found\n";
    exit(1);
}

// Test 3: Test recordFamilyLogin
echo "\n3. Testing recordFamilyLogin...\n";
$result = AIChatHistory::recordFamilyLogin('1200730', 'B', $keluarga->nama_keluarga, $karyawan->departemen->nama_departemen ?? null);
echo "Result: " . ($result ? "Record updated/created" : "Failed") . "\n";

// Test 4: Test recordFamilyAIChatAccess
echo "\n4. Testing recordFamilyAIChatAccess...\n";
$result = AIChatHistory::recordFamilyAIChatAccess('1200730', 'B');
echo "Result: " . ($result ? "Access recorded" : "Failed") . "\n";

// Test 5: Check database record
echo "\n5. Checking database record...\n";
$record = AIChatHistory::where('nik', '1200730-B')->first();
if ($record) {
    echo "✅ Record found in database:\n";
    echo "   NIK: " . $record->nik . "\n";
    echo "   Nama: " . $record->nama_karyawan . "\n";
    echo "   Departemen: " . $record->departemen . "\n";
    echo "   Kode Hubungan: " . $record->kode_hubungan . "\n";
    echo "   Tipe Pengguna: " . $record->tipe_pengguna . "\n";
    echo "   AI Chat Access Count: " . $record->ai_chat_access_count . "\n";
    echo "   Last AI Chat Access: " . ($record->last_ai_chat_access_at ? $record->last_ai_chat_access_at->format('Y-m-d H:i:s') : 'Never') . "\n";
} else {
    echo "❌ No record found in database\n";
}

// Test 6: Test direct recordAIChatAccess with NIK-KodeHubungan
echo "\n6. Testing direct recordAIChatAccess with NIK-KodeHubungan...\n";
$result = AIChatHistory::recordAIChatAccess('1200730-B');
echo "Result: " . ($result ? "Access recorded" : "Failed") . "\n";

// Test 7: Check accessor methods
echo "\n7. Testing accessor methods...\n";
$record = AIChatHistory::where('nik', '1200730-B')->first();
if ($record) {
    echo "Tipe Pengguna Label: " . $record->tipe_pengguna_label . "\n";
    echo "Hubungan Label: " . $record->hubungan_label . "\n";
    echo "Formatted NIK: " . $record->formatted_nik . "\n";
    echo "Display Name: " . $record->display_name . "\n";
}

echo "\n=== TESTING COMPLETED ===\n";
