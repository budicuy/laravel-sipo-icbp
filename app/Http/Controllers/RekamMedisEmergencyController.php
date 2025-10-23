<?php

namespace App\Http\Controllers;

use App\Events\RekamMedisEmergencyDeleted;
use App\Models\Diagnosa;
use App\Models\DiagnosaEmergency;
use App\Models\ExternalEmployee;
use App\Models\Keluhan;
use App\Models\RekamMedisEmergency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RekamMedisEmergencyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = RekamMedisEmergency::with(['user:id_user,username,nama_lengkap', 'externalEmployee', 'keluhans.diagnosaEmergency']);

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
        if (! in_array($perPage, [50, 100, 200])) {
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
        // Check if user has valid token
        if (! session('valid_emergency_token')) {
            return redirect()->route('token-emergency.validate')
                ->with('error', 'Token emergency diperlukan untuk membuat rekam medis emergency.');
        }

        // Get data for dropdowns with relationships
        $externalEmployees = ExternalEmployee::with(['vendor', 'kategori'])->aktif()->get();
        $diagnosaEmergency = DiagnosaEmergency::with('obats')->get();
        $diagnosas = Diagnosa::all();

        return view('rekam-medis-emergency.create', compact('externalEmployees', 'diagnosaEmergency', 'diagnosas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Check if user has valid token
        if (! session('valid_emergency_token')) {
            return redirect()->route('token-emergency.validate')
                ->with('error', 'Token emergency diperlukan untuk membuat rekam medis emergency.');
        }

        $validated = $request->validate([
            'external_employee_id' => 'required|exists:external_employees,id',
            'tanggal_periksa' => 'required|date',
            'waktu_periksa' => 'nullable|date_format:H:i',
            'status' => 'required|in:On Progress,Close',
            'keluhan' => 'required|string',
            'id_diagnosa_emergency' => 'required|exists:diagnosa_emergency,id_diagnosa_emergency',
            'terapi' => 'required|string',
            'catatan' => 'nullable|string',
            'obat_list' => 'nullable|array',
            'obat_list.*.id_obat' => 'required|exists:obat,id_obat',
            'obat_list.*.jumlah_obat' => 'nullable|integer|min:1|max:10000',
            'obat_list.*.aturan_pakai' => 'nullable|string',
        ]);

        try {
            // Get and use the token
            $currentUserId = Auth::id();
            $token = \App\Models\TokenEmergency::isValidTokenForUser(session('valid_emergency_token'), $currentUserId);

            if (! $token) {
                // Check if token exists but is not available for this user
                $existingToken = \App\Models\TokenEmergency::where('token', session('valid_emergency_token'))->first();
                if ($existingToken) {
                    if ($existingToken->status !== \App\Models\TokenEmergency::STATUS_AVAILABLE) {
                        return redirect()->route('token-emergency.validate')
                            ->with('error', 'Token sudah digunakan atau kadaluarsa.');
                    } elseif (! $existingToken->canBeUsedBy($currentUserId)) {
                        return redirect()->route('token-emergency.validate')
                            ->with('error', 'Token ini bukan milik Anda dan tidak dapat digunakan.');
                    }
                }

                return redirect()->route('token-emergency.validate')
                    ->with('error', 'Token tidak valid.');
            }

            // Use the token (mark as used)
            $token->useToken($currentUserId);

            // Add additional data
            $validated['id_user'] = Auth::id();

            // Create emergency medical record using transaction
            DB::beginTransaction();
            try {
                // Create emergency medical record
                $rekamMedisEmergency = RekamMedisEmergency::create([
                    'id_external_employee' => $validated['external_employee_id'],
                    'tanggal_periksa' => $validated['tanggal_periksa'],
                    'waktu_periksa' => $validated['waktu_periksa'] ?? null,
                    'status' => $validated['status'],
                    'keluhan' => $validated['keluhan'],
                    'catatan' => $validated['catatan'] ?? null,
                    'id_user' => Auth::id(),
                ]);

                // Prepare keluhan data
                $keluhanBaseData = [
                    'id_emergency' => $rekamMedisEmergency->id_emergency,
                    'id_rekam' => null, // Set to null for emergency records to avoid foreign key constraint
                    'id_diagnosa' => null, // Set to null for emergency records
                    'id_diagnosa_emergency' => $validated['id_diagnosa_emergency'], // Use emergency diagnosa
                    'terapi' => $validated['terapi'],
                    'keterangan' => $validated['keluhan'],
                    'id_keluarga' => null, // Emergency records don't use keluarga
                ];

                // Check if there are obat_list (multiple obat)
                if (isset($validated['obat_list']) && is_array($validated['obat_list'])) {
                    // Save multiple keluhan entries, one for each obat
                    foreach ($validated['obat_list'] as $obatData) {
                        Keluhan::create(array_merge($keluhanBaseData, [
                            'id_obat' => $obatData['id_obat'],
                            'jumlah_obat' => $obatData['jumlah_obat'] ?? 0, // Default to 0 if not provided
                            'aturan_pakai' => $obatData['aturan_pakai'] ?? null,
                        ]));
                    }
                } else {
                    // No obat selected, save keluhan without obat
                    Keluhan::create(array_merge($keluhanBaseData, [
                        'id_obat' => null,
                        'jumlah_obat' => 0, // Default to 0
                        'aturan_pakai' => null,
                    ]));
                }

                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }

            // Dispatch event untuk mengurangi stok obat otomatis
            RekamMedisEmergencyCreated::dispatch($rekamMedisEmergency);

            // Clear token from session after successful use
            session()->forget('valid_emergency_token');

            return redirect()->route('rekam-medis-emergency.index')->with('success', 'Data rekam medis emergency berhasil ditambahkan! Token telah digunakan.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data: '.$e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $rekamMedisEmergency = RekamMedisEmergency::with(['user:id_user,username,nama_lengkap', 'externalEmployee', 'keluhans.diagnosaEmergency'])->findOrFail($id);

        return view('rekam-medis-emergency.detail', compact('rekamMedisEmergency'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $rekamMedisEmergency = RekamMedisEmergency::with(['externalEmployee', 'keluhans.diagnosaEmergency'])->findOrFail($id);

        // Get data for dropdowns with relationships
        $externalEmployees = ExternalEmployee::with(['vendor', 'kategori'])->aktif()->get();
        $diagnosaEmergency = DiagnosaEmergency::all();
        $diagnosas = Diagnosa::all();

        return view('rekam-medis-emergency.edit', compact('rekamMedisEmergency', 'externalEmployees', 'diagnosaEmergency', 'diagnosas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $rekamMedisEmergency = RekamMedisEmergency::findOrFail($id);

        $validated = $request->validate([
            'external_employee_id' => 'required|exists:external_employees,id',
            'tanggal_periksa' => 'required|date',
            'waktu_periksa' => 'nullable|date_format:H:i',
            'status' => 'required|in:On Progress,Close',
            'keluhan' => 'required|string',
            'id_diagnosa_emergency' => 'required|exists:diagnosa_emergency,id_diagnosa_emergency',
            'terapi' => 'required|string',
            'catatan' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();
            try {
                // Update emergency medical record
                $rekamMedisEmergency->update([
                    'id_external_employee' => $validated['external_employee_id'],
                    'tanggal_periksa' => $validated['tanggal_periksa'],
                    'waktu_periksa' => $validated['waktu_periksa'],
                    'status' => $validated['status'],
                    'keluhan' => $validated['keluhan'],
                    'catatan' => $validated['catatan'],
                ]);

                // Update or create keluhan record
                $existingKeluhan = $rekamMedisEmergency->keluhans()->first();
                if ($existingKeluhan) {
                    // Update existing keluhan
                    $existingKeluhan->update([
                        'id_diagnosa' => null, // Set to null for emergency records
                        'id_diagnosa_emergency' => $validated['id_diagnosa_emergency'],
                        'terapi' => $validated['terapi'],
                        'keterangan' => $validated['keluhan'],
                    ]);
                } else {
                    // Create new keluhan using the model method
                    $rekamMedisEmergency->createKeluhan([
                        'id_diagnosa_emergency' => $validated['id_diagnosa_emergency'],
                        'terapi' => $validated['terapi'],
                        'keterangan' => $validated['keluhan'],
                    ]);
                }

                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }

            return redirect()->route('rekam-medis-emergency.index')->with('success', 'Data rekam medis emergency berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui data: '.$e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $rekamMedisEmergency = RekamMedisEmergency::findOrFail($id);

        // Dispatch event untuk mengembalikan stok obat otomatis
        RekamMedisEmergencyDeleted::dispatch($rekamMedisEmergency);

        $rekamMedisEmergency->delete();

        return redirect()->route('rekam-medis-emergency.index')->with('success', 'Data rekam medis emergency berhasil dihapus! Stok obat telah dikembalikan.');
    }

    /**
     * Update status via AJAX
     */
    public function updateStatus(Request $request, $id)
    {
        $rekamMedisEmergency = RekamMedisEmergency::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:On Progress,Close',
        ]);

        try {
            $rekamMedisEmergency->update([
                'status' => $validated['status'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Status berhasil diperbarui',
                'status' => $rekamMedisEmergency->status,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui status: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get obat by diagnosa emergency ID
     */
    public function getObatByDiagnosa(Request $request)
    {
        $diagnosaId = $request->get('diagnosa_id');

        if (! $diagnosaId) {
            return response()->json([]);
        }

        $diagnosa = DiagnosaEmergency::with('obats')->find($diagnosaId);

        if (! $diagnosa) {
            return response()->json([]);
        }

        $obats = $diagnosa->obats->map(function ($obat) {
            return [
                'id_obat' => $obat->id_obat,
                'nama_obat' => $obat->nama_obat,
            ];
        });

        return response()->json($obats);
    }

    /**
     * Get all diagnosa emergency with their obat
     */
    public function getDiagnosaWithObat()
    {
        $diagnosaEmergency = DiagnosaEmergency::with('obats')->get()->map(function ($diagnosa) {
            return [
                'id_diagnosa_emergency' => $diagnosa->id_diagnosa_emergency,
                'nama_diagnosa_emergency' => $diagnosa->nama_diagnosa_emergency,
                'obats' => $diagnosa->obats->map(function ($obat) {
                    return [
                        'id_obat' => $obat->id_obat,
                        'nama_obat' => $obat->nama_obat,
                    ];
                }),
            ];
        });

        return response()->json($diagnosaEmergency);
    }
}
