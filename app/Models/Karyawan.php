<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Karyawan extends Model
{
    use HasFactory;

    protected $table = 'karyawan';
    protected $primaryKey = 'id_karyawan';
    public $timestamps = true;

    protected $fillable = [
        'nik_karyawan',
        'nama_karyawan',
        'tanggal_lahir',
        'jenis_kelamin',
        'alamat',
        'no_hp',
        'id_departemen',
        'foto',
        'email',
        'bpjs_id',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    // Mutator: Convert J/P to full format when saving
    public function setJenisKelaminAttribute($value)
    {
        $value = strtoupper(trim($value));

        if ($value === 'L' || $value === 'J') {
            $this->attributes['jenis_kelamin'] = 'Laki - Laki';
        } elseif ($value === 'P') {
            $this->attributes['jenis_kelamin'] = 'Perempuan';
        } else {
            // If already full format, keep it
            $this->attributes['jenis_kelamin'] = $value;
        }
    }

    // Accessor: Get short format (optional, if needed)
    public function getJenisKelaminShortAttribute()
    {
        if ($this->jenis_kelamin === 'Laki - Laki') {
            return 'L';
        } elseif ($this->jenis_kelamin === 'Perempuan') {
            return 'P';
        }
        return $this->jenis_kelamin;
    }

    public function departemen(): BelongsTo
    {
        return $this->belongsTo(Departemen::class, 'id_departemen', 'id_departemen');
    }

    // Relasi ke Keluarga
    public function keluargas()
    {
        return $this->hasMany(Keluarga::class, 'id_karyawan', 'id_karyawan');
    }
}


