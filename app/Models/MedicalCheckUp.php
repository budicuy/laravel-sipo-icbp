<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalCheckUp extends Model
{
    use HasFactory;

    protected $table = 'medical_check_up';
    
    protected $primaryKey = 'id';
    
    public $incrementing = true;
    
    protected $keyType = 'int';

    protected $fillable = [
        'id_karyawan',
        'id_keluarga',
        'periode',
        'tanggal',
        'dikeluarkan_oleh',
        'bmi',
        'keterangan_bmi',
        'id_kondisi_kesehatan',
        'catatan',
        'file_path',
        'file_name',
        'file_size',
        'mime_type',
        'id_user',
    ];

    protected $casts = [
        'id' => 'integer',
        'id_karyawan' => 'integer',
        'id_keluarga' => 'integer',
        'periode' => 'integer',
        'tanggal' => 'date',
        'bmi' => 'decimal:2',
        'id_kondisi_kesehatan' => 'integer',
        'file_size' => 'integer',
        'id_user' => 'integer',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan', 'id_karyawan');
    }

    public function keluarga()
    {
        return $this->belongsTo(Keluarga::class, 'id_keluarga', 'id_keluarga');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function kondisiKesehatan()
    {
        return $this->belongsTo(KondisiKesehatan::class, 'id_kondisi_kesehatan', 'id');
    }

    // Accessor untuk mendapatkan nama kondisi kesehatan
    public function getKondisiKesehatanAttribute()
    {
        return $this->kondisiKesehatanRelation?->nama_kondisi;
    }

    // Relationship dengan nama yang berbeda untuk menghindari konflik dengan accessor
    public function kondisiKesehatanRelation()
    {
        return $this->belongsTo(KondisiKesehatan::class, 'id_kondisi_kesehatan', 'id');
    }
}