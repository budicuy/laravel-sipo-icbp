<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiagnosaEmergency extends Model
{
    use HasFactory;

    protected $table = 'diagnosa_emergency';
    protected $primaryKey = 'id_diagnosa_emergency';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'nama_diagnosa_emergency',
        'deskripsi',
    ];

    /**
     * Relasi ke tabel keluhan emergency (jika ada)
     */
    public function keluhans()
    {
        return $this->hasMany(Keluhan::class, 'id_diagnosa', 'id_diagnosa_emergency');
    }

    /**
     * Relasi many-to-many dengan tabel obat
     */
    public function obats()
    {
        return $this->belongsToMany(
            Obat::class,
            'diagnosa_emergency_obat',
            'id_diagnosa_emergency',
            'id_obat'
        )->withTimestamps();
    }

    /**
     * Scope untuk pencarian diagnosa
     */
    public function scopeSearch($query, $search)
    {
        if ($search) {
            $query->where('nama_diagnosa_emergency', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
        }
        return $query;
    }

    /**
     * Scope untuk mendapatkan diagnosa dengan obat tertentu
     */
    public function scopeWithObat($query, $obatId)
    {
        return $query->whereHas('obats', function($q) use ($obatId) {
            $q->where('obat.id_obat', $obatId);
        });
    }

    /**
     * Method untuk attach obat ke diagnosa emergency
     */
    public function attachObat($obatId, $attributes = [])
    {
        return $this->obats()->attach($obatId, $attributes);
    }

    /**
     * Method untuk detach obat dari diagnosa emergency
     */
    public function detachObat($obatId = null)
    {
        if ($obatId) {
            return $this->obats()->detach($obatId);
        }
        return $this->obats()->detach();
    }

    /**
     * Method untuk sync obat ke diagnosa emergency
     */
    public function syncObat($obatIds)
    {
        return $this->obats()->sync($obatIds);
    }
}