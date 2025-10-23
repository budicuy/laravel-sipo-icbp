<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Keluhan extends Model
{
    protected $table = 'keluhan';
    protected $primaryKey = 'id_keluhan';
    public $timestamps = false;

    protected $fillable = [
        'id_rekam',
        'id_emergency', // Kolom untuk relasi dengan rekam_medis_emergency
        'id_diagnosa',
        'id_diagnosa_emergency', // Kolom untuk relasi langsung dengan diagnosa_emergency
        'terapi',
        'keterangan',
        'id_obat',
        'jumlah_obat',
        'aturan_pakai',
        'id_keluarga',
    ];

    protected $casts = [
        'tanggal_periksa' => 'date',
        'jumlah_obat' => 'integer',
    ];

    /**
     * Relasi ke tabel rekam_medis
     */
    public function rekamMedis()
    {
        return $this->belongsTo(RekamMedis::class, 'id_rekam', 'id_rekam');
    }

    /**
     * Relasi ke tabel diagnosa
     */
    public function diagnosa()
    {
        return $this->belongsTo(Diagnosa::class, 'id_diagnosa', 'id_diagnosa');
    }

    /**
     * Relasi ke tabel obat
     */
    public function obat()
    {
        return $this->belongsTo(Obat::class, 'id_obat', 'id_obat');
    }

    /**
     * Relasi ke tabel keluarga
     */
    public function keluarga()
    {
        return $this->belongsTo(Keluarga::class, 'id_keluarga', 'id_keluarga');
    }

    /**
     * Relasi ke tabel rekam_medis_emergency
     */
    public function rekamMedisEmergency()
    {
        return $this->belongsTo(RekamMedisEmergency::class, 'id_emergency', 'id_emergency');
    }

    /**
     * Relasi ke diagnosa_emergency (relasi langsung)
     */
    public function diagnosaEmergency()
    {
        return $this->belongsTo(DiagnosaEmergency::class, 'id_diagnosa_emergency', 'id_diagnosa_emergency');
    }

    /**
     * Scope untuk mendapatkan keluhan emergency
     */
    public function scopeEmergency($query)
    {
        return $query->whereNotNull('id_emergency');
    }

    /**
     * Scope untuk mendapatkan keluhan regular
     */
    public function scopeRegular($query)
    {
        return $query->whereNotNull('id_rekam');
    }

    /**
     * Accessor untuk menentukan jenis keluhan
     */
    public function getJenisKeluhanAttribute()
    {
        if ($this->id_emergency) {
            return 'Emergency';
        } elseif ($this->id_rekam) {
            return 'Regular';
        }
        return 'Unknown';
    }

    /**
     * Mutator untuk memastikan hanya satu jenis relasi yang aktif
     */
    public function setIdEmergencyAttribute($value)
    {
        if ($value) {
            $this->attributes['id_rekam'] = null;
        }
        $this->attributes['id_emergency'] = $value;
    }

    /**
     * Mutator untuk memastikan hanya satu jenis relasi yang aktif
     */
    public function setIdRekamAttribute($value)
    {
        if ($value) {
            $this->attributes['id_emergency'] = null;
        }
        $this->attributes['id_rekam'] = $value;
    }

    // Scope untuk filter keluhan dengan obat
    public function scopeWithObat($query)
    {
        return $query->whereNotNull('id_obat');
    }

    // Scope untuk filter berdasarkan jenis terapi
    public function scopeByTerapi($query, $terapi)
    {
        return $query->where('terapi', $terapi);
    }

    // Accessor untuk menghitung subtotal biaya obat
    public function getSubtotalBiayaAttribute()
    {
        return $this->jumlah_obat * ($this->obat->harga_per_satuan ?? 0);
    }

    // Accessor untuk format terapi yang lebih deskriptif
    public function getTerapiDeskripsiAttribute()
    {
        $descriptions = [
            'Obat' => 'Terapi Obat',
            'Lab' => 'Pemeriksaan Laboratorium',
            'Istirahat' => 'Istirahat',
        ];

        return $descriptions[$this->terapi] ?? $this->terapi;
    }

    // Mutator untuk memastikan jumlah obat selalu positif
    public function setJumlahObatAttribute($value)
    {
        $this->attributes['jumlah_obat'] = max(0, (int) $value);
    }
}
