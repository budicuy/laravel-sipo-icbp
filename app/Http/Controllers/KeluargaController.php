<?php

namespace App\Http\Controllers;

use App\Models\Keluarga;
use App\Models\Karyawan;
use App\Models\Hubungan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KeluargaController extends Controller
{
    public function index(Request $request)
    {
        $query = Keluarga::with(['karyawan', 'hubungan']);

        // Filter pencarian
        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function ($sub) use ($q) {
                $sub->where('nama_keluarga', 'like', "%$q%")
                    ->orWhere('no_ktp', 'like', "%$q%")
                    ->orWhereHas('karyawan', function($karyawan) use ($q) {
                        $karyawan->where('nik_karyawan', 'like', "%$q%")
                                ->orWhere('nama_karyawan', 'like', "%$q%");
                    });
            });
        }

        // Handle sorting
        $allowedSorts = ['id_keluarga', 'id_karyawan', 'nama_keluarga', 'tanggal_lahir', 'jenis_kelamin', 'kode_hubungan', 'alamat'];
        $sortField = $request->input('sort', 'id_keluarga');
        $sortDirection = $request->input('direction', 'asc');

        if (!in_array($sortField, $allowedSorts)) {
            $sortField = 'id_keluarga';
        }
        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'asc';
        }

        $query->orderBy($sortField, $sortDirection);

        // Pagination
        $perPage = $request->input('per_page', 50);
        if (!in_array($perPage, [50, 100, 150, 200])) {
            $perPage = 50;
        }

        $keluargas = $query->paginate($perPage)->appends($request->except('page'));

        return view('keluarga.index', compact('keluargas'));
    }

    public function create()
    {
        $hubungans = Hubungan::all();
        return view('keluarga.create', compact('hubungans'));
    }

    public function store(Request $request)
    {
        $rules = [
            'id_karyawan' => 'required|exists:karyawan,id_karyawan',
            'nama_keluarga' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:L,P,Laki - Laki,Perempuan',
            'alamat' => 'required|string',
            'kode_hubungan' => 'required|exists:hubungan,kode_hubungan',
            'tanggal_daftar' => 'nullable|date',
        ];

        // Jika bukan "Diri Sendiri" (A), no_ktp wajib diisi
        if ($request->kode_hubungan !== 'A') {
            $rules['no_ktp'] = 'required|string|max:20|unique:keluarga,no_ktp';
        }

        $validated = $request->validate($rules);

        Keluarga::create($validated);

        return redirect()->route('keluarga.index')->with('success', 'Data keluarga berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $keluarga = Keluarga::findOrFail($id);
        $hubungans = Hubungan::all();

        return view('keluarga.edit', compact('keluarga', 'hubungans'));
    }

    public function update(Request $request, $id)
    {
        $keluarga = Keluarga::findOrFail($id);

        $rules = [
            'id_karyawan' => 'required|exists:karyawan,id_karyawan',
            'nama_keluarga' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:L,P,Laki - Laki,Perempuan',
            'alamat' => 'required|string',
            'kode_hubungan' => 'required|exists:hubungan,kode_hubungan',
            'tanggal_daftar' => 'nullable|date',
        ];

        // Jika bukan "Diri Sendiri" (A), no_ktp wajib diisi
        if ($request->kode_hubungan !== 'A') {
            $rules['no_ktp'] = [
                'required',
                'string',
                'max:20',
                Rule::unique('keluarga', 'no_ktp')->ignore($keluarga->id_keluarga, 'id_keluarga')
            ];
        }

        $validated = $request->validate($rules);

        $keluarga->update($validated);

        return redirect()->route('keluarga.index')->with('success', 'Data keluarga berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $keluarga = Keluarga::findOrFail($id);
        $keluarga->delete();

        return redirect()->route('keluarga.index')->with('success', 'Data keluarga berhasil dihapus!');
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:keluarga,id_keluarga'
        ]);

        $count = Keluarga::whereIn('id_keluarga', $request->ids)->delete();

        return redirect()->route('keluarga.index')
            ->with('success', "{$count} data keluarga berhasil dihapus!");
    }

    // API untuk pencarian karyawan (AJAX)
    public function searchKaryawan(Request $request)
    {
        $search = $request->input('q');

        $karyawans = Karyawan::where('nik_karyawan', 'like', "%{$search}%")
            ->orWhere('nama_karyawan', 'like', "%{$search}%")
            ->limit(10)
            ->get(['id_karyawan', 'nik_karyawan', 'nama_karyawan', 'jenis_kelamin', 'tanggal_lahir', 'alamat']);

        return response()->json($karyawans);
    }
}
