<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

echo "=== Testing Harga Obat Calculations ===\n\n";

// Step 1: Run the migration to update the harga_obat format
echo "Step 1: Running migration to update harga_obat format to decimal(20,3)...\n";
try {
    \Illuminate\Support\Facades\Artisan::call('migrate', ['--path' => 'database/migrations/2025_10_23_070000_update_harga_obat_columns_format.php']);
    echo "Migration completed successfully.\n\n";
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n\n";
}

// Step 2: Test the calculations
echo "Step 2: Testing harga obat calculations...\n";

use App\Models\HargaObatPerBulan;
use App\Models\Keluhan;
use App\Models\RekamMedis;
use App\Models\RekamMedisEmergency;
use Carbon\Carbon;

// Check harga_obat_per_bulan table structure
echo "Checking harga_obat_per_bulan table structure:\n";
$hargaObat = HargaObatPerBulan::first();
if ($hargaObat) {
    echo "Sample harga_obat record:\n";
    echo "ID: {$hargaObat->id_harga_obat}\n";
    echo "ID Obat: {$hargaObat->id_obat}\n";
    echo "Periode: {$hargaObat->periode}\n";
    echo "Harga per Satuan: {$hargaObat->harga_per_satuan}\n";
    echo "Harga per Kemasan: {$hargaObat->harga_per_kemasan}\n";
    echo "Type of harga_per_satuan: " . gettype($hargaObat->harga_per_satuan) . "\n\n";
} else {
    echo "No harga_obat records found.\n\n";
}

// Calculate total biaya for a specific month (August 2025)
echo "Calculating total biaya for August 2025:\n";
$bulan = 8;
$tahun = 2025;

// Get all keluhan with rekamMedis for the specified month and year
$keluhanDataReguler = Keluhan::with(['rekamMedis:id_rekam,tanggal_periksa'])
    ->whereHas('rekamMedis', function($query) use ($bulan, $tahun) {
        $query->whereMonth('tanggal_periksa', $bulan)
              ->whereYear('tanggal_periksa', $tahun);
    })
    ->whereNotNull('id_obat')
    ->get();

echo "Found " . $keluhanDataReguler->count() . " regular keluhan records\n";

// Get all keluhan with rekamMedisEmergency for the specified month and year
$keluhanDataEmergency = Keluhan::with(['rekamMedisEmergency:id_emergency,tanggal_periksa'])
    ->whereHas('rekamMedisEmergency', function($query) use ($bulan, $tahun) {
        $query->whereMonth('tanggal_periksa', $bulan)
              ->whereYear('tanggal_periksa', $tahun);
    })
    ->whereNotNull('id_obat')
    ->get();

echo "Found " . $keluhanDataEmergency->count() . " emergency keluhan records\n";

// Collect all unique obat IDs and periods
$obatPeriods = [];
foreach ($keluhanDataReguler as $keluhan) {
    $periode = $keluhan->rekamMedis->tanggal_periksa->format('m-y');
    $obatPeriods[] = [
        'id_obat' => $keluhan->id_obat,
        'periode' => $periode
    ];
}

foreach ($keluhanDataEmergency as $keluhan) {
    $periode = $keluhan->rekamMedisEmergency->tanggal_periksa->format('m-y');
    $obatPeriods[] = [
        'id_obat' => $keluhan->id_obat,
        'periode' => $periode
    ];
}

// Get unique combinations
$uniqueObatPeriods = collect($obatPeriods)->unique(function ($item) {
    return $item['id_obat'] . '_' . $item['periode'];
})->values()->toArray();

echo "Found " . count($uniqueObatPeriods) . " unique obat-period combinations\n";

// Get harga obat data
$hargaObatMap = [];
if (!empty($uniqueObatPeriods)) {
    $hargaObatResults = HargaObatPerBulan::getBulkHargaObatWithFallback($uniqueObatPeriods);
    
    foreach ($hargaObatResults as $key => $result) {
        if ($result && $result['harga']) {
            $hargaObatMap[$key] = $result['harga'];
        }
    }
}

echo "Found " . count($hargaObatMap) . " harga obat records\n";

// Calculate total biaya
$totalBiaya = 0;
$detailBiaya = [];

foreach ($keluhanDataReguler as $keluhan) {
    $periode = $keluhan->rekamMedis->tanggal_periksa->format('m-y');
    $key = $keluhan->id_obat . '_' . $periode;
    $hargaObat = $hargaObatMap[$key] ?? null;
    
    if ($hargaObat) {
        $subtotal = $keluhan->jumlah_obat * $hargaObat->harga_per_satuan;
        $totalBiaya += $subtotal;
        
        $detailBiaya[] = [
            'id_keluhan' => $keluhan->id_keluhan,
            'id_obat' => $keluhan->id_obat,
            'jumlah_obat' => $keluhan->jumlah_obat,
            'harga_per_satuan' => $hargaObat->harga_per_satuan,
            'subtotal' => $subtotal,
            'tipe' => 'Reguler'
        ];
    }
}

foreach ($keluhanDataEmergency as $keluhan) {
    $periode = $keluhan->rekamMedisEmergency->tanggal_periksa->format('m-y');
    $key = $keluhan->id_obat . '_' . $periode;
    $hargaObat = $hargaObatMap[$key] ?? null;
    
    if ($hargaObat) {
        $subtotal = $keluhan->jumlah_obat * $hargaObat->harga_per_satuan;
        $totalBiaya += $subtotal;
        
        $detailBiaya[] = [
            'id_keluhan' => $keluhan->id_keluhan,
            'id_obat' => $keluhan->id_obat,
            'jumlah_obat' => $keluhan->jumlah_obat,
            'harga_per_satuan' => $hargaObat->harga_per_satuan,
            'subtotal' => $subtotal,
            'tipe' => 'Emergency'
        ];
    }
}

echo "Total biaya calculated: Rp" . number_format($totalBiaya, 0, ',', '.') . "\n\n";

// Show first 10 details
echo "First 10 calculation details:\n";
for ($i = 0; $i < min(10, count($detailBiaya)); $i++) {
    $detail = $detailBiaya[$i];
    echo "{$detail['tipe']} - ID Keluhan: {$detail['id_keluhan']}, ID Obat: {$detail['id_obat']}, ";
    echo "Jumlah: {$detail['jumlah_obat']}, Harga: {$detail['harga_per_satuan']}, ";
    echo "Subtotal: {$detail['subtotal']}\n";
}

// Manual calculation verification
echo "\nManual calculation verification:\n";
$manualTotal = 0;
foreach ($detailBiaya as $detail) {
    $manualTotal += $detail['subtotal'];
}
echo "Manual total: Rp" . number_format($manualTotal, 0, ',', '.') . "\n";
echo "Code total:   Rp" . number_format($totalBiaya, 0, ',', '.') . "\n";
echo "Match: " . ($manualTotal == $totalBiaya ? "YES" : "NO") . "\n\n";

echo "=== Test Complete ===\n";