<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        return $namaBulan[$this->bulan].' '.$this->tahun;
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
        return self::where('obat_id', $obatId)
            ->where(function ($query) use ($tahun, $bulan) {
                $query->where('tahun', '<', $tahun)
                    ->orWhere(function ($subQuery) use ($tahun, $bulan) {
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
        return self::where('obat_id', $obatId)
            ->where(function ($query) use ($tahun, $bulan) {
                $query->where('tahun', '<', $tahun)
                    ->orWhere(function ($subQuery) use ($tahun, $bulan) {
                        $subQuery->where('tahun', $tahun)
                            ->where('bulan', '<=', $bulan);
                    });
            })
            ->sum('stok_pakai');
    }

    /**
     * Menghitung sisa stok untuk obat tertentu hingga periode tertentu (optimized version)
     */
    public static function getSisaStokHingga($obatId, $tahun, $bulan)
    {
        // Single query approach untuk menghindari N+1
        $result = DB::table('obat as o')
            ->selectRaw('o.stok_awal + COALESCE(SUM(sb.stok_masuk), 0) - COALESCE(SUM(sb.stok_pakai), 0) as sisa_stok')
            ->leftJoin('stok_bulanans as sb', function ($join) use ($tahun, $bulan) {
                $join->on('o.id_obat', '=', 'sb.obat_id')
                    ->where(function ($query) use ($tahun, $bulan) {
                        $query->where('sb.tahun', '<', $tahun)
                            ->orWhere(function ($subQuery) use ($tahun, $bulan) {
                                $subQuery->where('sb.tahun', $tahun)
                                    ->where('sb.bulan', '<=', $bulan);
                            });
                    });
            })
            ->where('o.id_obat', $obatId)
            ->groupBy('o.id_obat', 'o.stok_awal')
            ->value('sisa_stok');

        return $result ?? 0;
    }

    /**
     * Menghitung sisa stok untuk multiple obat sekaligus hingga periode tertentu
     */
    public static function getSisaStokHinggaBatch($obatIds, $tahun, $bulan)
    {
        if (empty($obatIds)) {
            return collect();
        }

        // Single query untuk semua obat
        $results = DB::table('obat as o')
            ->selectRaw('o.id_obat, o.stok_awal + COALESCE(SUM(sb.stok_masuk), 0) - COALESCE(SUM(sb.stok_pakai), 0) as sisa_stok')
            ->leftJoin('stok_bulanans as sb', function ($join) use ($tahun, $bulan) {
                $join->on('o.id_obat', '=', 'sb.obat_id')
                    ->where(function ($query) use ($tahun, $bulan) {
                        $query->where('sb.tahun', '<', $tahun)
                            ->orWhere(function ($subQuery) use ($tahun, $bulan) {
                                $subQuery->where('sb.tahun', $tahun)
                                    ->where('sb.bulan', '<=', $bulan);
                            });
                    });
            })
            ->whereIn('o.id_obat', $obatIds)
            ->groupBy('o.id_obat', 'o.stok_awal')
            ->pluck('sisa_stok', 'id_obat');

        // Pastikan semua obat ada di hasil (jika tidak ada stok bulanan)
        $result = collect();
        foreach ($obatIds as $obatId) {
            $result->put($obatId, $results->get($obatId, 0));
        }

        return $result;
    }

    /**
     * Menghitung sisa stok saat ini untuk obat tertentu
     */
    public static function getSisaStokSaatIni($obatId)
    {
        $now = now();

        return self::getSisaStokHingga($obatId, $now->year, $now->month);
    }

    /**
     * Menghitung sisa stok untuk multiple obat sekaligus (optimasi N+1)
     */
    public static function getSisaStokSaatIniBatch($obatIds)
    {
        if (empty($obatIds)) {
            return collect();
        }

        $now = now();
        $tahun = $now->year;
        $bulan = $now->month;

        // Ambil stok awal untuk semua obat sekaligus
        $stokAwalMap = Obat::whereIn('id_obat', $obatIds)
            ->pluck('stok_awal', 'id_obat');

        // Ambil total stok masuk untuk semua obat sekaligus
        $stokMasukMap = self::whereIn('obat_id', $obatIds)
            ->where(function ($query) use ($tahun, $bulan) {
                $query->where('tahun', '<', $tahun)
                    ->orWhere(function ($subQuery) use ($tahun, $bulan) {
                        $subQuery->where('tahun', $tahun)
                            ->where('bulan', '<=', $bulan);
                    });
            })
            ->selectRaw('obat_id, sum(stok_masuk) as total_masuk')
            ->groupBy('obat_id')
            ->pluck('total_masuk', 'obat_id');

        // Ambil total stok pakai untuk semua obat sekaligus
        $stokPakaiMap = self::whereIn('obat_id', $obatIds)
            ->where(function ($query) use ($tahun, $bulan) {
                $query->where('tahun', '<', $tahun)
                    ->orWhere(function ($subQuery) use ($tahun, $bulan) {
                        $subQuery->where('tahun', $tahun)
                            ->where('bulan', '<=', $bulan);
                    });
            })
            ->selectRaw('obat_id, sum(stok_pakai) as total_pakai')
            ->groupBy('obat_id')
            ->pluck('total_pakai', 'obat_id');

        // Hitung sisa stok untuk setiap obat
        $result = collect();
        foreach ($obatIds as $obatId) {
            $stokAwal = $stokAwalMap->get($obatId, 0);
            $totalMasuk = $stokMasukMap->get($obatId, 0);
            $totalPakai = $stokPakaiMap->get($obatId, 0);

            $result->put($obatId, $stokAwal + $totalMasuk - $totalPakai);
        }

        return $result;
    }

    /**
     * Mendapatkan riwayat stok bulanan untuk obat tertentu (optimized version)
     */
    public static function getRiwayatStok($obatId, $limit = 12)
    {
        $stokBulanan = self::where('obat_id', $obatId)
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->limit($limit)
            ->get();

        if ($stokBulanan->isEmpty()) {
            return $stokBulanan;
        }

        // Ambil stok awal obat sekali saja
        $stokAwal = DB::table('obat')
            ->where('id_obat', $obatId)
            ->value('stok_awal') ?? 0;

        // Optimasi: Hitung semua stok akhir dengan single query approach
        $result = $stokBulanan->map(function ($item) use ($obatId, $stokAwal) {
            // Hitung stok akhir dengan single query
            $stokAkhir = DB::table('stok_bulanans as sb')
                ->selectRaw('COALESCE(SUM(sb.stok_masuk), 0) - COALESCE(SUM(sb.stok_pakai), 0) as net_stok')
                ->where('sb.obat_id', $obatId)
                ->where(function ($query) use ($item) {
                    $query->where('sb.tahun', '<', $item->tahun)
                        ->orWhere(function ($subQuery) use ($item) {
                            $subQuery->where('sb.tahun', $item->tahun)
                                ->where('sb.bulan', '<=', $item->bulan);
                        });
                })
                ->value('net_stok') ?? 0;

            $item->stok_akhir = $stokAwal + $stokAkhir;

            return $item;
        });

        return $result;
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
}
