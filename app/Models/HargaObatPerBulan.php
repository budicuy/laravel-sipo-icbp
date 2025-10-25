<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class HargaObatPerBulan extends Model
{
    protected $table = 'harga_obat_per_bulan';
    protected $primaryKey = 'id_harga_obat';
    public $timestamps = true;

    protected $fillable = [
        'id_obat',
        'periode',
        'jumlah_per_kemasan',
        'harga_per_satuan',
        'harga_per_kemasan',
    ];

    protected $casts = [
        'jumlah_per_kemasan' => 'integer',
        'harga_per_satuan' => 'decimal:2',
        'harga_per_kemasan' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function obat()
    {
        return $this->belongsTo(Obat::class, 'id_obat', 'id_obat');
    }

    // Scope untuk filter berdasarkan periode
    public function scopePeriode($query, $periode)
    {
        if ($periode) {
            return $query->where('periode', $periode);
        }
        return $query;
    }

    // Scope untuk filter berdasarkan range periode
    public function scopePeriodeRange($query, $startPeriode, $endPeriode)
    {
        if ($startPeriode && $endPeriode) {
            return $query->whereBetween('periode', [$startPeriode, $endPeriode]);
        }
        return $query;
    }

    // Scope untuk filter berdasarkan obat
    public function scopeObat($query, $obatId)
    {
        if ($obatId) {
            return $query->where('id_obat', $obatId);
        }
        return $query;
    }

    // Accessor untuk format periode yang lebih mudah dibaca
    public function getPeriodeFormatAttribute()
    {
        // Convert MM-YY to Bulan YYYY
        $bulan = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember',
        ];

        if (preg_match('/^(\d{2})-(\d{2})$/', $this->periode, $matches)) {
            $month = $matches[1];
            $year = '20' . $matches[2]; // Convert YY to 20YY
            return ($bulan[$month] ?? $month) . ' ' . $year;
        }

        return $this->periode;
    }

    // Method untuk mendapatkan periode yang tersedia
    public static function getAvailablePeriodes()
    {
        return self::select('periode')
            ->distinct()
            ->orderByRaw("SUBSTRING(periode, 4, 2) DESC, SUBSTRING(periode, 1, 2) DESC")
            ->pluck('periode')
            ->map(function ($periode) {
                return [
                    'value' => $periode,
                    'label' => (new self(['periode' => $periode]))->periode_format
                ];
            });
    }

    /**
     * Mendapatkan harga obat untuk periode tertentu
     * Jika tidak ada, cari periode terakhir yang tersedia
     */
    public static function getHargaObat($idObat, $periode = null)
    {
        if (!$periode) {
            $periode = now()->format('m-y');
        }

        // Cari harga obat di periode tertentu
        $harga = self::where('id_obat', $idObat)
                    ->where('periode', $periode)
                    ->first();

        if ($harga) {
            return $harga;
        }

        // Jika tidak ada di periode tertentu, cari periode terakhir yang tersedia
        $hargaTerakhir = self::where('id_obat', $idObat)
                            ->orderByRaw("SUBSTRING(periode, 4, 2) DESC, SUBSTRING(periode, 1, 2) DESC")
                            ->first();

        return $hargaTerakhir;
    }

    /**
     * Mendapatkan harga obat dengan mekanisme warisan (fallback) rekursif
     * Jika harga tidak tersedia untuk bulan tertentu, akan mencari ke bulan sebelumnya secara berurutan
     *
     * @param int $idObat ID obat
     * @param string $periode Periode yang dicari (format: MM-YY)
     * @param int $maxDepth Maksimal kedalaman pencarian (bulan ke belakang)
     * @param array $result Array untuk menyimpan hasil pencarian (dipakai internally)
     * @return array|null Mengembalikan array dengan data harga dan info sumber, atau null jika tidak ditemukan
     */
    public static function getHargaObatWithFallback($idObat, $periode = null, $maxDepth = 12, &$result = [])
    {
        if (!$periode) {
            $periode = now()->format('m-y');
        }

        // Initialize result array if empty
        if (empty($result)) {
            $result = [
                'harga' => null,
                'sumber_periode' => null,
                'is_fallback' => false,
                'fallback_depth' => 0,
                'path' => []
            ];
        }

        // Cari harga obat di periode tertentu
        $harga = self::where('id_obat', $idObat)
                    ->where('periode', $periode)
                    ->first();

        if ($harga) {
            $result['harga'] = $harga;
            $result['sumber_periode'] = $periode;

            // Jika ini adalah fallback (bukan periode awal yang dicari)
            if ($result['fallback_depth'] > 0) {
                $result['is_fallback'] = true;
            }

            return $result;
        }

        // Jika sudah mencapai maksimal kedalaman pencarian, hentikan rekursi
        if ($result['fallback_depth'] >= $maxDepth) {
            return null;
        }

        // Tambahkan periode ini ke path pencarian
        $result['path'][] = $periode;
        $result['fallback_depth']++;

        // Hitung periode sebelumnya
        $previousPeriode = self::getPreviousPeriode($periode);

        // Jika tidak bisa menghitung periode sebelumnya, hentikan rekursi
        if (!$previousPeriode) {
            return null;
        }

        // Rekursi ke periode sebelumnya
        return self::getHargaObatWithFallback($idObat, $previousPeriode, $maxDepth, $result);
    }

    /**
     * Mendapatkan harga obat dengan mekanisme warisan untuk multiple obat sekaligus
     * Optimized untuk menghindari N+1 query problems
     *
     * @param array $obatPeriodes Array of ['id_obat' => X, 'periode' => 'MM-YY']
     * @param int $maxDepth Maksimal kedalaman pencarian
     * @return array Array dengan key id_obat_periode dan value hasil pencarian
     */
    public static function getBulkHargaObatWithFallback($obatPeriodes, $maxDepth = 12)
    {
        $results = [];
        $fallbackCache = [];

        // Get unique obat IDs for bulk query
        $obatIds = array_unique(array_column($obatPeriodes, 'id_obat'));

        // Bulk fetch all harga data for these obat IDs with eager loading obat
        $allHargaData = self::with(['obat:id_obat,nama_obat'])
                           ->whereIn('id_obat', $obatIds)
                           ->orderByRaw("SUBSTRING(periode, 4, 2) DESC, SUBSTRING(periode, 1, 2) DESC")
                           ->get()
                           ->groupBy('id_obat');

        // Pre-process fallback data for each obat
        foreach ($allHargaData as $idObat => $hargaData) {
            $fallbackCache[$idObat] = [
                'harga' => $hargaData->first(),
                'sumber_periode' => $hargaData->first()->periode,
                'is_fallback' => true,
                'fallback_depth' => 1,
                'path' => []
            ];

            // Create lookup map for this obat
            $hargaMap[$idObat] = [];
            foreach ($hargaData as $harga) {
                $hargaMap[$idObat][$harga->periode] = $harga;
            }
        }

        foreach ($obatPeriodes as $item) {
            $key = $item['id_obat'] . '_' . $item['periode'];

            // Check if we have harga data for this obat and periode
            if (isset($hargaMap[$item['id_obat']][$item['periode']])) {
                $results[$key] = [
                    'harga' => $hargaMap[$item['id_obat']][$item['periode']],
                    'sumber_periode' => $item['periode'],
                    'is_fallback' => false,
                    'fallback_depth' => 0,
                    'path' => []
                ];
            } else {
                // Jika tidak ada, gunakan cache fallback
                if (isset($fallbackCache[$item['id_obat']])) {
                    $fallbackData = $fallbackCache[$item['id_obat']];
                    $fallbackData['path'] = [$item['periode']];
                    $results[$key] = $fallbackData;
                } else {
                    $results[$key] = null;
                }
            }
        }

        return $results;
    }

    /**
     * Menghitung periode sebelumnya dari periode yang diberikan
     *
     * @param string $periode Periode saat ini (format: MM-YY)
     * @return string|null Periode sebelumnya atau null jika tidak bisa dihitung
     */
    public static function getPreviousPeriode($periode)
    {
        // Parse periode saat ini (format MM-YY)
        if (preg_match('/^(\d{2})-(\d{2})$/', $periode, $matches)) {
            $month = (int)$matches[1];
            $year = (int)$matches[2] + 2000; // Convert YY to YYYY

            // Hitung bulan sebelumnya
            if ($month == 1) {
                $prevMonth = 12;
                $prevYear = $year - 1;
            } else {
                $prevMonth = $month - 1;
                $prevYear = $year;
            }

            // Format kembali ke MM-YY
            return sprintf('%02d-%02d', $prevMonth, $prevYear % 100);
        }

        return null;
    }

    /**
     * Mendapatkan daftar obat yang belum diperbarui harganya dalam rentang waktu tertentu
     *
     * @param int $months Jumlah bulan sejak terakhir update harga
     * @return \Illuminate\Support\Collection
     */
    public static function getObatWithStaleHarga($months = 3)
    {
        $currentPeriode = now()->format('m-y');
        $thresholdPeriode = self::getPeriodeMonthsAgo($months);

        // Subquery untuk mendapatkan harga terakhir per obat
        $latestHargaSubquery = self::selectRaw('id_obat, MAX(periode) as latest_periode')
                                  ->groupBy('id_obat');

        // Main query untuk mendapatkan obat dengan harga yang sudah kadaluarsa
        return DB::table('harga_obat_per_bulan as h1')
                  ->joinSub($latestHargaSubquery, 'h2', function($join) {
                      $join->on('h1.id_obat', '=', 'h2.id_obat')
                           ->on('h1.periode', '=', 'h2.latest_periode');
                  })
                  ->join('obat', 'h1.id_obat', '=', 'obat.id_obat')
                  ->where('h1.periode', '<', $thresholdPeriode)
                  ->select('obat.id_obat', 'obat.nama_obat', 'h1.periode as last_harga_periode')
                  ->orderBy('h1.periode', 'asc')
                  ->get();
    }

    /**
     * Menghitung periode dari beberapa bulan yang lalu
     *
     * @param int $months Jumlah bulan ke belakang
     * @return string Periode dalam format MM-YY
     */
    public static function getPeriodeMonthsAgo($months)
    {
        $date = now()->subMonths($months);
        return $date->format('m-y');
    }

    /**
     * Validasi untuk memastikan tidak ada gap harga dalam rentang waktu tertentu
     *
     * @param int $idObat ID obat yang akan divalidasi
     * @param string $startPeriode Periode awal (format: MM-YY)
     * @param string $endPeriode Periode akhir (format: MM-YY)
     * @return array Array berisi informasi gap harga jika ada
     */
    public static function validateHargaContinuity($idObat, $startPeriode, $endPeriode)
    {
        $periodes = self::where('id_obat', $idObat)
                       ->whereBetween('periode', [$startPeriode, $endPeriode])
                       ->orderBy('periode', 'asc')
                       ->pluck('periode')
                       ->toArray();

        $expectedPeriodes = self::generatePeriodeRange($startPeriode, $endPeriode);
        $missingPeriodes = array_diff($expectedPeriodes, $periodes);

        return [
            'has_gap' => !empty($missingPeriodes),
            'missing_periodes' => $missingPeriodes,
            'total_expected' => count($expectedPeriodes),
            'total_found' => count($periodes)
        ];
    }

    /**
     * Validasi untuk memastikan tidak ada gap harga dalam rentang waktu tertentu untuk multiple obat sekaligus
     * Optimized untuk menghindari N+1 query problems
     *
     * @param array $obatIds Array of ID obat yang akan divalidasi
     * @param string $startPeriode Periode awal (format: MM-YY)
     * @param string $endPeriode Periode akhir (format: MM-YY)
     * @return array Array dengan key id_obat dan value hasil validasi
     */
    public static function validateBulkHargaContinuity($obatIds, $startPeriode, $endPeriode)
    {
        $expectedPeriodes = self::generatePeriodeRange($startPeriode, $endPeriode);

        // Bulk fetch all harga data for all obat in one query
        $allHargaData = self::whereIn('id_obat', $obatIds)
                           ->whereBetween('periode', [$startPeriode, $endPeriode])
                           ->orderBy('periode', 'asc')
                           ->get()
                           ->groupBy('id_obat');

        $results = [];

        foreach ($obatIds as $idObat) {
            $periodes = isset($allHargaData[$idObat])
                ? $allHargaData[$idObat]->pluck('periode')->toArray()
                : [];

            $missingPeriodes = array_diff($expectedPeriodes, $periodes);

            $results[$idObat] = [
                'has_gap' => !empty($missingPeriodes),
                'missing_periodes' => $missingPeriodes,
                'total_expected' => count($expectedPeriodes),
                'total_found' => count($periodes)
            ];
        }

        return $results;
    }

    /**
     * Mengenerate array periode dari rentang tertentu
     *
     * @param string $startPeriode Periode awal (format: MM-YY)
     * @param string $endPeriode Periode akhir (format: MM-YY)
     * @return array Array periode dalam format MM-YY
     */
    public static function generatePeriodeRange($startPeriode, $endPeriode)
    {
        $periodes = [];
        $currentPeriode = $startPeriode;

        while ($currentPeriode <= $endPeriode) {
            $periodes[] = $currentPeriode;
            $currentPeriode = self::getNextPeriode($currentPeriode);

            // Prevent infinite loop
            if (count($periodes) > 60) break; // Max 5 years
        }

        return $periodes;
    }

    /**
     * Menghitung periode berikutnya dari periode yang diberikan
     *
     * @param string $periode Periode saat ini (format: MM-YY)
     * @return string|null Periode berikutnya atau null jika tidak bisa dihitung
     */
    public static function getNextPeriode($periode)
    {
        // Parse periode saat ini (format MM-YY)
        if (preg_match('/^(\d{2})-(\d{2})$/', $periode, $matches)) {
            $month = (int)$matches[1];
            $year = (int)$matches[2] + 2000; // Convert YY to YYYY

            // Hitung bulan berikutnya
            if ($month == 12) {
                $nextMonth = 1;
                $nextYear = $year + 1;
            } else {
                $nextMonth = $month + 1;
                $nextYear = $year;
            }

            // Format kembali ke MM-YY
            return sprintf('%02d-%02d', $nextMonth, $nextYear % 100);
        }

        return null;
    }

    /**
     * Membuat harga obat awal dari stok obat pertama kali
     */
    public static function createInitialHargaFromStok($idObat)
    {
        // Cari periode pertama kali stok obat ada
        $stokPertama = StokObat::where('id_obat', $idObat)
                            ->orderByRaw("SUBSTRING(periode, 4, 2) ASC, SUBSTRING(periode, 1, 2) ASC")
                            ->first();

        if (!$stokPertama) {
            return null;
        }

        // Cek apakah harga obat sudah ada untuk periode tersebut
        $hargaExists = self::where('id_obat', $idObat)
                          ->where('periode', $stokPertama->periode)
                          ->exists();

        if ($hargaExists) {
            return null;
        }

        // Ambil data obat untuk mendapatkan harga default
        $obat = Obat::find($idObat);
        if (!$obat) {
            return null;
        }

        // Buat harga obat awal dengan nilai default
        return self::create([
            'id_obat' => $idObat,
            'periode' => $stokPertama->periode,
            'jumlah_per_kemasan' => 1,
            'harga_per_satuan' => 0,
            'harga_per_kemasan' => 0,
        ]);
    }

    /**
     * Update harga obat untuk periode berikutnya
     */
    public static function createForNextPeriod($idObat, $periode)
    {
        // Parse periode saat ini (format MM-YY)
        if (preg_match('/^(\d{2})-(\d{2})$/', $periode, $matches)) {
            $month = (int)$matches[1];
            $year = (int)$matches[2] + 2000; // Convert YY to YYYY

            // Hitung bulan berikutnya
            if ($month == 12) {
                $nextMonth = 1;
                $nextYear = $year + 1;
            } else {
                $nextMonth = $month + 1;
                $nextYear = $year;
            }

            // Format kembali ke MM-YY
            $nextPeriode = sprintf('%02d-%02d', $nextMonth, $nextYear % 100);

            // Cek apakah harga obat sudah ada untuk periode berikutnya
            $hargaExists = self::where('id_obat', $idObat)
                              ->where('periode', $nextPeriode)
                              ->exists();

            if (!$hargaExists) {
                // Ambil harga obat periode saat ini
                $hargaCurrent = self::where('id_obat', $idObat)
                                   ->where('periode', $periode)
                                   ->first();

                if ($hargaCurrent) {
                    // Copy harga obat ke periode berikutnya
                    return self::create([
                        'id_obat' => $idObat,
                        'periode' => $nextPeriode,
                        'jumlah_per_kemasan' => $hargaCurrent->jumlah_per_kemasan,
                        'harga_per_satuan' => $hargaCurrent->harga_per_satuan,
                        'harga_per_kemasan' => $hargaCurrent->harga_per_kemasan,
                    ]);
                }
            }
        }

        return null;
    }

}
