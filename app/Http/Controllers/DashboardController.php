<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\RekamMedis;
use App\Models\RekamMedisEmergency;
use App\Models\Kunjungan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

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

            $weeklyLabels[] = $weekStart->format('d M') . ' - ' . $weekEnd->format('d M');
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
}
