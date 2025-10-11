<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\RekamMedis;
use App\Models\Kunjungan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Get dashboard statistics
     */
    public function getStatistics()
    {
        $totalKaryawan = Karyawan::count();
        $totalRekamMedis = RekamMedis::count();
        $kunjunganHariIni = Kunjungan::whereDate('tanggal_kunjungan', now()->toDateString())->count();
        $onProgress = RekamMedis::where('status', 'On Orogres')->count();
        $close = RekamMedis::where('status', 'Close')->count();

        return response()->json([
            'total_karyawan' => $totalKaryawan,
            'total_rekam_medis' => $totalRekamMedis,
            'kunjungan_hari_ini' => $kunjunganHariIni,
            'on_progress' => $onProgress,
            'close' => $close,
        ]);
    }

    /**
     * Get visit analysis data
     */
    public function getVisitAnalysis(Request $request)
    {
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);

        // Kunjungan Harian (dari tanggal 1 sampai akhir bulan)
        $dailyVisits = $this->getDailyVisits($month, $year);

        // Kunjungan Mingguan (per minggu dalam bulan itu)
        $weeklyVisits = $this->getWeeklyVisits($month, $year);

        // Kunjungan Bulanan (dari januari sampai desember di tahun itu)
        $monthlyVisits = $this->getMonthlyVisits($year);

        return response()->json([
            'daily' => $dailyVisits,
            'weekly' => $weeklyVisits,
            'monthly' => $monthlyVisits,
        ]);
    }

    /**
     * Get daily visits for a specific month and year
     */
    private function getDailyVisits($month, $year)
    {
        $startDate = Carbon::create($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();
        $daysInMonth = $startDate->daysInMonth;

        $dailyData = [];

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $currentDate = Carbon::create($year, $month, $day);
            $count = Kunjungan::whereDate('tanggal_kunjungan', $currentDate->toDateString())->count();
            $dailyData[] = $count;
        }

        return [
            'labels' => range(1, $daysInMonth),
            'data' => $dailyData,
        ];
    }

    /**
     * Get weekly visits for a specific month and year
     */
    private function getWeeklyVisits($month, $year)
    {
        $startDate = Carbon::create($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();

        // Generate labels and data
        $weeklyLabels = [];
        $weeklyCounts = [];
        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            $weekStart = $currentDate->copy()->startOfWeek();
            $weekEnd = $weekStart->copy()->endOfWeek()->min($endDate);

            // Count visits for this week using Laravel's whereBetween
            $count = Kunjungan::whereBetween('tanggal_kunjungan', [
                $weekStart->toDateString(),
                $weekEnd->toDateString()
            ])->count();

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
     * Get monthly visits for a specific year
     */
    private function getMonthlyVisits($year)
    {
        $monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                      'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        $data = [];
        for ($month = 1; $month <= 12; $month++) {
            $count = Kunjungan::whereMonth('tanggal_kunjungan', $month)
                ->whereYear('tanggal_kunjungan', $year)
                ->count();
            $data[] = $count;
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
