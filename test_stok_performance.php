<?php

require_once 'vendor/autoload.php';

use App\Models\Obat;
use App\Models\StokBulanan;
use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TEST PERFORMA OPTIMASI N+1 QUERY STOK OBAT ===\n\n";

// Test 1: Metode lama (N+1 queries)
echo "1. Testing metode lama (N+1 queries):\n";
$startTime = microtime(true);
DB::enableQueryLog();

$obats = Obat::with(['satuanObat:id_satuan,nama_satuan'])->limit(50)->get();
$obatsWithStokLama = $obats->map(function ($obat) {
    $sisaStok = StokBulanan::getSisaStokSaatIni($obat->id_obat);
    $obat->sisa_stok = $sisaStok;

    return $obat;
});

$endTime = microtime(true);
$queriesLama = DB::getQueryLog();
$timeLama = $endTime - $startTime;

echo '   - Waktu eksekusi: '.number_format($timeLama, 4)." detik\n";
echo '   - Jumlah query: '.count($queriesLama)."\n";
echo '   - Jumlah obat: '.$obats->count()."\n";

// Tampilkan 5 query pertama untuk analisis
echo "   - 5 Query pertama:\n";
foreach (array_slice($queriesLama, 0, 5) as $i => $query) {
    echo '     '.($i + 1).'. '.$query['query']."\n";
}
echo "\n";

// Reset query log
DB::flushQueryLog();

// Test 2: Metode baru (optimized)
echo "2. Testing metode baru (optimized):\n";
$startTime = microtime(true);
DB::enableQueryLog();

$obats = Obat::with(['satuanObat:id_satuan,nama_satuan'])->limit(50)->get();
$obatIds = $obats->pluck('id_obat')->toArray();
$sisaStokMap = StokBulanan::getSisaStokSaatIniBatch($obatIds);
$obatsWithStokBaru = $obats->map(function ($obat) use ($sisaStokMap) {
    $obat->sisa_stok = $sisaStokMap->get($obat->id_obat, 0);

    return $obat;
});

$endTime = microtime(true);
$queriesBaru = DB::getQueryLog();
$timeBaru = $endTime - $startTime;

echo '   - Waktu eksekusi: '.number_format($timeBaru, 4)." detik\n";
echo '   - Jumlah query: '.count($queriesBaru)."\n";
echo '   - Jumlah obat: '.$obats->count()."\n";

// Tampilkan semua query baru
echo "   - Semua query:\n";
foreach ($queriesBaru as $i => $query) {
    echo '     '.($i + 1).'. '.$query['query']."\n";
}
echo "\n";

// Perbandingan hasil
echo "3. Perbandingan hasil:\n";
echo '   - Peningkatan performa: '.number_format(($timeLama / $timeBaru), 2)."x lebih cepat\n";
echo '   - Pengurangan query: '.count($queriesLama).' -> '.count($queriesBaru).' (';
echo number_format((1 - count($queriesBaru) / count($queriesLama)) * 100, 1)."% reduction)\n";

// Verifikasi hasil sama
$hasilSama = true;
foreach ($obatsWithStokLama as $index => $obatLama) {
    $obatBaru = $obatsWithStokBaru[$index];
    if ($obatLama->sisa_stok != $obatBaru->sisa_stok) {
        $hasilSama = false;
        echo '   - WARNING: Hasil tidak sama untuk obat ID '.$obatLama->id_obat.
             ' (lama: '.$obatLama->sisa_stok.', baru: '.$obatBaru->sisa_stok.")\n";
        break;
    }
}

if ($hasilSama) {
    echo "   - ✓ Semua hasil perhitungan stok sama\n";
}

// Test 3: Test riwayat stok (optimasi getRiwayatStok)
echo "4. Testing riwayat stok (getRiwayatStok):\n";
if ($obats->isNotEmpty()) {
    $testObatId = $obats->first()->id_obat;

    // Reset query log
    DB::flushQueryLog();

    $startTime = microtime(true);
    $riwayatStok = StokBulanan::getRiwayatStok($testObatId, 12);
    $endTime = microtime(true);

    $queriesRiwayat = DB::getQueryLog();
    $timeRiwayat = $endTime - $startTime;

    echo '   - Waktu eksekusi: '.number_format($timeRiwayat, 4)." detik\n";
    echo '   - Jumlah query: '.count($queriesRiwayat)."\n";
    echo '   - Jumlah riwayat: '.$riwayatStok->count()."\n";

    // Tampilkan query riwayat
    echo "   - Query riwayat:\n";
    foreach ($queriesRiwayat as $i => $query) {
        echo '     '.($i + 1).'. '.$query['query']."\n";
    }
}

echo "\n=== TEST SELESAI ===\n";
