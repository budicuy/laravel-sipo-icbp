<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
