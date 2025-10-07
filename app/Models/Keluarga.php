<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Keluarga extends Model
{
    use HasFactory;

    protected $table = 'keluarga';
    protected $primaryKey = 'id_keluarga';

    protected $fillable = [
        'id_karyawan',
        'nama_keluarga',
        'tanggal_lahir',
        'jenis_kelamin',
        'alamat',
        'tanggal_daftar',
        'no_rm',
        'kode_hubungan',
        'no_ktp'
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_daftar' => 'date',
    ];

    // Relasi ke Karyawan
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan', 'id_karyawan');
    }

    // Relasi ke Hubungan
    public function hubungan()
    {
        return $this->belongsTo(Hubungan::class, 'kode_hubungan', 'kode_hubungan');
    }

    // Accessor untuk jenis kelamin pendek
    public function getJenisKelaminShortAttribute()
    {
        return $this->jenis_kelamin === 'Laki - Laki' ? 'L' : 'P';
    }

    // Mutator untuk jenis kelamin
    public function setJenisKelaminAttribute($value)
    {
        if (in_array($value, ['L', 'Laki - Laki'])) {
            $this->attributes['jenis_kelamin'] = 'Laki - Laki';
        } elseif (in_array($value, ['P', 'J', 'Perempuan'])) {
            $this->attributes['jenis_kelamin'] = 'Perempuan';
        }
    }
}
