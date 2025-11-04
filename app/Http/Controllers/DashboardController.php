<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Kunjungan;
use App\Models\RekamMedis;
use App\Models\RekamMedisEmergency;
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

        $statistics = [
            'total_karyawan' => Karyawan::where('status', 'aktif')->count(),
            'total_rekam_medis' => $totalRekamMedisReguler + $totalRekamMedisEmergency,
            'kunjungan_hari_ini' => $kunjunganHariIniReguler + $kunjunganHariIniEmergency,
            'on_progress' => $onProgressReguler + $onProgressEmergency,
            'close' => $closeReguler + $closeEmergency,
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

        return response()->json([
            'daily' => $dailyVisits,
            'weekly' => $weeklyVisits,
            'monthly' => $monthlyVisits,
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
}
