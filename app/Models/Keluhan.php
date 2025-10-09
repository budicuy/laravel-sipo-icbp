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
        'id_diagnosa',
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
}
