<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SuratPengantarIstirahat extends Model
{
    use HasFactory;

    protected $table = 'surat_pengantar_istirahat';

    protected $primaryKey = 'id_surat';

    protected $fillable = [
        'id_rekam',
        'id_emergency',
        'id_keluarga',
        'tipe_pasien',
        'tanggal_surat',
        'lama_istirahat',
        'tanggal_mulai_istirahat',
        'tanggal_selesai_istirahat',
        'diagnosa_utama',
        'keterangan_tambahan',
        'id_dokter',
        'nomor_surat',
    ];

    protected $casts = [
        'tanggal_surat' => 'date',
        'tanggal_mulai_istirahat' => 'date',
        'tanggal_selesai_istirahat' => 'date',
    ];

    /**
     * Relasi ke RekamMedis
     */
    public function rekamMedis(): BelongsTo
    {
        return $this->belongsTo(RekamMedis::class, 'id_rekam', 'id_rekam');
    }

    /**
     * Relasi ke RekamMedisEmergency
     */
    public function rekamMedisEmergency(): BelongsTo
    {
        return $this->belongsTo(RekamMedisEmergency::class, 'id_emergency', 'id_emergency');
    }

    /**
     * Relasi ke Keluarga (Pasien)
     */
    public function keluarga(): BelongsTo
    {
        return $this->belongsTo(Keluarga::class, 'id_keluarga', 'id_keluarga');
    }

    /**
     * Relasi ke User (Dokter)
     */
    public function dokter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_dokter', 'id_user');
    }

    /**
     * Accessor untuk mendapatkan NIK karyawan melalui keluarga
     */
    public function getNikKaryawanAttribute()
    {
        return $this->keluarga?->karyawan?->nik_karyawan;
    }

    /**
     * Accessor untuk mendapatkan nama karyawan
     */
    public function getNamaKaryawanAttribute()
    {
        return $this->keluarga?->karyawan?->nama_karyawan;
    }

    /**
     * Accessor untuk mendapatkan nama pasien
     */
    public function getNamaPasienAttribute()
    {
        return $this->keluarga?->nama_keluarga;
    }

    /**
     * Accessor untuk mendapatkan departemen karyawan
     */
    public function getDepartemenAttribute()
    {
        return $this->keluarga?->karyawan?->departemen?->nama_departemen;
    }

    /**
     * Accessor untuk mendapatkan NIK pasien (baik dari karyawan atau external employee)
     */
    public function getNikPasienAttribute()
    {
        if ($this->tipe_pasien === 'emergency') {
            return $this->rekamMedisEmergency?->nik_pasien;
        }

        return $this->keluarga?->karyawan?->nik_karyawan;
    }

    /**
     * Accessor untuk mendapatkan nama pasien
     */
    public function getNamaPasienEmergencyAttribute()
    {
        if ($this->tipe_pasien === 'emergency') {
            return $this->rekamMedisEmergency?->nama_pasien;
        }

        return $this->keluarga?->nama_keluarga;
    }

    /**
     * Accessor untuk mendapatkan nama karyawan/external employee
     */
    public function getNamaKaryawanEmergencyAttribute()
    {
        if ($this->tipe_pasien === 'emergency') {
            return $this->rekamMedisEmergency?->nama_pasien; // Untuk emergency, nama pasien = nama employee
        }

        return $this->keluarga?->karyawan?->nama_karyawan;
    }

    /**
     * Accessor untuk mendapatkan departemen (untuk emergency akan menampilkan 'Emergency')
     */
    public function getDepartemenEmergencyAttribute()
    {
        if ($this->tipe_pasien === 'emergency') {
            return 'Emergency';
        }

        return $this->keluarga?->karyawan?->departemen?->nama_departemen;
    }

    /**
     * Mutator untuk menghitung tanggal selesai istirahat otomatis
     */
    public function setLamaIstirahatAttribute($value)
    {
        $this->attributes['lama_istirahat'] = $value;

        // Jika tanggal mulai istirahat sudah diisi, hitung tanggal selesai
        if (isset($this->attributes['tanggal_mulai_istirahat']) && $value) {
            $tanggalMulai = \Carbon\Carbon::parse($this->attributes['tanggal_mulai_istirahat']);
            $this->attributes['tanggal_selesai_istirahat'] = $tanggalMulai->addDays($value - 1)->format('Y-m-d');
        }
    }

    /**
     * Mutator untuk menghitung tanggal selesai istirahat saat tanggal mulai berubah
     */
    public function setTanggalMulaiIstirahatAttribute($value)
    {
        $this->attributes['tanggal_mulai_istirahat'] = $value;

        // Jika lama istirahat sudah diisi, hitung tanggal selesai
        if (isset($this->attributes['lama_istirahat']) && $this->attributes['lama_istirahat'] && $value) {
            $tanggalMulai = \Carbon\Carbon::parse($value);
            $this->attributes['tanggal_selesai_istirahat'] = $tanggalMulai->addDays($this->attributes['lama_istirahat'] - 1)->format('Y-m-d');
        }
    }

    /**
     * Scope untuk pencarian berdasarkan NIK atau nama
     */
    public function scopeSearchByNikOrName($query, $search)
    {
        return $query->whereHas('keluarga.karyawan', function ($karyawan) use ($search) {
            $karyawan->where('nik_karyawan', 'like', "%{$search}%")
                ->orWhere('nama_karyawan', 'like', "%{$search}%");
        })->orWhereHas('keluarga', function ($keluarga) use ($search) {
            $keluarga->where('nama_keluarga', 'like', "%{$search}%");
        })->orWhereHas('rekamMedisEmergency', function ($emergency) use ($search) {
            $emergency->where('nik_pasien', 'like', "%{$search}%")
                ->orWhere('nama_pasien', 'like', "%{$search}%");
        });
    }

    /**
     * Scope untuk filter berdasarkan status rekam medis (On Progress)
     */
    public function scopeWithRekamMedisOnProgress($query)
    {
        return $query->whereHas('rekamMedis', function ($rekamMedis) {
            $rekamMedis->where('status', 'On Progress');
        });
    }

    /**
     * Generate nomor surat otomatis
     */
    public static function generateNomorSurat()
    {
        $date = now();
        $year = $date->format('Y');
        $month = $date->format('m');

        // Cari nomor urut terakhir pada bulan dan tahun yang sama
        $lastSurat = self::whereYear('tanggal_surat', $year)
            ->whereMonth('tanggal_surat', $month)
            ->orderBy('id_surat', 'desc')
            ->first();

        // Jika ada surat di tahun yang sama, tambah 1
        // Jika tidak ada surat di tahun yang sama, mulai dari 1
        $nomorUrut = $lastSurat ? ((int) substr($lastSurat->nomor_surat, 0, 3)) + 1 : 1;

        return str_pad($nomorUrut, 3, '0', STR_PAD_LEFT).'/SKS/'.$month.'/'.$year;
    }
}
