<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalCheckUp extends Model
{
    use HasFactory;

    protected $table = 'medical_check_up';

    protected $fillable = [
        'id_karyawan',
        'id_keluarga',
        'id_user',
        'periode',
        'tanggal',
        'dikeluarkan_oleh',
        'kesimpulan_medis',
        'bmi',
        'imt',
        'rekomendasi',
        'file_path',
        'file_name',
        'file_size',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'periode' => 'integer',
    ];

    /**
     * Get the employee that owns the medical check up.
     */
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan');
    }

    /**
     * Get the family member that owns the medical check up.
     */
    public function keluarga()
    {
        return $this->belongsTo(Keluarga::class, 'id_keluarga');
    }

    /**
     * Get the user that created the medical check up.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    /**
     * Get formatted date
     */
    public function getFormattedTanggalAttribute()
    {
        return $this->tanggal ? $this->tanggal->format('d-m-Y') : '-';
    }

    /**
     * Get BMI with color class
     */
    public function getBmiWithClassAttribute()
    {
        $bmiClasses = [
            'Underweight' => 'bg-blue-100 text-blue-800',
            'Normal' => 'bg-green-100 text-green-800',
            'Overweight' => 'bg-yellow-100 text-yellow-800',
            'Obesitas Tk 1' => 'bg-orange-100 text-orange-800',
            'Obesitas Tk 2' => 'bg-red-100 text-red-800',
            'Obesitas Tk 3' => 'bg-red-200 text-red-900',
        ];

        return [
            'value' => $this->bmi,
            'class' => $bmiClasses[$this->bmi] ?? 'bg-gray-100 text-gray-800'
        ];
    }

    /**
     * Get IMT with color class
     */
    public function getImtWithClassAttribute()
    {
        $imtClasses = [
            'Kurus' => 'bg-blue-100 text-blue-800',
            'Normal' => 'bg-green-100 text-green-800',
            'Gemuk' => 'bg-yellow-100 text-yellow-800',
            'Obesitas' => 'bg-red-100 text-red-800',
        ];

        return [
            'value' => $this->imt,
            'class' => $imtClasses[$this->imt] ?? 'bg-gray-100 text-gray-800'
        ];
    }

    /**
     * Get file size in human readable format
     */
    public function getFormattedFileSizeAttribute()
    {
        if (!$this->file_size) {
            return '-';
        }

        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Scope to filter by employee
     */
    public function scopeByEmployee($query, $id_karyawan)
    {
        return $query->where('id_karyawan', $id_karyawan);
    }

    /**
     * Scope to filter by family member
     */
    public function scopeByFamilyMember($query, $id_keluarga)
    {
        return $query->where('id_keluarga', $id_keluarga);
    }

    /**
     * Scope to filter by period
     */
    public function scopeByPeriod($query, $periode)
    {
        return $query->where('periode', $periode);
    }

    /**
     * Scope to get latest records first
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('tanggal', 'desc')->orderBy('created_at', 'desc');
    }
}