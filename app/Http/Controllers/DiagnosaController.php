<?php

namespace App\Http\Controllers;

use App\Models\Diagnosa;
use App\Models\Obat;
use Illuminate\Http\Request;

class DiagnosaController extends Controller
{
    public function index(Request $request)
    {
        $query = Diagnosa::with('obats');

        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_diagnosa', 'like', '%' . $search . '%')
                    ->orWhere('deskripsi', 'like', '%' . $search . '%');
            });
        }

        // Sorting
        $sortField = $request->get('sort', 'id_diagnosa');
        $sortDirection = $request->get('direction', 'desc');

        if (in_array($sortField, ['nama_diagnosa', 'created_at', 'updated_at'])) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->orderBy('id_diagnosa', 'desc');
        }

        $diagnosas = $query->paginate(10);

        return view('diagnosa.index', compact('diagnosas'));
    }

    public function create()
    {
        $obats = Obat::orderBy('nama_obat', 'asc')->get();
        return view('diagnosa.create', compact('obats'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_diagnosa' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'obat_ids' => 'nullable|array',
            'obat_ids.*' => 'exists:obat,id_obat'
        ], [
            'nama_diagnosa.required' => 'Nama diagnosa wajib diisi',
            'nama_diagnosa.max' => 'Nama diagnosa maksimal 100 karakter',
        ]);

        $diagnosa = Diagnosa::create([
            'nama_diagnosa' => $validated['nama_diagnosa'],
            'deskripsi' => $validated['deskripsi'] ?? null,
        ]);

        // Attach obat yang direkomendasikan
        if (isset($validated['obat_ids']) && count($validated['obat_ids']) > 0) {
            $diagnosa->obats()->attach($validated['obat_ids']);
        }

        return redirect()->route('diagnosa.index')->with('success', 'Data diagnosa berhasil ditambahkan');
    }

    public function edit($id)
    {
        $diagnosa = Diagnosa::with('obats')->findOrFail($id);
        $obats = Obat::orderBy('nama_obat', 'asc')->get();
        return view('diagnosa.edit', compact('diagnosa', 'obats'));
    }

    public function update(Request $request, $id)
    {
        $diagnosa = Diagnosa::findOrFail($id);

        $validated = $request->validate([
            'nama_diagnosa' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'obat_ids' => 'nullable|array',
            'obat_ids.*' => 'exists:obat,id_obat'
        ], [
            'nama_diagnosa.required' => 'Nama diagnosa wajib diisi',
            'nama_diagnosa.max' => 'Nama diagnosa maksimal 100 karakter',
        ]);

        $diagnosa->update([
            'nama_diagnosa' => $validated['nama_diagnosa'],
            'deskripsi' => $validated['deskripsi'] ?? null,
        ]);

        // Sync obat yang direkomendasikan
        // Jika obat_ids ada (bahkan jika array kosong), sync dengan nilai tersebut
        // Jika tidak ada sama sekali di request, tetap pertahankan relasi yang ada
        if ($request->has('obat_ids')) {
            $diagnosa->obats()->sync($validated['obat_ids'] ?? []);
        }

        return redirect()->route('diagnosa.index')->with('success', 'Data diagnosa berhasil diperbarui');
    }

    public function destroy($id)
    {
        $diagnosa = Diagnosa::findOrFail($id);
        $diagnosa->obats()->detach(); // Hapus relasi dengan obat
        $diagnosa->delete();

        return redirect()->route('diagnosa.index')->with('success', 'Data diagnosa berhasil dihapus');
    }
}
