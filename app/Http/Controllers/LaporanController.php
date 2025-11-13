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

        // Gabungkan rekam medis reguler dan emergency untuk hitungan global
        $allRecords = collect();
        
        // Ambil semua rekam medis reguler dari 1 Agustus
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
        
        // Ambil semua rekam medis emergency dari 1 Agustus
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
        
        // Gabungkan dan urutkan semua record
        $allRecords = $regulerRecords->concat($emergencyRecords)
            ->sortBy(function($record) {
                return $record['tanggal'].' '.$record['waktu'];
            })
            ->values();
        
        // Cari posisi record saat ini
        $visitCount = 0;
        foreach ($allRecords as $index => $record) {
            if (($tipe === 'reguler' && $record['id'] == $rekamMedisId && $record['tipe'] === 'reguler') ||
                ($tipe === 'emergency' && $record['id'] == $rekamMedisId && $record['tipe'] === 'emergency')) {
                $visitCount = $index + 1;
                break;
            }
        }

        // Format nomor registrasi dengan 4 digit leading zeros
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
            'keluarga.karyawan:id_karyawan,nik_karyawan,nama_karyawan,id_departemen',
            'keluarga.karyawan.departemen:id_departemen,nama_departemen',
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
                        $query->where('tanggal_lahir', '>=', $minDate)
                              ->where('tanggal_lahir', '<=', $maxDate);
                    });
                    break;
                case '26-35':
                    $minDate = $today->copy()->subYears(35);
                    $maxDate = $today->copy()->subYears(26);
                    $rekamMedisQuery->whereHas('keluarga', function ($query) use ($minDate, $maxDate) {
                        $query->where('tanggal_lahir', '>=', $minDate)
                              ->where('tanggal_lahir', '<=', $maxDate);
                    });
                    break;
                case '36-50':
                    $minDate = $today->copy()->subYears(50);
                    $maxDate = $today->copy()->subYears(36);
                    $rekamMedisQuery->whereHas('keluarga', function ($query) use ($minDate, $maxDate) {
                        $query->where('tanggal_lahir', '>=', $minDate)
                              ->where('tanggal_lahir', '<=', $maxDate);
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

        // Filter by range biaya (will be applied after data is fetched)
        $filterByBiaya = !empty($minBiaya) || !empty($maxBiaya);

        // Get counts for pagination
        $regulerCount = $rekamMedisQuery->count();
        $emergencyCount = $rekamMedisEmergencyQuery->count();
        $totalCount = $regulerCount + $emergencyCount;

        // Get data with offset and limit
        $offset = ($currentPage - 1) * $perPage;
        
        // Get reguler data
        $rekamMedisData = $rekamMedisQuery
            ->orderBy('tanggal_periksa', 'desc')
            ->orderBy('waktu_periksa', 'desc')
            ->offset($offset)
            ->limit($perPage)
            ->get();

        // Get emergency data if needed
        $remainingLimit = $perPage - $rekamMedisData->count();
        $rekamMedisEmergencyData = collect();
        
        if ($remainingLimit > 0) {
            $rekamMedisEmergencyData = $rekamMedisEmergencyQuery
                ->orderBy('tanggal_periksa', 'desc')
                ->orderBy('waktu_periksa', 'desc')
                ->limit($remainingLimit)
                ->get();
        }

        // Collect all unique obat IDs and periods
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

        // Fetch harga obat data with fallback mechanism
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

        // Process reguler data
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

        // Process emergency data
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

        // Combine and sort results
        $allResults = $resultReguler->concat($resultEmergency)->sortByDesc(function ($item) {
            return \Carbon\Carbon::createFromFormat('d-m-Y', $item['tanggal']);
        })->values();

        // Apply biaya filter if specified
        if ($filterByBiaya) {
            $allResults = $allResults->filter(function ($item) use ($minBiaya, $maxBiaya) {
                $totalBiaya = $item['total_biaya'];
                
                // Apply minimum biaya filter
                if (!empty($minBiaya) && $totalBiaya < (float) $minBiaya) {
                    return false;
                }
                
                // Apply maximum biaya filter
                if (!empty($maxBiaya) && $totalBiaya > (float) $maxBiaya) {
                    return false;
                }
                
                return true;
            })->values();
            
            // Update total count after filtering
            $totalCount = $allResults->count();
        }

        // Create paginator
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

        // Fetch all harga obat data with fallback mechanism
        $hargaObatMap = [];
        $fallbackNotifications = [];

        if (! empty($obatPeriods)) {
            // Get unique combinations to avoid duplicates
            $uniqueObatPeriods = collect($obatPeriods)->unique(function ($item) {
                return $item['id_obat'].'_'.$item['periode'];
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
                            'fallback_depth' => $result['fallback_depth'],
                        ];
                    }
                }
            }
        }

        // Prepare bulk upsert data for kunjungan
        $kunjunganUpsertData = [];
        $kunjunganKeyMap = [];

        foreach ($rekamMedisData as $rekamMedis) {
            $key = $rekamMedis->id_keluarga.'_'.$rekamMedis->tanggal_periksa->format('Y-m-d');
            // Generate kode_transaksi format: 1(No Running)/NDL/BJM/MM/YYYY
            // Format nomor registrasi dengan 4 digit leading zeros
            $noRunning = str_pad($rekamMedis->id_rekam, 4, '0', STR_PAD_LEFT);
            $bulan = $rekamMedis->tanggal_periksa->format('m');
            $tahun = $rekamMedis->tanggal_periksa->format('Y');
            $kodeTransaksi = "{$noRunning}/NDL/BJM/{$bulan}/{$tahun}";

            $kunjunganUpsertData[] = [
                'id_keluarga' => $rekamMedis->id_keluarga,
                'tanggal_kunjungan' => $rekamMedis->tanggal_periksa,
                'kode_transaksi' => $kodeTransaksi,
                'created_at' => now(),
            ];

            $kunjunganKeyMap[$key] = $kodeTransaksi;
        }

        // Bulk upsert all kunjungan records at once
        if (! empty($kunjunganUpsertData)) {
            Kunjungan::upsert($kunjunganUpsertData, ['id_keluarga', 'tanggal_kunjungan'], ['kode_transaksi']);
        }

        // Get all kunjungan IDs in one query
        $kunjunganKeys = array_keys($kunjunganKeyMap);
        $kunjunganConditions = [];
        foreach ($kunjunganKeys as $key) {
            [$idKeluarga, $tanggal] = explode('_', $key);
            $kunjunganConditions[] = "(id_keluarga = {$idKeluarga} AND DATE(tanggal_kunjungan) = '{$tanggal}')";
        }

        $kunjunganIdMap = [];
        if (! empty($kunjunganConditions)) {
            $kunjunganRecords = Kunjungan::where(function ($query) use ($kunjunganKeys) {
                foreach ($kunjunganKeys as $key) {
                    [$idKeluarga, $tanggal] = explode('_', $key);
                    $query->orWhere(function ($q) use ($idKeluarga, $tanggal) {
                        $q->where('id_keluarga', $idKeluarga)
                            ->whereDate('tanggal_kunjungan', $tanggal);
                    });
                }
            })->get();

            foreach ($kunjunganRecords as $record) {
                $key = $record->id_keluarga.'_'.$record->tanggal;
                $kunjunganIdMap[$key] = $record->id_kunjungan;
            }
        }

        // Process reguler data
        $resultReguler = $rekamMedisData->map(function ($rekamMedis) use ($kunjunganIdMap, $kunjunganKeyMap, $hargaObatMap) {
            // Generate nomor registrasi yang konsisten dengan KunjunganController
            $nomorRegistrasi = $this->generateNomorRegistrasi($rekamMedis->id_rekam, $rekamMedis->tanggal_periksa, 'reguler');

            // Get kode_transaksi from map
            $kunjunganKey = $rekamMedis->id_keluarga.'_'.$rekamMedis->tanggal_periksa->format('Y-m-d');
            $kodeTransaksi = $kunjunganKeyMap[$kunjunganKey] ?? $nomorRegistrasi;

            // Get keluhan untuk menghitung total biaya dan dapatkan diagnosa + obat
            $keluhans = $rekamMedis->keluhans;
            $periode = $rekamMedis->tanggal_periksa->format('m-y');

            $totalBiaya = 0;
            $obatDetails = [];

            foreach ($keluhans as $keluhan) {
                if (! $keluhan->id_obat) {
                    continue;
                }

                // Get harga obat from our pre-fetched map
                $key = $keluhan->id_obat.'_'.$periode;
                $hargaObat = $hargaObatMap[$key] ?? null;
                $hargaSatuan = $hargaObat->harga_per_satuan ?? 0;
                $subtotalSebelumDiskon = $keluhan->jumlah_obat * $hargaSatuan;

                // Apply discount
                $diskon = $keluhan->diskon ?? 0;
                $subtotal = $subtotalSebelumDiskon * (1 - ($diskon / 100));

                $totalBiaya += $subtotal;

                // Store obat details for export
                $obatDetails[] = [
                    'nama_obat' => $keluhan->obat->nama_obat ?? '',
                    'jumlah_obat' => $keluhan->jumlah_obat,
                    'harga_satuan' => $hargaSatuan,
                    'diskon' => $diskon,
                    'subtotal' => $subtotal,
                ];
            }

            // Get unique diagnoses as an array
            $diagnosaList = $keluhans->pluck('diagnosa.nama_diagnosa')->filter()->unique()->values()->toArray();

            // Get kunjungan ID from optimized map
            $kunjunganId = $kunjunganIdMap[$kunjunganKey] ?? null;

            return [
                'id_kunjungan' => $kunjunganId,
                'kode_transaksi' => $kodeTransaksi,
                'no_rm' => ($rekamMedis->keluarga->karyawan->nik_karyawan ?? '').'-'.($rekamMedis->keluarga->kode_hubungan ?? ''),
                'nama_pasien' => $rekamMedis->keluarga->nama_keluarga,
                'hubungan' => $rekamMedis->keluarga->hubungan->hubungan ?? '-',
                'nik_karyawan' => $rekamMedis->keluarga->karyawan->nik_karyawan ?? '-',
                'nama_karyawan' => $rekamMedis->keluarga->karyawan->nama_karyawan ?? '-',
                'tanggal' => $rekamMedis->tanggal_periksa->format('d-m-Y'),
                'diagnosa_list' => $diagnosaList,
                'obat_details' => $obatDetails,
                'total_biaya' => $totalBiaya,
                'id_rekam' => $rekamMedis->id_rekam,
                'tipe' => 'Reguler',
            ];
        })->filter();

        // Process emergency data
        $resultEmergency = $rekamMedisEmergencyData->map(function ($rekamMedisEmergency) use ($hargaObatMap) {
            // Generate nomor registrasi yang konsisten dengan KunjunganController
            $nomorRegistrasi = $this->generateNomorRegistrasi($rekamMedisEmergency->id_emergency, $rekamMedisEmergency->tanggal_periksa, 'emergency');
            $kodeTransaksi = $nomorRegistrasi;

            // Get keluhan untuk menghitung total biaya dan dapatkan diagnosa + obat
            $keluhans = $rekamMedisEmergency->keluhans;
            $periode = $rekamMedisEmergency->tanggal_periksa->format('m-y');

            $totalBiaya = 0;
            $obatDetails = [];

            foreach ($keluhans as $keluhan) {
                if (! $keluhan->id_obat) {
                    continue;
                }

                // Get harga obat from our pre-fetched map
                $key = $keluhan->id_obat.'_'.$periode;
                $hargaObat = $hargaObatMap[$key] ?? null;
                $hargaSatuan = $hargaObat->harga_per_satuan ?? 0;
                $subtotalSebelumDiskon = $keluhan->jumlah_obat * $hargaSatuan;

                // Apply discount (emergency can also have discount)
                $diskon = $keluhan->diskon ?? 0;
                $subtotal = $subtotalSebelumDiskon * (1 - ($diskon / 100));

                $totalBiaya += $subtotal;

                // Store obat details for export
                $obatDetails[] = [
                    'nama_obat' => $keluhan->obat->nama_obat ?? '',
                    'jumlah_obat' => $keluhan->jumlah_obat,
                    'harga_satuan' => $hargaSatuan,
                    'diskon' => $diskon,
                    'subtotal' => $subtotal,
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
                'tipe' => 'Emergency',
            ];
        })->filter();

        // Combine results
        $allResults = $resultReguler->concat($resultEmergency);

        // Sort by date descending
        $allResults = $allResults->sortByDesc(function ($item) {
            return \Carbon\Carbon::createFromFormat('d-m-Y', $item['tanggal']);
        })->values();

        // Update the paginated data with processed results
        $paginatedData->setCollection($allResults);

        return [
            'data' => $paginatedData,
            'fallbackNotifications' => $fallbackNotifications,
        ];
    }

    /**
     * Get statistics untuk dashboard
     */
    private function getTransaksiStats($bulan, $tahun, $tipeKunjungan = '', $jenisKelamin = '', $departemen = '', $rangeUsia = '', $statusRekam = '', $minBiaya = '', $maxBiaya = '')
    {
        // Apply filters for statistics
        $rekamMedisQuery = RekamMedis::whereMonth('tanggal_periksa', $bulan)
            ->whereYear('tanggal_periksa', $tahun);
        
        $rekamMedisEmergencyQuery = RekamMedisEmergency::whereMonth('tanggal_periksa', $bulan)
            ->whereYear('tanggal_periksa', $tahun);

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
                        $query->where('tanggal_lahir', '>=', $minDate)
                              ->where('tanggal_lahir', '<=', $maxDate);
                    });
                    break;
                case '26-35':
                    $minDate = $today->copy()->subYears(35);
                    $maxDate = $today->copy()->subYears(26);
                    $rekamMedisQuery->whereHas('keluarga', function ($query) use ($minDate, $maxDate) {
                        $query->where('tanggal_lahir', '>=', $minDate)
                              ->where('tanggal_lahir', '<=', $maxDate);
                    });
                    break;
                case '36-50':
                    $minDate = $today->copy()->subYears(50);
                    $maxDate = $today->copy()->subYears(36);
                    $rekamMedisQuery->whereHas('keluarga', function ($query) use ($minDate, $maxDate) {
                        $query->where('tanggal_lahir', '>=', $minDate)
                              ->where('tanggal_lahir', '<=', $maxDate);
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

        $totalPemeriksaanReguler = $rekamMedisQuery->count();
        $totalPemeriksaanEmergency = $rekamMedisEmergencyQuery->count();
        $totalPemeriksaan = $totalPemeriksaanReguler + $totalPemeriksaanEmergency;

        // Get all keluhan with rekamMedis for the specified month and year with optimized eager loading
        $keluhanDataReguler = Keluhan::with([
            'rekamMedis:id_rekam,tanggal_periksa',
            'obat:id_obat,nama_obat'
        ])
            ->whereHas('rekamMedis', function ($query) use ($bulan, $tahun) {
                $query->whereMonth('tanggal_periksa', $bulan)
                    ->whereYear('tanggal_periksa', $tahun);
            })
            ->whereNotNull('id_obat')
            ->select('id_keluhan', 'id_rekam', 'id_obat', 'jumlah_obat', 'diskon')
            ->get();

        // Get all keluhan with rekamMedisEmergency for the specified month and year with optimized eager loading
        $keluhanDataEmergency = Keluhan::with([
            'rekamMedisEmergency:id_emergency,tanggal_periksa',
            'obat:id_obat,nama_obat'
        ])
            ->whereHas('rekamMedisEmergency', function ($query) use ($bulan, $tahun) {
                $query->whereMonth('tanggal_periksa', $bulan)
                    ->whereYear('tanggal_periksa', $tahun);
            })
            ->whereNotNull('id_obat')
            ->select('id_keluhan', 'id_emergency', 'id_obat', 'jumlah_obat', 'diskon')
            ->get();

        // Collect all unique obat IDs and periods to prevent duplicate queries
        $obatPeriods = [];
        foreach ($keluhanDataReguler as $keluhan) {
            $periode = $keluhan->rekamMedis->tanggal_periksa->format('m-y');
            $obatPeriods[] = [
                'id_obat' => $keluhan->id_obat,
                'periode' => $periode,
            ];
        }

        foreach ($keluhanDataEmergency as $keluhan) {
            $periode = $keluhan->rekamMedisEmergency->tanggal_periksa->format('m-y');
            $obatPeriods[] = [
                'id_obat' => $keluhan->id_obat,
                'periode' => $periode,
            ];
        }

        // Get unique combinations to avoid duplicates
        $uniqueObatPeriods = collect($obatPeriods)->unique(function ($item) {
            return $item['id_obat'].'_'.$item['periode'];
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
        $filteredKeluhanReguler = [];
        $filteredKeluhanEmergency = [];
        
        // Apply filters to reguler keluhan data
        foreach ($keluhanDataReguler as $keluhan) {
            $periode = $keluhan->rekamMedis->tanggal_periksa->format('m-y');
            $key = $keluhan->id_obat.'_'.$periode;

            // Get harga obat from our pre-fetched map
            $hargaObat = $hargaObatMap[$key] ?? null;

            if ($hargaObat) {
                $hargaSebelumDiskon = $keluhan->jumlah_obat * $hargaObat->harga_per_satuan;
                $diskon = $keluhan->diskon ?? 0;
                $subtotal = $hargaSebelumDiskon * (1 - ($diskon / 100));
                
                // Apply biaya filter if specified
                $includeInStats = true;
                if (!empty($minBiaya) && $subtotal < (float) $minBiaya) {
                    $includeInStats = false;
                }
                if (!empty($maxBiaya) && $subtotal > (float) $maxBiaya) {
                    $includeInStats = false;
                }
                
                if ($includeInStats) {
                    $totalBiaya += $subtotal;
                    $filteredKeluhanReguler[] = $keluhan;
                }
            }
        }

        // Apply filters to emergency keluhan data
        foreach ($keluhanDataEmergency as $keluhan) {
            $periode = $keluhan->rekamMedisEmergency->tanggal_periksa->format('m-y');
            $key = $keluhan->id_obat.'_'.$periode;

            // Get harga obat from our pre-fetched map
            $hargaObat = $hargaObatMap[$key] ?? null;

            if ($hargaObat) {
                $subtotal = $keluhan->jumlah_obat * $hargaObat->harga_per_satuan;
                
                // Apply biaya filter if specified
                $includeInStats = true;
                if (!empty($minBiaya) && $subtotal < (float) $minBiaya) {
                    $includeInStats = false;
                }
                if (!empty($maxBiaya) && $subtotal > (float) $maxBiaya) {
                    $includeInStats = false;
                }
                
                if ($includeInStats) {
                    $totalBiaya += $subtotal;
                    $filteredKeluhanEmergency[] = $keluhan;
                }
            }
        }
        
        // Recalculate counts based on filtered data
        $totalPemeriksaanReguler = count($filteredKeluhanReguler);
        $totalPemeriksaanEmergency = count($filteredKeluhanEmergency);
        $totalPemeriksaan = $totalPemeriksaanReguler + $totalPemeriksaanEmergency;

        return [
            'total_pemeriksaan' => $totalPemeriksaan,
            'total_pemeriksaan_reguler' => $totalPemeriksaanReguler,
            'total_pemeriksaan_emergency' => $totalPemeriksaanEmergency,
            'total_biaya' => $totalBiaya ?? 0,
            'bulan_nama' => $this->getBulanNama($bulan),
            'tahun' => $tahun,
        ];
    }

    /**
     * Detail transaksi
     */
    public function detailTransaksi($id)
    {
        $rekamMedis = RekamMedis::with([
            'keluarga' => function ($query) {
                $query->select('id_keluarga', 'id_karyawan', 'nama_keluarga', 'no_rm', 'kode_hubungan', 'tanggal_lahir', 'jenis_kelamin')
                    ->with(['karyawan:id_karyawan,nik_karyawan,nama_karyawan,id_departemen'])
                    ->with(['karyawan.departemen:id_departemen,nama_departemen'])
                    ->with(['hubungan:kode_hubungan,hubungan']);
            },
            'keluhans' => function ($query) {
                $query->select('id_keluhan', 'id_rekam', 'id_diagnosa', 'id_obat', 'jumlah_obat', 'diskon', 'aturan_pakai')
                    ->with(['diagnosa:id_diagnosa,nama_diagnosa'])
                    ->with(['obat:id_obat,nama_obat,id_satuan'])
                    ->with(['obat.satuanObat:id_satuan,nama_satuan']);
            },
            'user:id_user,username,nama_lengkap',
        ])
            ->select('id_rekam', 'id_keluarga', 'tanggal_periksa', 'waktu_periksa', 'status', 'id_user')
            ->findOrFail($id);

        // Generate nomor registrasi yang konsisten dengan KunjunganController
        $kodeTransaksi = $this->generateNomorRegistrasi($rekamMedis->id_rekam, $rekamMedis->tanggal_periksa, 'reguler');

        // Create or get kunjungan record for synchronization
        $kunjungan = Kunjungan::firstOrCreate(
            [
                'id_keluarga' => $rekamMedis->id_keluarga,
                'tanggal_kunjungan' => $rekamMedis->tanggal_periksa,
            ],
            [
                'kode_transaksi' => $kodeTransaksi,
            ]
        );

        // Add custom attributes to kunjungan object to match format in kunjungan page
        $kunjungan->no_rm = ($rekamMedis->keluarga->karyawan->nik_karyawan ?? '').'-'.($rekamMedis->keluarga->kode_hubungan ?? '');
        $kunjungan->nama_pasien = $rekamMedis->keluarga->nama_keluarga ?? '-';
        $kunjungan->hubungan = $rekamMedis->keluarga->hubungan->hubungan ?? '-';

        // Optimized harga obat fetching - collect all unique obat IDs first
        $periode = $rekamMedis->tanggal_periksa->format('m-y');
        $obatIds = $rekamMedis->keluhans->pluck('id_obat')->filter()->unique()->toArray();

        // Fetch all harga obat data with fallback mechanism
        $hargaObatMap = [];
        $fallbackNotifications = [];

        if (! empty($obatIds)) {
            // Prepare obat periods for bulk processing
            $obatPeriods = [];
            foreach ($obatIds as $idObat) {
                $obatPeriods[] = [
                    'id_obat' => $idObat,
                    'periode' => $periode,
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
                            'fallback_depth' => $result['fallback_depth'],
                        ];
                    }
                }
            }
        }

        // Calculate total biaya using pre-fetched harga data
        $totalBiaya = $rekamMedis->keluhans->sum(function ($keluhan) use ($hargaObatMap) {
            if (! $keluhan->id_obat) {
                return 0;
            }

            $hargaObat = $hargaObatMap[$keluhan->id_obat] ?? null;
            $hargaSebelumDiskon = $keluhan->jumlah_obat * ($hargaObat->harga_per_satuan ?? 0);
            $diskon = $keluhan->diskon ?? 0;

            return $hargaSebelumDiskon * (1 - ($diskon / 100));
        });

        // Group keluhan by diagnosa, only include those with obat
        $keluhanByDiagnosa = $rekamMedis->keluhans
            ->filter(function ($keluhan) {
                return $keluhan->id_obat !== null && $keluhan->obat !== null;
            })
            ->groupBy(function ($keluhan) {
                return $keluhan->diagnosa->nama_diagnosa ?? 'Unknown';
            })
            ->map(function ($keluhans) use ($hargaObatMap) {
                // Attach harga information to each keluhan using pre-fetched data
                return $keluhans->map(function ($keluhan) use ($hargaObatMap) {
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
            'externalEmployee' => function ($query) {
                $query->select('id', 'nik_employee', 'nama_employee', 'alamat', 'jenis_kelamin');
            },
            'keluhans' => function ($query) {
                $query->select('id_keluhan', 'id_emergency', 'id_diagnosa_emergency', 'id_obat', 'jumlah_obat', 'aturan_pakai', 'diskon')
                    ->with(['diagnosaEmergency:id_diagnosa_emergency,nama_diagnosa_emergency'])
                    ->with(['obat:id_obat,nama_obat,id_satuan'])
                    ->with(['obat.satuanObat:id_satuan,nama_satuan']);
            },
            'user:id_user,username,nama_lengkap',
        ])
            ->select('id_emergency', 'id_external_employee', 'tanggal_periksa', 'waktu_periksa', 'status', 'id_user')
            ->findOrFail($id);

        // Generate nomor registrasi yang konsisten dengan KunjunganController
        $kodeTransaksi = $this->generateNomorRegistrasi($rekamMedisEmergency->id_emergency, $rekamMedisEmergency->tanggal_periksa, 'emergency');

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

        if (! empty($obatIds)) {
            // Prepare obat periods for bulk processing
            $obatPeriods = [];
            foreach ($obatIds as $idObat) {
                $obatPeriods[] = [
                    'id_obat' => $idObat,
                    'periode' => $periode,
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
                            'fallback_depth' => $result['fallback_depth'],
                        ];
                    }
                }
            }
        }

        // Calculate total biaya using pre-fetched harga data
        $totalBiaya = $rekamMedisEmergency->keluhans->sum(function ($keluhan) use ($hargaObatMap) {
            if (! $keluhan->id_obat) {
                return 0;
            }

            $hargaObat = $hargaObatMap[$keluhan->id_obat] ?? null;

            return $keluhan->jumlah_obat * ($hargaObat->harga_per_satuan ?? 0);
        });

        // Group keluhan by diagnosa, only include those with obat
        $keluhanByDiagnosa = $rekamMedisEmergency->keluhans
            ->filter(function ($keluhan) {
                return $keluhan->id_obat !== null && $keluhan->obat !== null;
            })
            ->groupBy(function ($keluhan) {
                return $keluhan->diagnosaEmergency->nama_diagnosa_emergency ?? 'Unknown';
            })
            ->map(function ($keluhans) use ($hargaObatMap) {
                // Attach harga information to each keluhan using pre-fetched data
                return $keluhans->map(function ($keluhan) use ($hargaObatMap) {
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
            'externalEmployee' => function ($query) {
                $query->select('id', 'nik_employee', 'nama_employee', 'alamat', 'jenis_kelamin');
            },
            'keluhans' => function ($query) {
                $query->select('id_keluhan', 'id_emergency', 'id_diagnosa_emergency', 'id_obat', 'jumlah_obat', 'aturan_pakai', 'diskon')
                    ->with(['diagnosaEmergency:id_diagnosa_emergency,nama_diagnosa_emergency'])
                    ->with(['obat:id_obat,nama_obat,id_satuan'])
                    ->with(['obat.satuanObat:id_satuan,nama_satuan']);
            },
            'user:id_user,username,nama_lengkap',
        ])
            ->select('id_emergency', 'id_external_employee', 'tanggal_periksa', 'waktu_periksa', 'status', 'id_user')
            ->findOrFail($id);

        // Generate nomor registrasi yang konsisten dengan KunjunganController
        $kodeTransaksi = $this->generateNomorRegistrasi($rekamMedisEmergency->id_emergency, $rekamMedisEmergency->tanggal_periksa, 'emergency');

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

        if (! empty($obatIds)) {
            // Prepare obat periods for bulk processing
            $obatPeriods = [];
            foreach ($obatIds as $idObat) {
                $obatPeriods[] = [
                    'id_obat' => $idObat,
                    'periode' => $periode,
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
                            'fallback_depth' => $result['fallback_depth'],
                        ];
                    }
                }
            }
        }

        // Calculate total biaya using pre-fetched harga data
        $totalBiaya = $rekamMedisEmergency->keluhans->sum(function ($keluhan) use ($hargaObatMap) {
            if (! $keluhan->id_obat) {
                return 0;
            }

            $hargaObat = $hargaObatMap[$keluhan->id_obat] ?? null;

            return $keluhan->jumlah_obat * ($hargaObat->harga_per_satuan ?? 0);
        });

        // Group keluhan by diagnosa, only include those with obat
        $keluhanByDiagnosa = $rekamMedisEmergency->keluhans
            ->filter(function ($keluhan) {
                return $keluhan->id_obat !== null && $keluhan->obat !== null;
            })
            ->groupBy(function ($keluhan) {
                return $keluhan->diagnosaEmergency->nama_diagnosa_emergency ?? 'Unknown';
            })
            ->map(function ($keluhans) use ($hargaObatMap) {
                // Attach harga information to each keluhan using pre-fetched data
                return $keluhans->map(function ($keluhan) use ($hargaObatMap) {
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

        return $pdf->download('Detail_Transaksi_Emergency_'.$safeFilename.'.pdf');
    }

    /**
     * Cetak detail transaksi ke PDF
     */
    public function cetakDetailTransaksi($id)
    {
        $rekamMedis = RekamMedis::with([
            'keluarga' => function ($query) {
                $query->select('id_keluarga', 'id_karyawan', 'nama_keluarga', 'no_rm', 'kode_hubungan', 'tanggal_lahir', 'alamat', 'jenis_kelamin')
                    ->with(['karyawan:id_karyawan,nik_karyawan,nama_karyawan,id_departemen'])
                    ->with(['karyawan.departemen:id_departemen,nama_departemen'])
                    ->with(['hubungan:kode_hubungan,hubungan']);
            },
            'keluhans' => function ($query) {
                $query->select('id_keluhan', 'id_rekam', 'id_diagnosa', 'id_obat', 'jumlah_obat', 'diskon', 'aturan_pakai')
                    ->with(['diagnosa:id_diagnosa,nama_diagnosa'])
                    ->with(['obat:id_obat,nama_obat,id_satuan'])
                    ->with(['obat.satuanObat:id_satuan,nama_satuan']);
            },
            'user:id_user,username,nama_lengkap',
        ])
            ->select('id_rekam', 'id_keluarga', 'tanggal_periksa', 'waktu_periksa', 'status', 'id_user')
            ->findOrFail($id);

        // Generate nomor registrasi yang konsisten dengan KunjunganController
        $kodeTransaksi = $this->generateNomorRegistrasi($rekamMedis->id_rekam, $rekamMedis->tanggal_periksa, 'reguler');

        // Create or get kunjungan record for synchronization
        $kunjungan = Kunjungan::firstOrCreate(
            [
                'id_keluarga' => $rekamMedis->id_keluarga,
                'tanggal_kunjungan' => $rekamMedis->tanggal_periksa,
            ],
            [
                'kode_transaksi' => $kodeTransaksi,
            ]
        );

        // Add custom attributes to kunjungan object to match format in kunjungan page
        $kunjungan->no_rm = ($rekamMedis->keluarga->karyawan->nik_karyawan ?? '').'-'.($rekamMedis->keluarga->kode_hubungan ?? '');
        $kunjungan->nama_pasien = $rekamMedis->keluarga->nama_keluarga ?? '-';
        $kunjungan->hubungan = $rekamMedis->keluarga->hubungan->hubungan ?? '-';

        // Optimized harga obat fetching - collect all unique obat IDs first
        $periode = $rekamMedis->tanggal_periksa->format('m-y');
        $obatIds = $rekamMedis->keluhans->pluck('id_obat')->filter()->unique()->toArray();

        // Fetch all harga obat data with fallback mechanism
        $hargaObatMap = [];
        $fallbackNotifications = [];

        if (! empty($obatIds)) {
            // Prepare obat periods for bulk processing
            $obatPeriods = [];
            foreach ($obatIds as $idObat) {
                $obatPeriods[] = [
                    'id_obat' => $idObat,
                    'periode' => $periode,
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
                            'fallback_depth' => $result['fallback_depth'],
                        ];
                    }
                }
            }
        }

        // Calculate total biaya using pre-fetched harga data
        $totalBiaya = $rekamMedis->keluhans->sum(function ($keluhan) use ($hargaObatMap) {
            if (! $keluhan->id_obat) {
                return 0;
            }

            $hargaObat = $hargaObatMap[$keluhan->id_obat] ?? null;

            return $keluhan->jumlah_obat * ($hargaObat->harga_per_satuan ?? 0);
        });

        // Group keluhan by diagnosa, only include those with obat
        $keluhanByDiagnosa = $rekamMedis->keluhans
            ->filter(function ($keluhan) {
                return $keluhan->id_obat !== null && $keluhan->obat !== null;
            })
            ->groupBy(function ($keluhan) {
                return $keluhan->diagnosa->nama_diagnosa ?? 'Unknown';
            })
            ->map(function ($keluhans) use ($hargaObatMap) {
                // Attach harga information to each keluhan using pre-fetched data
                return $keluhans->map(function ($keluhan) use ($hargaObatMap) {
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

        return $pdf->download('Detail_Transaksi_'.$safeFilename.'.pdf');
    }

    /**
     * Export laporan transaksi ke Excel dengan format kolom kustom
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
                $month = (int) $matches[1];
                $year = (int) $matches[2] + 2000; // Convert YY to YYYY
            } else {
                // Default to current month if format is invalid
                $month = Carbon::now()->month;
                $year = Carbon::now()->year;
            }

            // Get all transaction data without pagination
            $tipeKunjungan = $request->get('tipe_kunjungan', '');
            $jenisKelamin = $request->get('jenis_kelamin', '');
            $departemen = $request->get('departemen', '');
            $rangeUsia = $request->get('range_usia', '');
            $statusRekam = $request->get('status_rekam', '');
            $minBiaya = $request->get('min_biaya', '');
            $maxBiaya = $request->get('max_biaya', '');
            
            $allTransaksiData = $this->getAllTransaksiDataForExportKustom($month, $year, $search, $tipeKunjungan, $jenisKelamin, $departemen, $rangeUsia, $statusRekam, $minBiaya, $maxBiaya);

            // Validate data before export
            if (empty($allTransaksiData)) {
                return redirect()->back()->with('error', 'Tidak ada data transaksi untuk periode yang dipilih.');
            }

            // Create a new Excel file with proper filename format
            $filename = 'LAPORAN_TRANSAKSI_'.date('Ymd').'.xlsx';

            // Create a new PHPExcel object
            $objPHPExcel = new \PhpOffice\PhpSpreadsheet\Spreadsheet;
            $objPHPExcel->getProperties()->setTitle('Laporan Transaksi');

            // Set active sheet
            $sheet = $objPHPExcel->getActiveSheet();
            $sheet->setTitle('Laporan Transaksi');

            // Set headers according to exact requirements
            $headers = [
                'A' => 'Tanggal / Hari',
                'B' => 'NIK',
                'C' => 'Kode RM',
                'D' => 'Nama Pasien',
                'E' => 'Diagnosa 1',
                'F' => 'Diagnosa 2',
                'G' => 'Diagnosa 3',
                'H' => 'Obat 1',
                'I' => 'Qty',
                'J' => 'Harga',
                'K' => 'Obat 2',
                'L' => 'Qty',
                'M' => 'Harga',
                'N' => 'Obat 3',
                'O' => 'Qty',
                'P' => 'Harga',
                'Q' => 'Obat 4',
                'R' => 'Qty',
                'S' => 'Harga',
                'T' => 'Obat 5',
                'U' => 'Qty',
                'V' => 'Harga',
                'W' => 'Obat 6',
                'X' => 'Qty',
                'Y' => 'Harga',
                'Z' => 'Obat 7',
                'AA' => 'Qty',
                'AB' => 'Harga',
                'AC' => 'Obat 8',
                'AD' => 'Qty',
                'AE' => 'Harga',
                'AF' => 'Obat 9',
                'AG' => 'Qty',
                'AH' => 'Harga',
                'AI' => 'Obat 10',
                'AJ' => 'Qty',
                'AK' => 'Harga',
                'AL' => 'Total Biaya'
            ];

            // Set headers
            foreach ($headers as $col => $header) {
                $sheet->setCellValue($col.'1', $header);
            }

            // Style header row
            $headerStyle = [
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'color' => ['rgb' => 'E2EFDA']],
            ];
            $sheet->getStyle('A1:AL1')->applyFromArray($headerStyle);

            // Set column widths
            $sheet->getColumnDimension('A')->setWidth(20);
            $sheet->getColumnDimension('B')->setWidth(15);
            $sheet->getColumnDimension('C')->setWidth(15);
            $sheet->getColumnDimension('D')->setWidth(25);
            $sheet->getColumnDimension('E')->setWidth(20);
            $sheet->getColumnDimension('F')->setWidth(20);
            $sheet->getColumnDimension('G')->setWidth(20);
            
            // Set widths for all columns
            for ($col = 'H'; $col <= 'AL'; $col++) {
                $sheet->getColumnDimension($col)->setWidth(15);
            }

            // Add data rows
            $row = 2;
            $totalBiaya = 0;

            foreach ($allTransaksiData as $index => $item) {
                // Tanggal / Hari
                $col = 'A';
                $tanggalValue = isset($item['tanggal']) ? $item['tanggal'] : '-';
                if ($tanggalValue !== '-') {
                    try {
                        if (is_string($tanggalValue)) {
                            // Coba format d/m/Y terlebih dahulu (sesuai dengan RekamMedisController)
                            $carbonDate = \Carbon\Carbon::createFromFormat('d/m/Y', $tanggalValue);
                            $tanggalValue = $carbonDate->format('d/m/Y');
                        }
                    } catch (\Exception $e) {
                        try {
                            // Jika gagal, coba format lain
                            $carbonDate = new \Carbon\Carbon($tanggalValue);
                            $tanggalValue = $carbonDate->format('d/m/Y');
                        } catch (\Exception $e2) {
                            $tanggalValue = '-';
                        }
                    }
                }
                $sheet->setCellValue($col.$row, $tanggalValue);
                
                // NIK
                $col = 'B';
                $sheet->setCellValue($col.$row, $item['nik_karyawan']);
                
                // Kode RM
                $col = 'C';
                $noRmValue = $item['no_rm'];
                if ($item['tipe'] == 'Emergency') {
                    $noRmValue = $noRmValue.' (F)';
                }
                $sheet->setCellValue($col.$row, $noRmValue);
                
                // Nama Pasien
                $col = 'D';
                $sheet->setCellValue($col.$row, $item['nama_pasien']);
                
                // Diagnosa 1, 2, 3
                $diagnosaList = $item['diagnosa_list'] ?? [];
                $sheet->setCellValue('E'.$row, $diagnosaList[0] ?? '-');
                $sheet->setCellValue('F'.$row, $diagnosaList[1] ?? '-');
                $sheet->setCellValue('G'.$row, $diagnosaList[2] ?? '-');
                
                // Obat details - flatten all obat details
                $obatDetails = $item['obat_details'] ?? [];
                $obatColumns = ['H', 'K', 'N', 'Q', 'T', 'W', 'Z', 'AC', 'AF', 'AI'];
                $qtyColumns = ['I', 'L', 'O', 'R', 'U', 'X', 'AA', 'AD', 'AG', 'AJ'];
                $hargaColumns = ['J', 'M', 'P', 'S', 'V', 'Y', 'AB', 'AE', 'AH', 'AK'];
                
                // Fill obat data
                for ($i = 0; $i < 10; $i++) {
                    if (isset($obatDetails[$i])) {
                        $obat = $obatDetails[$i];
                        $sheet->setCellValue($obatColumns[$i].$row, $obat['nama_obat']);
                        $sheet->setCellValue($qtyColumns[$i].$row, $obat['jumlah_obat']);
                        $sheet->setCellValue($hargaColumns[$i].$row, $obat['harga_satuan']);
                    } else {
                        $sheet->setCellValue($obatColumns[$i].$row, '-');
                        $sheet->setCellValue($qtyColumns[$i].$row, '-');
                        $sheet->setCellValue($hargaColumns[$i].$row, '-');
                    }
                }
                
                // Total Biaya
                $col = 'AL';
                $sheet->setCellValue($col.$row, $item['total_biaya']);
                $totalBiaya += $item['total_biaya'];

                // Style data rows
                $dataStyle = [
                    'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
                ];
                $sheet->getStyle('A'.$row.':AL'.$row)->applyFromArray($dataStyle);

                // Format currency columns
                $currencyColumns = ['J', 'M', 'P', 'S', 'V', 'Y', 'AB', 'AE', 'AH', 'AK', 'AL'];
                foreach ($currencyColumns as $currCol) {
                    $sheet->getStyle($currCol.$row)->getNumberFormat()->setFormatCode('#,##0');
                }

                $row++;
            }

            // Add total row
            $sheet->setCellValue('A'.$row, 'TOTAL');
            $sheet->mergeCells('A'.$row.':AK'.$row);
            $sheet->setCellValue('AL'.$row, $totalBiaya);

            // Style total row
            $totalStyle = [
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT],
                'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'color' => ['rgb' => 'D9EAD3']],
            ];
            $sheet->getStyle('A'.$row.':AL'.$row)->applyFromArray($totalStyle);
            $sheet->getStyle('AL'.$row)->getNumberFormat()->setFormatCode('#,##0');

            // Create Excel file
            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($objPHPExcel, 'Xlsx');
            $tempFile = tempnam(sys_get_temp_dir(), 'laporan_transaksi_');
            $writer->save($tempFile);

            // Register shutdown function to delete temp file
            register_shutdown_function(function() use ($tempFile) {
                if (file_exists($tempFile)) {
                    unlink($tempFile);
                }
            });

            return response()->download($tempFile, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            ]);

        } catch (\PhpOffice\PhpSpreadsheet\Exception $e) {
            \Log::error('Excel export error: '.$e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengekspor data ke Excel. Silakan coba lagi atau hubungi administrator.');
        } catch (\Exception $e) {
            \Log::error('General export error: '.$e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengekspor data. Silakan coba lagi atau hubungi administrator.');
        }
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
                        $query->where('tanggal_lahir', '>=', $minDate)
                              ->where('tanggal_lahir', '<=', $maxDate);
                    });
                    break;
                case '26-35':
                    $minDate = $today->copy()->subYears(35);
                    $maxDate = $today->copy()->subYears(26);
                    $rekamMedisQuery->whereHas('keluarga', function ($query) use ($minDate, $maxDate) {
                        $query->where('tanggal_lahir', '>=', $minDate)
                              ->where('tanggal_lahir', '<=', $maxDate);
                    });
                    break;
                case '36-50':
                    $minDate = $today->copy()->subYears(50);
                    $maxDate = $today->copy()->subYears(36);
                    $rekamMedisQuery->whereHas('keluarga', function ($query) use ($minDate, $maxDate) {
                        $query->where('tanggal_lahir', '>=', $minDate)
                              ->where('tanggal_lahir', '<=', $maxDate);
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

        // Filter by range biaya (will be applied after data is fetched)
        $filterByBiaya = !empty($minBiaya) || !empty($maxBiaya);

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

        // Fetch all harga obat data with fallback mechanism
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

        // Process reguler data
        $resultReguler = $rekamMedisData->map(function ($rekamMedis) use ($hargaObatMap) {
            // Generate nomor registrasi yang konsisten dengan KunjunganController
            $kodeTransaksi = $this->generateNomorRegistrasi($rekamMedis->id_rekam, $rekamMedis->tanggal_periksa, 'reguler');
            
            // Get keluhan untuk menghitung total biaya dan dapatkan diagnosa + obat
            $keluhans = $rekamMedis->keluhans;
            $periode = $rekamMedis->tanggal_periksa->format('m-y');
            
            $totalBiaya = 0;
            $obatDetails = [];
            
            foreach ($keluhans as $keluhan) {
                if (! $keluhan->id_obat) {
                    continue;
                }
                
                // Get harga obat from our pre-fetched map
                $key = $keluhan->id_obat.'_'.$periode;
                $hargaObat = $hargaObatMap[$key] ?? null;
                $hargaSatuan = $hargaObat ? $hargaObat->harga_per_satuan : 0;
                $subtotalSebelumDiskon = $keluhan->jumlah_obat * $hargaSatuan;
                
                // Apply discount
                $diskon = $keluhan->diskon ?? 0;
                $subtotal = $subtotalSebelumDiskon * (1 - ($diskon / 100));
                
                $totalBiaya += $subtotal;
                
                // Store obat details for export with proper null checks
                $obatDetails[] = [
                    'nama_obat' => $keluhan->obat ? $keluhan->obat->nama_obat : '',
                    'jumlah_obat' => $keluhan->jumlah_obat,
                    'harga_satuan' => $hargaSatuan,
                    'diskon' => $diskon,
                    'subtotal' => $subtotal,
                ];
            }
            
            // Get unique diagnoses as an array
            $diagnosaList = $keluhans->pluck('diagnosa.nama_diagnosa')->filter()->unique()->values()->toArray();
            
            // Get NIK and Nama Karyawan from keluarga table
            $nikKaryawan = $rekamMedis->keluarga->karyawan->nik_karyawan ?? '-';
            $namaKaryawan = $rekamMedis->keluarga->karyawan->nama_karyawan ?? '-';
            
            return [
                'kode_transaksi' => $kodeTransaksi,
                'no_rm' => ($nikKaryawan ?? '').'-'.($rekamMedis->keluarga->kode_hubungan ?? ''),
                'nama_pasien' => $rekamMedis->keluarga->nama_keluarga,
                'hubungan' => $rekamMedis->keluarga->hubungan->hubungan ?? '-',
                'nik_karyawan' => $nikKaryawan,
                'nama_karyawan' => $namaKaryawan,
                'tanggal' => $rekamMedis->tanggal_periksa ? $rekamMedis->tanggal_periksa->format('d/m/Y') : '-',
                'diagnosa_list' => $diagnosaList,
                'total_biaya' => $totalBiaya,
                'id_rekam' => $rekamMedis->id_rekam,
                'tipe' => 'Reguler',
                'obat_details' => $obatDetails,
            ];
        })->filter();

        // Process emergency data
        $resultEmergency = $rekamMedisEmergencyData->map(function ($rekamMedisEmergency) use ($hargaObatMap) {
            // Generate nomor registrasi yang konsisten dengan KunjunganController
            $kodeTransaksi = $this->generateNomorRegistrasi($rekamMedisEmergency->id_emergency, $rekamMedisEmergency->tanggal_periksa, 'emergency');
            
            // Get keluhan untuk menghitung total biaya dan dapatkan diagnosa + obat
            $keluhans = $rekamMedisEmergency->keluhans;
            $periode = $rekamMedisEmergency->tanggal_periksa->format('m-y');
            
            $totalBiaya = 0;
            $obatDetails = [];
            
            foreach ($keluhans as $keluhan) {
                if (! $keluhan->id_obat) {
                    continue;
                }
                
                // Get harga obat from our pre-fetched map
                $key = $keluhan->id_obat.'_'.$periode;
                $hargaObat = $hargaObatMap[$key] ?? null;
                $hargaSatuan = $hargaObat ? $hargaObat->harga_per_satuan : 0;
                $subtotal = $keluhan->jumlah_obat * $hargaSatuan;
                
                $totalBiaya += $subtotal;
                
                // Store obat details for export with proper null checks
                $obatDetails[] = [
                    'nama_obat' => $keluhan->obat ? $keluhan->obat->nama_obat : '',
                    'jumlah_obat' => $keluhan->jumlah_obat,
                    'harga_satuan' => $hargaSatuan,
                    'subtotal' => $subtotal,
                ];
            }
            
            // Get unique diagnoses as an array
            $diagnosaList = $keluhans->pluck('diagnosaEmergency.nama_diagnosa_emergency')->filter()->unique()->values()->toArray();
            
            // Get NIK and Nama Karyawan from externalEmployee table
            $nikKaryawan = $rekamMedisEmergency->externalEmployee->nik_employee ?? '-';
            $namaKaryawan = $rekamMedisEmergency->externalEmployee->nama_employee ?? '-';
            
            return [
                'kode_transaksi' => $kodeTransaksi,
                'no_rm' => $nikKaryawan,
                'nama_pasien' => $rekamMedisEmergency->externalEmployee ? $rekamMedisEmergency->externalEmployee->nama_employee : '-',
                'hubungan' => 'External Employee',
                'nik_karyawan' => $nikKaryawan,
                'nama_karyawan' => $namaKaryawan,
                'tanggal' => $rekamMedisEmergency->tanggal_periksa ? $rekamMedisEmergency->tanggal_periksa->format('d/m/Y') : '-',
                'diagnosa_list' => $diagnosaList,
                'total_biaya' => $totalBiaya,
                'id_rekam' => $rekamMedisEmergency->id_emergency,
                'tipe' => 'Emergency',
                'obat_details' => $obatDetails,
            ];
        })->filter();

        // Combine results
        $allResults = $resultReguler->concat($resultEmergency);

        // Apply biaya filter if specified
        if ($filterByBiaya) {
            $allResults = $allResults->filter(function ($item) use ($minBiaya, $maxBiaya) {
                $totalBiaya = $item['total_biaya'];
                
                // Apply minimum biaya filter
                if (!empty($minBiaya) && $totalBiaya < (float) $minBiaya) {
                    return false;
                }
                
                // Apply maximum biaya filter
                if (!empty($maxBiaya) && $totalBiaya > (float) $maxBiaya) {
                    return false;
                }
                
                return true;
            })->values();
        }

        // Sort by date descending
        $allResults = $allResults->sortByDesc(function ($item) {
            // Coba parsing tanggal dengan format yang sesuai
            try {
                // Coba format d/m/Y terlebih dahulu
                return \Carbon\Carbon::createFromFormat('d/m/Y', $item['tanggal']);
            } catch (\Exception $e) {
                // Jika gagal, coba format d-m-Y
                return \Carbon\Carbon::createFromFormat('d-m-Y', $item['tanggal']);
            }
        })->values();

        return $allResults;
    }

    /**
     * Get all transaction data for export (without pagination)
     */
    private function getAllTransaksiDataForExport($month, $year, $search = '')
    {
        // Optimized query with specific columns and eager loading for reguler - sesuai RekamMedisController
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

        // Optimized query with specific columns and eager loading for emergency - sesuai RekamMedisEmergencyController
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

        // Fetch all harga obat data with fallback mechanism
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

        // Process reguler data
        $resultReguler = $rekamMedisData->map(function ($rekamMedis) use ($hargaObatMap) {
            // Generate nomor registrasi yang konsisten dengan KunjunganController
            $kodeTransaksi = $this->generateNomorRegistrasi($rekamMedis->id_rekam, $rekamMedis->tanggal_periksa, 'reguler');
            
            // Get keluhan untuk menghitung total biaya dan dapatkan diagnosa + obat
            $keluhans = $rekamMedis->keluhans;
            $periode = $rekamMedis->tanggal_periksa->format('m-y');
            
            $totalBiaya = 0;
            $obatDetails = [];
            
            foreach ($keluhans as $keluhan) {
                if (! $keluhan->id_obat) {
                    continue;
                }
                
                // Get harga obat from our pre-fetched map
                $key = $keluhan->id_obat.'_'.$periode;
                $hargaObat = $hargaObatMap[$key] ?? null;
                $hargaSatuan = $hargaObat ? $hargaObat->harga_per_satuan : 0;
                $subtotalSebelumDiskon = $keluhan->jumlah_obat * $hargaSatuan;
                
                // Apply discount
                $diskon = $keluhan->diskon ?? 0;
                $subtotal = $subtotalSebelumDiskon * (1 - ($diskon / 100));
                
                $totalBiaya += $subtotal;
                
                // Store obat details for export with proper null checks
                $obatDetails[] = [
                    'nama_obat' => $keluhan->obat ? $keluhan->obat->nama_obat : '',
                    'jumlah_obat' => $keluhan->jumlah_obat,
                    'harga_satuan' => $hargaSatuan,
                    'diskon' => $diskon,
                    'subtotal' => $subtotal,
                ];
            }
            
            // Get unique diagnoses as an array
            $diagnosaList = $keluhans->pluck('diagnosa.nama_diagnosa')->filter()->unique()->values()->toArray();
            
            // Get NIK and Nama Karyawan from keluarga table - sesuai dengan RekamMedisController
            $nikKaryawan = $rekamMedis->keluarga->karyawan->nik_karyawan ?? '-';
            $namaKaryawan = $rekamMedis->keluarga->karyawan->nama_karyawan ?? '-';
            
            return [
                'kode_transaksi' => $kodeTransaksi,
                'no_rm' => ($nikKaryawan ?? '').'-'.($rekamMedis->keluarga->kode_hubungan ?? ''),
                'nama_pasien' => $rekamMedis->keluarga->nama_keluarga,
                'hubungan' => $rekamMedis->keluarga->hubungan->hubungan ?? '-',
                'nik_karyawan' => $nikKaryawan,
                'nama_karyawan' => $namaKaryawan,
                'tanggal' => $rekamMedis->tanggal_periksa ? $rekamMedis->tanggal_periksa->format('d/m/Y') : '-',
                'diagnosa_list' => $diagnosaList,
                'total_biaya' => $totalBiaya,
                'id_rekam' => $rekamMedis->id_rekam,
                'tipe' => 'Reguler',
                'obat_details' => $obatDetails,
            ];
        })->filter();

        // Process emergency data
        $resultEmergency = $rekamMedisEmergencyData->map(function ($rekamMedisEmergency) use ($hargaObatMap) {
            // Generate nomor registrasi yang konsisten dengan KunjunganController
            $kodeTransaksi = $this->generateNomorRegistrasi($rekamMedisEmergency->id_emergency, $rekamMedisEmergency->tanggal_periksa, 'emergency');
            
            // Get keluhan untuk menghitung total biaya dan dapatkan diagnosa + obat
            $keluhans = $rekamMedisEmergency->keluhans;
            $periode = $rekamMedisEmergency->tanggal_periksa->format('m-y');
            
            $totalBiaya = 0;
            $obatDetails = [];
            
            foreach ($keluhans as $keluhan) {
                if (! $keluhan->id_obat) {
                    continue;
                }
                
                // Get harga obat from our pre-fetched map
                $key = $keluhan->id_obat.'_'.$periode;
                $hargaObat = $hargaObatMap[$key] ?? null;
                $hargaSatuan = $hargaObat ? $hargaObat->harga_per_satuan : 0;
                $subtotal = $keluhan->jumlah_obat * $hargaSatuan;
                
                $totalBiaya += $subtotal;
                
                // Store obat details for export with proper null checks
                $obatDetails[] = [
                    'nama_obat' => $keluhan->obat ? $keluhan->obat->nama_obat : '',
                    'jumlah_obat' => $keluhan->jumlah_obat,
                    'harga_satuan' => $hargaSatuan,
                    'subtotal' => $subtotal,
                ];
            }
            
            // Get unique diagnoses as an array
            $diagnosaList = $keluhans->pluck('diagnosaEmergency.nama_diagnosa_emergency')->filter()->unique()->values()->toArray();
            
            // Get NIK and Nama Karyawan from externalEmployee table - sesuai dengan RekamMedisEmergencyController
            $nikKaryawan = $rekamMedisEmergency->externalEmployee->nik_employee ?? '-';
            $namaKaryawan = $rekamMedisEmergency->externalEmployee->nama_employee ?? '-';
            
            return [
                'kode_transaksi' => $kodeTransaksi,
                'no_rm' => $nikKaryawan,
                'nama_pasien' => $rekamMedisEmergency->externalEmployee ? $rekamMedisEmergency->externalEmployee->nama_employee : '-',
                'hubungan' => 'External Employee',
                'nik_karyawan' => $nikKaryawan,
                'nama_karyawan' => $namaKaryawan,
                'tanggal' => $rekamMedisEmergency->tanggal_periksa ? $rekamMedisEmergency->tanggal_periksa->format('d/m/Y') : '-',
                'diagnosa_list' => $diagnosaList,
                'total_biaya' => $totalBiaya,
                'id_rekam' => $rekamMedisEmergency->id_emergency,
                'tipe' => 'Emergency',
                'obat_details' => $obatDetails,
            ];
        })->filter();

        // Combine results
        $allResults = $resultReguler->concat($resultEmergency);

        // Sort by date descending
        $allResults = $allResults->sortByDesc(function ($item) {
            // Coba parsing tanggal dengan format yang sesuai
            try {
                // Coba format d/m/Y terlebih dahulu
                return \Carbon\Carbon::createFromFormat('d/m/Y', $item['tanggal']);
            } catch (\Exception $e) {
                // Jika gagal, coba format d-m-Y
                return \Carbon\Carbon::createFromFormat('d-m-Y', $item['tanggal']);
            }
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
            '12' => 'Desember',
        ];

        return $bulanNama[$bulan] ?? 'Unknown';
    }
}
