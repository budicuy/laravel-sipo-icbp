<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RekamMedis extends Model
{
    use HasFactory;

    protected $table = 'rekam_medis';
    protected $primaryKey = 'id_rekam';
    public $timestamps = false;

    protected $fillable = [
        'id_keluarga',
        'tanggal_periksa',
        'id_user',
        'jumlah_keluhan',
        'status',
    ];

    protected $casts = [
        'tanggal_periksa' => 'date',
    ];

    // Relasi ke Keluarga (Pasien)
    public function keluarga()
    {
        return $this->belongsTo(Keluarga::class, 'id_keluarga', 'id_keluarga');
    }

    // Relasi ke User (Petugas yang input)
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    // Relasi ke Keluhan
    public function keluhans()
    {
        return $this->hasMany(Keluhan::class, 'id_rekam', 'id_rekam');
    }

    // Relasi ke Kunjungan (melalui Keluarga)
    public function kunjungan()
    {
        return $this->hasOneThrough(
            Kunjungan::class,
            Keluarga::class,
            'id_keluarga', // Foreign key on keluarga table
            'id_keluarga', // Foreign key on kunjungan table
            'id_keluarga', // Local key on rekam_medis table
            'id_keluarga'  // Local key on keluarga table
        );
    }

    // Scope untuk filter berdasarkan status
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Scope untuk filter berdasarkan tanggal
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('tanggal_periksa', [$startDate, $endDate]);
    }

    // Scope untuk pencarian pasien
    public function scopeSearchPatient($query, $search)
    {
        return $query->whereHas('keluarga', function($keluarga) use ($search) {
            $keluarga->where('nama_keluarga', 'like', "%{$search}%")
                    ->orWhere('no_rm', 'like', "%{$search}%")
                    ->orWhere('bpjs_id', 'like', "%{$search}%")
                    ->orWhereHas('karyawan', function($karyawan) use ($search) {
                        $karyawan->where('nik_karyawan', 'like', "%{$search}%");
                    });
        });
    }

    // Accessor untuk menghitung total biaya obat
    public function getTotalBiayaAttribute()
    {
        return $this->keluhans->sum(function($keluhan) {
            return $keluhan->jumlah_obat * ($keluhan->obat->harga_per_satuan ?? 0);
        });
    }

    // Accessor untuk format kode transaksi
    public function getKodeTransaksiAttribute()
    {
        $noRunning = str_pad($this->id_rekam, 1, '0', STR_PAD_LEFT);
        $bulan = $this->tanggal_periksa?->format('m') ?? '00';
        $tahun = $this->tanggal_periksa?->format('Y') ?? '0000';
        return "1{$noRunning}/NDL/BJM/{$bulan}/{$tahun}";
    }
}
