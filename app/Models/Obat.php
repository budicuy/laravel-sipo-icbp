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
        'stok_awal',
        'stok_masuk',
        'stok_keluar',
        'stok_akhir',
        'jumlah_per_kemasan',
        'harga_per_satuan',
        'harga_per_kemasan',
        'tanggal_update',
    ];

    protected $casts = [
        'stok_awal' => 'integer',
        'stok_masuk' => 'integer',
        'stok_keluar' => 'integer',
        'stok_akhir' => 'integer',
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

    // Auto-calculate stok_akhir before saving
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($obat) {
            // Calculate stok_akhir: (stok_awal + stok_masuk - stok_keluar)
            $obat->stok_akhir = ($obat->stok_awal ?? 0) + ($obat->stok_masuk ?? 0) - ($obat->stok_keluar ?? 0);

            // Update tanggal_update
            $obat->tanggal_update = now();
        });
    }
}
