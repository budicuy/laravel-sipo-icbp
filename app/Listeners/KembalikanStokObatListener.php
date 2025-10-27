<?php

namespace App\Listeners;

use App\Events\RekamMedisDeleted;
use App\Models\Keluhan;
use App\Models\StokBulanan;
use Illuminate\Support\Facades\Log;

class KembalikanStokObatListener
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
    public function handle(RekamMedisDeleted $event): void
    {
        $rekamMedis = $event->rekamMedis;

        try {
            // Ambil keluhan dari relasi yang sudah di-eager load
            // Karena event dipanggil SEBELUM delete, relasi masih tersedia
            $keluhans = $rekamMedis->keluhans()
                ->whereNotNull('id_obat')
                ->where('jumlah_obat', '>', 0)
                ->get();

            if ($keluhans->isEmpty()) {
                Log::info('Tidak ada keluhan dengan obat untuk rekam medis ini', [
                    'id_rekam' => $rekamMedis->id_rekam,
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
                Log::info('Memproses pengembalian stok obat', [
                    'id_rekam' => $rekamMedis->id_rekam,
                    'id_keluhan' => $keluhan->id_keluhan,
                    'id_obat' => $obatId,
                    'jumlah_obat' => $jumlahObat,
                    'tahun' => $tahun,
                    'bulan' => $bulan,
                ]);

                // Cari record di tabel StokBulanan
                $stokBulanan = StokBulanan::where('obat_id', $obatId)
                    ->where('tahun', $tahun)
                    ->where('bulan', $bulan)
                    ->first();

                if ($stokBulanan) {
                    // Kurangi nilai stok_pakai (kembalikan stok)
                    $stokBulanan->stok_pakai = max(0, $stokBulanan->stok_pakai - $jumlahObat);
                    $stokBulanan->save();

                    Log::info('Stok obat berhasil dikembalikan', [
                        'id_obat' => $obatId,
                        'tahun' => $tahun,
                        'bulan' => $bulan,
                        'jumlah_dikembalikan' => $jumlahObat,
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

            Log::info('Proses pengembalian stok obat selesai', [
                'id_rekam' => $rekamMedis->id_rekam,
                'total_keluhan' => $keluhans->count(),
            ]);

        } catch (\Exception $e) {
            Log::error('Error dalam KembalikanStokObatListener', [
                'id_rekam' => $rekamMedis->id_rekam,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}
