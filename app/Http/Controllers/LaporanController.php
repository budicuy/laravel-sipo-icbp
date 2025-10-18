<?php

namespace App\Http\Controllers;

use App\Models\Kunjungan;
use App\Models\RekamMedis;
use App\Models\Keluhan;
use App\Models\HargaObatPerBulan;
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
        // Single query untuk semua bulan menggunakan raw expression
        $monthlyData = RekamMedis::selectRaw('MONTH(tanggal_periksa) as month, COUNT(*) as count')
            ->whereYear('tanggal_periksa', $tahun)
            ->groupByRaw('MONTH(tanggal_periksa)')
            ->pluck('count', 'month')
            ->toArray();

        // Format data untuk chart (12 bulan)
        $chartData = [];
        for ($i = 1; $i <= 12; $i++) {
            $chartData[] = $monthlyData[$i] ?? 0;
        }

        return $chartData;
    }

    /**
     * Get chart data untuk total biaya per bulan
     */
    private function getChartBiaya($tahun)
    {
        // Single query dengan JOIN untuk menghindari N+1 problems
        // Menggunakan harga obat per bulan
        $monthlyData = Keluhan::selectRaw('MONTH(rm.tanggal_periksa) as month, SUM(k.jumlah_obat * h.harga_per_satuan) as total')
            ->from('keluhan as k')
            ->join('rekam_medis as rm', 'k.id_rekam', '=', 'rm.id_rekam')
            ->join('harga_obat_per_bulan as h', function($join) {
                $join->on('k.id_obat', '=', 'h.id_obat')
                     ->on(DB::raw("DATE_FORMAT(rm.tanggal_periksa, '%m-%y')"), '=', 'h.periode');
            })
            ->whereYear('rm.tanggal_periksa', $tahun)
            ->whereNotNull('k.id_obat')
            ->groupByRaw('MONTH(rm.tanggal_periksa)')
            ->pluck('total', 'month')
            ->toArray();

        // Format data untuk chart (12 bulan)
        $chartData = [];
        for ($i = 1; $i <= 12; $i++) {
            $chartData[] = $monthlyData[$i] ?? 0;
        }

        return $chartData;
    }

    /**
     * Get data transaksi untuk tabel
     */
    private function getTransaksiData($tanggal_dari, $tanggal_sampai)
    {
        // Optimized query with specific columns and eager loading
        $rekamMedisData = RekamMedis::with([
                'keluarga' => function($query) {
                    $query->select('id_keluarga', 'id_karyawan', 'nama_keluarga', 'no_rm', 'kode_hubungan')
                          ->with(['karyawan:id_karyawan,nik_karyawan,nama_karyawan'])
                          ->with(['hubungan:kode_hubungan,hubungan']);
                },
                'keluhans' => function($query) {
                    $query->select('id_keluhan', 'id_rekam', 'id_diagnosa', 'id_obat', 'jumlah_obat')
                          ->with(['diagnosa:id_diagnosa,nama_diagnosa'])
                          ->with(['obat:id_obat,nama_obat']);
                },
                'user:id_user,username,nama_lengkap'
            ])
            ->select('id_rekam', 'id_keluarga', 'tanggal_periksa', 'status', 'id_user')
            ->whereBetween('tanggal_periksa', [$tanggal_dari, $tanggal_sampai])
            ->orderBy('tanggal_periksa', 'desc')
            ->get();

        // Collect all unique obat IDs and periods to prevent duplicate queries
        $obatPeriods = [];
        foreach ($rekamMedisData as $rekamMedis) {
            $periode = $rekamMedis->tanggal_periksa->format('m-y');
            foreach ($rekamMedis->keluhans as $keluhan) {
                if ($keluhan->id_obat) {
                    $obatPeriods[] = [
                        'id_obat' => $keluhan->id_obat,
                        'periode' => $periode
                    ];
                }
            }
        }

        // Fetch all harga obat data in one query
        $hargaObatMap = [];
        if (!empty($obatPeriods)) {
            // Get unique combinations to avoid duplicates
            $uniqueObatPeriods = collect($obatPeriods)->unique(function ($item) {
                return $item['id_obat'] . '_' . $item['periode'];
            })->values()->toArray();

            // Fetch harga obat for all combinations at once
            $hargaObatData = HargaObatPerBulan::whereIn(function($query) use ($uniqueObatPeriods) {
                foreach ($uniqueObatPeriods as $index => $item) {
                    if ($index === 0) {
                        $query->where(function($q) use ($item) {
                            $q->where('id_obat', $item['id_obat'])
                              ->where('periode', $item['periode']);
                        });
                    } else {
                        $query->orWhere(function($q) use ($item) {
                            $q->where('id_obat', $item['id_obat'])
                              ->where('periode', $item['periode']);
                        });
                    }
                }
            })->get();

            // Create a lookup map for easy access
            foreach ($hargaObatData as $harga) {
                $key = $harga->id_obat . '_' . $harga->periode;
                $hargaObatMap[$key] = $harga;
            }

            // Get fallback harga obat (latest price) for any obat that doesn't have price for specific period
            $obatIds = collect($uniqueObatPeriods)->pluck('id_obat')->unique()->toArray();
            $fallbackHargaObat = HargaObatPerBulan::whereIn('id_obat', $obatIds)
                ->orderByRaw("SUBSTRING(periode, 4, 2) DESC, SUBSTRING(periode, 1, 2) DESC")
                ->get()
                ->groupBy('id_obat')
                ->map(function($group) {
                    return $group->first();
                });

            // Add fallback prices to the map
            foreach ($fallbackHargaObat as $idObat => $harga) {
                foreach ($uniqueObatPeriods as $item) {
                    if ($item['id_obat'] == $idObat) {
                        $key = $idObat . '_' . $item['periode'];
                        if (!isset($hargaObatMap[$key])) {
                            $hargaObatMap[$key] = $harga;
                        }
                    }
                }
            }
        }

        // Prepare bulk upsert data for kunjungan
        $kunjunganUpsertData = [];
        $kunjunganKeyMap = [];

        foreach ($rekamMedisData as $rekamMedis) {
            $key = $rekamMedis->id_keluarga . '_' . $rekamMedis->tanggal_periksa->format('Y-m-d');
            // Generate kode_transaksi format: 1(No Running)/NDL/BJM/MM/YYYY
            $noRunning = str_pad($rekamMedis->id_rekam, 1, '0', STR_PAD_LEFT);
            $bulan = $rekamMedis->tanggal_periksa->format('m');
            $tahun = $rekamMedis->tanggal_periksa->format('Y');
            $kodeTransaksi = "1{$noRunning}/NDL/BJM/{$bulan}/{$tahun}";

            $kunjunganUpsertData[] = [
                'id_keluarga' => $rekamMedis->id_keluarga,
                'tanggal_kunjungan' => $rekamMedis->tanggal_periksa,
                'kode_transaksi' => $kodeTransaksi,
                'created_at' => now()
            ];

            $kunjunganKeyMap[$key] = $kodeTransaksi;
        }

        // Bulk upsert all kunjungan records at once
        if (!empty($kunjunganUpsertData)) {
            Kunjungan::upsert($kunjunganUpsertData, ['id_keluarga', 'tanggal_kunjungan'], ['kode_transaksi']);
        }

        // Get all kunjungan IDs in one query
        $kunjunganKeys = array_keys($kunjunganKeyMap);
        $kunjunganConditions = [];
        foreach ($kunjunganKeys as $key) {
            list($idKeluarga, $tanggal) = explode('_', $key);
            $kunjunganConditions[] = "(id_keluarga = {$idKeluarga} AND DATE(tanggal_kunjungan) = '{$tanggal}')";
        }

        $kunjunganIdMap = [];
        if (!empty($kunjunganConditions)) {
            $kunjunganRecords = DB::select("
                SELECT id_keluarga, DATE(tanggal_kunjungan) as tanggal, id_kunjungan
                FROM kunjungan
                WHERE " . implode(' OR ', $kunjunganConditions)
            );

            foreach ($kunjunganRecords as $record) {
                $key = $record->id_keluarga . '_' . $record->tanggal;
                $kunjunganIdMap[$key] = $record->id_kunjungan;
            }
        }

        return $rekamMedisData->map(function($rekamMedis) use ($kunjunganIdMap, $kunjunganKeyMap, $hargaObatMap) {
            // Generate kode_transaksi format: 1(No Running)/NDL/BJM/MM/YYYY
            $noRunning = str_pad($rekamMedis->id_rekam, 1, '0', STR_PAD_LEFT);
            $bulan = $rekamMedis->tanggal_periksa->format('m');
            $tahun = $rekamMedis->tanggal_periksa->format('Y');

            // Get kode_transaksi from map
            $kunjunganKey = $rekamMedis->id_keluarga . '_' . $rekamMedis->tanggal_periksa->format('Y-m-d');
            $kodeTransaksi = $kunjunganKeyMap[$kunjunganKey] ?? "1{$noRunning}/NDL/BJM/{$bulan}/{$tahun}";

            // Get keluhan untuk menghitung total biaya dan dapatkan diagnosa + obat
            $keluhans = $rekamMedis->keluhans;
            $periode = $rekamMedis->tanggal_periksa->format('m-y');

            $totalBiaya = $keluhans->sum(function($keluhan) use ($periode, $hargaObatMap) {
                if (!$keluhan->id_obat) return 0;

                // Get harga obat from our pre-fetched map
                $key = $keluhan->id_obat . '_' . $periode;
                $hargaObat = $hargaObatMap[$key] ?? null;

                return $keluhan->jumlah_obat * ($hargaObat->harga_per_satuan ?? 0);
            });

            $diagnosaList = $keluhans->pluck('diagnosa.nama_diagnosa')->filter()->unique()->implode(', ');
            $obatList = $keluhans->pluck('obat.nama_obat')->filter()->unique()->implode(', ');

            // Get kunjungan ID from optimized map
            $kunjunganId = $kunjunganIdMap[$kunjunganKey] ?? null;

            return [
                'id_kunjungan' => $kunjunganId,
                'kode_transaksi' => $kodeTransaksi,
                'no_rm' => ($rekamMedis->keluarga->karyawan->nik_karyawan ?? '') . '-' . ($rekamMedis->keluarga->kode_hubungan ?? ''),
                'nama_pasien' => $rekamMedis->keluarga->nama_keluarga,
                'hubungan' => $rekamMedis->keluarga->hubungan->hubungan ?? '-',
                'nik_karyawan' => $rekamMedis->keluarga->karyawan->nik_karyawan ?? '-',
                'nama_karyawan' => $rekamMedis->keluarga->karyawan->nama_karyawan ?? '-',
                'tanggal' => $rekamMedis->tanggal_periksa->format('d-m-Y'),
                'diagnosa' => $diagnosaList ?: '-',
                'obat' => $obatList ?: '-',
                'total_biaya' => $totalBiaya,
                'id_rekam' => $rekamMedis->id_rekam
            ];
        })->filter();
    }

    /**
     * Get statistics untuk dashboard
     */
    private function getTransaksiStats($bulan, $tahun)
    {
        $totalPemeriksaan = RekamMedis::whereMonth('tanggal_periksa', $bulan)
            ->whereYear('tanggal_periksa', $tahun)
            ->count();

        // Single query dengan JOIN untuk menghindari N+1 problems
        // Menggunakan harga obat per bulan
        $totalBiaya = Keluhan::selectRaw('SUM(k.jumlah_obat * h.harga_per_satuan) as total')
            ->from('keluhan as k')
            ->join('rekam_medis as rm', 'k.id_rekam', '=', 'rm.id_rekam')
            ->join('harga_obat_per_bulan as h', function($join) {
                $join->on('k.id_obat', '=', 'h.id_obat')
                     ->on(DB::raw("DATE_FORMAT(rm.tanggal_periksa, '%m-%y')"), '=', 'h.periode');
            })
            ->whereMonth('rm.tanggal_periksa', $bulan)
            ->whereYear('rm.tanggal_periksa', $tahun)
            ->whereNotNull('k.id_obat')
            ->value('total');

        return [
            'total_pemeriksaan' => $totalPemeriksaan,
            'total_biaya' => $totalBiaya ?? 0,
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
                'keluarga' => function($query) {
                    $query->select('id_keluarga', 'id_karyawan', 'nama_keluarga', 'no_rm', 'kode_hubungan', 'tanggal_lahir')
                          ->with(['karyawan:id_karyawan,nik_karyawan,nama_karyawan,id_departemen'])
                          ->with(['karyawan.departemen:id_departemen,nama_departemen'])
                          ->with(['hubungan:kode_hubungan,hubungan']);
                },
                'keluhans' => function($query) {
                    $query->select('id_keluhan', 'id_rekam', 'id_diagnosa', 'id_obat', 'jumlah_obat', 'aturan_pakai')
                          ->with(['diagnosa:id_diagnosa,nama_diagnosa'])
                          ->with(['obat:id_obat,nama_obat,id_satuan'])
                          ->with(['obat.satuanObat:id_satuan,nama_satuan']);
                },
                'user:id_user,username,nama_lengkap'
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

        // Add custom attributes to kunjungan object to match format in kunjungan page
        $kunjungan->no_rm = ($rekamMedis->keluarga->karyawan->nik_karyawan ?? '') . '-' . ($rekamMedis->keluarga->kode_hubungan ?? '');
        $kunjungan->nama_pasien = $rekamMedis->keluarga->nama_keluarga ?? '-';
        $kunjungan->hubungan = $rekamMedis->keluarga->hubungan->hubungan ?? '-';

        // Optimized harga obat fetching - collect all unique obat IDs first
        $periode = $rekamMedis->tanggal_periksa->format('m-y');
        $obatIds = $rekamMedis->keluhans->pluck('id_obat')->filter()->unique()->toArray();

        // Fetch all harga obat data in one query
        $hargaObatMap = [];
        if (!empty($obatIds)) {
            // Get harga obat for current period
            $hargaObatData = HargaObatPerBulan::whereIn('id_obat', $obatIds)
                ->where('periode', $periode)
                ->get()
                ->keyBy('id_obat');

            // Get fallback harga obat (latest price) for any obat that doesn't have price for current period
            $missingObatIds = array_diff($obatIds, $hargaObatData->keys()->toArray());
            if (!empty($missingObatIds)) {
                $fallbackHargaObat = HargaObatPerBulan::whereIn('id_obat', $missingObatIds)
                    ->orderByRaw("SUBSTRING(periode, 4, 2) DESC, SUBSTRING(periode, 1, 2) DESC")
                    ->get()
                    ->groupBy('id_obat')
                    ->map(function($group) {
                        return $group->first();
                    });

                // Merge with current period prices
                $hargaObatMap = $hargaObatData->merge($fallbackHargaObat);
            } else {
                $hargaObatMap = $hargaObatData;
            }
        }

        // Calculate total biaya using pre-fetched harga data
        $totalBiaya = $rekamMedis->keluhans->sum(function($keluhan) use ($hargaObatMap) {
            if (!$keluhan->id_obat) return 0;

            $hargaObat = $hargaObatMap->get($keluhan->id_obat);
            return $keluhan->jumlah_obat * ($hargaObat->harga_per_satuan ?? 0);
        });

        // Group keluhan by diagnosa, only include those with obat
        $keluhanByDiagnosa = $rekamMedis->keluhans
            ->filter(function($keluhan) {
                return $keluhan->id_obat !== null && $keluhan->obat !== null;
            })
            ->groupBy(function($keluhan) {
                return $keluhan->diagnosa->nama_diagnosa ?? 'Unknown';
            })
            ->map(function($keluhans) use ($hargaObatMap) {
                // Attach harga information to each keluhan using pre-fetched data
                return $keluhans->map(function($keluhan) use ($hargaObatMap) {
                    $hargaObat = $hargaObatMap->get($keluhan->id_obat);
                    // Add harga_satuan attribute to keluhan object
                    $keluhan->harga_satuan = $hargaObat->harga_per_satuan ?? 0;
                    return $keluhan;
                });
            });

        return view('laporan.detail-transaksi', compact(
            'rekamMedis',
            'kunjungan',
            'totalBiaya',
            'keluhanByDiagnosa'
        ));
    }

    /**
     * Cetak detail transaksi ke PDF
     */
    public function cetakDetailTransaksi($id)
    {
        $rekamMedis = RekamMedis::with([
                'keluarga' => function($query) {
                    $query->select('id_keluarga', 'id_karyawan', 'nama_keluarga', 'no_rm', 'kode_hubungan', 'tanggal_lahir', 'alamat')
                          ->with(['karyawan:id_karyawan,nik_karyawan,nama_karyawan,id_departemen'])
                          ->with(['karyawan.departemen:id_departemen,nama_departemen'])
                          ->with(['hubungan:kode_hubungan,hubungan']);
                },
                'keluhans' => function($query) {
                    $query->select('id_keluhan', 'id_rekam', 'id_diagnosa', 'id_obat', 'jumlah_obat', 'aturan_pakai')
                          ->with(['diagnosa:id_diagnosa,nama_diagnosa'])
                          ->with(['obat:id_obat,nama_obat,id_satuan'])
                          ->with(['obat.satuanObat:id_satuan,nama_satuan']);
                },
                'user:id_user,username,nama_lengkap'
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

        // Add custom attributes to kunjungan object to match format in kunjungan page
        $kunjungan->no_rm = ($rekamMedis->keluarga->karyawan->nik_karyawan ?? '') . '-' . ($rekamMedis->keluarga->kode_hubungan ?? '');
        $kunjungan->nama_pasien = $rekamMedis->keluarga->nama_keluarga ?? '-';
        $kunjungan->hubungan = $rekamMedis->keluarga->hubungan->hubungan ?? '-';

        // Optimized harga obat fetching - collect all unique obat IDs first
        $periode = $rekamMedis->tanggal_periksa->format('m-y');
        $obatIds = $rekamMedis->keluhans->pluck('id_obat')->filter()->unique()->toArray();

        // Fetch all harga obat data in one query
        $hargaObatMap = [];
        if (!empty($obatIds)) {
            // Get harga obat for current period
            $hargaObatData = HargaObatPerBulan::whereIn('id_obat', $obatIds)
                ->where('periode', $periode)
                ->get()
                ->keyBy('id_obat');

            // Get fallback harga obat (latest price) for any obat that doesn't have price for current period
            $missingObatIds = array_diff($obatIds, $hargaObatData->keys()->toArray());
            if (!empty($missingObatIds)) {
                $fallbackHargaObat = HargaObatPerBulan::whereIn('id_obat', $missingObatIds)
                    ->orderByRaw("SUBSTRING(periode, 4, 2) DESC, SUBSTRING(periode, 1, 2) DESC")
                    ->get()
                    ->groupBy('id_obat')
                    ->map(function($group) {
                        return $group->first();
                    });

                // Merge with current period prices
                $hargaObatMap = $hargaObatData->merge($fallbackHargaObat);
            } else {
                $hargaObatMap = $hargaObatData;
            }
        }

        // Calculate total biaya using pre-fetched harga data
        $totalBiaya = $rekamMedis->keluhans->sum(function($keluhan) use ($hargaObatMap) {
            if (!$keluhan->id_obat) return 0;

            $hargaObat = $hargaObatMap->get($keluhan->id_obat);
            return $keluhan->jumlah_obat * ($hargaObat->harga_per_satuan ?? 0);
        });

        // Group keluhan by diagnosa, only include those with obat
        $keluhanByDiagnosa = $rekamMedis->keluhans
            ->filter(function($keluhan) {
                return $keluhan->id_obat !== null && $keluhan->obat !== null;
            })
            ->groupBy(function($keluhan) {
                return $keluhan->diagnosa->nama_diagnosa ?? 'Unknown';
            })
            ->map(function($keluhans) use ($hargaObatMap) {
                // Attach harga information to each keluhan using pre-fetched data
                return $keluhans->map(function($keluhan) use ($hargaObatMap) {
                    $hargaObat = $hargaObatMap->get($keluhan->id_obat);
                    // Add harga_satuan attribute to keluhan object
                    $keluhan->harga_satuan = $hargaObat->harga_per_satuan ?? 0;
                    return $keluhan;
                });
            });

        // Load PDF view
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('laporan.cetak-detail-transaksi', compact(
            'rekamMedis',
            'kunjungan',
            'totalBiaya',
            'keluhanByDiagnosa'
        ));

        // Set paper size to A4 portrait
        $pdf->setPaper('A4', 'portrait');

        // Download PDF - replace "/" with "-" to avoid filename error
        $safeFilename = str_replace('/', '-', $kodeTransaksi);
        return $pdf->download('Detail_Transaksi_' . $safeFilename . '.pdf');
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
