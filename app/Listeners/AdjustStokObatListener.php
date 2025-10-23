<?php

namespace App\Listeners;

use App\Events\RekamMedisUpdated;
use App\Models\Keluhan;
use App\Models\StokBulanan;
use Illuminate\Support\Facades\Log;

class AdjustStokObatListener
{
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
    public function handle(RekamMedisUpdated $event): void
    {
        $rekamMedis = $event->rekamMedis;
        $oldKeluhans = $event->oldKeluhans;

        try {
            // Ambil data keluhan baru setelah update
            $newKeluhans = Keluhan::where('id_rekam', $rekamMedis->id_rekam)
                ->whereNotNull('id_obat')
                ->where('jumlah_obat', '>', 0)
                ->get();

            // Dapatkan tahun dan bulan dari tanggal periksa rekam medis
            $tanggalPeriksa = $rekamMedis->tanggal_periksa;
            $tahun = $tanggalPeriksa->year;
            $bulan = $tanggalPeriksa->month;

            // Group old dan new keluhans by obat_id untuk memudahkan perhitungan
            $oldKeluhansByObat = $oldKeluhans->whereNotNull('id_obat')
                ->where('jumlah_obat', '>', 0)
                ->groupBy('id_obat')
                ->map(function ($group) {
                    return $group->sum('jumlah_obat');
                });

            $newKeluhansByObat = $newKeluhans->groupBy('id_obat')
                ->map(function ($group) {
                    return $group->sum('jumlah_obat');
                });

            // Dapatkan semua obat_id yang terlibat (old dan new)
            $allObatIds = $oldKeluhansByObat->keys()->merge($newKeluhansByObat->keys())->unique();

            // Proses setiap obat untuk menyesuaikan stok
            foreach ($allObatIds as $obatId) {
                $oldJumlah = $oldKeluhansByObat->get($obatId, 0);
                $newJumlah = $newKeluhansByObat->get($obatId, 0);
                $selisih = $newJumlah - $oldJumlah;

                // Jika ada perubahan jumlah obat
                if ($selisih != 0) {
                    Log::info('Menyesuaikan stok obat', [
                        'id_rekam' => $rekamMedis->id_rekam,
                        'id_obat' => $obatId,
                        'jumlah_lama' => $oldJumlah,
                        'jumlah_baru' => $newJumlah,
                        'selisih' => $selisih,
                        'tahun' => $tahun,
                        'bulan' => $bulan,
                    ]);

                    // Cari record di tabel StokBulanan
                    $stokBulanan = StokBulanan::where('obat_id', $obatId)
                        ->where('tahun', $tahun)
                        ->where('bulan', $bulan)
                        ->first();

                    if ($stokBulanan) {
                        // Sesuaikan stok_pakai berdasarkan selisih
                        $stokBulanan->stok_pakai = max(0, $stokBulanan->stok_pakai + $selisih);
                        $stokBulanan->save();

                        Log::info('Stok obat berhasil disesuaikan', [
                            'id_obat' => $obatId,
                            'tahun' => $tahun,
                            'bulan' => $bulan,
                            'selisih' => $selisih,
                            'total_stok_pakai' => $stokBulanan->stok_pakai,
                        ]);
                    } else {
                        Log::warning('Tidak ditemukan record stok bulanan untuk obat', [
                            'id_obat' => $obatId,
                            'tahun' => $tahun,
                            'bulan' => $bulan,
                        ]);
                    }
                }
            }

            Log::info('Proses penyesuaian stok obat selesai', [
                'id_rekam' => $rekamMedis->id_rekam,
                'total_obat_disesuaikan' => $allObatIds->count(),
            ]);

        } catch (\Exception $e) {
            Log::error('Error dalam AdjustStokObatListener', [
                'id_rekam' => $rekamMedis->id_rekam,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}
