<?php

namespace App\Http\Controllers;

use App\Models\Obat;
use App\Models\StokBulanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class StokController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Query semua data Obat
        $query = Obat::with(['satuanObat:id_satuan,nama_satuan']);

        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_obat', 'like', '%' . $search . '%')
                  ->orWhere('keterangan', 'like', '%' . $search . '%');
            });
        }

        // Filter by stok status
        if ($request->has('stok_status') && $request->stok_status != '') {
            $query->whereHas('stokBulanans', function($q) use ($request) {
                // Hitung sisa stok dan filter berdasarkan status
                switch ($request->stok_status) {
                    case 'habis':
                        // Ini akan dihandle di collection level
                        break;
                    case 'rendah':
                        // Ini akan dihandle di collection level
                        break;
                    case 'tersedia':
                        // Ini akan dihandle di collection level
                        break;
                }
            });
        }

        $obats = $query->get();

        // Ambil semua ID obat untuk perhitungan stok sekaligus (menghindari N+1 query)
        $obatIds = $obats->pluck('id_obat')->toArray();
        $sisaStokCollection = StokBulanan::getSisaStokSaatIniForMultiple($obatIds);

        // Tambahkan sisa stok ke setiap obat
        $obatsWithStok = $obats->map(function ($obat) use ($sisaStokCollection) {
            $obat->sisa_stok = $sisaStokCollection->get($obat->id_obat) ?? 0;
            return $obat;
        });

        // Filter berdasarkan status stok (setelah perhitungan)
        if ($request->has('stok_status') && $request->stok_status != '') {
            switch ($request->stok_status) {
                case 'habis':
                    $obatsWithStok = $obatsWithStok->filter(function ($obat) {
                        return $obat->sisa_stok <= 0;
                    });
                    break;
                case 'rendah':
                    $obatsWithStok = $obatsWithStok->filter(function ($obat) {
                        return $obat->sisa_stok > 0 && $obat->sisa_stok <= 10;
                    });
                    break;
                case 'tersedia':
                    $obatsWithStok = $obatsWithStok->filter(function ($obat) {
                        return $obat->sisa_stok > 10;
                    });
                    break;
            }
        }

        // Sorting
        $sortField = $request->get('sort', 'nama_obat');
        $sortDirection = $request->get('direction', 'asc');

        if ($sortField === 'sisa_stok') {
            $obatsWithStok = $sortDirection === 'asc'
                ? $obatsWithStok->sortBy('sisa_stok')
                : $obatsWithStok->sortByDesc('sisa_stok');
        } elseif ($sortField === 'nama_obat') {
            $obatsWithStok = $sortDirection === 'asc'
                ? $obatsWithStok->sortBy('nama_obat')
                : $obatsWithStok->sortByDesc('nama_obat');
        }

        return view('stok.index', compact('obatsWithStok'));
    }

    /**
     * Display the specified resource.
     */
    public function show($obat_id)
    {
        // Ambil data Obat berdasarkan $obat_id
        $obat = Obat::with(['satuanObat:id_satuan,nama_satuan'])
                   ->findOrFail($obat_id);

        // Tampilkan riwayat stok bulanan
        $riwayatStok = StokBulanan::getRiwayatStok($obat_id, 24); // 24 bulan terakhir

        // Hitung sisa stok saat ini (menggunakan method yang sudah dioptimasi)
        $sisaStokCollection = StokBulanan::getSisaStokSaatIniForMultiple([$obat_id]);
        $sisaStok = $sisaStokCollection->get($obat_id) ?? 0;

        // Data untuk form stok masuk bulan ini
        $tahunSekarang = now()->year;
        $bulanSekarang = now()->month;

        // Cek apakah sudah ada stok bulanan untuk bulan ini
        $stokBulananIni = StokBulanan::where('obat_id', $obat_id)
                                   ->where('tahun', $tahunSekarang)
                                   ->where('bulan', $bulanSekarang)
                                   ->first();

        return view('stok.show', compact(
            'obat',
            'riwayatStok',
            'sisaStok',
            'tahunSekarang',
            'bulanSekarang',
            'stokBulananIni'
        ));
    }

    /**
     * Update the specified stok bulanan in storage.
     */
    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:stok_bulanans,id',
            'obat_id' => 'required|exists:obat,id_obat',
            'stok_masuk' => 'nullable|integer|min:0',
            'stok_pakai' => 'nullable|integer|min:0',
        ]);

        try {
            $stokBulanan = StokBulanan::findOrFail($request->id);

            // Simpan nilai lama untuk logging
            $oldValues = [
                'stok_masuk' => $stokBulanan->stok_masuk,
                'stok_pakai' => $stokBulanan->stok_pakai,
            ];

            // Update nilai
            $stokBulanan->stok_masuk = $request->stok_masuk ?? 0;
            $stokBulanan->stok_pakai = $request->stok_pakai ?? 0;
            $stokBulanan->save();

            // Log perubahan
            Log::info('Stok bulanan updated', [
                'stok_bulanan_id' => $stokBulanan->id,
                'obat_id' => $stokBulanan->obat_id,
                'periode' => $stokBulanan->periode,
                'old_values' => $oldValues,
                'new_values' => [
                    'stok_masuk' => $stokBulanan->stok_masuk,
                    'stok_pakai' => $stokBulanan->stok_pakai,
                ],
                'user_id' => Auth::user()->id_user ?? null,
            ]);

            return redirect()
                ->route('stok.show', $request->obat_id)
                ->with('success', 'Riwayat stok berhasil diperbarui');

        } catch (\Exception $e) {
            Log::error('Error updating stok bulanan', [
                'error' => $e->getMessage(),
                'request_data' => $request->all(),
                'user_id' => Auth::user()->id_user ?? null,
            ]);

            return redirect()
                ->route('stok.show', $request->obat_id)
                ->with('error', 'Terjadi kesalahan saat memperbarui riwayat stok: ' . $e->getMessage());
        }
    }
}
