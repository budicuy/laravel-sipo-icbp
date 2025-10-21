<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExternalEmployee extends Model
{
    use HasFactory;

    protected $table = 'external_employees';

    protected $fillable = [
        'nik_employee',
        'nama_employee',
        'kode_rm',
        'tanggal_lahir',
        'jenis_kelamin',
        'alamat',
        'no_hp',
        'id_vendor',
        'no_ktp',
        'bpjs_id',
        'id_kategori',
        'foto',
        'status',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'id_vendor', 'id_vendor');
    }

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'id_kategori', 'id_kategori');
    }

    public function getJenisKelaminAttribute()
    {
        return $this->attributes['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan';
    }

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    public function scopeByVendor($query, $vendorId)
    {
        return $query->where('id_vendor', $vendorId);
    }

    public function scopeByKategori($query, $kategoriId)
    {
        return $query->where('id_kategori', $kategoriId);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('nik_employee', 'like', "%{$search}%")
              ->orWhere('nama_employee', 'like', "%{$search}%")
              ->orWhere('kode_rm', 'like', "%{$search}%");
        });
    }
}