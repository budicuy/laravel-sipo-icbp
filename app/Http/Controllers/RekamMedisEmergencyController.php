<?php

namespace App\Http\Controllers;

use App\Models\RekamMedisEmergency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RekamMedisEmergencyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = RekamMedisEmergency::with(['user:id_user,username,nama_lengkap']);

        // Filter pencarian
        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->search($q);
        }

        // Filter tanggal
        if ($request->filled('dari_tanggal')) {
            $query->where('tanggal_periksa', '>=', $request->dari_tanggal);
        }

        if ($request->filled('sampai_tanggal')) {
            $query->where('tanggal_periksa', '<=', $request->sampai_tanggal);
        }

        // Filter status
        if ($request->filled('status')) {
            $query->status($request->status);
        }

        // Pagination
        $perPage = $request->input('per_page', 50);
        if (!in_array($perPage, [50, 100, 200])) {
            $perPage = 50;
        }

        $rekamMedisEmergency = $query->orderBy('id_emergency', 'desc')->paginate($perPage)->appends($request->except('page'));

        return view('rekam-medis-emergency.index', compact('rekamMedisEmergency'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('rekam-medis-emergency.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nik_pasien' => 'required|digits_between:1,16|numeric',
            'nama_pasien' => 'required|string|max:255',
            'no_rm' => 'required|string|max:30',
            'jenis_kelamin' => 'required|in:L,P',
            'tanggal_periksa' => 'required|date',
            'status_rekam_medis' => 'required|in:On Progress,Close',
            'keluhan' => 'required|string',
            'diagnosa' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);

        try {
            // Add additional data
            $validated['hubungan'] = 'Emergency';
            $validated['id_user'] = Auth::id();

            // Create emergency medical record
            $rekamMedisEmergency = RekamMedisEmergency::create($validated);

            return redirect()->route('rekam-medis-emergency.index')->with('success', 'Data rekam medis emergency berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $rekamMedisEmergency = RekamMedisEmergency::with(['user:id_user,username,nama_lengkap'])->findOrFail($id);
        return view('rekam-medis-emergency.detail', compact('rekamMedisEmergency'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $rekamMedisEmergency = RekamMedisEmergency::findOrFail($id);
        return view('rekam-medis-emergency.edit', compact('rekamMedisEmergency'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $rekamMedisEmergency = RekamMedisEmergency::findOrFail($id);

        $validated = $request->validate([
            'nik_pasien' => 'required|digits_between:1,16|numeric',
            'nama_pasien' => 'required|string|max:255',
            'no_rm' => 'required|string|max:30',
            'jenis_kelamin' => 'required|in:L,P',
            'tanggal_periksa' => 'required|date',
            'status_rekam_medis' => 'required|in:On Progress,Close',
            'keluhan' => 'required|string',
            'diagnosa' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);

        try {
            $rekamMedisEmergency->update($validated);
            return redirect()->route('rekam-medis-emergency.index')->with('success', 'Data rekam medis emergency berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $rekamMedisEmergency = RekamMedisEmergency::findOrFail($id);
        $rekamMedisEmergency->delete();

        return redirect()->route('rekam-medis-emergency.index')->with('success', 'Data rekam medis emergency berhasil dihapus!');
    }

    /**
     * Update status via AJAX
     */
    public function updateStatus(Request $request, $id)
    {
        $rekamMedisEmergency = RekamMedisEmergency::findOrFail($id);

        $validated = $request->validate([
            'status_rekam_medis' => 'required|in:On Progress,Close',
        ]);

        try {
            $rekamMedisEmergency->update([
                'status_rekam_medis' => $validated['status_rekam_medis'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Status berhasil diperbarui',
                'status' => $rekamMedisEmergency->status_rekam_medis,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui status: ' . $e->getMessage(),
            ], 500);
        }
    }
}