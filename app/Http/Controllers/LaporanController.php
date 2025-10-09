<?php

namespace App\Http\Controllers;

use App\Models\Kunjungan;
use App\Models\RekamMedis;
use App\Models\Keluhan;
use App\Models\Keluarga;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LaporanController extends Controller
{
    /**
     * Display laporan transaksi page
     */
    public function transaksi(Request $request)
    {
        // Get filter parameters
        $tahun = $request->get('tahun', date('Y'));
        $bulan = $request->get('bulan', date('m'));
        $tanggal_dari = $request->get('tanggal_dari', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $tanggal_sampai = $request->get('tanggal_sampai', Carbon::now()->endOfMonth()->format('Y-m-d'));

        // Get data untuk charts
        $chartPemeriksaan = $this->getChartPemeriksaan($tahun);
        $chartBiaya = $this->getChartBiaya($tahun);

        // Get data untuk tabel transaksi
        $transaksi = $this->getTransaksiData($tanggal_dari, $tanggal_sampai);

        // Get statistics
        $stats = $this->getTransaksiStats($bulan, $tahun);

        return view('laporan.transaksi', compact(
            'transaksi',
            'chartPemeriksaan',
            'chartBiaya',
            'stats',
            'tahun',
            'bulan',
            'tanggal_dari',
            'tanggal_sampai'
        ));
    }

    /**
     * Get chart data untuk jumlah pemeriksaan per bulan
     */
    private function getChartPemeriksaan($tahun)
    {
        $data = RekamMedis::select(
                DB::raw('MONTH(tanggal_periksa) as bulan'),
                DB::raw('COUNT(*) as jumlah')
            )
            ->whereYear('tanggal_periksa', $tahun)
            ->groupBy(DB::raw('MONTH(tanggal_periksa)'))
            ->orderBy(DB::raw('MONTH(tanggal_periksa)'))
            ->pluck('jumlah', 'bulan')
            ->toArray();

        // Format data untuk chart (12 bulan)
        $chartData = [];
        for ($i = 1; $i <= 12; $i++) {
            $chartData[] = $data[$i] ?? 0;
        }

        return $chartData;
    }

    /**
     * Get chart data untuk total biaya per bulan
     */
    private function getChartBiaya($tahun)
    {
        $data = Keluhan::join('rekam_medis', 'keluhan.id_rekam', '=', 'rekam_medis.id_rekam')
            ->leftJoin('obat', 'keluhan.id_obat', '=', 'obat.id_obat')
            ->select(
                DB::raw('MONTH(rekam_medis.tanggal_periksa) as bulan'),
                DB::raw('SUM(keluhan.jumlah_obat * COALESCE(obat.harga_per_satuan, 0)) as total_biaya')
            )
            ->whereYear('rekam_medis.tanggal_periksa', $tahun)
            ->groupBy(DB::raw('MONTH(rekam_medis.tanggal_periksa)'))
            ->orderBy(DB::raw('MONTH(rekam_medis.tanggal_periksa)'))
            ->pluck('total_biaya', 'bulan')
            ->toArray();

        // Format data untuk chart (12 bulan)
        $chartData = [];
        for ($i = 1; $i <= 12; $i++) {
            $chartData[] = $data[$i] ?? 0;
        }

        return $chartData;
    }

    /**
     * Get data transaksi untuk tabel
     */
    private function getTransaksiData($tanggal_dari, $tanggal_sampai)
    {
        return RekamMedis::with([
                'keluarga.karyawan',
                'keluarga.hubungan',
                'keluhans.diagnosa',
                'keluhans.obat',
                'user'
            ])
            ->whereBetween('tanggal_periksa', [$tanggal_dari, $tanggal_sampai])
            ->orderBy('tanggal_periksa', 'desc')
            ->get()
            ->map(function($rekamMedis) {
                // Generate kode_transaksi format: 1(No Running)/NDL/BJM/MM/YYYY
                $noRunning = str_pad($rekamMedis->id_rekam, 1, '0', STR_PAD_LEFT);
                $bulan = $rekamMedis->tanggal_periksa->format('m');
                $tahun = $rekamMedis->tanggal_periksa->format('Y');
                $kodeTransaksi = "1{$noRunning}/NDL/BJM/{$bulan}/{$tahun}";

                // Get keluhan untuk menghitung total biaya dan dapatkan diagnosa + obat
                $keluhans = $rekamMedis->keluhans;

                $totalBiaya = $keluhans->sum(function($keluhan) {
                    return $keluhan->jumlah_obat * ($keluhan->obat->harga_per_satuan ?? 0);
                });

                $diagnosaList = $keluhans->pluck('diagnosa.nama_diagnosa')->filter()->unique()->implode(', ');
                $obatList = $keluhans->pluck('obat.nama_obat')->filter()->unique()->implode(', ');

                // Create or get kunjungan record for synchronization
                $kunjungan = Kunjungan::firstOrCreate(
                    [
                        'id_keluarga' => $rekamMedis->id_keluarga,
                        'tanggal_kunjungan' => $rekamMedis->tanggal_periksa
                    ],
                    [
                        'kode_transaksi' => $kodeTransaksi
                    ]
                );

                return [
                    'id_kunjungan' => $kunjungan->id_kunjungan,
                    'kode_transaksi' => $kodeTransaksi,
                    'no_rm' => $rekamMedis->keluarga->no_rm,
                    'nama_pasien' => $rekamMedis->keluarga->nama_keluarga,
                    'hubungan' => $rekamMedis->keluarga->hubungan->nama_hubungan ?? '-',
                    'nik_karyawan' => $rekamMedis->keluarga->karyawan->nik_karyawan ?? '-',
                    'nama_karyawan' => $rekamMedis->keluarga->karyawan->nama_karyawan ?? '-',
                    'tanggal' => $rekamMedis->tanggal_periksa->format('d-m-Y'),
                    'diagnosa' => $diagnosaList ?: '-',
                    'obat' => $obatList ?: '-',
                    'total_biaya' => $totalBiaya,
                    'id_rekam' => $rekamMedis->id_rekam
                ];
            })
            ->filter();
    }

    /**
     * Get statistics untuk dashboard
     */
    private function getTransaksiStats($bulan, $tahun)
    {
        $totalPemeriksaan = RekamMedis::whereMonth('tanggal_periksa', $bulan)
            ->whereYear('tanggal_periksa', $tahun)
            ->count();

        $totalBiaya = Keluhan::join('rekam_medis', 'keluhan.id_rekam', '=', 'rekam_medis.id_rekam')
            ->leftJoin('obat', 'keluhan.id_obat', '=', 'obat.id_obat')
            ->whereMonth('rekam_medis.tanggal_periksa', $bulan)
            ->whereYear('rekam_medis.tanggal_periksa', $tahun)
            ->sum(DB::raw('keluhan.jumlah_obat * COALESCE(obat.harga_per_satuan, 0)'));

        return [
            'total_pemeriksaan' => $totalPemeriksaan,
            'total_biaya' => $totalBiaya,
            'bulan_nama' => $this->getBulanNama($bulan),
            'tahun' => $tahun
        ];
    }

    /**
     * Detail transaksi
     */
    public function detailTransaksi($id)
    {
        $rekamMedis = RekamMedis::with([
                'keluarga.karyawan',
                'keluarga.hubungan',
                'keluhans.diagnosa',
                'keluhans.obat',
                'user'
            ])
            ->findOrFail($id);

        // Generate kode_transaksi format: 1(No Running)/NDL/BJM/MM/YYYY
        $noRunning = str_pad($rekamMedis->id_rekam, 1, '0', STR_PAD_LEFT);
        $bulan = $rekamMedis->tanggal_periksa->format('m');
        $tahun = $rekamMedis->tanggal_periksa->format('Y');
        $kodeTransaksi = "1{$noRunning}/NDL/BJM/{$bulan}/{$tahun}";

        // Create or get kunjungan record for synchronization
        $kunjungan = Kunjungan::firstOrCreate(
            [
                'id_keluarga' => $rekamMedis->id_keluarga,
                'tanggal_kunjungan' => $rekamMedis->tanggal_periksa
            ],
            [
                'kode_transaksi' => $kodeTransaksi
            ]
        );

        // Calculate total biaya
        $totalBiaya = $rekamMedis->keluhans->sum(function($keluhan) {
            return $keluhan->jumlah_obat * ($keluhan->obat->harga_per_satuan ?? 0);
        });

        // Group keluhan by diagnosa, only include those with obat
        $keluhanByDiagnosa = $rekamMedis->keluhans
            ->filter(function($keluhan) {
                return $keluhan->id_obat !== null && $keluhan->obat !== null;
            })
            ->groupBy(function($keluhan) {
                return $keluhan->diagnosa->nama_diagnosa ?? 'Unknown';
            });

        return view('laporan.detail-transaksi', compact(
            'rekamMedis',
            'kunjungan',
            'totalBiaya',
            'keluhanByDiagnosa'
        ));
    }

    /**
     * Export laporan transaksi ke Excel
     */
    public function exportTransaksi(Request $request)
    {
        // Implementation for Excel export
        // This would require a package like maatwebsite/excel
        return redirect()->back()->with('success', 'Fitur export akan segera tersedia');
    }

    /**
     * Helper function untuk get nama bulan
     */
    private function getBulanNama($bulan)
    {
        $bulanNama = [
            '1' => 'Januari',
            '2' => 'Februari',
            '3' => 'Maret',
            '4' => 'April',
            '5' => 'Mei',
            '6' => 'Juni',
            '7' => 'Juli',
            '8' => 'Agustus',
            '9' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember'
        ];

        return $bulanNama[$bulan] ?? 'Unknown';
    }
}
