<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hubungan extends Model
{
    protected $table = 'hubungan';
    protected $primaryKey = 'kode_hubungan';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'kode_hubungan',
        'hubungan',
    ];

    // Relasi ke Keluarga
    public function keluargas()
    {
        return $this->hasMany(Keluarga::class, 'kode_hubungan', 'kode_hubungan');
    }
}
