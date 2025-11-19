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

    // Many-to-many relationship dengan medical check up
    public function medicalCheckUps()
    {
        return $this->belongsToMany(MedicalCheckUp::class, 'medical_check_up_kondisi_kesehatan', 'id_kondisi_kesehatan', 'id_medical_check_up')
                    ->withTimestamps();
    }

    // Backward compatibility - single relationship (deprecated)
    public function medicalCheckUpSingle()
    {
        return $this->hasMany(MedicalCheckUp::class, 'id_kondisi_kesehatan', 'id_medical_check_up');
    }
}
