<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SuratRekomendasiMedis extends Model
{
    protected $table = 'surat_rekomendasi_medis';
    
    protected $fillable = [
        'id_karyawan',
        'id_keluarga',
        'tanggal',
        'penerbit_surat',
        'catatan_medis',
        'file_path',
        'file_name',
        'file_size',
        'mime_type',
        'created_by',
    ];
    
    protected $casts = [
        'tanggal' => 'date',
        'file_size' => 'integer',
    ];
    
    /**
     * Get the employee that owns the medical recommendation letter
     */
    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan', 'id_karyawan');
    }
    
    /**
     * Get the family member that owns the medical recommendation letter
     */
    public function keluarga(): BelongsTo
    {
        return $this->belongsTo(Keluarga::class, 'id_keluarga', 'id_keluarga');
    }
    
    /**
     * Get the user who created the medical recommendation letter
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id_user');
    }
}
