<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_kategori';
    protected $table = 'kategoris';

    protected $fillable = [
        'kode_kategori',
        'nama_kategori',
    ];

    public function externalEmployees()
    {
        return $this->hasMany(ExternalEmployee::class, 'id_kategori', 'id_kategori');
    }

    public static function getByKode($kode)
    {
        return self::where('kode_kategori', $kode)->first();
    }
}