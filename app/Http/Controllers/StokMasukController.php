<?php

namespace App\Http\Controllers;

use App\Models\Obat;
use App\Models\StokBulanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StokMasukController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'obat_id' => 'required|exists:obat,id_obat',
            'jumlah_stok_masuk' => 'required|integer|min:1',
        ], [
            'obat_id.required' => 'Obat wajib dipilih',
            'obat_id.exists' => 'Obat tidak ditemukan',
            'jumlah_stok_masuk.required' => 'Jumlah stok masuk wajib diisi',
            'jumlah_stok_masuk.integer' => 'Jumlah stok masuk harus berupa angka',
            'jumlah_stok_masuk.min' => 'Jumlah stok masuk minimal 1',
        ]);

        try {
            DB::beginTransaction();

            $obatId = $validated['obat_id'];
            $jumlahStokMasuk = $validated['jumlah_stok_masuk'];

            // Dapatkan tahun dan bulan saat ini
            $tahunSekarang = now()->year;
            $bulanSekarang = now()->month;

            // Log untuk debugging
            Log::info('Menambah stok masuk', [
                'obat_id' => $obatId,
                'jumlah' => $jumlahStokMasuk,
                'tahun' => $tahunSekarang,
                'bulan' => $bulanSekarang
            ]);

            // Cari atau buat record StokBulanan untuk obat_id, tahun, dan bulan tersebut
            $stokBulanan = StokBulanan::getOrCreate($obatId, $tahunSekarang, $bulanSekarang);

            // Tambahkan jumlah_stok_masuk ke kolom stok_masuk
            $stokBulanan->stok_masuk += $jumlahStokMasuk;
            $stokBulanan->save();

            Log::info('Stok masuk berhasil ditambahkan', [
                'id_stok_bulanan' => $stokBulanan->id,
                'stok_masuk_total' => $stokBulanan->stok_masuk
            ]);

            DB::commit();

            return redirect()->route('stok.show', $obatId)
                           ->with('success', "Stok masuk sebanyak {$jumlahStokMasuk} berhasil ditambahkan untuk bulan " .
                                   date('F Y', mktime(0, 0, 0, $bulanSekarang, 1, $tahunSekarang)));

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error menambah stok masuk', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Gagal menambah stok masuk: ' . $e->getMessage())
                        ->withInput();
        }
    }
}
