<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diagnosa extends Model
{
    use HasFactory;

    protected $table = 'diagnosa';
    protected $primaryKey = 'id_diagnosa';

    protected $fillable = [
        'nama_diagnosa',
        'deskripsi',
    ];

    // Relasi many-to-many dengan Obat (rekomendasi obat)
    public function obats()
    {
        return $this->belongsToMany(
            Obat::class,
            'diagnosa_obat',
            'id_diagnosa',
            'id_obat'
        );
    }
}
