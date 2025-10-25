<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StokBulanan extends Model
{
    protected $table = 'stok_bulanans';
    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'obat_id',
        'tahun',
        'bulan',
        'stok_masuk',
        'stok_pakai',
    ];

    protected $casts = [
        'tahun' => 'integer',
        'bulan' => 'integer',
        'stok_masuk' => 'integer',
        'stok_pakai' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function obat()
    {
        return $this->belongsTo(Obat::class, 'obat_id', 'id_obat');
    }

    // Scope untuk filter berdasarkan tahun
    public function scopeTahun($query, $tahun)
    {
        if ($tahun) {
            return $query->where('tahun', $tahun);
        }
        return $query;
    }

    // Scope untuk filter berdasarkan bulan
    public function scopeBulan($query, $bulan)
    {
        if ($bulan) {
            return $query->where('bulan', $bulan);
        }
        return $query;
    }

    // Scope untuk filter berdasarkan obat
    public function scopeObat($query, $obatId)
    {
        if ($obatId) {
            return $query->where('obat_id', $obatId);
        }
        return $query;
    }

    // Accessor untuk format bulan-tahun
    public function getPeriodeAttribute()
    {
        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        return $namaBulan[$this->bulan] . ' ' . $this->tahun;
    }

    // Accessor untuk format periode singkat (MM-YYYY)
    public function getPeriodeSingkatAttribute()
    {
        return sprintf('%02d-%d', $this->bulan, $this->tahun);
    }

    /**
     * Mendapatkan atau membuat record stok bulanan untuk obat tertentu
     */
    public static function getOrCreate($obatId, $tahun, $bulan)
    {
        return self::firstOrCreate(
            [
                'obat_id' => $obatId,
                'tahun' => $tahun,
                'bulan' => $bulan,
            ],
            [
                'stok_masuk' => 0,
                'stok_pakai' => 0,
            ]
        );
    }

    /**
     * Menambah stok masuk untuk obat tertentu pada periode tertentu
     */
    public static function tambahStokMasuk($obatId, $tahun, $bulan, $jumlah)
    {
        $stokBulanan = self::getOrCreate($obatId, $tahun, $bulan);
        $stokBulanan->stok_masuk += $jumlah;
        $stokBulanan->save();

        return $stokBulanan;
    }

    /**
     * Menambah stok pakai untuk obat tertentu pada periode tertentu
     */
    public static function tambahStokPakai($obatId, $tahun, $bulan, $jumlah)
    {
        $stokBulanan = self::getOrCreate($obatId, $tahun, $bulan);
        $stokBulanan->stok_pakai += $jumlah;
        $stokBulanan->save();

        return $stokBulanan;
    }

    /**
     * Menghitung total stok masuk untuk obat tertentu hingga periode tertentu
     */
    public static function getTotalStokMasukHingga($obatId, $tahun, $bulan)
    {
        // Log warning untuk debugging N+1 query
        Log::warning('getTotalStokMasukHingga dipanggil - ini bisa menyebabkan N+1 query', [
            'obat_id' => $obatId,
            'tahun' => $tahun,
            'bulan' => $bulan,
            'trace' => array_slice(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), 0, 5)
        ]);

        return self::where('obat_id', $obatId)
            ->where(function($query) use ($tahun, $bulan) {
                $query->where('tahun', '<', $tahun)
                      ->orWhere(function($subQuery) use ($tahun, $bulan) {
                          $subQuery->where('tahun', $tahun)
                                   ->where('bulan', '<=', $bulan);
                      });
            })
            ->sum('stok_masuk');
    }

    /**
     * Menghitung total stok pakai untuk obat tertentu hingga periode tertentu
     */
    public static function getTotalStokPakaiHingga($obatId, $tahun, $bulan)
    {
        // Log warning untuk debugging N+1 query
        Log::warning('getTotalStokPakaiHingga dipanggil - ini bisa menyebabkan N+1 query', [
            'obat_id' => $obatId,
            'tahun' => $tahun,
            'bulan' => $bulan,
            'trace' => array_slice(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), 0, 5)
        ]);

        return self::where('obat_id', $obatId)
            ->where(function($query) use ($tahun, $bulan) {
                $query->where('tahun', '<', $tahun)
                      ->orWhere(function($subQuery) use ($tahun, $bulan) {
                          $subQuery->where('tahun', $tahun)
                                   ->where('bulan', '<=', $bulan);
                      });
            })
            ->sum('stok_pakai');
    }

    /**
     * Menghitung total stok masuk untuk multiple obat hingga periode tertentu (batch version)
     */
    public static function getTotalStokMasukHinggaBatch($obatIds, $tahun, $bulan)
    {
        return DB::table('stok_bulanans')
            ->whereIn('obat_id', $obatIds)
            ->where(function($query) use ($tahun, $bulan) {
                $query->where('tahun', '<', $tahun)
                      ->orWhere(function($subQuery) use ($tahun, $bulan) {
                          $subQuery->where('tahun', $tahun)
                                   ->where('bulan', '<=', $bulan);
                      });
            })
            ->selectRaw('obat_id, SUM(stok_masuk) as total_stok_masuk')
            ->groupBy('obat_id')
            ->get()
            ->keyBy('obat_id');
    }

    /**
     * Menghitung total stok pakai untuk multiple obat hingga periode tertentu (batch version)
     */
    public static function getTotalStokPakaiHinggaBatch($obatIds, $tahun, $bulan)
    {
        return DB::table('stok_bulanans')
            ->whereIn('obat_id', $obatIds)
            ->where(function($query) use ($tahun, $bulan) {
                $query->where('tahun', '<', $tahun)
                      ->orWhere(function($subQuery) use ($tahun, $bulan) {
                          $subQuery->where('tahun', $tahun)
                                   ->where('bulan', '<=', $bulan);
                      });
            })
            ->selectRaw('obat_id, SUM(stok_pakai) as total_stok_pakai')
            ->groupBy('obat_id')
            ->get()
            ->keyBy('obat_id');
    }

    /**
     * Menghitung sisa stok untuk obat tertentu hingga periode tertentu
     */
    public static function getSisaStokHingga($obatId, $tahun, $bulan)
    {
        // Log warning untuk debugging N+1 query
        Log::warning('getSisaStokHingga dipanggil - ini bisa menyebabkan N+1 query', [
            'obat_id' => $obatId,
            'tahun' => $tahun,
            'bulan' => $bulan,
            'trace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5)
        ]);

        // Hindari N+1 query dengan mengambil semua data sekaligus
        $stokAwal = DB::table('obat')
            ->where('id_obat', $obatId)
            ->value('stok_awal') ?? 0;

        // Hitung total stok masuk langsung dengan single query
        $totalStokMasuk = self::where('obat_id', $obatId)
            ->where(function($query) use ($tahun, $bulan) {
                $query->where('tahun', '<', $tahun)
                      ->orWhere(function($subQuery) use ($tahun, $bulan) {
                          $subQuery->where('tahun', $tahun)
                                   ->where('bulan', '<=', $bulan);
                      });
            })
            ->sum('stok_masuk');

        // Hitung total stok pakai langsung dengan single query
        $totalStokPakai = self::where('obat_id', $obatId)
            ->where(function($query) use ($tahun, $bulan) {
                $query->where('tahun', '<', $tahun)
                      ->orWhere(function($subQuery) use ($tahun, $bulan) {
                          $subQuery->where('tahun', $tahun)
                                   ->where('bulan', '<=', $bulan);
                      });
            })
            ->sum('stok_pakai');

        return $stokAwal + $totalStokMasuk - $totalStokPakai;
    }

    /**
     * Menghitung sisa stok saat ini untuk obat tertentu
     */
    public static function getSisaStokSaatIni($obatId)
    {
        // Log warning untuk debugging N+1 query
        Log::warning('getSisaStokSaatIni dipanggil - ini bisa menyebabkan N+1 query', [
            'obat_id' => $obatId,
            'trace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5)
        ]);

        $now = now();
        return self::getSisaStokHingga($obatId, $now->year, $now->month);
    }

    /**
     * Mendapatkan riwayat stok bulanan untuk obat tertentu
     */
    public static function getRiwayatStok($obatId, $limit = 12)
    {
        // Ambil stok awal sekali untuk menghindari N+1 query
        $stokAwal = DB::table('obat')
            ->where('id_obat', $obatId)
            ->value('stok_awal') ?? 0;

        // Ambil semua data stok bulanan sekaligus
        $stokBulanans = self::where('obat_id', $obatId)
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->limit($limit)
            ->get();

        // Jika tidak ada data, kembalikan collection kosong
        if ($stokBulanans->isEmpty()) {
            return $stokBulanans;
        }

        // Hitung cumulative totals untuk semua periode sekaligus
        $cumulativeData = [];
        $runningTotalMasuk = 0;
        $runningTotalPakai = 0;

        // Urutkan dari yang terlama ke terbaru untuk perhitungan kumulatif
        $sortedStokBulanans = $stokBulanans->sortBy(function($item) {
            return $item->tahun * 100 + $item->bulan;
        });

        foreach ($sortedStokBulanans as $item) {
            $runningTotalMasuk += $item->stok_masuk;
            $runningTotalPakai += $item->stok_pakai;

            $key = $item->tahun . '-' . str_pad($item->bulan, 2, '0', STR_PAD_LEFT);
            $cumulativeData[$key] = [
                'total_masuk' => $runningTotalMasuk,
                'total_pakai' => $runningTotalPakai,
                'stok_akhir' => $stokAwal + $runningTotalMasuk - $runningTotalPakai
            ];
        }

        // Tambahkan stok_akhir ke setiap item
        return $stokBulanans->map(function($item) use ($cumulativeData) {
            $key = $item->tahun . '-' . str_pad($item->bulan, 2, '0', STR_PAD_LEFT);
            $item->stok_akhir = $cumulativeData[$key]['stok_akhir'] ?? 0;
            return $item;
        });
    }

    /**
     * Mendapatkan tahun-tahun yang tersedia di data stok
     */
    public static function getAvailableTahun()
    {
        return self::select('tahun')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');
    }

    /**
     * Mendapatkan bulan-bulan yang tersedia untuk tahun tertentu
     */
    public static function getAvailableBulan($tahun)
    {
        return self::select('bulan')
            ->where('tahun', $tahun)
            ->distinct()
            ->orderBy('bulan')
            ->pluck('bulan');
    }

    /**
     * Menghitung sisa stok saat ini untuk multiple obat sekaligus (menghindari N+1 query)
     */
    public static function getSisaStokSaatIniForMultiple($obatIds)
    {
        if (empty($obatIds)) {
            return collect();
        }

        $now = now();
        $tahun = $now->year;
        $bulan = $now->month;

        // Ambil stok awal untuk semua obat
        $stokAwalCollection = DB::table('obat')
            ->whereIn('id_obat', $obatIds)
            ->select('id_obat', 'stok_awal')
            ->get()
            ->keyBy('id_obat');

        // Ambil total stok masuk untuk semua obat hingga periode saat ini
        $totalStokMasukCollection = DB::table('stok_bulanans')
            ->whereIn('obat_id', $obatIds)
            ->where(function($query) use ($tahun, $bulan) {
                $query->where('tahun', '<', $tahun)
                      ->orWhere(function($subQuery) use ($tahun, $bulan) {
                          $subQuery->where('tahun', $tahun)
                                   ->where('bulan', '<=', $bulan);
                      });
            })
            ->selectRaw('obat_id, SUM(stok_masuk) as total_stok_masuk')
            ->groupBy('obat_id')
            ->get()
            ->keyBy('obat_id');

        // Ambil total stok pakai untuk semua obat hingga periode saat ini
        $totalStokPakaiCollection = DB::table('stok_bulanans')
            ->whereIn('obat_id', $obatIds)
            ->where(function($query) use ($tahun, $bulan) {
                $query->where('tahun', '<', $tahun)
                      ->orWhere(function($subQuery) use ($tahun, $bulan) {
                          $subQuery->where('tahun', $tahun)
                                   ->where('bulan', '<=', $bulan);
                      });
            })
            ->selectRaw('obat_id, SUM(stok_pakai) as total_stok_pakai')
            ->groupBy('obat_id')
            ->get()
            ->keyBy('obat_id');

        // Hitung sisa stok untuk setiap obat
        $result = collect();
        foreach ($obatIds as $obatId) {
            $stokAwal = $stokAwalCollection->get($obatId)?->stok_awal ?? 0;
            $totalStokMasuk = $totalStokMasukCollection->get($obatId)?->total_stok_masuk ?? 0;
            $totalStokPakai = $totalStokPakaiCollection->get($obatId)?->total_stok_pakai ?? 0;

            $sisaStok = $stokAwal + $totalStokMasuk - $totalStokPakai;
            $result->put($obatId, $sisaStok);
        }

        return $result;
    }
}
