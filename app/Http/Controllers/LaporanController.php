<?php

namespace App\Http\Controllers;

use App\Models\Kunjungan;
use App\Models\RekamMedis;
use App\Models\RekamMedisEmergency;
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
        $periode = $request->get('periode', Carbon::now()->format('m-y'));
        $perPage = $request->get('per_page', 50);
        $perPage = in_array($perPage, [50, 100, 200]) ? $perPage : 50;

        // Get data untuk charts
        $chartPemeriksaan = $this->getChartPemeriksaan($tahun);
        $chartBiaya = $this->getChartBiaya($tahun);

        // Get data untuk tabel transaksi dengan pagination
        $transaksiData = $this->getTransaksiData($periode, $perPage);
        $transaksi = $transaksiData['data'];
        $fallbackNotifications = $transaksiData['fallbackNotifications'] ?? [];

        // Get statistics
        $stats = $this->getTransaksiStats($bulan, $tahun);

        return view('laporan.transaksi', compact(
            'transaksi',
            'chartPemeriksaan',
            'chartBiaya',
            'stats',
            'tahun',
            'bulan',
            'periode',
            'perPage',
            'fallbackNotifications'
        ));
    }

    /**
     * Get chart data untuk jumlah pemeriksaan per bulan
     */
    private function getChartPemeriksaan($tahun)
    {
        // Single query untuk semua bulan menggunakan raw expression
        $monthlyDataReguler = RekamMedis::selectRaw('MONTH(tanggal_periksa) as month, COUNT(*) as count')
            ->whereYear('tanggal_periksa', $tahun)
            ->groupByRaw('MONTH(tanggal_periksa)')
            ->pluck('count', 'month')
            ->toArray();
            
        // Query untuk data emergency
        $monthlyDataEmergency = RekamMedisEmergency::selectRaw('MONTH(tanggal_periksa) as month, COUNT(*) as count')
            ->whereYear('tanggal_periksa', $tahun)
            ->groupByRaw('MONTH(tanggal_periksa)')
            ->pluck('count', 'month')
            ->toArray();

        // Format data untuk chart (12 bulan)
        $chartDataReguler = [];
        $chartDataEmergency = [];
        for ($i = 1; $i <= 12; $i++) {
            $chartDataReguler[] = $monthlyDataReguler[$i] ?? 0;
            $chartDataEmergency[] = $monthlyDataEmergency[$i] ?? 0;
        }

        return [
            'reguler' => $chartDataReguler,
            'emergency' => $chartDataEmergency
        ];
    }

    /**
     * Get chart data untuk total biaya per bulan
     */
    private function getChartBiaya($tahun)
    {
        // Get all keluhan with rekamMedis for the specified year
        $keluhanDataReguler = Keluhan::with(['rekamMedis:id_rekam,tanggal_periksa'])
            ->whereHas('rekamMedis', function($query) use ($tahun) {
                $query->whereYear('tanggal_periksa', $tahun);
            })
            ->whereNotNull('id_obat')
            ->get();
            
        // Get all keluhan with rekamMedisEmergency for the specified year
        $keluhanDataEmergency = Keluhan::with(['rekamMedisEmergency:id_emergency,tanggal_periksa'])
            ->whereHas('rekamMedisEmergency', function($query) use ($tahun) {
                $query->whereYear('tanggal_periksa', $tahun);
            })
            ->whereNotNull('id_obat')
            ->get();

        // Collect all unique obat IDs and periods for reguler
        $obatPeriodsReguler = [];
        foreach ($keluhanDataReguler as $keluhan) {
            $periode = $keluhan->rekamMedis->tanggal_periksa->format('m-y');
            $obatPeriodsReguler[] = [
                'id_obat' => $keluhan->id_obat,
                'periode' => $periode
            ];
        }
        
        // Collect all unique obat IDs and periods for emergency
        $obatPeriodsEmergency = [];
        foreach ($keluhanDataEmergency as $keluhan) {
            $periode = $keluhan->rekamMedisEmergency->tanggal_periksa->format('m-y');
            $obatPeriodsEmergency[] = [
                'id_obat' => $keluhan->id_obat,
                'periode' => $periode
            ];
        }

        // Get unique combinations to avoid duplicates for reguler
        $uniqueObatPeriodsReguler = collect($obatPeriodsReguler)->unique(function ($item) {
            return $item['id_obat'] . '_' . $item['periode'];
        })->values()->toArray();
        
        // Get unique combinations to avoid duplicates for emergency
        $uniqueObatPeriodsEmergency = collect($obatPeriodsEmergency)->unique(function ($item) {
            return $item['id_obat'] . '_' . $item['periode'];
        })->values()->toArray();

        // Use the bulk fallback method for optimized performance for reguler
        $hargaObatResultsReguler = HargaObatPerBulan::getBulkHargaObatWithFallback($uniqueObatPeriodsReguler);
        
        // Use the bulk fallback method for optimized performance for emergency
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
            $key = $keluhan->id_obat . '_' . $periode;

            // Get harga obat from our pre-fetched map
            $hargaObat = $hargaObatMapReguler[$key] ?? null;

            if ($hargaObat) {
                $monthlyDataReguler[$month] += $keluhan->jumlah_obat * $hargaObat->harga_per_satuan;
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
            $key = $keluhan->id_obat . '_' . $periode;

            // Get harga obat from our pre-fetched map
            $hargaObat = $hargaObatMapEmergency[$key] ?? null;

            if ($hargaObat) {
                $monthlyDataEmergency[$month] += $keluhan->jumlah_obat * $hargaObat->harga_per_satuan;
            }
        }

        // Format data untuk chart (12 bulan)
        $chartDataReguler = [];
        $chartDataEmergency = [];
        for ($i = 1; $i <= 12; $i++) {
            $chartDataReguler[] = $monthlyDataReguler[$i] ?? 0;
            $chartDataEmergency[] = $monthlyDataEmergency[$i] ?? 0;
        }

        return [
            'reguler' => $chartDataReguler,
            'emergency' => $chartDataEmergency
        ];
    }

    /**
     * Get data transaksi untuk tabel
     */
    private function getTransaksiData($periode, $perPage = 50)
    {
        // Parse periode format MM-YY to get month and year
        if (preg_match('/^(\d{2})-(\d{2})$/', $periode, $matches)) {
            $month = (int)$matches[1];
            $year = (int)$matches[2] + 2000; // Convert YY to YYYY
        } else {
            // Default to current month if format is invalid
            $month = Carbon::now()->month;
            $year = Carbon::now()->year;
        }

        // Optimized query with specific columns and eager loading for reguler
        $rekamMedisQuery = RekamMedis::with([
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
            ->whereMonth('tanggal_periksa', $month)
            ->whereYear('tanggal_periksa', $year)
            ->orderBy('tanggal_periksa', 'desc');

        // Optimized query with specific columns and eager loading for emergency
        $rekamMedisEmergencyQuery = RekamMedisEmergency::with([
                'externalEmployee' => function($query) {
                    $query->select('id', 'nik_employee', 'nama_employee');
                },
                'keluhans' => function($query) {
                    $query->select('id_keluhan', 'id_emergency', 'id_diagnosa_emergency', 'id_obat', 'jumlah_obat')
                          ->with(['diagnosaEmergency:id_diagnosa_emergency,nama_diagnosa_emergency'])
                          ->with(['obat:id_obat,nama_obat']);
                },
                'user:id_user,username,nama_lengkap'
            ])
            ->select('id_emergency', 'id_external_employee', 'tanggal_periksa', 'status', 'id_user')
            ->whereMonth('tanggal_periksa', $month)
            ->whereYear('tanggal_periksa', $year)
            ->orderBy('tanggal_periksa', 'desc');

        // Get data for both reguler and emergency
        $rekamMedisData = $rekamMedisQuery->get();
        $rekamMedisEmergencyData = $rekamMedisEmergencyQuery->get();

        // Combine data for pagination
        $allData = $rekamMedisData->concat($rekamMedisEmergencyData);
        $currentPage = request()->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        $itemsForCurrentPage = $allData->slice($offset, $perPage)->values();
        $paginatedData = new \Illuminate\Pagination\LengthAwarePaginator(
            $itemsForCurrentPage,
            $allData->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

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
        
        foreach ($rekamMedisEmergencyData as $rekamMedisEmergency) {
            $periode = $rekamMedisEmergency->tanggal_periksa->format('m-y');
            foreach ($rekamMedisEmergency->keluhans as $keluhan) {
                if ($keluhan->id_obat) {
                    $obatPeriods[] = [
                        'id_obat' => $keluhan->id_obat,
                        'periode' => $periode
                    ];
                }
            }
        }

        // Fetch all harga obat data with fallback mechanism
        $hargaObatMap = [];
        $fallbackNotifications = [];

        if (!empty($obatPeriods)) {
            // Get unique combinations to avoid duplicates
            $uniqueObatPeriods = collect($obatPeriods)->unique(function ($item) {
                return $item['id_obat'] . '_' . $item['periode'];
            })->values()->toArray();

            // Use the new bulk fallback method for optimized performance
            $hargaObatResults = HargaObatPerBulan::getBulkHargaObatWithFallback($uniqueObatPeriods);

            // Create a lookup map and collect fallback notifications
            foreach ($hargaObatResults as $key => $result) {
                if ($result && $result['harga']) {
                    $hargaObatMap[$key] = $result['harga'];

                    // Collect notifications for fallback prices
                    if ($result['is_fallback']) {
                        $fallbackNotifications[] = [
                            'id_obat' => $result['harga']->id_obat,
                            'nama_obat' => $result['harga']->obat->nama_obat ?? 'Unknown',
                            'target_periode' => explode('_', $key)[1],
                            'source_periode' => $result['sumber_periode'],
                            'fallback_depth' => $result['fallback_depth']
                        ];
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
            $kunjunganRecords = Kunjungan::where(function($query) use ($kunjunganKeys) {
                foreach ($kunjunganKeys as $key) {
                    list($idKeluarga, $tanggal) = explode('_', $key);
                    $query->orWhere(function($q) use ($idKeluarga, $tanggal) {
                        $q->where('id_keluarga', $idKeluarga)
                          ->whereDate('tanggal_kunjungan', $tanggal);
                    });
                }
            })->get();

            foreach ($kunjunganRecords as $record) {
                $key = $record->id_keluarga . '_' . $record->tanggal;
                $kunjunganIdMap[$key] = $record->id_kunjungan;
            }
        }

        // Process reguler data
        $resultReguler = $rekamMedisData->map(function($rekamMedis) use ($kunjunganIdMap, $kunjunganKeyMap, $hargaObatMap) {
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
                'id_rekam' => $rekamMedis->id_rekam,
                'tipe' => 'Reguler'
            ];
        })->filter();
        
        // Process emergency data
        $resultEmergency = $rekamMedisEmergencyData->map(function($rekamMedisEmergency) use ($hargaObatMap) {
            // Generate kode_transaksi format: 2(No Running)/NDL/BJM/MM/YYYY
            $noRunning = str_pad($rekamMedisEmergency->id_emergency, 1, '0', STR_PAD_LEFT);
            $bulan = $rekamMedisEmergency->tanggal_periksa->format('m');
            $tahun = $rekamMedisEmergency->tanggal_periksa->format('Y');
            $kodeTransaksi = "2{$noRunning}/NDL/BJM/{$bulan}/{$tahun}";

            // Get keluhan untuk menghitung total biaya dan dapatkan diagnosa + obat
            $keluhans = $rekamMedisEmergency->keluhans;
            $periode = $rekamMedisEmergency->tanggal_periksa->format('m-y');

            $totalBiaya = $keluhans->sum(function($keluhan) use ($periode, $hargaObatMap) {
                if (!$keluhan->id_obat) return 0;

                // Get harga obat from our pre-fetched map
                $key = $keluhan->id_obat . '_' . $periode;
                $hargaObat = $hargaObatMap[$key] ?? null;

                return $keluhan->jumlah_obat * ($hargaObat->harga_per_satuan ?? 0);
            });

            $diagnosaList = $keluhans->pluck('diagnosaEmergency.nama_diagnosa_emergency')->filter()->unique()->implode(', ');
            $obatList = $keluhans->pluck('obat.nama_obat')->filter()->unique()->implode(', ');

            return [
                'id_kunjungan' => null,
                'kode_transaksi' => $kodeTransaksi,
                'no_rm' => $rekamMedisEmergency->externalEmployee->nik_employee ?? '-',
                'nama_pasien' => $rekamMedisEmergency->externalEmployee->nama_employee ?? '-',
                'hubungan' => 'External Employee',
                'nik_karyawan' => '-',
                'nama_karyawan' => '-',
                'tanggal' => $rekamMedisEmergency->tanggal_periksa->format('d-m-Y'),
                'diagnosa' => $diagnosaList ?: '-',
                'obat' => $obatList ?: '-',
                'total_biaya' => $totalBiaya,
                'id_rekam' => $rekamMedisEmergency->id_emergency,
                'tipe' => 'Emergency'
            ];
        })->filter();

        // Combine results
        $allResults = $resultReguler->concat($resultEmergency);
        
        // Sort by date descending
        $allResults = $allResults->sortByDesc(function($item) {
            return \Carbon\Carbon::createFromFormat('d-m-Y', $item['tanggal']);
        })->values();

        // Update the paginated data with processed results
        $paginatedData->setCollection($allResults);

        return [
            'data' => $paginatedData,
            'fallbackNotifications' => $fallbackNotifications
        ];
    }

    /**
     * Get statistics untuk dashboard
     */
    private function getTransaksiStats($bulan, $tahun)
    {
        $totalPemeriksaanReguler = RekamMedis::whereMonth('tanggal_periksa', $bulan)
            ->whereYear('tanggal_periksa', $tahun)
            ->count();
            
        $totalPemeriksaanEmergency = RekamMedisEmergency::whereMonth('tanggal_periksa', $bulan)
            ->whereYear('tanggal_periksa', $tahun)
            ->count();
            
        $totalPemeriksaan = $totalPemeriksaanReguler + $totalPemeriksaanEmergency;

        // Get all keluhan with rekamMedis for the specified month and year
        $keluhanDataReguler = Keluhan::with(['rekamMedis:id_rekam,tanggal_periksa'])
            ->whereHas('rekamMedis', function($query) use ($bulan, $tahun) {
                $query->whereMonth('tanggal_periksa', $bulan)
                      ->whereYear('tanggal_periksa', $tahun);
            })
            ->whereNotNull('id_obat')
            ->get();
            
        // Get all keluhan with rekamMedisEmergency for the specified month and year
        $keluhanDataEmergency = Keluhan::with(['rekamMedisEmergency:id_emergency,tanggal_periksa'])
            ->whereHas('rekamMedisEmergency', function($query) use ($bulan, $tahun) {
                $query->whereMonth('tanggal_periksa', $bulan)
                      ->whereYear('tanggal_periksa', $tahun);
            })
            ->whereNotNull('id_obat')
            ->get();

        // Collect all unique obat IDs and periods to prevent duplicate queries
        $obatPeriods = [];
        foreach ($keluhanDataReguler as $keluhan) {
            $periode = $keluhan->rekamMedis->tanggal_periksa->format('m-y');
            $obatPeriods[] = [
                'id_obat' => $keluhan->id_obat,
                'periode' => $periode
            ];
        }
        
        foreach ($keluhanDataEmergency as $keluhan) {
            $periode = $keluhan->rekamMedisEmergency->tanggal_periksa->format('m-y');
            $obatPeriods[] = [
                'id_obat' => $keluhan->id_obat,
                'periode' => $periode
            ];
        }

        // Get unique combinations to avoid duplicates
        $uniqueObatPeriods = collect($obatPeriods)->unique(function ($item) {
            return $item['id_obat'] . '_' . $item['periode'];
        })->values()->toArray();

        // Use the bulk fallback method for optimized performance
        $hargaObatResults = HargaObatPerBulan::getBulkHargaObatWithFallback($uniqueObatPeriods);

        // Create a lookup map
        $hargaObatMap = [];
        foreach ($hargaObatResults as $key => $result) {
            if ($result && $result['harga']) {
                $hargaObatMap[$key] = $result['harga'];
            }
        }

        // Calculate total biaya using pre-fetched harga
        $totalBiaya = 0;
        foreach ($keluhanDataReguler as $keluhan) {
            $periode = $keluhan->rekamMedis->tanggal_periksa->format('m-y');
            $key = $keluhan->id_obat . '_' . $periode;

            // Get harga obat from our pre-fetched map
            $hargaObat = $hargaObatMap[$key] ?? null;

            if ($hargaObat) {
                $totalBiaya += $keluhan->jumlah_obat * $hargaObat->harga_per_satuan;
            }
        }
        
        foreach ($keluhanDataEmergency as $keluhan) {
            $periode = $keluhan->rekamMedisEmergency->tanggal_periksa->format('m-y');
            $key = $keluhan->id_obat . '_' . $periode;

            // Get harga obat from our pre-fetched map
            $hargaObat = $hargaObatMap[$key] ?? null;

            if ($hargaObat) {
                $totalBiaya += $keluhan->jumlah_obat * $hargaObat->harga_per_satuan;
            }
        }

        return [
            'total_pemeriksaan' => $totalPemeriksaan,
            'total_pemeriksaan_reguler' => $totalPemeriksaanReguler,
            'total_pemeriksaan_emergency' => $totalPemeriksaanEmergency,
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

        // Fetch all harga obat data with fallback mechanism
        $hargaObatMap = [];
        $fallbackNotifications = [];

        if (!empty($obatIds)) {
            // Prepare obat periods for bulk processing
            $obatPeriods = [];
            foreach ($obatIds as $idObat) {
                $obatPeriods[] = [
                    'id_obat' => $idObat,
                    'periode' => $periode
                ];
            }

            // Use the new bulk fallback method for optimized performance
            $hargaObatResults = HargaObatPerBulan::getBulkHargaObatWithFallback($obatPeriods);

            // Create a lookup map by id_obat and collect fallback notifications
            foreach ($hargaObatResults as $key => $result) {
                if ($result && $result['harga']) {
                    $idObat = explode('_', $key)[0];
                    $hargaObatMap[$idObat] = $result['harga'];

                    // Collect notifications for fallback prices
                    if ($result['is_fallback']) {
                        $fallbackNotifications[] = [
                            'id_obat' => $result['harga']->id_obat,
                            'nama_obat' => $result['harga']->obat->nama_obat ?? 'Unknown',
                            'target_periode' => $periode,
                            'source_periode' => $result['sumber_periode'],
                            'fallback_depth' => $result['fallback_depth']
                        ];
                    }
                }
            }
        }

        // Calculate total biaya using pre-fetched harga data
        $totalBiaya = $rekamMedis->keluhans->sum(function($keluhan) use ($hargaObatMap) {
            if (!$keluhan->id_obat) return 0;

            $hargaObat = $hargaObatMap[$keluhan->id_obat] ?? null;
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
                    $hargaObat = $hargaObatMap[$keluhan->id_obat] ?? null;

                    // Add harga_satuan attribute to keluhan object
                    $keluhan->harga_satuan = $hargaObat->harga_per_satuan ?? 0;
                    return $keluhan;
                });
            });

        return view('laporan.detail-transaksi', compact(
            'rekamMedis',
            'kunjungan',
            'totalBiaya',
            'keluhanByDiagnosa',
            'fallbackNotifications'
        ));
    }

    /**
     * Detail transaksi emergency
     */
    public function detailTransaksiEmergency($id)
    {
        $rekamMedisEmergency = RekamMedisEmergency::with([
                'externalEmployee' => function($query) {
                    $query->select('id', 'nik_employee', 'nama_employee', 'alamat');
                },
                'keluhans' => function($query) {
                    $query->select('id_keluhan', 'id_emergency', 'id_diagnosa_emergency', 'id_obat', 'jumlah_obat', 'aturan_pakai')
                          ->with(['diagnosaEmergency:id_diagnosa_emergency,nama_diagnosa_emergency'])
                          ->with(['obat:id_obat,nama_obat,id_satuan'])
                          ->with(['obat.satuanObat:id_satuan,nama_satuan']);
                },
                'user:id_user,username,nama_lengkap'
            ])
            ->findOrFail($id);

        // Generate kode_transaksi format: 2(No Running)/NDL/BJM/MM/YYYY
        $noRunning = str_pad($rekamMedisEmergency->id_emergency, 1, '0', STR_PAD_LEFT);
        $bulan = $rekamMedisEmergency->tanggal_periksa->format('m');
        $tahun = $rekamMedisEmergency->tanggal_periksa->format('Y');
        $kodeTransaksi = "2{$noRunning}/NDL/BJM/{$bulan}/{$tahun}";

        // Add custom attributes to match format in kunjungan page
        $rekamMedisEmergency->no_rm = $rekamMedisEmergency->externalEmployee->nik_employee ?? '-';
        $rekamMedisEmergency->nama_pasien = $rekamMedisEmergency->externalEmployee->nama_employee ?? '-';
        $rekamMedisEmergency->hubungan = 'External Employee';

        // Optimized harga obat fetching - collect all unique obat IDs first
        $periode = $rekamMedisEmergency->tanggal_periksa->format('m-y');
        $obatIds = $rekamMedisEmergency->keluhans->pluck('id_obat')->filter()->unique()->toArray();

        // Fetch all harga obat data with fallback mechanism
        $hargaObatMap = [];
        $fallbackNotifications = [];

        if (!empty($obatIds)) {
            // Prepare obat periods for bulk processing
            $obatPeriods = [];
            foreach ($obatIds as $idObat) {
                $obatPeriods[] = [
                    'id_obat' => $idObat,
                    'periode' => $periode
                ];
            }

            // Use the new bulk fallback method for optimized performance
            $hargaObatResults = HargaObatPerBulan::getBulkHargaObatWithFallback($obatPeriods);

            // Create a lookup map by id_obat and collect fallback notifications
            foreach ($hargaObatResults as $key => $result) {
                if ($result && $result['harga']) {
                    $idObat = explode('_', $key)[0];
                    $hargaObatMap[$idObat] = $result['harga'];

                    // Collect notifications for fallback prices
                    if ($result['is_fallback']) {
                        $fallbackNotifications[] = [
                            'id_obat' => $result['harga']->id_obat,
                            'nama_obat' => $result['harga']->obat->nama_obat ?? 'Unknown',
                            'target_periode' => $periode,
                            'source_periode' => $result['sumber_periode'],
                            'fallback_depth' => $result['fallback_depth']
                        ];
                    }
                }
            }
        }

        // Calculate total biaya using pre-fetched harga data
        $totalBiaya = $rekamMedisEmergency->keluhans->sum(function($keluhan) use ($hargaObatMap) {
            if (!$keluhan->id_obat) return 0;

            $hargaObat = $hargaObatMap[$keluhan->id_obat] ?? null;
            return $keluhan->jumlah_obat * ($hargaObat->harga_per_satuan ?? 0);
        });

        // Group keluhan by diagnosa, only include those with obat
        $keluhanByDiagnosa = $rekamMedisEmergency->keluhans
            ->filter(function($keluhan) {
                return $keluhan->id_obat !== null && $keluhan->obat !== null;
            })
            ->groupBy(function($keluhan) {
                return $keluhan->diagnosaEmergency->nama_diagnosa_emergency ?? 'Unknown';
            })
            ->map(function($keluhans) use ($hargaObatMap) {
                // Attach harga information to each keluhan using pre-fetched data
                return $keluhans->map(function($keluhan) use ($hargaObatMap) {
                    $hargaObat = $hargaObatMap[$keluhan->id_obat] ?? null;

                    // Add harga_satuan attribute to keluhan object
                    $keluhan->harga_satuan = $hargaObat->harga_per_satuan ?? 0;
                    return $keluhan;
                });
            });

        return view('laporan.detail-transaksi-emergency', compact(
            'rekamMedisEmergency',
            'kodeTransaksi',
            'totalBiaya',
            'keluhanByDiagnosa',
            'fallbackNotifications'
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

        // Fetch all harga obat data with fallback mechanism
        $hargaObatMap = [];
        $fallbackNotifications = [];

        if (!empty($obatIds)) {
            // Prepare obat periods for bulk processing
            $obatPeriods = [];
            foreach ($obatIds as $idObat) {
                $obatPeriods[] = [
                    'id_obat' => $idObat,
                    'periode' => $periode
                ];
            }

            // Use the new bulk fallback method for optimized performance
            $hargaObatResults = HargaObatPerBulan::getBulkHargaObatWithFallback($obatPeriods);

            // Create a lookup map by id_obat and collect fallback notifications
            foreach ($hargaObatResults as $key => $result) {
                if ($result && $result['harga']) {
                    $idObat = explode('_', $key)[0];
                    $hargaObatMap[$idObat] = $result['harga'];

                    // Collect notifications for fallback prices
                    if ($result['is_fallback']) {
                        $fallbackNotifications[] = [
                            'id_obat' => $result['harga']->id_obat,
                            'nama_obat' => $result['harga']->obat->nama_obat ?? 'Unknown',
                            'target_periode' => $periode,
                            'source_periode' => $result['sumber_periode'],
                            'fallback_depth' => $result['fallback_depth']
                        ];
                    }
                }
            }
        }

        // Calculate total biaya using pre-fetched harga data
        $totalBiaya = $rekamMedis->keluhans->sum(function($keluhan) use ($hargaObatMap) {
            if (!$keluhan->id_obat) return 0;

            $hargaObat = $hargaObatMap[$keluhan->id_obat] ?? null;
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
                    $hargaObat = $hargaObatMap[$keluhan->id_obat] ?? null;

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
            'keluhanByDiagnosa',
            'fallbackNotifications'
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
