<?php

namespace App\Listeners;

use App\Events\RekamMedisEmergencyCreated;
use App\Models\Keluhan;
use App\Models\StokBulanan;
use Illuminate\Support\Facades\Log;

class KurangiStokObatEmergencyListener
{
    /**
     * Static cache untuk StokBulanan dalam satu request
     * Format: ['obat_id_tahun_bulan' => StokBulanan instance]
     */
    protected static $stokCache = [];

    /**
     * Flag untuk suspend event selama bulk import
     */
    protected static $suspended = false;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(RekamMedisEmergencyCreated $event): void
    {
        // Skip jika suspended (sedang bulk import)
        if (self::$suspended) {
            return;
        }

        $rekamMedisEmergency = $event->rekamMedisEmergency;

        try {
            // Ambil semua data keluhan yang terkait dengan RekamMedisEmergency tersebut
            $keluhans = Keluhan::where('id_emergency', $rekamMedisEmergency->id_emergency)
                ->whereNotNull('id_obat')
                ->where('jumlah_obat', '>', 0)
                ->get();

            if ($keluhans->isEmpty()) {
                Log::info('Tidak ada keluhan dengan obat untuk rekam medis emergency ini', [
                    'id_emergency' => $rekamMedisEmergency->id_emergency,
                ]);

                return;
            }

            // Dapatkan tahun dan bulan dari tanggal periksa rekam medis emergency
            $tanggalPeriksa = $rekamMedisEmergency->tanggal_periksa;
            $tahun = $tanggalPeriksa->year;
            $bulan = $tanggalPeriksa->month;

            // Group keluhans by obat untuk mengurangi query
            $keluhansByObat = $keluhans->groupBy('id_obat');

            // Proses setiap obat (bukan setiap keluhan)
            foreach ($keluhansByObat as $obatId => $keluhanGroup) {
                $totalJumlah = $keluhanGroup->sum('jumlah_obat');

                // Log untuk debugging
                Log::info('Memproses pengurangan stok obat emergency', [
                    'id_emergency' => $rekamMedisEmergency->id_emergency,
                    'id_obat' => $obatId,
                    'jumlah_obat' => $totalJumlah,
                    'tahun' => $tahun,
                    'bulan' => $bulan,
                ]);

                // Cari atau buat record di tabel StokBulanan dengan cache
                $stokBulanan = $this->getOrCreateStokBulanan($obatId, $tahun, $bulan);

                // Tambahkan nilai jumlah_obat ke kolom stok_pakai
                $stokBulanan->stok_pakai += $totalJumlah;

                // Save immediately for regular operations
                if (!self::$suspended) {
                    $stokBulanan->save();
                }

                // Update cache dengan instance terbaru
                $cacheKey = "{$obatId}_{$tahun}_{$bulan}";
                self::$stokCache[$cacheKey] = $stokBulanan;

                Log::info('Stok obat emergency berhasil dikurangi', [
                    'id_obat' => $obatId,
                    'tahun' => $tahun,
                    'bulan' => $bulan,
                    'jumlah_dikurangi' => $totalJumlah,
                    'total_stok_pakai' => $stokBulanan->stok_pakai,
                ]);
            }

            Log::info('Proses pengurangan stok obat emergency selesai', [
                'id_emergency' => $rekamMedisEmergency->id_emergency,
                'total_obat_diproses' => $keluhansByObat->count(),
            ]);

        } catch (\Exception $e) {
            Log::error('Error dalam KurangiStokObatEmergencyListener', [
                'id_emergency' => $rekamMedisEmergency->id_emergency,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Get or create StokBulanan dengan cache
     */
    protected function getOrCreateStokBulanan($obatId, $tahun, $bulan)
    {
        $cacheKey = "{$obatId}_{$tahun}_{$bulan}";

        // Cek cache terlebih dahulu
        if (isset(self::$stokCache[$cacheKey])) {
            // Kembalikan dari cache tanpa refresh (untuk performa)
            return self::$stokCache[$cacheKey];
        }

        // Jika tidak ada di cache, query dari database
        $stokBulanan = StokBulanan::where('obat_id', $obatId)
            ->where('tahun', $tahun)
            ->where('bulan', $bulan)
            ->first();

        if (!$stokBulanan) {
            // Buat baru jika tidak ada
            $stokBulanan = StokBulanan::create([
                'obat_id' => $obatId,
                'tahun' => $tahun,
                'bulan' => $bulan,
                'stok_masuk' => 0,
                'stok_pakai' => 0,
            ]);
        }

        // Simpan ke cache
        self::$stokCache[$cacheKey] = $stokBulanan;

        return $stokBulanan;
    }

    /**
     * Warm cache dengan data yang sudah di-load sebelumnya
     */
    public static function warmCache($cacheKey, $stokBulanan)
    {
        self::$stokCache[$cacheKey] = $stokBulanan;
    }

    /**
     * Set suspended flag
     */
    public static function setSuspended($suspended)
    {
        self::$suspended = $suspended;
    }

    /**
     * Save semua perubahan yang ada di cache ke database
     */
    public static function saveAllCachedChanges()
    {
        $savedCount = 0;
        foreach (self::$stokCache as $cacheKey => $stokBulanan) {
            if ($stokBulanan->isDirty()) {
                $stokBulanan->save();
                $savedCount++;
            }
        }

        if ($savedCount > 0) {
            Log::info('Batch save stok bulanan emergency completed', [
                'total_saved' => $savedCount
            ]);
        }
    }

    /**
     * Clear cache - dipanggil setelah import selesai
     */
    public static function clearCache()
    {
        self::$stokCache = [];
    }
}
