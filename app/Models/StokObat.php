<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StokObat extends Model
{
    protected $table = 'stok_obat';
    protected $primaryKey = 'id_stok_obat';

    // Disable timestamps if not using default created_at/updated_at
    public $timestamps = true;

    protected $fillable = [
        'id_obat',
        'periode',
        'stok_awal',
        'stok_masuk',
        'stok_pakai',
        'stok_akhir',
        'is_initial_stok',
        'keterangan',
    ];

    protected $casts = [
        'stok_awal' => 'integer',
        'stok_masuk' => 'integer',
        'stok_pakai' => 'integer',
        'stok_akhir' => 'integer',
        'is_initial_stok' => 'boolean',
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

    // Scope untuk stok awal
    public function scopeInitialStok($query, $isInitial = true)
    {
        return $query->where('is_initial_stok', $isInitial);
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
     * Mendapatkan stok akhir dari bulan sebelumnya untuk obat tertentu
     */
    public static function getStokAkhirBulanSebelumnya($idObat, $periode)
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
            $prevPeriode = sprintf('%02d-%02d', $prevMonth, $prevYear % 100);

            // Cari stok obat di periode sebelumnya
            $stokSebelumnya = self::where('id_obat', $idObat)
                                ->where('periode', $prevPeriode)
                                ->first();

            // Jika ada data stok di bulan sebelumnya, kembalikan stok akhirnya
            if ($stokSebelumnya) {
                return $stokSebelumnya->stok_akhir;
            }
        }

        // Jika tidak ada data bulan sebelumnya, kembalikan 0
        return 0;
    }

    /**
     * Menghitung stok akhir berdasarkan rumus: Stok Awal + Stok Masuk - Stok Pakai
     */
    public static function hitungStokAkhir($stokAwal, $stokPakai, $stokMasuk)
    {
        return $stokAwal + $stokMasuk - $stokPakai;
    }

    /**
     * Validasi konsistensi data stok
     */
    public function validateStokConsistency()
    {
        $expectedStokAkhir = self::hitungStokAkhir($this->stok_awal, $this->stok_pakai, $this->stok_masuk);

        return [
            'is_valid' => $this->stok_akhir == $expectedStokAkhir,
            'expected_stok_akhir' => $expectedStokAkhir,
            'actual_stok_akhir' => $this->stok_akhir,
            'difference' => $this->stok_akhir - $expectedStokAkhir
        ];
    }

    /**
     * Update stok awal otomatis dari stok akhir bulan sebelumnya
     */
    public static function updateStokAwalFromPreviousMonth($idObat, $periode)
    {
        $stokAwal = self::getStokAkhirBulanSebelumnya($idObat, $periode);

        // Update atau create stok obat dengan stok awal yang benar
        self::updateOrCreate(
            [
                'id_obat' => $idObat,
                'periode' => $periode,
            ],
            [
                'stok_awal' => $stokAwal,
            ]
        );

        return $stokAwal;
    }

    /**
     * Hitung stok pakai dari tabel keluhan berdasarkan periode dan id_obat
     */
    public static function hitungStokPakaiDariKeluhan($idObat, $periode)
    {
        // Parse periode untuk mendapatkan bulan dan tahun
        if (preg_match('/^(\d{2})-(\d{2})$/', $periode, $matches)) {
            $month = $matches[1];
            $year = '20' . $matches[2]; // Convert YY to YYYY

            // Query untuk menghitung total jumlah_obat dari tabel keluhan
            $totalPakai = DB::table('keluhan as k')
                ->join('rekam_medis as r', 'k.id_rekam', '=', 'r.id_rekam')
                ->where('k.id_obat', $idObat)
                ->whereYear('r.tanggal_periksa', $year)
                ->whereMonth('r.tanggal_periksa', $month)
                ->sum('k.jumlah_obat');

            return (int) $totalPakai;
        }

        return 0;
    }

    /**
     * Update stok pakai untuk semua obat pada periode tertentu
     */
    public static function updateStokPakaiPerPeriode($periode)
    {
        $obats = Obat::all();
        $updatedCount = 0;

        foreach ($obats as $obat) {
            $stokPakai = self::hitungStokPakaiDariKeluhan($obat->id_obat, $periode);
            
            // Update stok pakai dan hitung ulang stok akhir
            $stokObat = self::where('id_obat', $obat->id_obat)
                           ->where('periode', $periode)
                           ->first();

            if ($stokObat) {
                $stokAkhir = self::hitungStokAkhir($stokObat->stok_awal, $stokPakai, $stokObat->stok_masuk);
                
                $stokObat->update([
                    'stok_pakai' => $stokPakai,
                    'stok_akhir' => $stokAkhir
                ]);
                
                $updatedCount++;
            }
        }

        return $updatedCount;
    }

    /**
     * Tambah stok masuk untuk periode tertentu
     */
    public static function tambahStokMasuk($idObat, $periode, $jumlah, $keterangan = null)
    {
        $stokObat = self::updateOrCreate(
            [
                'id_obat' => $idObat,
                'periode' => $periode,
            ],
            [
                'stok_awal' => self::getStokAkhirBulanSebelumnya($idObat, $periode),
                'stok_masuk' => 0,
                'stok_pakai' => 0,
                'stok_akhir' => 0,
                'keterangan' => $keterangan,
            ]
        );

        // Update stok masuk
        $stokObat->stok_masuk += $jumlah;
        
        // Hitung ulang stok akhir
        $stokObat->stok_akhir = self::hitungStokAkhir(
            $stokObat->stok_awal, 
            $stokObat->stok_pakai, 
            $stokObat->stok_masuk
        );
        
        $stokObat->save();

        return $stokObat;
    }

    /**
     * Buat stok awal pertama kali untuk obat
     */
    public static function buatStokAwalPertama($idObat, $periode, $jumlah)
    {
        return self::updateOrCreate(
            [
                'id_obat' => $idObat,
                'periode' => $periode,
            ],
            [
                'stok_awal' => 0,
                'stok_masuk' => $jumlah,
                'stok_pakai' => 0,
                'stok_akhir' => $jumlah,
                'is_initial_stok' => true,
                'keterangan' => 'Stok awal pertama kali',
            ]
        );
    }

    /**
     * Cek apakah obat sudah memiliki stok awal pertama
     */
    public static function hasInitialStok($idObat)
    {
        return self::where('id_obat', $idObat)
                   ->where('is_initial_stok', true)
                   ->exists();
    }
}