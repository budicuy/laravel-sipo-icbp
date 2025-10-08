<?php

namespace App\Http\Controllers;

use App\Models\RekamMedis;
use App\Models\Keluarga;
use App\Models\Keluhan;
use App\Models\Diagnosa;
use App\Models\Obat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RekamMedisController extends Controller
{
    public function index(Request $request)
    {
        $query = RekamMedis::with(['keluarga.karyawan', 'keluarga.hubungan', 'user', 'keluhans.diagnosa', 'keluhans.obat']);

        // Filter pencarian
        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function ($sub) use ($q) {
                $sub->whereHas('keluarga', function($keluarga) use ($q) {
                    $keluarga->where('nama_keluarga', 'like', "%$q%")
                            ->orWhere('no_rm', 'like', "%$q%")
                            ->orWhere('no_ktp', 'like', "%$q%")
                            ->orWhereHas('karyawan', function($karyawan) use ($q) {
                                $karyawan->where('nik_karyawan', 'like', "%$q%");
                            });
                });
            });
        }

        // Filter tanggal
        if ($request->filled('dari_tanggal')) {
            $query->where('tanggal_periksa', '>=', $request->dari_tanggal);
        }

        if ($request->filled('sampai_tanggal')) {
            $query->where('tanggal_periksa', '<=', $request->sampai_tanggal);
        }

        // Pagination
        $perPage = $request->input('per_page', 50);
        if (!in_array($perPage, [50, 100, 200])) {
            $perPage = 50;
        }

        $rekamMedis = $query->orderBy('id_rekam', 'desc')->paginate($perPage)->appends($request->except('page'));

        return view('rekam-medis.index', compact('rekamMedis'));
    }

    public function create()
    {
        // Get all diagnosa and obat for keluhan inputs
        $diagnosas = Diagnosa::orderBy('nama_diagnosa')->get();
        $obats = Obat::orderBy('nama_obat')->get();

        return view('rekam-medis.create', compact('diagnosas', 'obats'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_keluarga' => 'required|exists:keluarga,id_keluarga',
            'tanggal_periksa' => 'required|date',
            'jumlah_keluhan' => 'required|integer|min:1|max:3',

            // Validasi untuk setiap keluhan
            'keluhan.*.id_diagnosa' => 'required|exists:diagnosa,id_diagnosa',
            'keluhan.*.terapi' => 'required|in:Obat,Lab,Istirahat',
            'keluhan.*.keterangan' => 'nullable|string',
            'keluhan.*.id_obat' => 'nullable|exists:obat,id_obat',
            'keluhan.*.jumlah_obat' => 'nullable|integer|min:1',
            'keluhan.*.aturan_pakai' => 'nullable|string',
            'keluhan.*.waktu_pakai' => 'nullable|integer|min:1',
        ]);

        DB::beginTransaction();

        try {
            // Simpan data rekam medis
            $rekamMedis = RekamMedis::create([
                'id_keluarga' => $validated['id_keluarga'],
                'tanggal_periksa' => $validated['tanggal_periksa'],
                'id_user' => Auth::id(),
                'jumlah_keluhan' => $validated['jumlah_keluhan'],
            ]);

            // Simpan data keluhan sesuai jumlah
            if (isset($request->keluhan)) {
                foreach ($request->keluhan as $keluhanData) {
                    Keluhan::create([
                        'id_rekam' => $rekamMedis->id_rekam,
                        'id_keluarga' => $validated['id_keluarga'],
                        'id_diagnosa' => $keluhanData['id_diagnosa'],
                        'terapi' => $keluhanData['terapi'],
                        'keterangan' => $keluhanData['keterangan'] ?? null,
                        'id_obat' => $keluhanData['id_obat'] ?? null,
                        'jumlah_obat' => $keluhanData['jumlah_obat'] ?? null,
                        'aturan_pakai' => $keluhanData['aturan_pakai'] ?? null,
                        'waktu_pakai' => $keluhanData['waktu_pakai'] ?? null,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('rekam-medis.index')->with('success', 'Data rekam medis berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $rekamMedis = RekamMedis::with(['keluarga.karyawan', 'keluarga.hubungan', 'user', 'keluhans.diagnosa', 'keluhans.obat'])
            ->findOrFail($id);

        // Ambil semua riwayat kunjungan pasien ini (semua rekam medis dengan id_keluarga yang sama)
        $riwayatKunjungan = RekamMedis::with(['user', 'keluhans.diagnosa', 'keluhans.obat'])
            ->where('id_keluarga', $rekamMedis->id_keluarga)
            ->orderBy('tanggal_periksa', 'desc')
            ->get();

        return view('rekam-medis.detail', compact('rekamMedis', 'riwayatKunjungan'));
    }

    public function edit($id)
    {
        $rekamMedis = RekamMedis::with(['keluhans'])->findOrFail($id);
        $diagnosas = Diagnosa::orderBy('nama_diagnosa')->get();
        $obats = Obat::orderBy('nama_obat')->get();

        return view('rekam-medis.edit', compact('rekamMedis', 'diagnosas', 'obats'));
    }

    public function update(Request $request, $id)
    {
        $rekamMedis = RekamMedis::findOrFail($id);

        $validated = $request->validate([
            'id_keluarga' => 'required|exists:keluarga,id_keluarga',
            'tanggal_periksa' => 'required|date',
            'jumlah_keluhan' => 'required|integer|min:1|max:3',

            // Validasi untuk setiap keluhan
            'keluhan.*.id_diagnosa' => 'required|exists:diagnosa,id_diagnosa',
            'keluhan.*.terapi' => 'required|in:Obat,Lab,Istirahat',
            'keluhan.*.keterangan' => 'nullable|string',
            'keluhan.*.id_obat' => 'nullable|exists:obat,id_obat',
            'keluhan.*.jumlah_obat' => 'nullable|integer|min:1',
            'keluhan.*.aturan_pakai' => 'nullable|string',
            'keluhan.*.waktu_pakai' => 'nullable|integer|min:1',
        ]);

        DB::beginTransaction();

        try {
            // Update data rekam medis
            $rekamMedis->update([
                'id_keluarga' => $validated['id_keluarga'],
                'tanggal_periksa' => $validated['tanggal_periksa'],
                'jumlah_keluhan' => $validated['jumlah_keluhan'],
            ]);

            // Hapus keluhan lama
            $rekamMedis->keluhans()->delete();

            // Simpan keluhan baru
            if (isset($request->keluhan)) {
                foreach ($request->keluhan as $keluhanData) {
                    Keluhan::create([
                        'id_rekam' => $rekamMedis->id_rekam,
                        'id_keluarga' => $validated['id_keluarga'],
                        'id_diagnosa' => $keluhanData['id_diagnosa'],
                        'terapi' => $keluhanData['terapi'],
                        'keterangan' => $keluhanData['keterangan'] ?? null,
                        'id_obat' => $keluhanData['id_obat'] ?? null,
                        'jumlah_obat' => $keluhanData['jumlah_obat'] ?? null,
                        'aturan_pakai' => $keluhanData['aturan_pakai'] ?? null,
                        'waktu_pakai' => $keluhanData['waktu_pakai'] ?? null,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('rekam-medis.index')->with('success', 'Data rekam medis berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $rekamMedis = RekamMedis::findOrFail($id);
        $rekamMedis->delete();

        return redirect()->route('rekam-medis.index')->with('success', 'Data rekam medis berhasil dihapus!');
    }

    // API untuk pencarian pasien (AJAX)
    public function searchPasien(Request $request)
    {
        $search = $request->input('q');

        $pasiens = Keluarga::with(['karyawan', 'hubungan'])
            ->where(function($query) use ($search) {
                $query->where('nama_keluarga', 'like', "%{$search}%")
                      ->orWhere('no_ktp', 'like', "%{$search}%")
                      ->orWhere('no_rm', 'like', "%{$search}%");
            })
            ->limit(10)
            ->get()
            ->map(function($keluarga) {
                return [
                    'id' => $keluarga->id_keluarga,
                    'no_rm' => $keluarga->no_rm,
                    'nama' => $keluarga->nama_keluarga,
                    'nik' => $keluarga->no_ktp,
                    'nik_karyawan' => $keluarga->karyawan->nik_karyawan ?? '',
                    'kode_hubungan' => $keluarga->kode_hubungan,
                    'hubungan' => $keluarga->hubungan->hubungan ?? '',
                    'jenis_kelamin' => $keluarga->jenis_kelamin,
                    'tanggal_lahir' => $keluarga->tanggal_lahir ? $keluarga->tanggal_lahir->format('d/m/Y') : '',
                ];
            });

        return response()->json($pasiens);
    }
}
