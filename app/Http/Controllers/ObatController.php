<?php

namespace App\Http\Controllers;

use App\Models\Obat;
use App\Models\JenisObat;
use App\Models\SatuanObat;
use Illuminate\Http\Request;

class ObatController extends Controller
{
    public function index(Request $request)
    {
        $query = Obat::with(['jenisObat', 'satuanObat']);

        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_obat', 'like', '%' . $search . '%')
                    ->orWhere('keterangan', 'like', '%' . $search . '%')
                    ->orWhereHas('jenisObat', function ($q) use ($search) {
                        $q->where('nama_jenis_obat', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('satuanObat', function ($q) use ($search) {
                        $q->where('nama_satuan', 'like', '%' . $search . '%');
                    });
            });
        }

        // Filter by jenis obat
        if ($request->has('jenis_obat') && $request->jenis_obat != '') {
            $query->where('id_jenis_obat', $request->jenis_obat);
        }

        // Filter by satuan obat
        if ($request->has('satuan_obat') && $request->satuan_obat != '') {
            $query->where('id_satuan', $request->satuan_obat);
        }

        // Filter by tanggal
        if ($request->has('tanggal_mulai') && $request->tanggal_mulai != '') {
            $query->whereDate('tanggal_update', '>=', $request->tanggal_mulai);
        }

        if ($request->has('tanggal_selesai') && $request->tanggal_selesai != '') {
            $query->whereDate('tanggal_update', '<=', $request->tanggal_selesai);
        }

        // Sorting
        $sortField = $request->get('sort', 'id_obat');
        $sortDirection = $request->get('direction', 'desc');

        if (in_array($sortField, ['nama_obat', 'jenis_obat', 'satuan_obat', 'jumlah_per_kemasan', 'stok_awal', 'stok_masuk', 'stok_keluar', 'stok_akhir', 'harga_per_kemasan', 'harga_per_satuan', 'keterangan', 'tanggal_update'])) {
            // Handle sorting for related fields
            if ($sortField === 'jenis_obat') {
                $query->join('jenis_obat', 'obat.id_jenis_obat', '=', 'jenis_obat.id_jenis_obat')
                      ->orderBy('jenis_obat.nama_jenis_obat', $sortDirection)
                      ->select('obat.*');
            } elseif ($sortField === 'satuan_obat') {
                $query->join('satuan_obat', 'obat.id_satuan', '=', 'satuan_obat.id_satuan')
                      ->orderBy('satuan_obat.nama_satuan', $sortDirection)
                      ->select('obat.*');
            } else {
                $query->orderBy($sortField, $sortDirection);
            }
        } else {
            $query->orderBy('id_obat', 'desc');
        }

        // Pagination dengan nilai dinamis
        $perPage = $request->get('per_page', 50);
        $perPage = in_array($perPage, [50, 100, 150, 200]) ? $perPage : 50;

        $obats = $query->paginate($perPage);
        $jenisObats = JenisObat::all();
        $satuanObats = SatuanObat::all();

        return view('obat.index', compact('obats', 'jenisObats', 'satuanObats'));
    }

    public function create()
    {
        $jenisObats = JenisObat::all();
        $satuanObats = SatuanObat::all();
        return view('obat.create', compact('jenisObats', 'satuanObats'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_obat' => 'required|string|max:100|unique:obat,nama_obat',
            'keterangan' => 'nullable|string',
            'id_jenis_obat' => 'required|exists:jenis_obat,id_jenis_obat',
            'id_satuan' => 'required|exists:satuan_obat,id_satuan',
            'stok_awal' => 'required|integer|min:0',
            'stok_masuk' => 'required|integer|min:0',
            'stok_keluar' => 'required|integer|min:0',
            'jumlah_per_kemasan' => 'required|integer|min:1',
            'harga_per_kemasan' => 'required|numeric|min:0',
            'harga_per_satuan' => 'required|numeric|min:0',
        ], [
            'nama_obat.required' => 'Nama obat wajib diisi',
            'nama_obat.unique' => 'Nama obat sudah terdaftar',
            'id_jenis_obat.required' => 'Jenis obat wajib dipilih',
            'id_satuan.required' => 'Satuan obat wajib dipilih',
            'stok_awal.required' => 'Stok awal wajib diisi',
            'stok_masuk.required' => 'Stok masuk wajib diisi',
            'stok_keluar.required' => 'Stok keluar wajib diisi',
            'jumlah_per_kemasan.required' => 'Jumlah per kemasan wajib diisi',
            'harga_per_kemasan.required' => 'Harga per kemasan wajib diisi',
            'harga_per_satuan.required' => 'Harga per satuan wajib diisi',
        ]);

        Obat::create($validated);

        return redirect()->route('obat.index')->with('success', 'Data obat berhasil ditambahkan');
    }

    public function edit($id)
    {
        $obat = Obat::findOrFail($id);
        $jenisObats = JenisObat::all();
        $satuanObats = SatuanObat::all();
        return view('obat.edit', compact('obat', 'jenisObats', 'satuanObats'));
    }

    public function update(Request $request, $id)
    {
        $obat = Obat::findOrFail($id);

        $validated = $request->validate([
            'nama_obat' => 'required|string|max:100|unique:obat,nama_obat,' . $id . ',id_obat',
            'keterangan' => 'nullable|string',
            'id_jenis_obat' => 'required|exists:jenis_obat,id_jenis_obat',
            'id_satuan' => 'required|exists:satuan_obat,id_satuan',
            'stok_awal' => 'required|integer|min:0',
            'stok_masuk' => 'required|integer|min:0',
            'stok_keluar' => 'required|integer|min:0',
            'jumlah_per_kemasan' => 'required|integer|min:1',
            'harga_per_kemasan' => 'required|numeric|min:0',
            'harga_per_satuan' => 'required|numeric|min:0',
        ], [
            'nama_obat.required' => 'Nama obat wajib diisi',
            'nama_obat.unique' => 'Nama obat sudah terdaftar',
            'id_jenis_obat.required' => 'Jenis obat wajib dipilih',
            'id_satuan.required' => 'Satuan obat wajib dipilih',
            'stok_awal.required' => 'Stok awal wajib diisi',
            'stok_masuk.required' => 'Stok masuk wajib diisi',
            'stok_keluar.required' => 'Stok keluar wajib diisi',
            'jumlah_per_kemasan.required' => 'Jumlah per kemasan wajib diisi',
            'harga_per_kemasan.required' => 'Harga per kemasan wajib diisi',
            'harga_per_satuan.required' => 'Harga per satuan wajib diisi',
        ]);

        $obat->update($validated);

        return redirect()->route('obat.index')->with('success', 'Data obat berhasil diperbarui');
    }

    public function destroy($id)
    {
        $obat = Obat::findOrFail($id);
        $obat->delete();

        return response()->json(['success' => true, 'message' => 'Data obat berhasil dihapus']);
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'Tidak ada data yang dipilih'], 400);
        }

        Obat::whereIn('id_obat', $ids)->delete();

        return response()->json(['success' => true, 'message' => count($ids) . ' data obat berhasil dihapus']);
    }
}
