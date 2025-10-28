<?php

require_once 'vendor/autoload.php';

use App\Models\Obat;
use App\Models\StokBulanan;
use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TEST FINAL N+1 QUERY STOK OBAT ===\n\n";

// Test 1: Simulasi akses halaman index stok (seperti di StokController@index)
echo "1. Testing simulasi akses halaman index stok:\n";
$startTime = microtime(true);
DB::enableQueryLog();

// Simulasi logic dari StokController@index
$query = Obat::with(['satuanObat:id_satuan,nama_satuan']);

$obats = $query->limit(20)->get(); // 20 obat untuk test

// Hitung sisa stok untuk setiap obat (menggunakan batch approach)
$obatIds = $obats->pluck('id_obat')->toArray();
$sisaStokMap = StokBulanan::getSisaStokSaatIniBatch($obatIds);

$obatsWithStok = $obats->map(function ($obat) use ($sisaStokMap) {
    $obat->sisa_stok = $sisaStokMap->get($obat->id_obat, 0);

    return $obat;
});

// Filter berdasarkan status stok (seperti di view)
$obatsTersedia = $obatsWithStok->filter(function ($obat) {
    return $obat->sisa_stok > 10;
});

$obatsRendah = $obatsWithStok->filter(function ($obat) {
    return $obat->sisa_stok > 0 && $obat->sisa_stok <= 10;
});

$obatsHabis = $obatsWithStok->filter(function ($obat) {
    return $obat->sisa_stok <= 0;
});

$endTime = microtime(true);
$queries = DB::getQueryLog();
$timeElapsed = $endTime - $startTime;

echo '   - Waktu eksekusi: '.number_format($timeElapsed, 4)." detik\n";
echo '   - Jumlah query: '.count($queries)."\n";
echo '   - Jumlah obat: '.$obats->count()."\n";
echo '   - Stok tersedia: '.$obatsTersedia->count()."\n";
echo '   - Stok rendah: '.$obatsRendah->count()."\n";
echo '   - Stok habis: '.$obatsHabis->count()."\n";

// Tampilkan semua query
echo "   - Semua query:\n";
foreach ($queries as $i => $query) {
    echo '     '.($i + 1).'. '.$query['query']."\n";
}

echo "\n";

// Test 2: Test individual obat access (seperti di StokController@show)
echo "2. Testing akses individual obat:\n";
if ($obats->isNotEmpty()) {
    $testObatId = $obats->first()->id_obat;

    DB::flushQueryLog();
    $startTime = microtime(true);

    // Simulasi logic dari StokController@show
    $obat = Obat::with(['satuanObat:id_satuan,nama_satuan'])
        ->findOrFail($testObatId);

    $riwayatStok = StokBulanan::getRiwayatStok($testObatId, 12);

    $sisaStokMap = StokBulanan::getSisaStokSaatIniBatch([$testObatId]);
    $sisaStok = $sisaStokMap->get($testObatId, 0);

    $endTime = microtime(true);
    $queries = DB::getQueryLog();
    $timeElapsed = $endTime - $startTime;

    echo '   - Waktu eksekusi: '.number_format($timeElapsed, 4)." detik\n";
    echo '   - Jumlah query: '.count($queries)."\n";
    echo '   - Jumlah riwayat: '.$riwayatStok->count()."\n";
    echo '   - Sisa stok: '.$sisaStok."\n";

    // Tampilkan query
    echo "   - Semua query:\n";
    foreach ($queries as $i => $query) {
        echo '     '.($i + 1).'. '.$query['query']."\n";
    }
}

echo "\n=== ANALISIS N+1 QUERY ===\n";

// Analisis apakah masih ada N+1 query
$totalQueries = count($queries);
$expectedQueries = 5; // Expected: 1 (obat) + 1 (satuan) + 1 (stok awal batch) + 1 (stok masuk batch) + 1 (stok pakai batch)

if ($totalQueries <= $expectedQueries) {
    echo "✓ OPTIMASI BERHASIL: Tidak ada N+1 query yang terdeteksi\n";
    echo '  - Total query: '.$totalQueries.' (expected: '.$expectedQueries.")\n";
    echo "  - Status: SANGAT OPTIMAL\n";
} else {
    echo "⚠ MASIH ADA N+1 QUERY:\n";
    echo '  - Total query: '.$totalQueries.' (expected: '.$expectedQueries.")\n";
    echo '  - Kelebihan: '.($totalQueries - $expectedQueries)." query\n";
}

echo "\n=== TEST SELESAI ===\n";
