<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SatuanObat extends Model
{
    protected $table = 'satuan_obat';
    protected $primaryKey = 'id_satuan';
    public $timestamps = false;

    protected $fillable = [
        'nama_satuan',
    ];

    public function obats()
    {
        return $this->hasMany(Obat::class, 'id_satuan', 'id_satuan');
    }

    // Satuan yang dihitung per kemasan (field jumlah_per_kemasan bisa dipilih)
    public static function satuanPerKemasan()
    {
        return ['Kapsul', 'Box', 'Dus', 'Strip', 'Sachet', 'Pcs', 'Bungkus'];
    }

    // Satuan yang dihitung per satuan (jumlah_per_kemasan = 1 dan disabled)
    public static function satuanPerUnit()
    {
        return ['Ampul', 'Botol', 'Injek'];
    }
}
