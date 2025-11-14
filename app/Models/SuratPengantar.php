<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratPengantar extends Model
{
    use HasFactory;

    protected $table = 'surat_pengantars';

    protected $fillable = [
        'nomor_surat',
        'nama_pasien',
        'nik_karyawan_penanggung_jawab',
        'tanggal_pengantar',
        'diagnosa',
        'catatan',
        'lama_istirahat',
        'tanggal_mulai_istirahat',
        'petugas_medis',
        'qrcode_path',
    ];

    protected $casts = [
        'diagnosa' => 'array',
        'tanggal_pengantar' => 'date',
        'tanggal_mulai_istirahat' => 'date',
    ];

    /**
     * Generate nomor surat otomatis
     */
    public static function generateNomorSurat()
    {
        $year = date('Y');
        $month = date('m');

        $lastSurat = self::whereYear('tanggal_pengantar', $year)
            ->whereMonth('tanggal_pengantar', $month)
            ->orderBy('nomor_surat', 'desc')
            ->first();

        if ($lastSurat) {
            $lastNumber = intval(substr($lastSurat->nomor_surat, 0, 4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return $newNumber . '/SP-ICBP/' . $month . '/' . $year;
    }

    /**
     * Get tanggal selesai istirahat
     */
    public function getTanggalSelesaiIstirahatAttribute()
    {
        if ($this->tanggal_mulai_istirahat && $this->lama_istirahat) {
            return $this->tanggal_mulai_istirahat->addDays($this->lama_istirahat - 1);
        }
        return null;
    }
}
