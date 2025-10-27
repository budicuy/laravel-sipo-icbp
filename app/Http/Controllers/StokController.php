<?php

namespace App\Http\Controllers;

use App\Models\Obat;
use App\Models\StokBulanan;
use Illuminate\Http\Request;

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
                $q->where('nama_obat', 'like', '%'.$search.'%')
                    ->orWhere('keterangan', 'like', '%'.$search.'%');
            });
        }

        $obats = $query->get();

        // Optimasi N+1: Hitung sisa stok untuk semua obat sekaligus
        $obatIds = $obats->pluck('id_obat')->toArray();
        $sisaStokMap = StokBulanan::getSisaStokSaatIniBatch($obatIds);

        // Assign sisa stok ke setiap obat
        $obatsWithStok = $obats->map(function ($obat) use ($sisaStokMap) {
            $obat->sisa_stok = $sisaStokMap->get($obat->id_obat, 0);

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

        // Hitung sisa stok saat ini menggunakan batch approach (untuk konsistensi)
        $sisaStokMap = StokBulanan::getSisaStokSaatIniBatch([$obat_id]);
        $sisaStok = $sisaStokMap->get($obat_id, 0);

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
}
