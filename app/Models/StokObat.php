<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StokObat extends Model
{
    protected $table = 'stok_bulanan';
    protected $primaryKey = 'id_stok_bulanan';

    // Disable timestamps if not using default created_at/updated_at
    public $timestamps = true;

    protected $fillable = [
        'id_obat',
        'periode',
        'stok_awal',
        'stok_pakai',
        'stok_akhir',
        'stok_masuk',
    ];

    protected $casts = [
        'stok_awal' => 'integer',
        'stok_pakai' => 'integer',
        'stok_akhir' => 'integer',
        'stok_masuk' => 'integer',
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
}
