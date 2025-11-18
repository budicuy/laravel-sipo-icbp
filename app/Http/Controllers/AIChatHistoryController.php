<?php

namespace App\Http\Controllers;

use App\Models\AIChatHistory;
use Illuminate\Http\Request;

class AIChatHistoryController extends Controller
{
    /**
     * Display AI Chat History dashboard with statistics
     */
    public function index()
    {
        $statistics = AIChatHistory::getStatistics();
        $userHistories = AIChatHistory::getUserHistory(15);

        return view('ai-chat-history.index', compact('statistics', 'userHistories'));
    }

    /**
     * Display detailed information for a specific user
     */
    public function show($nik)
    {
        $userDetail = AIChatHistory::getUserDetail($nik);

        if (!$userDetail) {
            return redirect()->route('ai-chat-history.index')
                ->with('error', 'Data user tidak ditemukan');
        }

        return view('ai-chat-history.show', compact('userDetail'));
    }

    /**
     * Search users by name or NIK
     */
    public function search(Request $request)
    {
        $query = $request->get('q');

        if (empty($query)) {
            return response()->json([
                'success' => false,
                'message' => 'Query pencarian tidak boleh kosong'
            ]);
        }

        $users = AIChatHistory::searchUsers($query);

        return response()->json([
            'success' => true,
            'data' => $users->map(function ($user) {
                return [
                    'nik' => $user->nik,
                    'nama_karyawan' => $user->nama_karyawan,
                    'display_name' => $user->display_name,
                    'departemen' => $user->departemen,
                    'tipe_pengguna' => $user->tipe_pengguna,
                    'tipe_pengguna_label' => $user->tipe_pengguna_label,
                    'kode_hubungan' => $user->kode_hubungan,
                    'hubungan_label' => $user->hubungan_label,
                    'login_count' => $user->login_count,
                    'ai_chat_access_count' => $user->ai_chat_access_count,
                    'last_login_at' => $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : '-',
                    'last_ai_chat_access_at' => $user->last_ai_chat_access_at ? $user->last_ai_chat_access_at->format('d/m/Y H:i') : '-',
                ];
            })
        ]);
    }

    /**
     * Get statistics for API (for dashboard widgets)
     */
    public function getStatistics()
    {
        $statistics = AIChatHistory::getStatistics();

        return response()->json([
            'success' => true,
            'data' => $statistics
        ]);
    }

    /**
     * Export user history to CSV
     */
    public function export()
    {
        $userHistories = AIChatHistory::orderBy('last_ai_chat_access_at', 'desc')->get();

        $csvData = [];
        $csvData[] = ['NIK', 'Nama', 'Tipe', 'Hubungan', 'Departemen', 'Jumlah Login', 'Terakhir Login', 'Jumlah Akses AI Chat', 'Terakhir Akses AI Chat'];

        foreach ($userHistories as $history) {
            $csvData[] = [
                $history->nik,
                $history->nama_karyawan,
                $history->tipe_pengguna_label,
                $history->hubungan_label,
                $history->departemen ?? '-',
                $history->login_count,
                $history->last_login_at ? $history->last_login_at->format('d/m/Y H:i') : '-',
                $history->ai_chat_access_count,
                $history->last_ai_chat_access_at ? $history->last_ai_chat_access_at->format('d/m/Y H:i') : '-',
            ];
        }

        $filename = 'ai-chat-history-' . date('Y-m-d-H-i-s') . '.csv';

        $handle = fopen('php://output', 'w');
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        foreach ($csvData as $row) {
            fputcsv($handle, $row);
        }

        fclose($handle);
        exit;
    }

    /**
     * Get chart data for visualization
     */
    public function getChartData(Request $request)
    {
        $period = $request->get('period', 'week'); // week, month, year

        $data = [];
        $labels = [];

        switch ($period) {
            case 'week':
                // Last 7 days
                for ($i = 6; $i >= 0; $i--) {
                    $date = now()->subDays($i);
                    $labels[] = $date->format('D');
                    $data[] = AIChatHistory::whereDate('last_ai_chat_access_at', $date->format('Y-m-d'))->count();
                }
                break;

            case 'month':
                // Last 30 days
                for ($i = 29; $i >= 0; $i--) {
                    $date = now()->subDays($i);
                    $labels[] = $date->format('d/m');
                    $data[] = AIChatHistory::whereDate('last_ai_chat_access_at', $date->format('Y-m-d'))->count();
                }
                break;

            case 'year':
                // Last 12 months
                for ($i = 11; $i >= 0; $i--) {
                    $date = now()->subMonths($i);
                    $labels[] = $date->format('M');
                    $data[] = AIChatHistory::whereMonth('last_ai_chat_access_at', $date->month)
                        ->whereYear('last_ai_chat_access_at', $date->year)
                        ->count();
                }
                break;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'labels' => $labels,
                'data' => $data,
            ]
        ]);
    }
}
