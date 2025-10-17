<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Obat extends Model
{
    protected $table = 'obat';
    protected $primaryKey = 'id_obat';
    public $timestamps = false;

    protected $fillable = [
        'nama_obat',
        'keterangan',
        'id_jenis_obat',
        'id_satuan',
        'jumlah_per_kemasan',
        'harga_per_satuan',
        'harga_per_kemasan',
        'tanggal_update',
    ];

    protected $casts = [
        'jumlah_per_kemasan' => 'integer',
        'harga_per_satuan' => 'decimal:2',
        'harga_per_kemasan' => 'decimal:2',
        'tanggal_update' => 'datetime',
    ];

    // Relationships
    public function jenisObat()
    {
        return $this->belongsTo(JenisObat::class, 'id_jenis_obat', 'id_jenis_obat');
    }

    public function satuanObat()
    {
        return $this->belongsTo(SatuanObat::class, 'id_satuan', 'id_satuan');
    }

    public function diagnosas()
    {
        return $this->belongsToMany(
            Diagnosa::class,
            'diagnosa_obat',
            'id_obat',
            'id_diagnosa'
        );
    }

    // Relasi ke Keluhan
    public function keluhans()
    {
        return $this->hasMany(Keluhan::class, 'id_obat', 'id_obat');
    }

    // Relasi ke Stok Bulanan
    public function stokBulanans()
    {
        return $this->hasMany(StokObat::class, 'id_obat', 'id_obat');
    }

    // Auto-update tanggal_update before saving
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($obat) {
            // Update tanggal_update only if not already set
            if (!$obat->tanggal_update) {
                $obat->tanggal_update = now();
            }
        });

        // Add logging for debugging
        static::created(function ($obat) {
            \Log::info('Obat created successfully', [
                'id_obat' => $obat->id_obat,
                'nama_obat' => $obat->nama_obat,
                'id_jenis_obat' => $obat->id_jenis_obat,
                'id_satuan' => $obat->id_satuan
            ]);
        });

        static::updated(function ($obat) {
            \Log::info('Obat updated successfully', [
                'id_obat' => $obat->id_obat,
                'nama_obat' => $obat->nama_obat,
                'id_jenis_obat' => $obat->id_jenis_obat,
                'id_satuan' => $obat->id_satuan
            ]);
        });
    }
}
