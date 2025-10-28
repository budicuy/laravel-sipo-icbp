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
     * Menghitung sisa stok saat ini untuk obat tertentu (optimized)
     */
    public static function getSisaStokSaatIni($obatId)
    {
        // Gunakan batch approach untuk konsistensi dan menghindari N+1
        $result = self::getSisaStokSaatIniBatch([$obatId]);

        return $result->get($obatId, 0);
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

        // Single query approach untuk semua obat sekaligus
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
     * Mendapatkan riwayat stok bulanan untuk obat tertentu (fully optimized version)
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

        // Fully optimized: Hitung semua stok akhir dengan single query untuk semua periode
        $periodes = $stokBulanan->map(function ($item) {
            return [
                'tahun' => $item->tahun,
                'bulan' => $item->bulan,
                'index_key' => $item->tahun.'_'.$item->bulan, // Unique key untuk mapping
            ];
        });

        // Build single query untuk semua periode sekaligus
        $stokAkhirMap = collect();
        if ($periodes->isNotEmpty()) {
            $stokAkhirQuery = DB::table('stok_bulanans as sb')
                ->selectRaw('sb.tahun, sb.bulan, COALESCE(SUM(sb.stok_masuk), 0) - COALESCE(SUM(sb.stok_pakai), 0) as net_stok')
                ->where('sb.obat_id', $obatId)
                ->where(function ($query) use ($periodes) {
                    foreach ($periodes as $periode) {
                        $query->orWhere(function ($subQuery) use ($periode) {
                            $subQuery->where('sb.tahun', '<', $periode['tahun'])
                                ->orWhere(function ($subSubQuery) use ($periode) {
                                    $subSubQuery->where('sb.tahun', $periode['tahun'])
                                        ->where('sb.bulan', '<=', $periode['bulan']);
                                });
                        });
                    }
                })
                ->groupBy('sb.tahun', 'sb.bulan')
                ->get();

            // Map hasil ke periode yang sesuai
            foreach ($stokAkhirQuery as $result) {
                $key = $result->tahun.'_'.$result->bulan;
                $stokAkhirMap->put($key, $result->net_stok);
            }
        }

        // Assign stok akhir ke setiap item
        $result = $stokBulanan->map(function ($item) use ($stokAwal, $stokAkhirMap) {
            $key = $item->tahun.'_'.$item->bulan;
            $netStok = $stokAkhirMap->get($key, 0);
            $item->stok_akhir = $stokAwal + $netStok;

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
