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
}
