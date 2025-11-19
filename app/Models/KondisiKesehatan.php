<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KondisiKesehatan extends Model
{
    use HasFactory;

    protected $table = 'kondisi_kesehatan';
    
    protected $primaryKey = 'id';
    
    public $incrementing = true;
    
    protected $keyType = 'int';

    protected $fillable = [
        'nama_kondisi',
        'deskripsi',
    ];

    protected $casts = [
        'id' => 'integer',
    ];

    public function medicalCheckUps()
    {
        return $this->hasMany(MedicalCheckUp::class, 'id_kondisi_kesehatan', 'id');
    }
}