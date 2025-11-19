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

    // Many-to-many relationship dengan kondisi kesehatan
    public function kondisiKesehatan()
    {
        return $this->belongsToMany(KondisiKesehatan::class, 'medical_check_up_kondisi_kesehatan', 'medical_check_up_id', 'kondisi_kesehatan_id')
                    ->withTimestamps();
    }

    // Accessor untuk mendapatkan nama kondisi kesehatan sebagai string yang digabung
    public function getKondisiKesehatanListAttribute()
    {
        return $this->kondisiKesehatan->pluck('nama_kondisi')->implode(', ');
    }

    // Method untuk mendapatkan array ID kondisi kesehatan
    public function getKondisiKesehatanIdsAttribute()
    {
        return $this->kondisiKesehatan->pluck('id')->toArray();
    }

    // Method untuk sync kondisi kesehatan
    public function syncKondisiKesehatan(array $kondisiIds)
    {
        return $this->kondisiKesehatan()->sync($kondisiIds);
    }

    // Method untuk attach kondisi kesehatan
    public function attachKondisiKesehatan($kondisiId)
    {
        return $this->kondisiKesehatan()->attach($kondisiId);
    }

    // Method untuk detach kondisi kesehatan
    public function detachKondisiKesehatan($kondisiId)
    {
        return $this->kondisiKesehatan()->detach($kondisiId);
    }

    // Backward compatibility - single kondisi kesehatan (deprecated)
    public function getSingleKondisiKesehatanAttribute()
    {
        return $this->kondisiKesehatan->first()?->nama_kondisi;
    }
}