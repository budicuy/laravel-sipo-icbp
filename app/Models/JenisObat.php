<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisObat extends Model
{
    protected $table = 'jenis_obat';
    protected $primaryKey = 'id_jenis_obat';
    public $timestamps = false;

    protected $fillable = [
        'nama_jenis',
    ];

    public function obats()
    {
        return $this->hasMany(Obat::class, 'id_jenis_obat', 'id_jenis_obat');
    }
}
