<?php

namespace App\Http\Controllers;

use App\Models\HargaObatPerBulan;
use App\Models\Keluhan;
use App\Models\Kunjungan;
use App\Models\RekamMedis;
use App\Models\RekamMedisEmergency;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    /**
     * Helper method untuk generate nomor registrasi yang konsisten dengan KunjunganController
     * Format: [urutan_global_dari_1_agustus]/NDL/BJM/[bulan]/[tahun]
     */
    private function generateNomorRegistrasi($rekamMedisId, $tanggalPeriksa, $tipe = 'reguler')
    {
        $bulan = $tanggalPeriksa->format('m');
        $tahun = $tanggalPeriksa->format('Y');

        $allRecords = collect();
        
        $regulerRecords = RekamMedis::where('tanggal_periksa', '>=', '2025-08-01')
            ->orderBy('tanggal_periksa')
            ->orderBy('waktu_periksa')
            ->get()
            ->map(function($record) {
                return [
                    'id' => $record->id_rekam,
                    'tanggal' => $record->tanggal_periksa,
                    'waktu' => $record->waktu_periksa,
                    'tipe' => 'reguler'
                ];
            });
        
        $emergencyRecords = RekamMedisEmergency::where('tanggal_periksa', '>=', '2025-08-01')
            ->orderBy('tanggal_periksa')
            ->orderBy('waktu_periksa')
            ->get()
            ->map(function($record) {
                return [
                    'id' => $record->id_emergency,
                    'tanggal' => $record->tanggal_periksa,
                    'waktu' => $record->waktu_periksa,
                    'tipe' => 'emergency'
                ];
            });
        
        $allRecords = $regulerRecords->concat($emergencyRecords)
            ->sortBy(function($record) {
                return $record['tanggal'].' '.$record['waktu'];
            })
            ->values();
        
        $visitCount = 0;
        foreach ($allRecords as $index => $record) {
            if (($tipe === 'reguler' && $record['id'] == $rekamMedisId && $record['tipe'] === 'reguler') ||
                ($tipe === 'emergency' && $record['id'] == $rekamMedisId && $record['tipe'] === 'emergency')) {
                $visitCount = $index + 1;
                break;
            }
        }

        $formattedVisitCount = str_pad($visitCount, 4, '0', STR_PAD_LEFT);
        return "{$formattedVisitCount}/NDL/BJM/{$bulan}/{$tahun}";
    }

    /**
     * Helper method untuk validasi nomor registrasi unik
     */
    private function validateNomorRegistrasi($nomorRegistrasi, $excludeId = null)
    {
        $query = Kunjungan::where('kode_transaksi', $nomorRegistrasi);

        if ($excludeId) {
            $query->where('id_kunjungan', '!=', $excludeId);
        }

        return ! $query->exists();
    }

    /**
     * Helper method untuk optimasi query dengan eager loading
     */
    private function optimizeTransaksiQuery($query, $withRelations = [])
    {
        $defaultRelations = [
            'keluarga.karyawan:id_karyawan,nik_karyawan,nama_karyawan',
            'keluarga.hubungan:kode_hubungan,hubungan',
            'keluhans:id_keluhan,id_rekam,id_diagnosa,id_obat,jumlah_obat,diskon,aturan_pakai',
            'keluhans.diagnosa:id_diagnosa,nama_diagnosa',
            'keluhans.obat:id_obat,nama_obat',
            'user:id_user,username,nama_lengkap',
        ];

        return $query->with(array_merge($defaultRelations, $withRelations));
    }

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
        
        // Enhanced search parameters
        $tipeKunjungan = $request->get('tipe_kunjungan', '');
        $jenisKelamin = $request->get('jenis_kelamin', '');
        $departemen = $request->get('departemen', '');
        $rangeUsia = $request->get('range_usia', '');
        $statusRekam = $request->get('status_rekam', '');
        $minBiaya = $request->get('min_biaya', '');
        $maxBiaya = $request->get('max_biaya', '');

        // Cache key untuk charts dan stats
        $chartCacheKey = "chart_data_{$tahun}";
        $statsCacheKey = "stats_data_{$bulan}_{$tahun}";

        // Get data untuk charts dengan cache
        $chartPemeriksaan = cache()->remember($chartCacheKey.'_pemeriksaan', 3600, function() use ($tahun) {
            return $this->getChartPemeriksaan($tahun);
        });
        
        $chartBiaya = cache()->remember($chartCacheKey.'_biaya', 3600, function() use ($tahun) {
            return $this->getChartBiaya($tahun);
        });

        // Get data untuk tabel transaksi dengan pagination (tidak di-cache karena dinamis)
        $transaksiData = $this->getTransaksiData($periode, $perPage, $search, $tipeKunjungan, $jenisKelamin, $departemen, $rangeUsia, $statusRekam, $minBiaya, $maxBiaya);
        $transaksi = $transaksiData['data'];
        $fallbackNotifications = $transaksiData['fallbackNotifications'] ?? [];

        // Get statistics dengan cache (disable cache when filters are applied)
        $hasFilters = !empty($tipeKunjungan) || !empty($jenisKelamin) || !empty($departemen) ||
                     !empty($rangeUsia) || !empty($statusRekam) || !empty($minBiaya) || !empty($maxBiaya);
        
        if ($hasFilters) {
            // Don't cache when filters are applied
            $stats = $this->getTransaksiStats($bulan, $tahun, $tipeKunjungan, $jenisKelamin, $departemen, $rangeUsia, $statusRekam, $minBiaya, $maxBiaya);
        } else {
            // Use cache when no filters are applied
            $stats = cache()->remember($statsCacheKey, 1800, function() use ($bulan, $tahun) {
                return $this->getTransaksiStats($bulan, $tahun);
            });
        }

        // Get data for dropdown filters with optimized query
        $departemens = \App\Models\Departemen::select('id_departemen', 'nama_departemen')
            ->orderBy('nama_departemen')
            ->get();
        
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
            'fallbackNotifications',
            'tipeKunjungan',
            'jenisKelamin',
            'departemen',
            'rangeUsia',
            'statusRekam',
            'minBiaya',
            'maxBiaya',
            'departemens'
        ));
    }

    /**
     * Get chart data untuk jumlah pemeriksaan per bulan
     */
    private function getChartPemeriksaan($tahun)
    {
        // Optimasi: Single query dengan UNION untuk menggabungkan data reguler dan emergency
        $monthlyData = \DB::select("
            SELECT
                MONTH(tanggal_periksa) as month,
                SUM(CASE WHEN type = 'reguler' THEN 1 ELSE 0 END) as reguler_count,
                SUM(CASE WHEN type = 'emergency' THEN 1 ELSE 0 END) as emergency_count
            FROM (
                SELECT tanggal_periksa, 'reguler' as type
                FROM rekam_medis
                WHERE YEAR(tanggal_periksa) = :tahun1
                
                UNION ALL
                
                SELECT tanggal_periksa, 'emergency' as type
                FROM rekam_medis_emergency
                WHERE YEAR(tanggal_periksa) = :tahun2
            ) combined_data
            GROUP BY MONTH(tanggal_periksa)
        ", ['tahun1' => $tahun, 'tahun2' => $tahun]);

        // Format data untuk chart (12 bulan)
        $chartDataReguler = [];
        $chartDataEmergency = [];
        $chartDataTotal = [];
        
        $monthlyDataArray = [];
        foreach ($monthlyData as $data) {
            $monthlyDataArray[$data->month] = $data;
        }
        
        for ($i = 1; $i <= 12; $i++) {
            $data = $monthlyDataArray[$i] ?? null;
            $reguler = $data ? $data->reguler_count : 0;
            $emergency = $data ? $data->emergency_count : 0;
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

    /**
     * Get chart data untuk total biaya per bulan
     */
    private function getChartBiaya($tahun)
    {
        // Get all keluhan with rekamMedis for the specified year with optimized eager loading
        $keluhanDataReguler = Keluhan::with([
            'rekamMedis:id_rekam,tanggal_periksa',
            'obat:id_obat,nama_obat'
        ])
            ->whereHas('rekamMedis', function ($query) use ($tahun) {
                $query->whereYear('tanggal_periksa', $tahun);
            })
            ->whereNotNull('id_obat')
            ->select('id_keluhan', 'id_rekam', 'id_obat', 'jumlah_obat', 'diskon')
            ->get();

        // Get all keluhan with rekamMedisEmergency for the specified year with optimized eager loading
        $keluhanDataEmergency = Keluhan::with([
            'rekamMedisEmergency:id_emergency,tanggal_periksa',
            'obat:id_obat,nama_obat'
        ])
            ->whereHas('rekamMedisEmergency', function ($query) use ($tahun) {
                $query->whereYear('tanggal_periksa', $tahun);
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

    /**
     * Get data transaksi untuk tabel
     */
    private function getTransaksiData($periode, $perPage = 50, $search = '', $tipeKunjungan = '', $jenisKelamin = '', $departemen = '', $rangeUsia = '', $statusRekam = '', $minBiaya = '', $maxBiaya = '')
    {
        // Parse periode format MM-YY to get month and year
        if (preg_match('/^(\d{2})-(\d{2})$/', $periode, $matches)) {
            $month = (int) $matches[1];
            $year = (int) $matches[2] + 2000; // Convert YY to YYYY
        } else {
            // Default to current month if format is invalid
            $month = Carbon::now()->month;
            $year = Carbon::now()->year;
        }

        // Optimasi: Query langsung dengan pagination di database level
        $currentPage = request()->get('page', 1);
        
        // Build base queries dengan eager loading yang sudah dioptimasi
        $rekamMedisQuery = RekamMedis::with([
            'keluarga.karyawan:id_karyawan,nik_karyawan,nama_karyawan',
            'keluarga.hubungan:kode_hubungan,hubungan',
            'keluhans:id_keluhan,id_rekam,id_diagnosa,id_obat,jumlah_obat,diskon,aturan_pakai',
            'keluhans.diagnosa:id_diagnosa,nama_diagnosa',
            'keluhans.obat:id_obat,nama_obat,id_satuan',
            'keluhans.obat.satuanObat:id_satuan,nama_satuan',
            'user:id_user,username,nama_lengkap',
        ])
        ->select('id_rekam', 'id_keluarga', 'tanggal_periksa', 'waktu_periksa', 'status', 'id_user')
        ->whereMonth('tanggal_periksa', $month)
        ->whereYear('tanggal_periksa', $year);

        $rekamMedisEmergencyQuery = RekamMedisEmergency::with([
            'externalEmployee:id,nik_employee,nama_employee,alamat,jenis_kelamin',
            'keluhans:id_keluhan,id_emergency,id_diagnosa_emergency,id_obat,jumlah_obat,aturan_pakai',
            'keluhans.diagnosaEmergency:id_diagnosa_emergency,nama_diagnosa_emergency',
            'keluhans.obat:id_obat,nama_obat,id_satuan',
            'keluhans.obat.satuanObat:id_satuan,nama_satuan',
            'user:id_user,username,nama_lengkap',
        ])
        ->select('id_emergency', 'id_external_employee', 'tanggal_periksa', 'waktu_periksa', 'status', 'id_user')
        ->whereMonth('tanggal_periksa', $month)
        ->whereYear('tanggal_periksa', $year);

        // Apply basic search filter if provided
        if (!empty($search)) {
            $searchTerm = '%'.$search.'%';
            $rekamMedisQuery->whereHas('keluarga', function ($query) use ($searchTerm) {
                $query->where('nama_keluarga', 'like', $searchTerm)
                    ->orWhere('no_rm', 'like', $searchTerm)
                    ->orWhereHas('karyawan', function ($karyawanQuery) use ($searchTerm) {
                        $karyawanQuery->where('nik_karyawan', 'like', $searchTerm)
                            ->orWhere('nama_karyawan', 'like', $searchTerm);
                    });
            });

            $rekamMedisEmergencyQuery->whereHas('externalEmployee', function ($query) use ($searchTerm) {
                $query->where('nama_employee', 'like', $searchTerm)
                    ->orWhere('nik_employee', 'like', $searchTerm);
            });
        }

        // Enhanced filters
        // Filter by tipe kunjungan
        if (!empty($tipeKunjungan)) {
            if ($tipeKunjungan === 'reguler') {
                // Emergency query diabaikan (kosongkan)
                $rekamMedisEmergencyQuery->whereRaw('1 = 0');
            } elseif ($tipeKunjungan === 'emergency') {
                // Reguler query diabaikan (kosongkan)
                $rekamMedisQuery->whereRaw('1 = 0');
            }
        }

        // Filter by jenis kelamin
        if (!empty($jenisKelamin)) {
            $jenisKelaminFull = $jenisKelamin === 'L' ? 'Laki - Laki' : 'Perempuan';
            $rekamMedisQuery->whereHas('keluarga', function ($query) use ($jenisKelaminFull) {
                $query->where('jenis_kelamin', $jenisKelaminFull);
            });
            
            // Also filter emergency records by jenis kelamin
            $rekamMedisEmergencyQuery->whereHas('externalEmployee', function ($query) use ($jenisKelaminFull) {
                $query->where('jenis_kelamin', $jenisKelaminFull);
            });
        }

        // Filter by departemen
        if (!empty($departemen)) {
            $rekamMedisQuery->whereHas('keluarga.karyawan', function ($query) use ($departemen) {
                $query->where('id_departemen', $departemen);
            });
        }

        // Filter by range usia
        if (!empty($rangeUsia)) {
            $today = Carbon::now();
            switch ($rangeUsia) {
                case '0-17':
                    $minDate = $today->copy()->subYears(17);
                    $rekamMedisQuery->whereHas('keluarga', function ($query) use ($minDate) {
                        $query->where('tanggal_lahir', '>=', $minDate);
                    });
                    break;
                case '18-25':
                    $minDate = $today->copy()->subYears(25);
                    $maxDate = $today->copy()->subYears(18);
                    $rekamMedisQuery->whereHas('keluarga', function ($query) use ($minDate, $maxDate) {
                        $query->whereBetween('tanggal_lahir', [$minDate, $maxDate]);
                    });
                    break;
                case '26-35':
                    $minDate = $today->copy()->subYears(35);
                    $maxDate = $today->copy()->subYears(26);
                    $rekamMedisQuery->whereHas('keluarga', function ($query) use ($minDate, $maxDate) {
                        $query->whereBetween('tanggal_lahir', [$minDate, $maxDate]);
                    });
                    break;
                case '36-50':
                    $minDate = $today->copy()->subYears(50);
                    $maxDate = $today->copy()->subYears(36);
                    $rekamMedisQuery->whereHas('keluarga', function ($query) use ($minDate, $maxDate) {
                        $query->whereBetween('tanggal_lahir', [$minDate, $maxDate]);
                    });
                    break;
                case '50+':
                    $maxDate = $today->copy()->subYears(50);
                    $rekamMedisQuery->whereHas('keluarga', function ($query) use ($maxDate) {
                        $query->where('tanggal_lahir', '<=', $maxDate);
                    });
                    break;
            }
        }

        // Filter by status rekam medis
        if (!empty($statusRekam)) {
            $rekamMedisQuery->where('status', $statusRekam);
            $rekamMedisEmergencyQuery->where('status', $statusRekam);
        }

        $filterByBiaya = !empty($minBiaya) || !empty($maxBiaya);

        // Get counts and data
        $regulerCount = $rekamMedisQuery->count();
        $emergencyCount = $rekamMedisEmergencyQuery->count();
        $totalCount = $regulerCount + $emergencyCount;

        $offset = ($currentPage - 1) * $perPage;
        
        $rekamMedisData = $rekamMedisQuery
            ->orderBy('tanggal_periksa', 'desc')
            ->orderBy('waktu_periksa', 'desc')
            ->offset($offset)
            ->limit($perPage)
            ->get();

        $remainingLimit = $perPage - $rekamMedisData->count();
        $rekamMedisEmergencyData = collect();
        
        if ($remainingLimit > 0) {
            $rekamMedisEmergencyData = $rekamMedisEmergencyQuery
                ->orderBy('tanggal_periksa', 'desc')
                ->orderBy('waktu_periksa', 'desc')
                ->limit($remainingLimit)
                ->get();
        }

        // Bulk fetch harga obat
        $obatPeriods = [];
        $periodeFormat = $periode;

        foreach ($rekamMedisData as $rekamMedis) {
            foreach ($rekamMedis->keluhans as $keluhan) {
                if ($keluhan->id_obat) {
                    $obatPeriods[] = [
                        'id_obat' => $keluhan->id_obat,
                        'periode' => $periodeFormat,
                    ];
                }
            }
        }

        foreach ($rekamMedisEmergencyData as $rekamMedisEmergency) {
            foreach ($rekamMedisEmergency->keluhans as $keluhan) {
                if ($keluhan->id_obat) {
                    $obatPeriods[] = [
                        'id_obat' => $keluhan->id_obat,
                        'periode' => $periodeFormat,
                    ];
                }
            }
        }

        $hargaObatMap = [];
        if (!empty($obatPeriods)) {
            $uniqueObatPeriods = collect($obatPeriods)->unique(function ($item) {
                return $item['id_obat'].'_'.$item['periode'];
            })->values()->toArray();

            $hargaObatResults = HargaObatPerBulan::getBulkHargaObatWithFallback($uniqueObatPeriods);

            foreach ($hargaObatResults as $key => $result) {
                if ($result && $result['harga']) {
                    $hargaObatMap[$key] = $result['harga'];
                }
            }
        }

        // Process data (reguler & emergency)
        $resultReguler = $rekamMedisData->map(function ($rekamMedis) use ($hargaObatMap) {
            $kodeTransaksi = $this->generateNomorRegistrasi($rekamMedis->id_rekam, $rekamMedis->tanggal_periksa, 'reguler');
            
            $keluhans = $rekamMedis->keluhans;
            $totalBiaya = 0;
            $obatDetails = [];
            
            foreach ($keluhans as $keluhan) {
                if (!$keluhan->id_obat) continue;
                
                $key = $keluhan->id_obat.'_'.$rekamMedis->tanggal_periksa->format('m-y');
                $hargaObat = $hargaObatMap[$key] ?? null;
                $hargaSatuan = $hargaObat ? $hargaObat->harga_per_satuan : 0;
                $subtotalSebelumDiskon = $keluhan->jumlah_obat * $hargaSatuan;
                $diskon = $keluhan->diskon ?? 0;
                $subtotal = $subtotalSebelumDiskon * (1 - ($diskon / 100));
                $totalBiaya += $subtotal;
                
                $obatDetails[] = [
                    'nama_obat' => $keluhan->obat ? $keluhan->obat->nama_obat : '',
                    'jumlah_obat' => $keluhan->jumlah_obat,
                    'harga_satuan' => $hargaSatuan,
                    'diskon' => $diskon,
                    'subtotal' => $subtotal,
                ];
            }
            
            $diagnosaList = $keluhans->pluck('diagnosa.nama_diagnosa')->filter()->unique()->values()->toArray();
            $nikKaryawan = $rekamMedis->keluarga->karyawan->nik_karyawan ?? '-';
            
            return [
                'kode_transaksi' => $kodeTransaksi,
                'no_rm' => $nikKaryawan.'-'.($rekamMedis->keluarga->kode_hubungan ?? ''),
                'nama_pasien' => $rekamMedis->keluarga->nama_keluarga,
                'hubungan' => $rekamMedis->keluarga->hubungan->hubungan ?? '-',
                'nik_karyawan' => $nikKaryawan,
                'nama_karyawan' => $rekamMedis->keluarga->karyawan->nama_karyawan ?? '-',
                'tanggal' => $rekamMedis->tanggal_periksa->format('d-m-Y'),
                'diagnosa_list' => $diagnosaList,
                'obat_details' => $obatDetails,
                'total_biaya' => $totalBiaya,
                'id_rekam' => $rekamMedis->id_rekam,
                'tipe' => 'Reguler',
            ];
        });

        $resultEmergency = $rekamMedisEmergencyData->map(function ($rekamMedisEmergency) use ($hargaObatMap) {
            $kodeTransaksi = $this->generateNomorRegistrasi($rekamMedisEmergency->id_emergency, $rekamMedisEmergency->tanggal_periksa, 'emergency');
            
            $keluhans = $rekamMedisEmergency->keluhans;
            $totalBiaya = 0;
            $obatDetails = [];
            
            foreach ($keluhans as $keluhan) {
                if (!$keluhan->id_obat) continue;
                
                $key = $keluhan->id_obat.'_'.$rekamMedisEmergency->tanggal_periksa->format('m-y');
                $hargaObat = $hargaObatMap[$key] ?? null;
                $hargaSatuan = $hargaObat ? $hargaObat->harga_per_satuan : 0;
                $subtotal = $keluhan->jumlah_obat * $hargaSatuan;
                $totalBiaya += $subtotal;
                
                $obatDetails[] = [
                    'nama_obat' => $keluhan->obat ? $keluhan->obat->nama_obat : '',
                    'jumlah_obat' => $keluhan->jumlah_obat,
                    'harga_satuan' => $hargaSatuan,
                    'subtotal' => $subtotal,
                ];
            }
            
            $diagnosaList = $keluhans->pluck('diagnosaEmergency.nama_diagnosa_emergency')->filter()->unique()->values()->toArray();
            $nikKaryawan = $rekamMedisEmergency->externalEmployee->nik_employee ?? '-';
            
            return [
                'kode_transaksi' => $kodeTransaksi,
                'no_rm' => $nikKaryawan,
                'nama_pasien' => $rekamMedisEmergency->externalEmployee ? $rekamMedisEmergency->externalEmployee->nama_employee : '-',
                'hubungan' => 'External Employee',
                'nik_karyawan' => $nikKaryawan,
                'nama_karyawan' => $rekamMedisEmergency->externalEmployee ? $rekamMedisEmergency->externalEmployee->nama_employee : '-',
                'tanggal' => $rekamMedisEmergency->tanggal_periksa->format('d-m-Y'),
                'diagnosa_list' => $diagnosaList,
                'obat_details' => $obatDetails,
                'total_biaya' => $totalBiaya,
                'id_rekam' => $rekamMedisEmergency->id_emergency,
                'tipe' => 'Emergency',
            ];
        });

        $allResults = $resultReguler->concat($resultEmergency)->sortByDesc(function ($item) {
            return \Carbon\Carbon::createFromFormat('d-m-Y', $item['tanggal']);
        })->values();

        if ($filterByBiaya) {
            $allResults = $allResults->filter(function ($item) use ($minBiaya, $maxBiaya) {
                $totalBiaya = $item['total_biaya'];
                
                if (!empty($minBiaya) && $totalBiaya < (float) $minBiaya) {
                    return false;
                }
                
                if (!empty($maxBiaya) && $totalBiaya > (float) $maxBiaya) {
                    return false;
                }
                
                return true;
            })->values();
            
            $totalCount = $allResults->count();
        }

        $paginatedData = new \Illuminate\Pagination\LengthAwarePaginator(
            $allResults,
            $totalCount,
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return [
            'data' => $paginatedData,
            'fallbackNotifications' => [],
        ];
    }

    /**
     * Helper method untuk mengelompokkan data berdasarkan diagnosa
     */
    private function groupDataByDiagnosis($item)
    {
        $groups = [
            0 => ['diagnosa' => '-', 'keluhan' => '-', 'obat' => []],
            1 => ['diagnosa' => '-', 'keluhan' => '-', 'obat' => []],
            2 => ['diagnosa' => '-', 'keluhan' => '-', 'obat' => []],
        ];

        // Group obat by diagnosis
        if (isset($item['diagnosa_list']) && is_array($item['diagnosa_list'])) {
            foreach ($item['diagnosa_list'] as $index => $diagnosa) {
                if ($index < 3) {
                    $groups[$index]['diagnosa'] = $diagnosa;
                    $groups[$index]['keluhan'] = $diagnosa; // Untuk sekarang, keluhan sama dengan diagnosa
                }
            }
        }

        // Group obat details
        if (isset($item['obat_details']) && is_array($item['obat_details'])) {
            $obatIndex = 0;
            $groupIndex = 0;
            $obatPerGroup = 3;

            foreach ($item['obat_details'] as $obat) {
                if ($obatIndex >= $obatPerGroup) {
                    $groupIndex++;
                    $obatIndex = 0;
                }
                
                if ($groupIndex < 3) {
                    $groups[$groupIndex]['obat'][] = $obat;
                    $obatIndex++;
                }
            }
        }

        return $groups;
    }

    /**
     * Get all transaction data for export kustom (without pagination)
     */
    private function getAllTransaksiDataForExportKustom($month, $year, $search = '', $tipeKunjungan = '', $jenisKelamin = '', $departemen = '', $rangeUsia = '', $statusRekam = '', $minBiaya = '', $maxBiaya = '')
    {
        // Optimized query with specific columns and eager loading for reguler
        $rekamMedisQuery = RekamMedis::with([
            'keluarga.karyawan:id_karyawan,nik_karyawan,nama_karyawan,id_departemen',
            'keluarga.karyawan.departemen:id_departemen,nama_departemen',
            'keluarga.hubungan:kode_hubungan,hubungan',
            'keluhans:id_keluhan,id_rekam,id_diagnosa,id_obat,jumlah_obat,diskon,aturan_pakai',
            'keluhans.diagnosa:id_diagnosa,nama_diagnosa',
            'keluhans.obat:id_obat,nama_obat,id_satuan',
            'keluhans.obat.satuanObat:id_satuan,nama_satuan',
            'user:id_user,username,nama_lengkap',
        ])
            ->select('id_rekam', 'id_keluarga', 'tanggal_periksa', 'status', 'id_user')
            ->whereMonth('tanggal_periksa', $month)
            ->whereYear('tanggal_periksa', $year)
            ->orderBy('tanggal_periksa', 'desc');

        // Optimized query with specific columns and eager loading for emergency
        $rekamMedisEmergencyQuery = RekamMedisEmergency::with([
            'user:id_user,username,nama_lengkap',
            'externalEmployee:id,nik_employee,nama_employee,alamat,jenis_kelamin',
            'keluhans:id_keluhan,id_emergency,id_diagnosa_emergency,id_obat,jumlah_obat,aturan_pakai,diskon',
            'keluhans.diagnosaEmergency:id_diagnosa_emergency,nama_diagnosa_emergency',
            'keluhans.obat:id_obat,nama_obat,id_satuan',
            'keluhans.obat.satuanObat:id_satuan,nama_satuan',
        ])
            ->select('id_emergency', 'id_external_employee', 'tanggal_periksa', 'status', 'id_user')
            ->whereMonth('tanggal_periksa', $month)
            ->whereYear('tanggal_periksa', $year)
            ->orderBy('tanggal_periksa', 'desc');

        // Apply search filter if provided
        if (! empty($search)) {
            $searchTerm = '%'.$search.'%';

            // Filter reguler records
            $rekamMedisQuery->whereHas('keluarga', function ($query) use ($searchTerm) {
                $query->where('nama_keluarga', 'like', $searchTerm)
                    ->orWhere('no_rm', 'like', $searchTerm)
                    ->orWhereHas('karyawan', function ($karyawanQuery) use ($searchTerm) {
                        $karyawanQuery->where('nik_karyawan', 'like', $searchTerm)
                            ->orWhere('nama_karyawan', 'like', $searchTerm);
                    });
            });

            // Filter emergency records
            $rekamMedisEmergencyQuery->whereHas('externalEmployee', function ($query) use ($searchTerm) {
                $query->where('nama_employee', 'like', $searchTerm)
                    ->orWhere('nik_employee', 'like', $searchTerm);
            });
        }

        // Enhanced filters for export
        // Filter by tipe kunjungan
        if (!empty($tipeKunjungan)) {
            if ($tipeKunjungan === 'reguler') {
                // Emergency query diabaikan (kosongkan)
                $rekamMedisEmergencyQuery->whereRaw('1 = 0');
            } elseif ($tipeKunjungan === 'emergency') {
                // Reguler query diabaikan (kosongkan)
                $rekamMedisQuery->whereRaw('1 = 0');
            }
        }

        // Filter by jenis kelamin
        if (!empty($jenisKelamin)) {
            $jenisKelaminFull = $jenisKelamin === 'L' ? 'Laki - Laki' : 'Perempuan';
            $rekamMedisQuery->whereHas('keluarga', function ($query) use ($jenisKelaminFull) {
                $query->where('jenis_kelamin', $jenisKelaminFull);
            });
            
            // Also filter emergency records by jenis kelamin
            $rekamMedisEmergencyQuery->whereHas('externalEmployee', function ($query) use ($jenisKelaminFull) {
                $query->where('jenis_kelamin', $jenisKelaminFull);
            });
        }

        // Filter by departemen
        if (!empty($departemen)) {
            $rekamMedisQuery->whereHas('keluarga.karyawan', function ($query) use ($departemen) {
                $query->where('id_departemen', $departemen);
            });
        }

        // Filter by range usia
        if (!empty($rangeUsia)) {
            $today = Carbon::now();
            switch ($rangeUsia) {
                case '0-17':
                    $minDate = $today->copy()->subYears(17);
                    $rekamMedisQuery->whereHas('keluarga', function ($query) use ($minDate) {
                        $query->where('tanggal_lahir', '>=', $minDate);
                    });
                    break;
                case '18-25':
                    $minDate = $today->copy()->subYears(25);
                    $maxDate = $today->copy()->subYears(18);
                    $rekamMedisQuery->whereHas('keluarga', function ($query) use ($minDate, $maxDate) {
                        $query->whereBetween('tanggal_lahir', [$minDate, $maxDate]);
                    });
                    break;
                case '26-35':
                    $minDate = $today->copy()->subYears(35);
                    $maxDate = $today->copy()->subYears(26);
                    $rekamMedisQuery->whereHas('keluarga', function ($query) use ($minDate, $maxDate) {
                        $query->whereBetween('tanggal_lahir', [$minDate, $maxDate]);
                    });
                    break;
                case '36-50':
                    $minDate = $today->copy()->subYears(50);
                    $maxDate = $today->copy()->subYears(36);
                    $rekamMedisQuery->whereHas('keluarga', function ($query) use ($minDate, $maxDate) {
                        $query->whereBetween('tanggal_lahir', [$minDate, $maxDate]);
                    });
                    break;
                case '50+':
                    $maxDate = $today->copy()->subYears(50);
                    $rekamMedisQuery->whereHas('keluarga', function ($query) use ($maxDate) {
                        $query->where('tanggal_lahir', '<=', $maxDate);
                    });
                    break;
            }
        }

        // Filter by status rekam medis
        if (!empty($statusRekam)) {
            $rekamMedisQuery->where('status', $statusRekam);
            $rekamMedisEmergencyQuery->where('status', $statusRekam);
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
                        'periode' => $periode,
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
                        'periode' => $periode,
                    ];
                }
            }
        }

        // Fetch harga obat data with fallback mechanism
        $hargaObatMap = [];

        if (! empty($obatPeriods)) {
            // Get unique combinations to avoid duplicates
            $uniqueObatPeriods = collect($obatPeriods)->unique(function ($item) {
                return $item['id_obat'].'_'.$item['periode'];
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

        // Process data (reguler & emergency)
        $resultReguler = $rekamMedisData->map(function ($rekamMedis) use ($hargaObatMap) {
            $kodeTransaksi = $this->generateNomorRegistrasi($rekamMedis->id_rekam, $rekamMedis->tanggal_periksa, 'reguler');
            
            $keluhans = $rekamMedis->keluhans;
            $totalBiaya = 0;
            $obatDetails = [];
            
            foreach ($keluhans as $keluhan) {
                if (!$keluhan->id_obat) continue;
                
                $key = $keluhan->id_obat.'_'.$rekamMedis->tanggal_periksa->format('m-y');
                $hargaObat = $hargaObatMap[$key] ?? null;
                $hargaSatuan = $hargaObat ? $hargaObat->harga_per_satuan : 0;
                $subtotalSebelumDiskon = $keluhan->jumlah_obat * $hargaSatuan;
                $diskon = $keluhan->diskon ?? 0;
                $subtotal = $subtotalSebelumDiskon * (1 - ($diskon / 100));
                $totalBiaya += $subtotal;
                
                $obatDetails[] = [
                    'nama_obat' => $keluhan->obat ? $keluhan->obat->nama_obat : '',
                    'jumlah_obat' => $keluhan->jumlah_obat,
                    'harga_satuan' => $hargaSatuan,
                    'diskon' => $diskon,
                    'subtotal' => $subtotal,
                ];
            }
            
            $diagnosaList = $keluhans->pluck('diagnosa.nama_diagnosa')->filter()->unique()->values()->toArray();
            $nikKaryawan = $rekamMedis->keluarga->karyawan->nik_karyawan ?? '-';
            
            return [
                'kode_transaksi' => $kodeTransaksi,
                'no_rm' => $nikKaryawan.'-'.($rekamMedis->keluarga->kode_hubungan ?? ''),
                'nama_pasien' => $rekamMedis->keluarga->nama_keluarga,
                'hubungan' => $rekamMedis->keluarga->hubungan->hubungan ?? '-',
                'nik_karyawan' => $nikKaryawan,
                'nama_karyawan' => $rekamMedis->keluarga->karyawan->nama_karyawan ?? '-',
                'tanggal' => $rekamMedis->tanggal_periksa->format('d-m-Y'),
                'diagnosa_list' => $diagnosaList,
                'obat_details' => $obatDetails,
                'total_biaya' => $totalBiaya,
                'id_rekam' => $rekamMedis->id_rekam,
                'tipe' => 'Reguler',
            ];
        });

        $resultEmergency = $rekamMedisEmergencyData->map(function ($rekamMedisEmergency) use ($hargaObatMap) {
            $kodeTransaksi = $this->generateNomorRegistrasi($rekamMedisEmergency->id_emergency, $rekamMedisEmergency->tanggal_periksa, 'emergency');
            
            $keluhans = $rekamMedisEmergency->keluhans;
            $totalBiaya = 0;
            $obatDetails = [];
            
            foreach ($keluhans as $keluhan) {
                if (!$keluhan->id_obat) continue;
                
                $key = $keluhan->id_obat.'_'.$rekamMedisEmergency->tanggal_periksa->format('m-y');
                $hargaObat = $hargaObatMap[$key] ?? null;
                $hargaSatuan = $hargaObat ? $hargaObat->harga_per_satuan : 0;
                $subtotal = $keluhan->jumlah_obat * $hargaSatuan;
                $totalBiaya += $subtotal;
                
                $obatDetails[] = [
                    'nama_obat' => $keluhan->obat ? $keluhan->obat->nama_obat : '',
                    'jumlah_obat' => $keluhan->jumlah_obat,
                    'harga_satuan' => $hargaSatuan,
                    'subtotal' => $subtotal,
                ];
            }
            
            $diagnosaList = $keluhans->pluck('diagnosaEmergency.nama_diagnosa_emergency')->filter()->unique()->values()->toArray();
            $nikKaryawan = $rekamMedisEmergency->externalEmployee->nik_employee ?? '-';
            
            return [
                'kode_transaksi' => $kodeTransaksi,
                'no_rm' => $nikKaryawan,
                'nama_pasien' => $rekamMedisEmergency->externalEmployee ? $rekamMedisEmergency->externalEmployee->nama_employee : '-',
                'hubungan' => 'External Employee',
                'nik_karyawan' => $nikKaryawan,
                'nama_karyawan' => $rekamMedisEmergency->externalEmployee ? $rekamMedisEmergency->externalEmployee->nama_employee : '-',
                'tanggal' => $rekamMedisEmergency->tanggal_periksa->format('d-m-Y'),
                'diagnosa_list' => $diagnosaList,
                'obat_details' => $obatDetails,
                'total_biaya' => $totalBiaya,
                'id_rekam' => $rekamMedisEmergency->id_emergency,
                'tipe' => 'Emergency',
            ];
        });

        $allResults = $resultReguler->concat($resultEmergency)->sortByDesc(function ($item) {
            return \Carbon\Carbon::createFromFormat('d-m-Y', $item['tanggal']);
        })->values();

        if ($filterByBiaya) {
            $allResults = $allResults->filter(function ($item) use ($minBiaya, $maxBiaya) {
                $totalBiaya = $item['total_biaya'];
                
                if (!empty($minBiaya) && $totalBiaya < (float) $minBiaya) {
                    return false;
                }
                
                if (!empty($maxBiaya) && $totalBiaya > (float) $maxBiaya) {
                    return false;
                }
                
                return true;
            })->values();
            
            $totalCount = $allResults->count();
        }

        $paginatedData = new \Illuminate\Pagination\LengthAwarePaginator(
            $allResults,
            $totalCount,
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return [
            'data' => $paginatedData,
            'fallbackNotifications' => [],
        ];
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
            '12' => 'Desember',
        ];

        return $bulanNama[$bulan] ?? 'Unknown';
    }
}
