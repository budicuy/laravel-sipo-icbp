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
     * Menghitung sisa stok untuk obat tertentu hingga periode tertentu
     */
    public static function getSisaStokHingga($obatId, $tahun, $bulan)
    {
        $obat = Obat::find($obatId);
        if (!$obat) {
            return 0;
        }

        $stokAwal = $obat->stok_awal;
        $totalStokMasuk = self::getTotalStokMasukHingga($obatId, $tahun, $bulan);
        $totalStokPakai = self::getTotalStokPakaiHingga($obatId, $tahun, $bulan);

        return $stokAwal + $totalStokMasuk - $totalStokPakai;
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
     * Mendapatkan riwayat stok bulanan untuk obat tertentu
     */
    public static function getRiwayatStok($obatId, $limit = 12)
    {
        return self::where('obat_id', $obatId)
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->limit($limit)
            ->get()
            ->map(function($item) use ($obatId) {
                $item->stok_akhir = self::getSisaStokHingga($obatId, $item->tahun, $item->bulan);
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
}
