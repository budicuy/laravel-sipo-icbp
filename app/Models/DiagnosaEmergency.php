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
     * Get the obats for the diagnosa.
     */
    public function obats()
    {
        return $this->belongsToMany(Obat::class, 'diagnosa_emergency_obat', 'id_diagnosa_emergency', 'id_obat');
    }

    /**
     * Get the keluhans for the diagnosa.
     */
    public function keluhans()
    {
        return $this->hasMany(Keluhan::class, 'id_diagnosa_emergency', 'id_diagnosa_emergency');
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'id_diagnosa_emergency';
    }
}