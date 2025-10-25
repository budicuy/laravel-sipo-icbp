<?php

namespace App\Listeners;

use App\Events\RekamMedisEmergencyCreated;
use App\Models\Keluhan;
use App\Models\StokBulanan;
use Illuminate\Support\Facades\Log;

class KurangiStokObatEmergencyListener
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
    public function handle(RekamMedisEmergencyCreated $event): void
    {
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

            // Proses setiap keluhan
            foreach ($keluhans as $keluhan) {
                $obatId = $keluhan->id_obat;
                $jumlahObat = $keluhan->jumlah_obat;

                // Log untuk debugging
                Log::info('Memproses pengurangan stok obat emergency', [
                    'id_emergency' => $rekamMedisEmergency->id_emergency,
                    'id_keluhan' => $keluhan->id_keluhan,
                    'id_obat' => $obatId,
                    'jumlah_obat' => $jumlahObat,
                    'tahun' => $tahun,
                    'bulan' => $bulan,
                ]);

                // Cari atau buat record di tabel StokBulanan
                $stokBulanan = StokBulanan::getOrCreate($obatId, $tahun, $bulan);

                // Tambahkan nilai jumlah_obat ke kolom stok_pakai
                $stokBulanan->stok_pakai += $jumlahObat;
                $stokBulanan->save();

                Log::info('Stok obat emergency berhasil dikurangi', [
                    'id_obat' => $obatId,
                    'tahun' => $tahun,
                    'bulan' => $bulan,
                    'jumlah_dikurangi' => $jumlahObat,
                    'total_stok_pakai' => $stokBulanan->stok_pakai,
                ]);
            }

            Log::info('Proses pengurangan stok obat emergency selesai', [
                'id_emergency' => $rekamMedisEmergency->id_emergency,
                'total_keluhan' => $keluhans->count(),
            ]);

        } catch (\Exception $e) {
            Log::error('Error dalam KurangiStokObatEmergencyListener', [
                'id_emergency' => $rekamMedisEmergency->id_emergency,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}
