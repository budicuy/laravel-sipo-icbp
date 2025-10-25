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
        'id_external_employee',
        'tanggal_periksa',
        'waktu_periksa',
        'status',
        'keluhan',
        'catatan',
        'id_user',
    ];

    protected $casts = [
        'tanggal_periksa' => 'date',
        'waktu_periksa' => 'datetime:H:i',
        'status' => 'string',
    ];

    /**
     * Get the user that created the emergency medical record.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    /**
     * Get the external employee associated with this emergency record.
     */
    public function externalEmployee()
    {
        return $this->belongsTo(ExternalEmployee::class, 'id_external_employee', 'id');
    }

    /**
     * Get the keluhans associated with this emergency record (one-to-many).
     */
    public function keluhans()
    {
        return $this->hasMany(Keluhan::class, 'id_emergency', 'id_emergency');
    }

    /**
     * Get the diagnosa through keluhan relationship (regular diagnosa).
     */
    public function diagnosas()
    {
        return $this->hasManyThrough(
            Diagnosa::class,
            Keluhan::class,
            'id_emergency', // Foreign key on keluhan table
            'id_diagnosa', // Foreign key on diagnosa table
            'id_emergency', // Local key on rekam_medis_emergency table
            'id_diagnosa'  // Local key on keluhan table
        );
    }

    /**
     * Get the diagnosa emergency through keluhan relationship.
     */
    public function diagnosaEmergencies()
    {
        return $this->hasManyThrough(
            DiagnosaEmergency::class,
            Keluhan::class,
            'id_emergency', // Foreign key on keluhan table
            'id_diagnosa_emergency', // Foreign key on diagnosa_emergency table
            'id_emergency', // Local key on rekam_medis_emergency table
            'id_diagnosa_emergency'  // Local key on keluhan table (references id_diagnosa_emergency)
        );
    }

    /**
     * Create a new keluhan for this emergency record.
     */
    public function createKeluhan($data)
    {
        // Ensure proper data structure for emergency records
        $keluhanData = array_merge([
            'id_emergency' => $this->id_emergency,
            'id_rekam' => null, // Set to null for emergency records to avoid foreign key constraint
            'id_diagnosa' => null, // Set to null for emergency records
            'id_diagnosa_emergency' => null, // Will be set from $data
            'id_keluarga' => null, // Emergency records don't use keluarga
            'id_obat' => null,
            'jumlah_obat' => 0, // Default to 0
            'aturan_pakai' => null,
        ], $data);

        return $this->keluhans()->create($keluhanData);
    }

    /**
     * Get the latest keluhan for this emergency record.
     */
    public function latestKeluhan()
    {
        return $this->keluhans()->latest()->first();
    }

    /**
     * Scope a query to only include records with specific status.
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
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
     * Scope a query to search by name or NIK through external employee.
     */
    public function scopeSearch($query, $search)
    {
        if ($search) {
            $query->whereHas('externalEmployee', function ($sub) use ($search) {
                $sub->where('nama_employee', 'like', "%{$search}%")
                    ->orWhere('nik_employee', 'like', "%{$search}%")
                    ->orWhere('kode_rm', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    /**
     * Accessor for getting NIK from external employee.
     */
    public function getNikPasienAttribute()
    {
        return $this->externalEmployee?->nik_employee;
    }

    /**
     * Accessor for getting patient name from external employee.
     */
    public function getNamaPasienAttribute()
    {
        return $this->externalEmployee?->nama_employee;
    }

    /**
     * Accessor for getting RM number from external employee.
     */
    public function getNoRmAttribute()
    {
        return $this->externalEmployee?->kode_rm;
    }

    /**
     * Accessor for getting gender from external employee.
     */
    public function getJenisKelaminAttribute()
    {
        return $this->externalEmployee?->jenis_kelamin;
    }
}
