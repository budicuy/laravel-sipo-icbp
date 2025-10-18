<?php

namespace App\Http\Controllers;

use App\Models\HargaObatPerBulan;
use App\Models\Obat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MonitoringHargaController extends Controller
{
    /**
     * Display monitoring harga obat page
     */
    public function index(Request $request)
    {
        $months = $request->get('months', 3); // Default 3 bulan

        // Get obat dengan harga yang belum diperbarui
        $obatStaleHarga = HargaObatPerBulan::getObatWithStaleHarga($months);

        // Get statistik monitoring
        $stats = $this->getMonitoringStats($months);

        // Get obat dengan gap harga
        $obatWithGaps = $this->getObatWithHargaGaps();

        return view('monitoring-harga.index', compact(
            'obatStaleHarga',
            'stats',
            'months',
            'obatWithGaps'
        ));
    }

    /**
     * Get monitoring statistics
     */
    private function getMonitoringStats($months)
    {
        $currentPeriode = now()->format('m-y');
        $thresholdPeriode = HargaObatPerBulan::getPeriodeMonthsAgo($months);

        // Total obat aktif
        $totalObat = Obat::count();

        // Obat dengan harga terkini
        $obatWithCurrentHarga = HargaObatPerBulan::where('periode', $currentPeriode)
            ->distinct('id_obat')
            ->count('id_obat');

        // Obat dengan harga kadaluarsa
        $obatWithStaleHarga = HargaObatPerBulan::getObatWithStaleHarga($months)->count();

        // Persentase obat dengan harga terkini
        $percentageCurrent = $totalObat > 0 ? ($obatWithCurrentHarga / $totalObat) * 100 : 0;

        return [
            'total_obat' => $totalObat,
            'obat_with_current_harga' => $obatWithCurrentHarga,
            'obat_with_stale_harga' => $obatWithStaleHarga,
            'percentage_current' => round($percentageCurrent, 2),
            'threshold_periode' => $thresholdPeriode,
            'current_periode' => $currentPeriode
        ];
    }

    /**
     * Get obat dengan gap harga
     */
    private function getObatWithHargaGaps()
    {
        $endPeriode = now()->format('m-y');
        $startPeriode = HargaObatPerBulan::getPeriodeMonthsAgo(6); // Check 6 months back

        $obatWithGaps = [];

        // Get all obat
        $allObat = Obat::all();

        foreach ($allObat as $obat) {
            $validation = HargaObatPerBulan::validateHargaContinuity(
                $obat->id_obat,
                $startPeriode,
                $endPeriode
            );

            if ($validation['has_gap']) {
                $obatWithGaps[] = [
                    'obat' => $obat,
                    'validation' => $validation
                ];
            }
        }

        return collect($obatWithGaps);
    }

    /**
     * API endpoint untuk validasi harga continuity
     */
    public function validateHargaContinuity(Request $request)
    {
        $request->validate([
            'id_obat' => 'required|integer',
            'start_periode' => 'required|string',
            'end_periode' => 'required|string'
        ]);

        $validation = HargaObatPerBulan::validateHargaContinuity(
            $request->id_obat,
            $request->start_periode,
            $request->end_periode
        );

        return response()->json($validation);
    }

    /**
     * Generate harga recommendations untuk periode tertentu
     */
    public function generateRecommendations(Request $request)
    {
        $targetPeriode = $request->get('target_periode', now()->format('m-y'));

        // Get obat yang tidak memiliki harga di target periode
        $obatWithoutHarga = DB::select("
            SELECT o.id_obat, o.nama_obat
            FROM obat o
            WHERE o.id_obat NOT IN (
                SELECT DISTINCT id_obat
                FROM harga_obat_per_bulan
                WHERE periode = ?
            )
        ", [$targetPeriode]);

        // Get rekomendasi harga dari periode sebelumnya
        $recommendations = [];

        foreach ($obatWithoutHarga as $obat) {
            $lastHarga = HargaObatPerBulan::where('id_obat', $obat->id_obat)
                ->orderByRaw("SUBSTRING(periode, 4, 2) DESC, SUBSTRING(periode, 1, 2) DESC")
                ->first();

            if ($lastHarga) {
                $recommendations[] = [
                    'id_obat' => $obat->id_obat,
                    'nama_obat' => $obat->nama_obat,
                    'last_periode' => $lastHarga->periode,
                    'recommended_harga' => $lastHarga->harga_per_satuan,
                    'recommended_kemasan' => $lastHarga->harga_per_kemasan,
                    'recommended_jumlah' => $lastHarga->jumlah_per_kemasan
                ];
            }
        }

        return response()->json($recommendations);
    }

    /**
     * Bulk create harga dari rekomendasi
     */
    public function bulkCreateHarga(Request $request)
    {
        $request->validate([
            'target_periode' => 'required|string',
            'recommendations' => 'required|array',
            'recommendations.*.id_obat' => 'required|integer',
            'recommendations.*.harga_per_satuan' => 'required|numeric|min:0',
            'recommendations.*.harga_per_kemasan' => 'required|numeric|min:0',
            'recommendations.*.jumlah_per_kemasan' => 'required|integer|min:1'
        ]);

        $createdCount = 0;
        $errors = [];

        DB::beginTransaction();

        try {
            foreach ($request->recommendations as $recommendation) {
                // Check if harga already exists
                $exists = HargaObatPerBulan::where('id_obat', $recommendation['id_obat'])
                    ->where('periode', $request->target_periode)
                    ->exists();

                if (!$exists) {
                    HargaObatPerBulan::create([
                        'id_obat' => $recommendation['id_obat'],
                        'periode' => $request->target_periode,
                        'harga_per_satuan' => $recommendation['harga_per_satuan'],
                        'harga_per_kemasan' => $recommendation['harga_per_kemasan'],
                        'jumlah_per_kemasan' => $recommendation['jumlah_per_kemasan']
                    ]);
                    $createdCount++;
                } else {
                    $errors[] = "Harga untuk obat ID {$recommendation['id_obat']} di periode {$request->target_periode} sudah ada";
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'created_count' => $createdCount,
                'errors' => $errors,
                'message' => "Berhasil membuat {$createdCount} harga obat baru"
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export laporan monitoring harga ke Excel
     */
    public function exportMonitoring(Request $request)
    {
        $months = $request->get('months', 3);

        // Get data for export
        $obatStaleHarga = HargaObatPerBulan::getObatWithStaleHarga($months);
        $stats = $this->getMonitoringStats($months);

        // This would require a package like maatwebsite/excel
        // For now, return a simple CSV download
        $filename = "monitoring_harga_{$months}_bulan_" . date('Y-m-d') . ".csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($obatStaleHarga, $stats) {
            $file = fopen('php://output', 'w');

            // Header
            fputcsv($file, ['LAPORAN MONITORING HARGA OBAT']);
            fputcsv($file, ['Tanggal Generate', date('d-m-Y H:i:s')]);
            fputcsv($file, []);

            // Statistik
            fputcsv($file, ['STATISTIK']);
            fputcsv($file, ['Total Obat', $stats['total_obat']]);
            fputcsv($file, ['Obat dengan Harga Terkini', $stats['obat_with_current_harga']]);
            fputcsv($file, ['Obat dengan Harga Kadaluarsa', $stats['obat_with_stale_harga']]);
            fputcsv($file, ['Persentase Harga Terkini', $stats['percentage_current'] . '%']);
            fputcsv($file, []);

            // Detail obat dengan harga kadaluarsa
            fputcsv($file, ['OBAT DENGAN HARGA KADALUARSA']);
            fputcsv($file, ['ID Obat', 'Nama Obat', 'Periode Terakhir Harga']);

            foreach ($obatStaleHarga as $obat) {
                fputcsv($file, [
                    $obat->id_obat,
                    $obat->nama_obat,
                    $obat->last_harga_periode
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * API untuk mendapatkan histori harga obat
     */
    public function getHargaHistory($idObat)
    {
        $history = HargaObatPerBulan::where('id_obat', $idObat)
            ->orderByRaw("SUBSTRING(periode, 4, 2) DESC, SUBSTRING(periode, 1, 2) DESC")
            ->get()
            ->map(function($item) {
                return [
                    'periode' => $item->periode,
                    'periode_format' => $item->periode_format,
                    'harga_per_satuan' => $item->harga_per_satuan,
                    'harga_per_kemasan' => $item->harga_per_kemasan,
                    'jumlah_per_kemasan' => $item->jumlah_per_kemasan,
                    'created_at' => $item->created_at->format('d-m-Y H:i:s'),
                    'updated_at' => $item->updated_at->format('d-m-Y H:i:s')
                ];
            });

        return response()->json($history);
    }
}
