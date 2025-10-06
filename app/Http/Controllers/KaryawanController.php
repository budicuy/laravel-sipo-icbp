<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Departemen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class KaryawanController extends Controller
{
    public function index(Request $request)
    {
        $departemens = Departemen::orderBy('nama_departemen')->get();
        $query = Karyawan::with('departemen');

        if ($request->filled('departemen')) {
            $query->where('id_departemen', $request->input('departemen'));
        }
        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function ($sub) use ($q) {
                $sub->where('nik_karyawan', 'like', "%$q%")
                    ->orWhere('nama_karyawan', 'like', "%$q%");
            });
        }

        $karyawans = $query->paginate(50);
        return view('karyawan.index', compact('karyawans', 'departemens'));
    }

    public function create()
    {
        $departemens = Departemen::orderBy('nama_departemen')->get();
        return view('karyawan.create', compact('departemens'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nik' => ['required','string','size:16','unique:karyawan,nik_karyawan'],
            'nama' => ['required','string','max:100'],
            'tanggal_lahir' => ['required','date'],
            'jenis_kelamin' => ['required', Rule::in(['Laki - Laki','Perempuan'])],
            'alamat' => ['required','string'],
            'no_hp' => ['required','regex:/^08\d+$/'],
            'departemen' => ['required','integer','exists:departemen,id_departemen'],
            'foto' => ['nullable','image','max:30'],
        ]);

        $path = null;
        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('karyawan', 'public');
        }

        Karyawan::create([
            'nik_karyawan' => $validated['nik'],
            'nama_karyawan' => $validated['nama'],
            'tanggal_lahir' => $validated['tanggal_lahir'],
            'jenis_kelamin' => $validated['jenis_kelamin'],
            'alamat' => $validated['alamat'],
            'no_hp' => $validated['no_hp'],
            'id_departemen' => $validated['departemen'],
            'foto' => $path,
        ]);

        return redirect()->route('karyawan.index')->with('success', 'Karyawan berhasil ditambahkan');
    }

    public function edit(Karyawan $karyawan)
    {
        $departemens = Departemen::orderBy('nama_departemen')->get();
        return view('karyawan.edit', compact('karyawan','departemens'));
    }

    public function update(Request $request, Karyawan $karyawan)
    {
        $validated = $request->validate([
            'nik' => ['required','string','size:16', Rule::unique('karyawan','nik_karyawan')->ignore($karyawan->id_karyawan, 'id_karyawan')],
            'nama' => ['required','string','max:100'],
            'tanggal_lahir' => ['required','date'],
            'jenis_kelamin' => ['required', Rule::in(['Laki - Laki','Perempuan'])],
            'alamat' => ['required','string'],
            'no_hp' => ['required','regex:/^08\d+$/'],
            'departemen' => ['required','integer','exists:departemen,id_departemen'],
            'foto' => ['nullable','image','max:30'],
        ]);

        $data = [
            'nik_karyawan' => $validated['nik'],
            'nama_karyawan' => $validated['nama'],
            'tanggal_lahir' => $validated['tanggal_lahir'],
            'jenis_kelamin' => $validated['jenis_kelamin'],
            'alamat' => $validated['alamat'],
            'no_hp' => $validated['no_hp'],
            'id_departemen' => $validated['departemen'],
        ];

        if ($request->hasFile('foto')) {
            if ($karyawan->foto) {
                Storage::disk('public')->delete($karyawan->foto);
            }
            $data['foto'] = $request->file('foto')->store('karyawan', 'public');
        }

        $karyawan->update($data);

        return redirect()->route('karyawan.index')->with('success', 'Karyawan berhasil diperbarui');
    }

    public function destroy(Karyawan $karyawan)
    {
        if ($karyawan->foto) {
            Storage::disk('public')->delete($karyawan->foto);
        }
        $karyawan->delete();
        return back()->with('success', 'Karyawan dihapus');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required','file','mimes:csv,txt,xlsx,xls'],
        ]);

        // Simple CSV handling; xlsx/xls should be pre-converted or handled by extra package (not required here)
        $path = $request->file('file')->getRealPath();
        $handle = fopen($path, 'r');
        $header = fgetcsv($handle);
        $created = 0;
        while (($row = fgetcsv($handle)) !== false) {
            $data = array_combine($header, $row);

            $nik = trim((string)($data['nik'] ?? ''));
            if (strlen($nik) !== 16) {
                continue;
            }

            $departemenName = trim((string)($data['departemen'] ?? ''));
            $departemen = Departemen::firstOrCreate(['nama_departemen' => $departemenName]);

            Karyawan::updateOrCreate(
                ['nik_karyawan' => $nik],
                [
                    'nama_karyawan' => (string)($data['nama'] ?? ''),
                    'tanggal_lahir' => (string)($data['tanggal_lahir'] ?? ''),
                    'jenis_kelamin' => in_array(($data['jenis_kelamin'] ?? ''), ['Laki - Laki','Perempuan']) ? $data['jenis_kelamin'] : 'Laki - Laki',
                    'alamat' => (string)($data['alamat'] ?? ''),
                    'no_hp' => (string)($data['no_hp'] ?? ''),
                    'id_departemen' => $departemen->id_departemen,
                    'foto' => null,
                ]
            );
            $created++;
        }
        fclose($handle);

        return back()->with('success', "Import selesai: $created baris");
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'exists:karyawan,id_karyawan']
        ]);

        $ids = $request->input('ids');

        // Get karyawan records to delete their photos
        $karyawans = Karyawan::whereIn('id_karyawan', $ids)->get();

        // Delete photos from storage
        foreach ($karyawans as $karyawan) {
            if ($karyawan->foto) {
                Storage::disk('public')->delete($karyawan->foto);
            }
        }

        // Delete karyawan records
        $deleted = Karyawan::whereIn('id_karyawan', $ids)->delete();

        return back()->with('success', "$deleted karyawan berhasil dihapus");
    }


}


