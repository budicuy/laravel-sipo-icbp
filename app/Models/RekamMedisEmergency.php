<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RekamMedisEmergency extends Model
{
    use HasFactory;

    protected $table = 'rekam_medis_emergency';
    protected $primaryKey = 'id_emergency';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'nik_pasien',
        'nama_pasien',
        'no_rm',
        'hubungan',
        'jenis_kelamin',
        'tanggal_periksa',
        'status_rekam_medis',
        'diagnosa',
        'keluhan',
        'catatan',
        'id_user',
    ];

    protected $casts = [
        'tanggal_periksa' => 'date',
        'status_rekam_medis' => 'string',
        'jenis_kelamin' => 'string',
    ];

    /**
     * Get the user that created the emergency medical record.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    /**
     * Scope a query to only include records with specific status.
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status_rekam_medis', $status);
    }

    /**
     * Scope a query to only include records within date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        if ($startDate) {
            $query->where('tanggal_periksa', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('tanggal_periksa', '<=', $endDate);
        }
        return $query;
    }

    /**
     * Scope a query to search by name or NIK.
     */
    public function scopeSearch($query, $search)
    {
        if ($search) {
            $query->where(function($sub) use ($search) {
                $sub->where('nama_pasien', 'like', "%{$search}%")
                    ->orWhere('nik_pasien', 'like', "%{$search}%")
                    ->orWhere('no_rm', 'like', "%{$search}%");
            });
        }
        return $query;
    }
}