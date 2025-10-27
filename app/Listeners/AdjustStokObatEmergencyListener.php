<?php

namespace App\Listeners;

use App\Events\RekamMedisEmergencyUpdated;
use App\Models\Keluhan;
use App\Models\StokBulanan;
use Illuminate\Support\Facades\Log;

class AdjustStokObatEmergencyListener
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
    public function handle(RekamMedisEmergencyUpdated $event): void
    {
        $rekamMedisEmergency = $event->rekamMedisEmergency;
        $oldKeluhans = $event->oldKeluhans;

        try {
            // Ambil data keluhan baru setelah update
            $newKeluhans = Keluhan::where('id_emergency', $rekamMedisEmergency->id_emergency)
                ->whereNotNull('id_obat')
                ->where('jumlah_obat', '>', 0)
                ->get();

            // Dapatkan tahun dan bulan dari tanggal periksa rekam medis emergency
            $tanggalPeriksa = $rekamMedisEmergency->tanggal_periksa;
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
                    Log::info('Menyesuaikan stok obat emergency', [
                        'id_emergency' => $rekamMedisEmergency->id_emergency,
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

                        Log::info('Stok obat emergency berhasil disesuaikan', [
                            'id_obat' => $obatId,
                            'tahun' => $tahun,
                            'bulan' => $bulan,
                            'selisih' => $selisih,
                            'total_stok_pakai' => $stokBulanan->stok_pakai,
                        ]);
                    } else {
                        // Buat record baru jika tidak ada
                        $stokBulanan = StokBulanan::create([
                            'obat_id' => $obatId,
                            'tahun' => $tahun,
                            'bulan' => $bulan,
                            'stok_masuk' => 0,
                            'stok_pakai' => max(0, $selisih),
                        ]);

                        Log::info('Stok bulanan baru dibuat untuk emergency', [
                            'id_obat' => $obatId,
                            'tahun' => $tahun,
                            'bulan' => $bulan,
                            'stok_pakai' => $stokBulanan->stok_pakai,
                        ]);
                    }
                }
            }

            Log::info('Proses penyesuaian stok obat emergency selesai', [
                'id_emergency' => $rekamMedisEmergency->id_emergency,
                'total_obat_disesuaikan' => $allObatIds->count(),
            ]);

        } catch (\Exception $e) {
            Log::error('Error dalam AdjustStokObatEmergencyListener', [
                'id_emergency' => $rekamMedisEmergency->id_emergency,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}
