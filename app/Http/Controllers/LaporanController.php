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
        $search = $request->get('search', '');
        $perPage = $request->get('per_page', 50);
        $perPage = in_array($perPage, [50, 100, 200]) ? $perPage : 50;

        // Get data untuk charts
        $chartPemeriksaan = $this->getChartPemeriksaan($tahun);
        $chartBiaya = $this->getChartBiaya($tahun);

        // Get data untuk tabel transaksi dengan pagination
        $transaksiData = $this->getTransaksiData($periode, $perPage, $search);
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
            'search',
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
            'total' => $chartDataTotal
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
            'total' => $chartDataTotal
        ];
    }

    /**
     * Get data transaksi untuk tabel
     */
    private function getTransaksiData($periode, $perPage = 50, $search = '')
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

        // Get all transaction data first to apply proper filtering
        $allTransaksiData = $this->getAllTransaksiDataForExport($month, $year, $search);

        // Create a LengthAwarePaginator
        $currentPage = request()->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        $itemsForCurrentPage = collect($allTransaksiData)->slice($offset, $perPage)->values();
        $paginatedData = new \Illuminate\Pagination\LengthAwarePaginator(
            $itemsForCurrentPage,
            count($allTransaksiData),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        // Since we're using getAllTransaksiDataForExport which already returns processed data,
        // we can directly return the paginated data
        return [
            'data' => $paginatedData,
            'fallbackNotifications' => []
        ];

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

            $totalBiaya = 0;
            $obatDetails = [];

            foreach ($keluhans as $keluhan) {
                if (!$keluhan->id_obat) continue;

                // Get harga obat from our pre-fetched map
                $key = $keluhan->id_obat . '_' . $periode;
                $hargaObat = $hargaObatMap[$key] ?? null;
                $hargaSatuan = $hargaObat->harga_per_satuan ?? 0;
                $subtotal = $keluhan->jumlah_obat * $hargaSatuan;

                $totalBiaya += $subtotal;

                // Store obat details for export
                $obatDetails[] = [
                    'nama_obat' => $keluhan->obat->nama_obat ?? '',
                    'jumlah_obat' => $keluhan->jumlah_obat,
                    'harga_satuan' => $hargaSatuan,
                    'subtotal' => $subtotal
                ];
            }

            // Get unique diagnoses as an array
            $diagnosaList = $keluhans->pluck('diagnosa.nama_diagnosa')->filter()->unique()->values()->toArray();

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
                'diagnosa_list' => $diagnosaList,
                'obat_details' => $obatDetails,
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

            $totalBiaya = 0;
            $obatDetails = [];

            foreach ($keluhans as $keluhan) {
                if (!$keluhan->id_obat) continue;

                // Get harga obat from our pre-fetched map
                $key = $keluhan->id_obat . '_' . $periode;
                $hargaObat = $hargaObatMap[$key] ?? null;
                $hargaSatuan = $hargaObat->harga_per_satuan ?? 0;
                $subtotal = $keluhan->jumlah_obat * $hargaSatuan;

                $totalBiaya += $subtotal;

                // Store obat details for export
                $obatDetails[] = [
                    'nama_obat' => $keluhan->obat->nama_obat ?? '',
                    'jumlah_obat' => $keluhan->jumlah_obat,
                    'harga_satuan' => $hargaSatuan,
                    'subtotal' => $subtotal
                ];
            }

            // Get unique diagnoses as an array
            $diagnosaList = $keluhans->pluck('diagnosaEmergency.nama_diagnosa_emergency')->filter()->unique()->values()->toArray();

            return [
                'id_kunjungan' => null,
                'kode_transaksi' => $kodeTransaksi,
                'no_rm' => $rekamMedisEmergency->externalEmployee->nik_employee ?? '-',
                'nama_pasien' => $rekamMedisEmergency->externalEmployee->nama_employee ?? '-',
                'hubungan' => 'External Employee',
                'nik_karyawan' => $rekamMedisEmergency->externalEmployee->nik_employee ?? '-',
                'nama_karyawan' => $rekamMedisEmergency->externalEmployee->nama_employee ?? '-',
                'tanggal' => $rekamMedisEmergency->tanggal_periksa->format('d-m-Y'),
                'diagnosa_list' => $diagnosaList,
                'obat_details' => $obatDetails,
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
     * Cetak detail transaksi emergency ke PDF
     */
    public function cetakDetailTransaksiEmergency($id)
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

        // Load PDF view
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('laporan.cetak-detail-transaksi-emergency', compact(
            'rekamMedisEmergency',
            'kodeTransaksi',
            'totalBiaya',
            'keluhanByDiagnosa',
            'fallbackNotifications'
        ));

        // Set paper size to A4 portrait
        $pdf->setPaper('A4', 'portrait');

        // Download PDF - replace "/" with "-" to avoid filename error
        $safeFilename = str_replace('/', '-', $kodeTransaksi);
        return $pdf->download('Detail_Transaksi_Emergency_' . $safeFilename . '.pdf');
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
        try {
            // Get filter parameters
            $tahun = $request->get('tahun', date('Y'));
            $bulan = $request->get('bulan', date('m'));
            $periode = $request->get('periode', Carbon::now()->format('m-y'));
            $search = $request->get('search', '');

        // Parse periode format MM-YY to get month and year
        if (preg_match('/^(\d{2})-(\d{2})$/', $periode, $matches)) {
            $month = (int)$matches[1];
            $year = (int)$matches[2] + 2000; // Convert YY to YYYY
        } else {
            // Default to current month if format is invalid
            $month = Carbon::now()->month;
            $year = Carbon::now()->year;
        }

        // Get all transaction data without pagination
        $allTransaksiData = $this->getAllTransaksiDataForExport($month, $year, $search);

        // Validate data before export
        if (empty($allTransaksiData)) {
            return redirect()->back()->with('error', 'Tidak ada data transaksi untuk periode yang dipilih.');
        }

        // Log data for debugging
        \Log::info('Export Data Count', [
            'month' => $month,
            'year' => $year,
            'search' => $search,
            'total_records' => count($allTransaksiData),
            'sample_data' => $allTransaksiData->take(3)->toArray() // Log first 3 records for debugging
        ]);

        // Log sample data structure to verify 'tanggal' field
        if (count($allTransaksiData) > 0) {
            $firstRecord = $allTransaksiData->first();
            \Log::info('Sample Data Structure', [
                'first_record' => $firstRecord,
                'tanggal_field' => $firstRecord['tanggal'] ?? 'NOT_FOUND',
                'tanggal_type' => gettype($firstRecord['tanggal'] ?? null),
                'all_keys' => array_keys($firstRecord)
            ]);
        }

        // Create a new Excel file with proper filename format
        $filename = 'LAPORAN_TRANSAKSI_POLIKLINIK_' . date('Ymd') . '.xlsx';

        // Create a new PHPExcel object
        $objPHPExcel = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $objPHPExcel->getProperties()->setTitle("Laporan Transaksi");

        // Set active sheet
        $sheet = $objPHPExcel->getActiveSheet();
        $sheet->setTitle("Laporan Transaksi");

        // Set headers according to exact requirements
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'No Registrasi');
        $sheet->setCellValue('C1', 'Tipe');
        $sheet->setCellValue('D1', 'No RM');
        $sheet->setCellValue('E1', 'Nama Pasien');
        $sheet->setCellValue('F1', 'Hubungan');
        $sheet->setCellValue('G1', 'NIK Karyawan');
        $sheet->setCellValue('H1', 'Nama Karyawan');
        $sheet->setCellValue('I1', 'Tanggal Periksa');
        $sheet->setCellValue('J1', 'Diagnosa 1');
        $sheet->setCellValue('K1', 'Diagnosa 2');
        $sheet->setCellValue('L1', 'Diagnosa 3');
        $sheet->setCellValue('M1', 'Obat 1');
        $sheet->setCellValue('N1', 'Jumlah');
        $sheet->setCellValue('O1', 'Harga');
        $sheet->setCellValue('P1', 'Subtotal');
        $sheet->setCellValue('Q1', 'Obat 2');
        $sheet->setCellValue('R1', 'Jumlah');
        $sheet->setCellValue('S1', 'Harga');
        $sheet->setCellValue('T1', 'Subtotal');
        $sheet->setCellValue('U1', 'Obat 3');
        $sheet->setCellValue('V1', 'Jumlah');
        $sheet->setCellValue('W1', 'Harga');
        $sheet->setCellValue('X1', 'Subtotal');
        $sheet->setCellValue('Y1', 'Obat 4');
        $sheet->setCellValue('Z1', 'Jumlah');
        $sheet->setCellValue('AA1', 'Harga');
        $sheet->setCellValue('AB1', 'Subtotal');
        $sheet->setCellValue('AC1', 'Obat 5');
        $sheet->setCellValue('AD1', 'Jumlah');
        $sheet->setCellValue('AE1', 'Harga');
        $sheet->setCellValue('AF1', 'Subtotal');
        $sheet->setCellValue('AG1', 'Total Biaya');
        $totalCol = 'AG';

        // Style the header row
        $headerStyle = [
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => ['rgb' => 'E2EFDA']
            ]
        ];
        $sheet->getStyle('A1:' . $totalCol . '1')->applyFromArray($headerStyle);

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(12);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(25);
        $sheet->getColumnDimension('F')->setWidth(15);
        $sheet->getColumnDimension('G')->setWidth(25);
        $sheet->getColumnDimension('H')->setWidth(15);
        $sheet->getColumnDimension('I')->setWidth(15);
        $sheet->getColumnDimension('J')->setWidth(25);
        $sheet->getColumnDimension('K')->setWidth(25);
        $sheet->getColumnDimension('L')->setWidth(25);

        // Set widths for obat columns
        $obatColumns = ['M', 'Q', 'U', 'Y', 'AC'];
        foreach ($obatColumns as $col) {
            $sheet->getColumnDimension($col)->setWidth(25);
        }

        // Set widths for jumlah, harga, subtotal columns
        $detailColumns = ['N', 'O', 'P', 'R', 'S', 'T', 'V', 'W', 'X', 'Z', 'AA', 'AB', 'AD', 'AE', 'AF'];
        foreach ($detailColumns as $col) {
            $sheet->getColumnDimension($col)->setWidth(15);
        }

        $sheet->getColumnDimension('AG')->setWidth(15);

        // Add data rows
        $row = 2;
        $totalBiaya = 0;

        foreach ($allTransaksiData as $index => $item) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $item['kode_transaksi']);
            $sheet->setCellValue('C' . $row, $item['tipe']);

            // Special handling for emergency patients - add "F" to NO RM
            $noRmValue = $item['no_rm'];
            if ($item['tipe'] == 'Emergency') {
                $noRmValue = $noRmValue . ' (F)';
            }
            $sheet->setCellValue('D' . $row, $noRmValue);

            $sheet->setCellValue('E' . $row, $item['nama_pasien']);
            $sheet->setCellValue('F' . $row, $item['hubungan']);
            $sheet->setCellValue('G' . $row, $item['nik_karyawan']);
            $sheet->setCellValue('H' . $row, $item['nama_karyawan']);
            // PASTIKAN KOLOM I BERISI TANGGAL PERIKSA
            $tanggalValue = isset($item['tanggal']) ? $item['tanggal'] : '-';
            if ($tanggalValue !== '-') {
                // Validasi format tanggal dan konversi jika perlu
                try {
                    if (is_string($tanggalValue)) {
                        // Coba parse tanggal dari format d-m-Y
                        $carbonDate = \Carbon\Carbon::createFromFormat('d-m-Y', $tanggalValue);
                        $tanggalValue = $carbonDate->format('d-m-Y');
                    }
                } catch (\Exception $e) {
                    // Jika gagal parse, coba format lain atau gunakan default
                    try {
                        $carbonDate = new \Carbon\Carbon($tanggalValue);
                        $tanggalValue = $carbonDate->format('d-m-Y');
                    } catch (\Exception $e2) {
                        $tanggalValue = '-';
                    }
                }
            }
            $sheet->setCellValue('I' . $row, $tanggalValue);

            // Add diagnoses (up to 3) - use "-" for empty values
            if (isset($item['diagnosa_list']) && is_array($item['diagnosa_list'])) {
                for ($i = 0; $i < 3; $i++) {
                    // Use PhpSpreadsheet's stringFromColumnIndex to handle column letters correctly
                    $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(9 + $i); // J=9, K=10, L=11
                    $sheet->setCellValue($col . $row, $item['diagnosa_list'][$i] ?? '-');
                }
            } else {
                $sheet->setCellValue('J' . $row, '-');
                $sheet->setCellValue('K' . $row, '-');
                $sheet->setCellValue('L' . $row, '-');
            }

            // Add medicines (up to 5) with details - use "-" for empty values
            if (isset($item['obat_details']) && is_array($item['obat_details'])) {
                for ($i = 0; $i < 5; $i++) {
                    // Use PhpSpreadsheet's stringFromColumnIndex to handle column letters correctly
                    $obatCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(12 + ($i * 4)); // M=12, Q=16, U=20, Y=24, AC=28
                    $jumlahCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(13 + ($i * 4)); // N=13, R=17, V=21, Z=25, AD=29
                    $hargaCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(14 + ($i * 4)); // O=14, S=18, W=22, AA=26, AE=30
                    $subtotalCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(15 + ($i * 4)); // P=15, T=19, X=23, AB=27, AF=31

                    if (isset($item['obat_details'][$i])) {
                        $obat = $item['obat_details'][$i];
                        $sheet->setCellValue($obatCol . $row, $obat['nama_obat']);
                        $sheet->setCellValue($jumlahCol . $row, $obat['jumlah_obat']);
                        $sheet->setCellValue($hargaCol . $row, $obat['harga_satuan']);
                        $sheet->setCellValue($subtotalCol . $row, $obat['subtotal']);
                    } else {
                        $sheet->setCellValue($obatCol . $row, '-');
                        $sheet->setCellValue($jumlahCol . $row, '-');
                        $sheet->setCellValue($hargaCol . $row, '-');
                        $sheet->setCellValue($subtotalCol . $row, '-');
                    }
                }
            } else {
                // Empty all obat columns if no obat details - use "-" for empty values
                for ($i = 0; $i < 5; $i++) {
                    // Use PhpSpreadsheet's stringFromColumnIndex to handle column letters correctly
                    $obatCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(12 + ($i * 4)); // M=12, Q=16, U=20, Y=24, AC=28
                    $jumlahCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(13 + ($i * 4)); // N=13, R=17, V=21, Z=25, AD=29
                    $hargaCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(14 + ($i * 4)); // O=14, S=18, W=22, AA=26, AE=30
                    $subtotalCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(15 + ($i * 4)); // P=15, T=19, X=23, AB=27, AF=31

                    $sheet->setCellValue($obatCol . $row, '-');
                    $sheet->setCellValue($jumlahCol . $row, '-');
                    $sheet->setCellValue($hargaCol . $row, '-');
                    $sheet->setCellValue($subtotalCol . $row, '-');
                }
            }

            // Add total biaya
            $sheet->setCellValue('AG' . $row, $item['total_biaya']);

            // Accumulate total biaya
            $totalBiaya += $item['total_biaya'];

            // Style data rows
            $dataStyle = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ];
            $sheet->getStyle('A' . $row . ':' . $totalCol . $row)->applyFromArray($dataStyle);

            // Format currency columns
            $currencyColumns = ['O', 'P', 'S', 'T', 'W', 'X', 'AA', 'AB', 'AE', 'AF', 'AG'];
            foreach ($currencyColumns as $col) {
                $sheet->getStyle($col . $row)->getNumberFormat()->setFormatCode('#,##0');
            }

            $row++;
        }

        // Add total row
        $sheet->setCellValue('A' . $row, 'TOTAL');
        $lastColIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($totalCol);
        $lastColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($lastColIndex - 1);
        $sheet->mergeCells('A' . $row . ':' . $lastColLetter . $row);
        $sheet->setCellValue($totalCol . $row, $totalBiaya);

        // Style total row
        $totalStyle = [
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => ['rgb' => 'D9EAD3']
            ]
        ];
        $sheet->getStyle('A' . $row . ':' . $totalCol . $row)->applyFromArray($totalStyle);
        $sheet->getStyle($totalCol . $row)->getNumberFormat()->setFormatCode('#,##0');

        // Add summary section
        $summaryRow = $row + 2;
        $sheet->setCellValue('A' . $summaryRow, 'RINGKASAN LAPORAN TRANSAKSI');
        $lastColIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($totalCol);
        $lastColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($lastColIndex - 1);
        $sheet->mergeCells('A' . $summaryRow . ':' . $lastColLetter . $summaryRow);

        $summaryStyle = [
            'font' => [
                'bold' => true,
                'size' => 14,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ];
        $sheet->getStyle('A' . $summaryRow . ':' . $totalCol . $summaryRow)->applyFromArray($summaryStyle);

        $summaryRow++;
        $sheet->setCellValue('A' . $summaryRow, 'Periode:');
        $sheet->setCellValue('B' . $summaryRow, $month . ' - ' . $year);

        $summaryRow++;
        $sheet->setCellValue('A' . $summaryRow, 'Total Transaksi:');
        $sheet->setCellValue('B' . $summaryRow, count($allTransaksiData));

        $summaryRow++;
        $sheet->setCellValue('A' . $summaryRow, 'Total Biaya:');
        $sheet->setCellValue('B' . $summaryRow, 'Rp ' . number_format($totalBiaya, 0, ',', '.'));

        // Create the Excel file
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($objPHPExcel, 'Xlsx');
        // Save to temporary file
        $tempFile = tempnam(sys_get_temp_dir(), 'laporan_transaksi_');
        $writer->save($tempFile);

            // Return file download response
            return response()->download($tempFile, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"'
            ])->deleteFileAfterSend(true);

        } catch (\PhpOffice\PhpSpreadsheet\Exception $e) {
            // Log the error for debugging
            \Log::error('Excel export error: ' . $e->getMessage());

            // Return user-friendly error message
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengekspor data ke Excel. Silakan coba lagi atau hubungi administrator.');
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('General export error: ' . $e->getMessage());

            // Return user-friendly error message
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengekspor data. Silakan coba lagi atau hubungi administrator.');
        }
    }

    /**
     * Get all transaction data for export (without pagination)
     */
    private function getAllTransaksiDataForExport($month, $year, $search = '')
    {
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

        // Apply search filter if provided
        if (!empty($search)) {
            $searchTerm = '%' . $search . '%';

            // Filter reguler records
            $rekamMedisQuery->whereHas('keluarga', function($query) use ($searchTerm) {
                $query->where('nama_keluarga', 'like', $searchTerm)
                      ->orWhere('no_rm', 'like', $searchTerm)
                      ->orWhereHas('karyawan', function($karyawanQuery) use ($searchTerm) {
                          $karyawanQuery->where('nik_karyawan', 'like', $searchTerm)
                                       ->orWhere('nama_karyawan', 'like', $searchTerm);
                      });
            });

            // Filter emergency records
            $rekamMedisEmergencyQuery->whereHas('externalEmployee', function($query) use ($searchTerm) {
                $query->where('nama_employee', 'like', $searchTerm)
                      ->orWhere('nik_employee', 'like', $searchTerm);
            });
        }

        // Get data for both reguler and emergency
        $rekamMedisData = $rekamMedisQuery->get();
        $rekamMedisEmergencyData = $rekamMedisEmergencyQuery->get();

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

        if (!empty($obatPeriods)) {
            // Get unique combinations to avoid duplicates
            $uniqueObatPeriods = collect($obatPeriods)->unique(function ($item) {
                return $item['id_obat'] . '_' . $item['periode'];
            })->values()->toArray();

            // Use the bulk fallback method for optimized performance
            $hargaObatResults = HargaObatPerBulan::getBulkHargaObatWithFallback($uniqueObatPeriods);

            // Create a lookup map
            foreach ($hargaObatResults as $key => $result) {
                if ($result && $result['harga']) {
                    $hargaObatMap[$key] = $result['harga'];
                }
            }
        }

        // Process reguler data
        $resultReguler = $rekamMedisData->map(function($rekamMedis) use ($hargaObatMap) {
            // Generate kode_transaksi format: 1(No Running)/NDL/BJM/MM/YYYY
            $noRunning = str_pad($rekamMedis->id_rekam, 1, '0', STR_PAD_LEFT);
            $bulan = $rekamMedis->tanggal_periksa->format('m');
            $tahun = $rekamMedis->tanggal_periksa->format('Y');
            $kodeTransaksi = "1{$noRunning}/NDL/BJM/{$bulan}/{$tahun}";

            // Get keluhan untuk menghitung total biaya dan dapatkan diagnosa + obat
            $keluhans = $rekamMedis->keluhans;
            $periode = $rekamMedis->tanggal_periksa->format('m-y');

            $totalBiaya = 0;
            $obatDetails = [];

            foreach ($keluhans as $keluhan) {
                if (!$keluhan->id_obat) continue;

                // Get harga obat from our pre-fetched map
                $key = $keluhan->id_obat . '_' . $periode;
                $hargaObat = $hargaObatMap[$key] ?? null;
                $hargaSatuan = $hargaObat ? $hargaObat->harga_per_satuan : 0;
                $subtotal = $keluhan->jumlah_obat * $hargaSatuan;

                $totalBiaya += $subtotal;

                // Store obat details for export with proper null checks
                $obatDetails[] = [
                    'nama_obat' => $keluhan->obat ? $keluhan->obat->nama_obat : '',
                    'jumlah_obat' => $keluhan->jumlah_obat,
                    'harga_satuan' => $hargaSatuan,
                    'subtotal' => $subtotal
                ];
            }

            // Get unique diagnoses as an array
            $diagnosaList = $keluhans->pluck('diagnosa.nama_diagnosa')->filter()->unique()->values()->toArray();

            return [
                'kode_transaksi' => $kodeTransaksi,
                'no_rm' => ($rekamMedis->keluarga->karyawan->nik_karyawan ?? '') . '-' . ($rekamMedis->keluarga->kode_hubungan ?? ''),
                'nama_pasien' => $rekamMedis->keluarga->nama_keluarga,
                'hubungan' => $rekamMedis->keluarga->hubungan->hubungan ?? '-',
                'nik_karyawan' => $rekamMedis->keluarga->karyawan->nik_karyawan ?? '-',
                'nama_karyawan' => $rekamMedis->keluarga->karyawan->nama_karyawan ?? '-',
                'tanggal' => $rekamMedis->tanggal_periksa ? $rekamMedis->tanggal_periksa->format('d-m-Y') : '-',
                'diagnosa_list' => $diagnosaList,
                'total_biaya' => $totalBiaya,
                'id_rekam' => $rekamMedis->id_rekam,
                'tipe' => 'Reguler',
                'obat_details' => $obatDetails
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

            $totalBiaya = 0;
            $obatDetails = [];

            foreach ($keluhans as $keluhan) {
                if (!$keluhan->id_obat) continue;

                // Get harga obat from our pre-fetched map
                $key = $keluhan->id_obat . '_' . $periode;
                $hargaObat = $hargaObatMap[$key] ?? null;
                $hargaSatuan = $hargaObat ? $hargaObat->harga_per_satuan : 0;
                $subtotal = $keluhan->jumlah_obat * $hargaSatuan;

                $totalBiaya += $subtotal;

                // Store obat details for export with proper null checks
                $obatDetails[] = [
                    'nama_obat' => $keluhan->obat ? $keluhan->obat->nama_obat : '',
                    'jumlah_obat' => $keluhan->jumlah_obat,
                    'harga_satuan' => $hargaSatuan,
                    'subtotal' => $subtotal
                ];
            }

            // Get unique diagnoses as an array
            $diagnosaList = $keluhans->pluck('diagnosaEmergency.nama_diagnosa_emergency')->filter()->unique()->values()->toArray();

            return [
                'kode_transaksi' => $kodeTransaksi,
                'no_rm' => $rekamMedisEmergency->externalEmployee->nik_employee ?? '-',
                'nama_pasien' => $rekamMedisEmergency->externalEmployee->nama_employee ?? '-',
                'hubungan' => 'External Employee',
                'nik_karyawan' => $rekamMedisEmergency->externalEmployee->nik_employee ?? '-',
                'nama_karyawan' => $rekamMedisEmergency->externalEmployee->nama_employee ?? '-',
                'tanggal' => $rekamMedisEmergency->tanggal_periksa ? $rekamMedisEmergency->tanggal_periksa->format('d-m-Y') : '-',
                'diagnosa_list' => $diagnosaList,
                'total_biaya' => $totalBiaya,
                'id_rekam' => $rekamMedisEmergency->id_emergency,
                'tipe' => 'Emergency',
                'obat_details' => $obatDetails
            ];
        })->filter();

        // Combine results
        $allResults = $resultReguler->concat($resultEmergency);

        // Sort by date descending
        $allResults = $allResults->sortByDesc(function($item) {
            return \Carbon\Carbon::createFromFormat('d-m-Y', $item['tanggal']);
        })->values();

        return $allResults;
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
