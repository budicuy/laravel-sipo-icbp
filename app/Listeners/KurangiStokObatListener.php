<?php

namespace App\Listeners;

use App\Events\RekamMedisCreated;
use App\Models\Keluhan;
use App\Models\StokBulanan;
use Illuminate\Support\Facades\Log;

class KurangiStokObatListener
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
    public function handle(RekamMedisCreated $event): void
    {
        $rekamMedis = $event->rekamMedis;

        try {
            // Ambil semua data keluhan yang terkait dengan RekamMedis tersebut
            $keluhans = Keluhan::where('id_rekam', $rekamMedis->id_rekam)
                               ->whereNotNull('id_obat')
                               ->where('jumlah_obat', '>', 0)
                               ->get();

            if ($keluhans->isEmpty()) {
                Log::info('Tidak ada keluhan dengan obat untuk rekam medis ini', [
                    'id_rekam' => $rekamMedis->id_rekam
                ]);
                return;
            }

            // Dapatkan tahun dan bulan dari tanggal periksa rekam medis
            $tanggalPeriksa = $rekamMedis->tanggal_periksa;
            $tahun = $tanggalPeriksa->year;
            $bulan = $tanggalPeriksa->month;

            // Proses setiap keluhan
            foreach ($keluhans as $keluhan) {
                $obatId = $keluhan->id_obat;
                $jumlahObat = $keluhan->jumlah_obat;

                // Log untuk debugging
                Log::info('Memproses pengurangan stok obat', [
                    'id_rekam' => $rekamMedis->id_rekam,
                    'id_keluhan' => $keluhan->id_keluhan,
                    'id_obat' => $obatId,
                    'jumlah_obat' => $jumlahObat,
                    'tahun' => $tahun,
                    'bulan' => $bulan
                ]);

                // Cari atau buat record di tabel StokBulanan
                $stokBulanan = StokBulanan::getOrCreate($obatId, $tahun, $bulan);

                // Tambahkan nilai jumlah_obat ke kolom stok_pakai
                $stokBulanan->stok_pakai += $jumlahObat;
                $stokBulanan->save();

                Log::info('Stok obat berhasil dikurangi', [
                    'id_obat' => $obatId,
                    'tahun' => $tahun,
                    'bulan' => $bulan,
                    'jumlah_dikurangi' => $jumlahObat,
                    'total_stok_pakai' => $stokBulanan->stok_pakai
                ]);
            }

            Log::info('Proses pengurangan stok obat selesai', [
                'id_rekam' => $rekamMedis->id_rekam,
                'total_keluhan' => $keluhans->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Error dalam KurangiStokObatListener', [
                'id_rekam' => $rekamMedis->id_rekam,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
