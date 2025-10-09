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
}
