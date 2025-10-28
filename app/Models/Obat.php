<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Obat extends Model
{
    protected $table = 'obat';
    protected $primaryKey = 'id_obat';
    public $timestamps = false;

    protected $fillable = [
        'nama_obat',
        'keterangan',
        'id_satuan',
        'stok_awal',
        'tanggal_update',
    ];

    protected $casts = [
        'tanggal_update' => 'datetime',
    ];

    // Relationships
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

    /**
     * Relasi many-to-many dengan diagnosa emergency
     */
    public function diagnosaEmergencies()
    {
        return $this->belongsToMany(
            DiagnosaEmergency::class,
            'diagnosa_emergency_obat',
            'id_obat',
            'id_diagnosa_emergency'
        )->withTimestamps();
    }

    // Relasi ke Keluhan
    public function keluhans()
    {
        return $this->hasMany(Keluhan::class, 'id_obat', 'id_obat');
    }

    // Relasi ke Stok Bulanan (lama)
    public function stokObats()
    {
        return $this->hasMany(StokObat::class, 'id_obat', 'id_obat');
    }

    // Relasi ke Stok Bulanan (baru)
    public function stokBulanans()
    {
        return $this->hasMany(StokBulanan::class, 'obat_id', 'id_obat');
    }

    // Relasi ke Harga Obat Per Bulan
    public function hargaObatPerBulans()
    {
        return $this->hasMany(HargaObatPerBulan::class, 'id_obat', 'id_obat');
    }

    // Hapus fungsi harga karena tidak lagi relevan dengan struktur baru

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
            Log::info('Obat created successfully', [
                'id_obat' => $obat->id_obat,
                'nama_obat' => $obat->nama_obat,
                'id_satuan' => $obat->id_satuan
            ]);
        });

        static::updated(function ($obat) {
            Log::info('Obat updated successfully', [
                'id_obat' => $obat->id_obat,
                'nama_obat' => $obat->nama_obat,
                'id_satuan' => $obat->id_satuan
            ]);
        });
    }

    /**
     * Menghitung sisa stok saat ini
     *
     * Accessor ini hanya digunakan jika sisa_stok belum di-set secara manual
     * Untuk menghindari N+1 query, gunakan batch approach di controller
     */
    public function getSisaStokAttribute()
    {
        // Jika sisa_stok sudah di-set (misal dari batch calculation di controller),
        // gunakan nilai tersebut untuk menghindari query individual
        if (array_key_exists('sisa_stok', $this->attributes)) {
            return $this->attributes['sisa_stok'];
        }

        // Fallback ke individual query hanya jika benar-benar diperlukan
        // dan log untuk debugging
        Log::warning('N+1 Query Warning: getSisaStokAttribute dipanggil secara individual', [
            'obat_id' => $this->id_obat,
            'nama_obat' => $this->nama_obat,
            'trace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5)
        ]);

        return StokBulanan::getSisaStokSaatIni($this->id_obat);
    }

    /**
     * Mendapatkan riwayat stok bulanan
     */
    public function getRiwayatStok($limit = 12)
    {
        return StokBulanan::getRiwayatStok($this->id_obat, $limit);
    }
}
