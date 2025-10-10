<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kunjungan extends Model
{
    use HasFactory;

    protected $table = 'kunjungan';
    protected $primaryKey = 'id_kunjungan';
    public $timestamps = false;

    protected $fillable = [
        'id_keluarga',
        'kode_transaksi',
        'tanggal_kunjungan',
    ];

    protected $casts = [
        'tanggal_kunjungan' => 'date',
    ];

    // Relasi ke Keluarga (Pasien)
    public function keluarga()
    {
        return $this->belongsTo(Keluarga::class, 'id_keluarga', 'id_keluarga');
    }

    // Accessor untuk nomor registrasi (ubah dari kode_transaksi)
    public function getNomorRegistrasiAttribute()
    {
        return $this->kode_transaksi;
    }

    // Mutator untuk nomor registrasi (ubah dari kode_transaksi)
    public function setNomorRegistrasiAttribute($value)
    {
        $this->kode_transaksi = $value;
    }

    // Scope untuk mendapatkan kunjungan berdasarkan rekam medis
    public function scopeFromRekamMedis($query)
    {
        return $query->whereIn('id_keluarga', function($subquery) {
            $subquery->select('id_keluarga')
                     ->from('rekam_medis');
        });
    }

    // Scope untuk filter berdasarkan rentang tanggal
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('tanggal_kunjungan', [$startDate, $endDate]);
    }

    // Scope untuk filter berdasarkan bulan dan tahun
    public function scopeByMonth($query, $month, $year)
    {
        return $query->whereMonth('tanggal_kunjungan', $month)
                    ->whereYear('tanggal_kunjungan', $year);
    }

    // Scope untuk filter berdasarkan tahun
    public function scopeByYear($query, $year)
    {
        return $query->whereYear('tanggal_kunjungan', $year);
    }

    // Accessor untuk format tanggal kunjungan
    public function getTanggalFormatAttribute()
    {
        return $this->tanggal_kunjungan?->format('d-m-Y') ?? '';
    }

    // Accessor untuk format tanggal dengan hari
    public function getTanggalDenganHariAttribute()
    {
        return $this->tanggal_kunjungan?->locale('id')->format('l, d F Y') ?? '';
    }

    // Relasi ke RekamMedis (melalui Keluarga)
    public function rekamMedis()
    {
        return $this->hasOneThrough(
            RekamMedis::class,
            Keluarga::class,
            'id_keluarga', // Foreign key on keluarga table
            'id_keluarga', // Foreign key on rekam_medis table
            'id_keluarga', // Local key on kunjungan table
            'id_keluarga'  // Local key on keluarga table
        )->whereDate('rekam_medis.tanggal_periksa', '=', $this->tanggal_kunjungan);
    }

    // Method untuk membuat atau update kunjungan dari rekam medis
    public static function createFromRekamMedis(RekamMedis $rekamMedis)
    {
        return self::updateOrCreate(
            [
                'id_keluarga' => $rekamMedis->id_keluarga,
                'tanggal_kunjungan' => $rekamMedis->tanggal_periksa
            ],
            [
                'kode_transaksi' => $rekamMedis->kode_transaksi
            ]
        );
    }
}
