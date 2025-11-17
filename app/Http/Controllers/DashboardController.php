<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Kunjungan;
use App\Models\Obat;
use App\Models\RekamMedis;
use App\Models\RekamMedisEmergency;
use App\Models\StokBulanan;
use App\Models\HargaObatPerBulan;
use App\Models\Keluhan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Get dashboard statistics
     */
    public function getStatistics()
    {
        // Realtime statistics (no cache untuk data yang harus up-to-date)
        $totalRekamMedisReguler = RekamMedis::count();
        $totalRekamMedisEmergency = RekamMedisEmergency::count();

        $kunjunganHariIniReguler = RekamMedis::whereDate('tanggal_periksa', now()->toDateString())->count();
        $kunjunganHariIniEmergency = RekamMedisEmergency::whereDate('tanggal_periksa', now()->toDateString())->count();

        $onProgressReguler = RekamMedis::where('status', 'On Progress')->count();
        $onProgressEmergency = RekamMedisEmergency::where('status', 'On Progress')->count();

        $closeReguler = RekamMedis::where('status', 'Close')->count();
        $closeEmergency = RekamMedisEmergency::where('status', 'Close')->count();

        // Get warning obat count (stok < 10 atau stok = 0)
        $warningObat = $this->getWarningObatCount();

        $statistics = [
            'total_karyawan' => Karyawan::where('status', 'aktif')->count(),
            'total_rekam_medis' => $totalRekamMedisReguler + $totalRekamMedisEmergency,
            'kunjungan_hari_ini' => $kunjunganHariIniReguler + $kunjunganHariIniEmergency,
            'on_progress' => $onProgressReguler + $onProgressEmergency,
            'close' => $closeReguler + $closeEmergency,
            'warning_obat' => $warningObat,
        ];

        return response()->json($statistics);
    }

    /**
     * Get visit analysis data - OPTIMIZED for realtime data
     */
    public function getVisitAnalysis(Request $request)
    {
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);

        // Kunjungan Harian (realtime, no cache)
        $dailyVisits = $this->getDailyVisits($month, $year);

        // Kunjungan Mingguan (realtime, no cache)
        $weeklyVisits = $this->getWeeklyVisits($month, $year);

        // Kunjungan Bulanan (realtime, no cache)
        $monthlyVisits = $this->getMonthlyVisits($year);

        // Total Biaya Transaksi (realtime, no cache)
        $totalBiaya = $this->getTotalBiaya($month, $year);

        return response()->json([
            'daily' => $dailyVisits,
            'weekly' => $weeklyVisits,
            'monthly' => $monthlyVisits,
            'totalBiaya' => $totalBiaya,
        ]);
    }

    /**
     * Get daily visits for a specific month and year - OPTIMIZED
     */
    private function getDailyVisits($month, $year)
    {
        $startDate = Carbon::create($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();
        $daysInMonth = $startDate->daysInMonth;

        // Single query dengan GROUP BY untuk semua hari dalam sebulan (Reguler)
        $dailyVisitsReguler = RekamMedis::selectRaw('DAY(tanggal_periksa) as day, COUNT(*) as count')
            ->whereMonth('tanggal_periksa', $month)
            ->whereYear('tanggal_periksa', $year)
            ->groupBy(DB::raw('DAY(tanggal_periksa)'))
            ->orderBy('day')
            ->pluck('count', 'day')
            ->toArray();

        // Single query dengan GROUP BY untuk semua hari dalam sebulan (Emergency)
        $dailyVisitsEmergency = RekamMedisEmergency::selectRaw('DAY(tanggal_periksa) as day, COUNT(*) as count')
            ->whereMonth('tanggal_periksa', $month)
            ->whereYear('tanggal_periksa', $year)
            ->groupBy(DB::raw('DAY(tanggal_periksa)'))
            ->orderBy('day')
            ->pluck('count', 'day')
            ->toArray();

        // Build array dengan 0 untuk hari yang tidak ada kunjungan
        $dailyData = [];
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $dailyData[] = ($dailyVisitsReguler[$day] ?? 0) + ($dailyVisitsEmergency[$day] ?? 0);
        }

        return [
            'labels' => range(1, $daysInMonth),
            'data' => $dailyData,
        ];
    }

    /**
     * Get weekly visits for a specific month and year - OPTIMIZED
     */
    private function getWeeklyVisits($month, $year)
    {
        $startDate = Carbon::create($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();

        // Single query dengan GROUP BY untuk semua minggu dalam sebulan (Reguler)
        $weeklyVisitsReguler = RekamMedis::selectRaw('WEEK(tanggal_periksa, 1) as week, COUNT(*) as count')
            ->whereBetween('tanggal_periksa', [$startDate->toDateString(), $endDate->toDateString()])
            ->groupBy(DB::raw('WEEK(tanggal_periksa, 1)'))
            ->orderBy('week')
            ->pluck('count', 'week')
            ->toArray();

        // Single query dengan GROUP BY untuk semua minggu dalam sebulan (Emergency)
        $weeklyVisitsEmergency = RekamMedisEmergency::selectRaw('WEEK(tanggal_periksa, 1) as week, COUNT(*) as count')
            ->whereBetween('tanggal_periksa', [$startDate->toDateString(), $endDate->toDateString()])
            ->groupBy(DB::raw('WEEK(tanggal_periksa, 1)'))
            ->orderBy('week')
            ->pluck('count', 'week')
            ->toArray();

        // Generate labels and data
        $weeklyLabels = [];
        $weeklyCounts = [];
        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            $weekStart = $currentDate->copy()->startOfWeek();
            $weekEnd = $weekStart->copy()->endOfWeek()->min($endDate);
            $weekNumber = $weekStart->weekOfYear;

            $count = ($weeklyVisitsReguler[$weekNumber] ?? 0) + ($weeklyVisitsEmergency[$weekNumber] ?? 0);

            $weeklyLabels[] = $weekStart->format('d M').' - '.$weekEnd->format('d M');
            $weeklyCounts[] = $count;

            $currentDate = $weekEnd->copy()->addDay();
        }

        return [
            'labels' => $weeklyLabels,
            'data' => $weeklyCounts,
        ];
    }

    /**
     * Get monthly visits for a specific year - OPTIMIZED
     */
    private function getMonthlyVisits($year)
    {
        $monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        // Single query dengan GROUP BY untuk semua bulan dalam setahun (Reguler)
        $monthlyVisitsReguler = RekamMedis::selectRaw('MONTH(tanggal_periksa) as month, COUNT(*) as count')
            ->whereYear('tanggal_periksa', $year)
            ->groupBy(DB::raw('MONTH(tanggal_periksa)'))
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();

        // Single query dengan GROUP BY untuk semua bulan dalam setahun (Emergency)
        $monthlyVisitsEmergency = RekamMedisEmergency::selectRaw('MONTH(tanggal_periksa) as month, COUNT(*) as count')
            ->whereYear('tanggal_periksa', $year)
            ->groupBy(DB::raw('MONTH(tanggal_periksa)'))
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();

        // Build array dengan 0 untuk bulan yang tidak ada kunjungan
        $data = [];
        for ($month = 1; $month <= 12; $month++) {
            $data[] = ($monthlyVisitsReguler[$month] ?? 0) + ($monthlyVisitsEmergency[$month] ?? 0);
        }

        return [
            'labels' => $monthNames,
            'data' => $data,
        ];
    }

    /**
     * Get real-time dashboard data (for auto-refresh)
     */
    public function getRealtimeData()
    {
        return response()->json([
            'statistics' => $this->getStatistics()->getData(true),
            'timestamp' => Carbon::now()->toISOString(),
        ]);
    }

    /**
     * Get top diagnoses for current month
     */
    public function getTopDiagnoses(Request $request)
    {
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);
        $limit = $request->input('limit', 10);

        // Get diagnosa from reguler rekam medis
        $diagnosaReguler = DB::table('keluhan')
            ->join('rekam_medis', 'keluhan.id_rekam', '=', 'rekam_medis.id_rekam')
            ->join('diagnosa', 'keluhan.id_diagnosa', '=', 'diagnosa.id_diagnosa')
            ->whereMonth('rekam_medis.tanggal_periksa', $month)
            ->whereYear('rekam_medis.tanggal_periksa', $year)
            ->whereNotNull('keluhan.id_diagnosa')
            ->select('diagnosa.nama_diagnosa', DB::raw('COUNT(*) as total'))
            ->groupBy('diagnosa.id_diagnosa', 'diagnosa.nama_diagnosa')
            ->get();

        // Get diagnosa from emergency rekam medis
        $diagnosaEmergency = DB::table('keluhan')
            ->join('rekam_medis_emergency', 'keluhan.id_emergency', '=', 'rekam_medis_emergency.id_emergency')
            ->join('diagnosa_emergency', 'keluhan.id_diagnosa_emergency', '=', 'diagnosa_emergency.id_diagnosa_emergency')
            ->whereMonth('rekam_medis_emergency.tanggal_periksa', $month)
            ->whereYear('rekam_medis_emergency.tanggal_periksa', $year)
            ->whereNotNull('keluhan.id_diagnosa_emergency')
            ->select('diagnosa_emergency.nama_diagnosa_emergency as nama_diagnosa', DB::raw('COUNT(*) as total'))
            ->groupBy('diagnosa_emergency.id_diagnosa_emergency', 'diagnosa_emergency.nama_diagnosa_emergency')
            ->get();

        // Combine and aggregate results
        $combinedDiagnosa = collect();

        // Add reguler diagnosa
        foreach ($diagnosaReguler as $diagnosa) {
            $existing = $combinedDiagnosa->firstWhere('nama_diagnosa', $diagnosa->nama_diagnosa);
            if ($existing) {
                $existing->total += $diagnosa->total;
            } else {
                $combinedDiagnosa->push((object) [
                    'nama_diagnosa' => $diagnosa->nama_diagnosa,
                    'total' => $diagnosa->total,
                ]);
            }
        }

        // Add emergency diagnosa
        foreach ($diagnosaEmergency as $diagnosa) {
            $existing = $combinedDiagnosa->firstWhere('nama_diagnosa', $diagnosa->nama_diagnosa);
            if ($existing) {
                $existing->total += $diagnosa->total;
            } else {
                $combinedDiagnosa->push((object) [
                    'nama_diagnosa' => $diagnosa->nama_diagnosa,
                    'total' => $diagnosa->total,
                ]);
            }
        }

        // Sort by total descending and limit
        $topDiagnosa = $combinedDiagnosa->sortByDesc('total')->take($limit)->values();

        // Calculate total for percentage
        $totalAll = $topDiagnosa->sum('total');

        // Add percentage to each diagnosa
        $result = $topDiagnosa->map(function ($item) use ($totalAll) {
            $item->percentage = $totalAll > 0 ? round(($item->total / $totalAll) * 100, 1) : 0;

            return $item;
        });

        return response()->json([
            'diagnoses' => $result,
            'total_cases' => $totalAll,
            'month' => $month,
            'year' => $year,
        ]);
    }

    /**
     * Get count of obat with low stock (stok ≤ 10 termasuk stok habis ≤ 0)
     */
    private function getWarningObatCount()
    {
        // Get current month and year
        $currentMonth = now()->month;
        $currentYear = now()->year;

        // Get all active obat IDs
        $obatIds = Obat::where('status', 'aktif')->pluck('id_obat')->toArray();

        if (empty($obatIds)) {
            return 0;
        }

        // Get current stock for all active obat
        $currentStocks = StokBulanan::getSisaStokSaatIniBatch($obatIds);

        // Count obat with stock <= 10 (termasuk stok habis <= 0)
        $warningCount = 0;
        foreach ($currentStocks as $obatId => $stock) {
            // Warning untuk stok <= 10, termasuk stok habis (<= 0)
            if ($stock <= 10) {
                $warningCount++;
            }
        }

        return $warningCount;
    }

    /**
     * Get total biaya transaksi for a specific month and year
     */
    private function getTotalBiaya($month, $year)
    {
        // Get all keluhan with rekamMedis for specified year with optimized eager loading
        $keluhanDataReguler = Keluhan::with([
            'rekamMedis:id_rekam,tanggal_periksa',
            'obat:id_obat,nama_obat'
        ])
            ->whereHas('rekamMedis', function ($query) use ($year) {
                $query->whereYear('tanggal_periksa', $year);
            })
            ->whereNotNull('id_obat')
            ->select('id_keluhan', 'id_rekam', 'id_obat', 'jumlah_obat', 'diskon')
            ->get();

        // Get all keluhan with rekamMedisEmergency for specified year with optimized eager loading
        $keluhanDataEmergency = Keluhan::with([
            'rekamMedisEmergency:id_emergency,tanggal_periksa',
            'obat:id_obat,nama_obat'
        ])
            ->whereHas('rekamMedisEmergency', function ($query) use ($year) {
                $query->whereYear('tanggal_periksa', $year);
            })
            ->whereNotNull('id_obat')
            ->select('id_keluhan', 'id_emergency', 'id_obat', 'jumlah_obat', 'diskon')
            ->get();

        // Collect all unique obat IDs and periods for reguler
        $obatPeriodsReguler = [];
        foreach ($keluhanDataReguler as $keluhan) {
            $periode = $keluhan->rekamMedis->tanggal_periksa->format('m-y');
            $obatPeriodsReguler[] = [
                'id_obat' => $keluhan->id_obat,
                'periode' => $periode,
            ];
        }

        // Collect all unique obat IDs and periods for emergency
        $obatPeriodsEmergency = [];
        foreach ($keluhanDataEmergency as $keluhan) {
            $periode = $keluhan->rekamMedisEmergency->tanggal_periksa->format('m-y');
            $obatPeriodsEmergency[] = [
                'id_obat' => $keluhan->id_obat,
                'periode' => $periode,
            ];
        }

        // Get unique combinations to avoid duplicates for reguler
        $uniqueObatPeriodsReguler = collect($obatPeriodsReguler)->unique(function ($item) {
            return $item['id_obat'].'_'.$item['periode'];
        })->values()->toArray();

        // Get unique combinations to avoid duplicates for emergency
        $uniqueObatPeriodsEmergency = collect($obatPeriodsEmergency)->unique(function ($item) {
            return $item['id_obat'].'_'.$item['periode'];
        })->values()->toArray();

        // Use bulk fallback method for optimized performance for reguler
        $hargaObatResultsReguler = HargaObatPerBulan::getBulkHargaObatWithFallback($uniqueObatPeriodsReguler);

        // Use bulk fallback method for optimized performance for emergency
        $hargaObatResultsEmergency = HargaObatPerBulan::getBulkHargaObatWithFallback($uniqueObatPeriodsEmergency);

        // Create a lookup map for reguler
        $hargaObatMapReguler = [];
        foreach ($hargaObatResultsReguler as $key => $result) {
            if ($result && $result['harga']) {
                $hargaObatMapReguler[$key] = $result['harga'];
            }
        }

        // Create a lookup map for emergency
        $hargaObatMapEmergency = [];
        foreach ($hargaObatResultsEmergency as $key => $result) {
            if ($result && $result['harga']) {
                $hargaObatMapEmergency[$key] = $result['harga'];
            }
        }

        // Group by month and calculate total using pre-fetched harga for reguler
        $monthlyDataReguler = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlyDataReguler[$i] = 0;
        }

        foreach ($keluhanDataReguler as $keluhan) {
            $month = $keluhan->rekamMedis->tanggal_periksa->format('n');
            $periode = $keluhan->rekamMedis->tanggal_periksa->format('m-y');
            $key = $keluhan->id_obat.'_'.$periode;

            // Get harga obat from our pre-fetched map
            $hargaObat = $hargaObatMapReguler[$key] ?? null;

            if ($hargaObat) {
                $hargaSebelumDiskon = $keluhan->jumlah_obat * $hargaObat->harga_per_satuan;
                $diskon = $keluhan->diskon ?? 0;
                $hargaSetelahDiskon = $hargaSebelumDiskon * (1 - ($diskon / 100));
                $monthlyDataReguler[$month] += $hargaSetelahDiskon;
            }
        }

        // Group by month and calculate total using pre-fetched harga for emergency
        $monthlyDataEmergency = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlyDataEmergency[$i] = 0;
        }

        foreach ($keluhanDataEmergency as $keluhan) {
            $month = $keluhan->rekamMedisEmergency->tanggal_periksa->format('n');
            $periode = $keluhan->rekamMedisEmergency->tanggal_periksa->format('m-y');
            $key = $keluhan->id_obat.'_'.$periode;

            // Get harga obat from our pre-fetched map
            $hargaObat = $hargaObatMapEmergency[$key] ?? null;

            if ($hargaObat) {
                $monthlyDataEmergency[$month] += $keluhan->jumlah_obat * $hargaObat->harga_per_satuan;
            }
        }

        // Format data untuk chart (12 bulan)
        $chartDataReguler = [];
        $chartDataEmergency = [];
        $chartDataTotal = [];
        for ($i = 1; $i <= 12; $i++) {
            $reguler = $monthlyDataReguler[$i] ?? 0;
            $emergency = $monthlyDataEmergency[$i] ?? 0;
            $chartDataReguler[] = $reguler;
            $chartDataEmergency[] = $emergency;
            $chartDataTotal[] = $reguler + $emergency;
        }

        return [
            'reguler' => $chartDataReguler,
            'emergency' => $chartDataEmergency,
            'total' => $chartDataTotal,
        ];
    }
}
