<?php

namespace App\Http\Controllers;

use App\Models\RekamMedis;
use App\Models\SuratPengantarIstirahat;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SuratPengantarIstirahatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = SuratPengantarIstirahat::with(['rekamMedis', 'keluarga.karyawan', 'dokter'])
            ->orderBy('created_at', 'desc');

        // Search functionality
        if ($request->has('search') && ! empty($request->search)) {
            $search = $request->search;
            $query->searchByNikOrName($search);
        }

        $surats = $query->paginate(10);

        return view('surat-pengantar-istirahat.index', compact('surats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('surat-pengantar-istirahat.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_rekam' => 'required|exists:rekam_medis,id_rekam',
            'id_keluarga' => 'required|exists:keluarga,id_keluarga',
            'lama_istirahat' => 'required|integer|min:1|max:30',
            'tanggal_mulai_istirahat' => 'required|date|after_or_equal:today',
            'diagnosa_utama' => 'required|string|max:500',
            'keterangan_tambahan' => 'nullable|string|max:1000',
        ], [
            'id_rekam.required' => 'Rekam medis harus dipilih',
            'id_rekam.exists' => 'Rekam medis tidak ditemukan',
            'id_keluarga.required' => 'Pasien harus dipilih',
            'id_keluarga.exists' => 'Pasien tidak ditemukan',
            'lama_istirahat.required' => 'Lama istirahat harus diisi',
            'lama_istirahat.min' => 'Lama istirahat minimal 1 hari',
            'lama_istirahat.max' => 'Lama istirahat maksimal 30 hari',
            'tanggal_mulai_istirahat.required' => 'Tanggal mulai istirahat harus diisi',
            'tanggal_mulai_istirahat.after_or_equal' => 'Tanggal mulai istirahat tidak boleh kurang dari hari ini',
            'diagnosa_utama.required' => 'Diagnosa utama harus diisi',
            'diagnosa_utama.max' => 'Diagnosa utama maksimal 500 karakter',
            'keterangan_tambahan.max' => 'Keterangan tambahan maksimal 1000 karakter',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Generate nomor surat
            $nomorSurat = SuratPengantarIstirahat::generateNomorSurat();

            // Create surat pengantar istirahat
            $surat = SuratPengantarIstirahat::create([
                'id_rekam' => $request->id_rekam,
                'id_keluarga' => $request->id_keluarga,
                'tanggal_surat' => now()->format('Y-m-d'),
                'lama_istirahat' => $request->lama_istirahat,
                'tanggal_mulai_istirahat' => $request->tanggal_mulai_istirahat,
                'diagnosa_utama' => $request->diagnosa_utama,
                'keterangan_tambahan' => $request->keterangan_tambahan,
                'id_dokter' => Auth::id(),
                'nomor_surat' => $nomorSurat,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Surat Pengantar Istirahat berhasil dibuat',
                'data' => $surat,
                'redirect_url' => route('surat-pengantar-istirahat.show', $surat->id_surat),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat membuat surat: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(SuratPengantarIstirahat $suratPengantarIstirahat)
    {
        $surat = $suratPengantarIstirahat->load(['rekamMedis', 'keluarga.karyawan.departemen', 'dokter']);

        return view('surat-pengantar-istirahat.show', compact('surat'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SuratPengantarIstirahat $suratPengantarIstirahat)
    {
        $surat = $suratPengantarIstirahat->load(['rekamMedis', 'keluarga.karyawan', 'dokter']);

        return view('surat-pengantar-istirahat.edit', compact('surat'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SuratPengantarIstirahat $suratPengantarIstirahat)
    {
        $validator = Validator::make($request->all(), [
            'lama_istirahat' => 'required|integer|min:1|max:30',
            'tanggal_mulai_istirahat' => 'required|date',
            'diagnosa_utama' => 'required|string|max:500',
            'keterangan_tambahan' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $suratPengantarIstirahat->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Surat Pengantar Istirahat berhasil diperbarui',
                'redirect_url' => route('surat-pengantar-istirahat.show', $suratPengantarIstirahat->id_surat),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui surat: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SuratPengantarIstirahat $suratPengantarIstirahat)
    {
        try {
            $suratPengantarIstirahat->delete();

            return response()->json([
                'success' => true,
                'message' => 'Surat Pengantar Istirahat berhasil dihapus',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus surat: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * API untuk mencari rekam medis dengan status On Progress berdasarkan NIK atau nama
     */
    public function searchRekamMedis(Request $request)
    {
        $search = $request->get('q');

        if (empty($search)) {
            return response()->json([]);
        }

        $rekamMedis = RekamMedis::with(['keluarga.karyawan.departemen'])
            ->where('status', 'On Progress')
            ->whereHas('keluarga', function ($keluarga) use ($search) {
                $keluarga->where('nama_keluarga', 'like', "%{$search}%")
                    ->orWhereHas('karyawan', function ($karyawan) use ($search) {
                        $karyawan->where('nik_karyawan', 'like', "%{$search}%")
                            ->orWhere('nama_karyawan', 'like', "%{$search}%");
                    });
            })
            ->orderBy('tanggal_periksa', 'desc')
            ->limit(20)
            ->get()
            ->map(function ($item) {
                return [
                    'id_rekam' => $item->id_rekam,
                    'id_keluarga' => $item->id_keluarga,
                    'nama_pasien' => $item->keluarga->nama_keluarga,
                    'nik_karyawan' => $item->keluarga->karyawan->nik_karyawan ?? null,
                    'nama_karyawan' => $item->keluarga->karyawan->nama_karyawan ?? null,
                    'departemen' => $item->keluarga->karyawan->departemen->nama_departemen ?? null,
                    'tanggal_periksa' => $item->tanggal_periksa->format('d/m/Y'),
                    'display_text' => ($item->keluarga->karyawan->nik_karyawan ?? 'Tidak ada NIK').
                                     ' - '.$item->keluarga->nama_keluarga.
                                     ' ('.($item->keluarga->karyawan->nama_karyawan ?? 'External').')',
                ];
            });

        return response()->json($rekamMedis);
    }

    /**
     * Cetak surat pengantar istirahat
     */
    public function cetak(SuratPengantarIstirahat $suratPengantarIstirahat)
    {
        $surat = $suratPengantarIstirahat->load(['rekamMedis', 'keluarga.karyawan.departemen', 'dokter']);

        $pdf = PDF::loadView('surat-pengantar-istirahat.cetak', compact('surat'))
            ->setPaper('A4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'chroot' => public_path('/'),
                'defaultFont' => 'sans-serif',
            ]);

        // Fix filename dengan mengganti "/" menjadi "_"
        $filename = 'Surat-Pengantar-Istirahat-'.str_replace('/', '_', $surat->nomor_surat).'.pdf';

        return $pdf->stream($filename);
    }

    /**
     * Get detail rekam medis untuk form
     */
    public function getRekamMedisDetail($id_rekam)
    {
        $rekamMedis = RekamMedis::with(['keluarga.karyawan.departemen', 'keluhans.diagnosa'])
            ->findOrFail($id_rekam);

        // Ambil diagnosa utama dari keluhan pertama
        $diagnosaUtama = $rekamMedis->keluhans->first()->diagnosa->nama_diagnosa ?? '';

        return response()->json([
            'success' => true,
            'data' => [
                'id_rekam' => $rekamMedis->id_rekam,
                'id_keluarga' => $rekamMedis->id_keluarga,
                'nama_pasien' => $rekamMedis->keluarga->nama_keluarga,
                'nik_karyawan' => $rekamMedis->keluarga->karyawan->nik_karyawan ?? null,
                'nama_karyawan' => $rekamMedis->keluarga->karyawan->nama_karyawan ?? null,
                'departemen' => $rekamMedis->keluarga->karyawan->departemen->nama_departemen ?? null,
                'tanggal_periksa' => $rekamMedis->tanggal_periksa->format('d/m/Y'),
                'diagnosa_utama' => $diagnosaUtama,
            ],
        ]);
    }
}
