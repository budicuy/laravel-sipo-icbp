<?php

namespace App\Http\Controllers;

use App\Models\SuratPengantar;
use App\Models\RekamMedis;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class SuratPengantarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = SuratPengantar::query();

        // Search by nomor surat
        if ($request->filled('search')) {
            $query->where('nomor_surat', 'like', '%' . $request->search . '%');
        }

        $surats = $query->orderBy('created_at', 'desc')->paginate(50);
        return view('surat-pengantar.index', compact('surats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $rekamMedisId = $request->query('rekam_medis_id');

        if (!$rekamMedisId) {
            return redirect()->route('rekam-medis.index')
                ->with('error', 'Rekam medis tidak ditemukan');
        }

        $rekamMedis = RekamMedis::with(['keluarga.karyawan', 'keluhans.diagnosa'])->findOrFail($rekamMedisId);

        return view('surat-pengantar.create', compact('rekamMedis'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'rekam_medis_id' => 'required|exists:rekam_medis,id_rekam',
            'lama_istirahat' => 'required|integer|min:1|max:365',
            'tanggal_mulai_istirahat' => 'required|date',
            'catatan' => 'nullable|string|max:500',
        ]);

        $rekamMedis = RekamMedis::with(['keluarga.karyawan', 'keluarga.hubungan', 'keluhans.diagnosa', 'user'])->findOrFail($validated['rekam_medis_id']);

        // Generate nomor surat
        $nomorSurat = SuratPengantar::generateNomorSurat();

        // Ambil nama diagnosa dari keluhans
        $diagnosaNama = $rekamMedis->keluhans->map(function($keluhan) {
            return $keluhan->diagnosa->nama_diagnosa ?? null;
        })->filter()->unique()->values()->toArray();

        // Format NIK dengan kode hubungan (e.g., 12321-A untuk karyawan, 12321-B untuk spouse)
        $nikKaryawan = $rekamMedis->keluarga->karyawan->nik_karyawan ?? null;
        $kodeHubungan = $rekamMedis->keluarga->kode_hubungan ?? null;
        $nikFormatted = $nikKaryawan && $kodeHubungan ? $nikKaryawan . '-' . $kodeHubungan : $nikKaryawan;

        // Create surat pengantar
        $surat = SuratPengantar::create([
            'nomor_surat' => $nomorSurat,
            'nama_pasien' => $rekamMedis->keluarga->nama_keluarga,
            'nik_karyawan_penanggung_jawab' => $nikFormatted,
            'tanggal_pengantar' => now(),
            'diagnosa' => $diagnosaNama,
            'catatan' => $validated['catatan'],
            'lama_istirahat' => $validated['lama_istirahat'],
            'tanggal_mulai_istirahat' => $validated['tanggal_mulai_istirahat'],
            'petugas_medis' => $rekamMedis->user->nama_lengkap ?? $rekamMedis->user->name ?? 'N/A',
            'nik_petugas' => $rekamMedis->user->nik ?? null,
            'link_random' => \Illuminate\Support\Str::random(32),
        ]);

        return redirect()->route('surat-pengantar.print', $surat->id)
            ->with('success', 'Surat pengantar berhasil dibuat');
    }

    /**
     * Display the specified resource.
     */
    public function show(SuratPengantar $suratPengantar)
    {
        return view('surat-pengantar.show', compact('suratPengantar'));
    }

    /**
     * Verify surat pengantar publicly (no authentication required)
     */
    public function verifyPublic($token)
    {
        $suratPengantar = SuratPengantar::where('link_random', $token)->first();

        if (!$suratPengantar) {
            abort(404, 'Surat tidak ditemukan atau token tidak valid');
        }

        return view('surat-pengantar.verify-public', compact('suratPengantar'));
    }

    /**
     * Print surat pengantar
     */
    public function print(SuratPengantar $suratPengantar)
    {
        $pdf = Pdf::loadView('surat-pengantar.print', compact('suratPengantar'))
            ->setPaper('a4', 'portrait');

        // Replace "/" and "\" with "-" in filename
        $filename = 'surat-pengantar-' . str_replace(['/', '\\'], '-', $suratPengantar->nomor_surat) . '.pdf';

        return $pdf->stream($filename);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SuratPengantar $suratPengantar)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SuratPengantar $suratPengantar)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SuratPengantar $suratPengantar)
    {
        $suratPengantar->delete();

        return redirect()->route('surat-pengantar.index')
            ->with('success', 'Surat pengantar berhasil dihapus');
    }
}
