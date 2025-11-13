<?php

namespace App\Http\Controllers;

use App\Events\RekamMedisEmergencyCreated;
use App\Events\RekamMedisEmergencyDeleted;
use App\Events\RekamMedisEmergencyUpdated;
use App\Models\Diagnosa;
use App\Models\DiagnosaEmergency;
use App\Models\ExternalEmployee;
use App\Models\Keluhan;
use App\Models\RekamMedisEmergency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class RekamMedisEmergencyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = RekamMedisEmergency::with(['user:id_user,username,nama_lengkap', 'externalEmployee:id,nik_employee,nama_employee,kode_rm,jenis_kelamin,alamat', 'keluhans.diagnosaEmergency']);

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
            return redirect()->route('token-emergency.validate.form')
                ->with('error', 'Token emergency diperlukan untuk membuat rekam medis emergency.');
        }

        // Get data for dropdowns with relationships
        $externalEmployees = ExternalEmployee::with(['vendor', 'kategori'])->aktif()->get();
        $diagnosaEmergency = DiagnosaEmergency::where('status', 'aktif')->with('obats')->get();
        $diagnosas = Diagnosa::where('status', 'aktif')->get();

        // Kumpulkan semua obat IDs dari diagnosa emergency
        $allObatIds = $diagnosaEmergency->flatMap(function ($diagnosa) {
            return $diagnosa->obats->pluck('id_obat');
        })->unique()->toArray();

        // Get sisa stok batch untuk semua obat sekaligus
        $stokMap = \App\Models\StokBulanan::getSisaStokSaatIniBatch($allObatIds);

        // Tambahkan stok_akhir ke setiap obat di diagnosa
        $diagnosaEmergency->each(function ($diagnosa) use ($stokMap) {
            $diagnosa->obats->each(function ($obat) use ($stokMap) {
                $obat->stok_akhir = $stokMap[$obat->id_obat] ?? 0;
            });
        });

        return view('rekam-medis-emergency.create', compact('externalEmployees', 'diagnosaEmergency', 'diagnosas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Check if user has valid token
        if (! session('valid_emergency_token')) {
            return redirect()->route('token-emergency.validate.form')
                ->with('error', 'Token emergency diperlukan untuk membuat rekam medis emergency.');
        }

        // Validate that external_employee_id is not undefined or empty
        if (empty($request->input('external_employee_id')) || $request->input('external_employee_id') === 'undefined') {
            return redirect()->back()
                ->withInput()
                ->withErrors(['external_employee_id' => 'Silakan pilih karyawan external terlebih dahulu'])
                ->with('error', 'Silakan pilih karyawan external sebelum mengirimkan form.');
        }

        // Clean up waktu_periksa - convert empty string to null
        if (empty($request->input('waktu_periksa'))) {
            $request->merge(['waktu_periksa' => null]);
        }

        $validated = $request->validate([
            'external_employee_id' => 'required|exists:external_employees,id',
            'tanggal_periksa' => 'required|date|before_or_equal:today',
            'waktu_periksa' => 'nullable|date_format:H:i',
            'status' => 'required|in:On Progress,Close',
            'keluhan' => 'required|string|min:10|max:1000',
            'id_diagnosa_emergency' => 'required|exists:diagnosa_emergency,id_diagnosa_emergency',
            'terapi' => 'required|in:Obat,Lab,Istirahat,Emergency',
            'catatan' => 'nullable|string|max:2000',
            // Obat list validation
            'obat_list' => 'sometimes|array|min:1',
            'obat_list.*.id_obat' => 'required_with:obat_list|exists:obat,id_obat',
            'obat_list.*.jumlah_obat' => 'required_with:obat_list|integer|min:1|max:100',
            'obat_list.*.aturan_pakai' => 'nullable|string|max:500',
        ], [
            // External Employee
            'external_employee_id.required' => 'Karyawan emergency harus dipilih.',
            'external_employee_id.exists' => 'Karyawan emergency yang dipilih tidak valid atau tidak ditemukan dalam sistem.',

            // Tanggal Periksa
            'tanggal_periksa.required' => 'Tanggal periksa wajib diisi.',
            'tanggal_periksa.date' => 'Format tanggal periksa tidak valid. Harap gunakan format tanggal yang benar (YYYY-MM-DD).',
            'tanggal_periksa.before_or_equal' => 'Tanggal periksa tidak boleh melebihi tanggal hari ini.',

            // Waktu Periksa
            'waktu_periksa.date_format' => 'Format waktu periksa tidak valid. Harap gunakan format HH:MM (contoh: 14:30).',

            // Status
            'status.required' => 'Status rekam medis harus dipilih.',
            'status.in' => 'Status yang dipilih tidak valid. Pilihan yang tersedia: On Progress atau Close.',

            // Keluhan
            'keluhan.required' => 'Keluhan pasien wajib diisi untuk melanjutkan.',
            'keluhan.min' => 'Keluhan pasien terlalu singkat. Minimal 10 karakter untuk memberikan deskripsi yang memadai.',
            'keluhan.max' => 'Keluhan pasien terlalu panjang. Maksimal 1000 karakter.',

            // Diagnosa Emergency
            'id_diagnosa_emergency.required' => 'Diagnosa emergency harus dipilih.',
            'id_diagnosa_emergency.exists' => 'Diagnosa emergency yang dipilih tidak valid atau tidak ditemukan dalam sistem.',

            // Terapi
            'terapi.required' => 'Jenis terapi harus dipilih.',
            'terapi.in' => 'Jenis terapi yang dipilih tidak valid. Pilihan yang tersedia: Obat, Konsul Faskes Lanjutan, Istirahat, atau Emergency.',

            // Catatan
            'catatan.max' => 'Catatan terlalu panjang. Maksimal 2000 karakter.',

            // Obat List
            'obat_list.array' => 'Format data obat tidak valid. Harap refresh halaman dan coba lagi.',
            'obat_list.min' => 'Minimal harus ada 1 obat yang dipilih jika terapi menggunakan obat.',
            'obat_list.*.id_obat.required_with' => 'Setiap baris obat harus memilih nama obat yang valid.',
            'obat_list.*.id_obat.exists' => 'Salah satu obat yang dipilih tidak valid atau tidak ditemukan dalam sistem.',
            'obat_list.*.jumlah_obat.required_with' => 'Jumlah obat wajib diisi untuk setiap obat yang dipilih.',
            'obat_list.*.jumlah_obat.integer' => 'Jumlah obat harus berupa angka bulat.',
            'obat_list.*.jumlah_obat.min' => 'Jumlah obat minimal adalah 1.',
            'obat_list.*.jumlah_obat.max' => 'Jumlah obat maksimal adalah 100 per item.',
            'obat_list.*.aturan_pakai.max' => 'Aturan pakai maksimal 500 karakter.',
        ]);

        try {
            // Get and use the token
            $currentUserId = Auth::id();
            $token = \App\Models\TokenEmergency::where('token', session('valid_emergency_token'))
                ->where('status', \App\Models\TokenEmergency::STATUS_AVAILABLE)
                ->first();

            if (! $token) {
                // Check if token exists but is not available for this user
                $existingToken = \App\Models\TokenEmergency::where('token', session('valid_emergency_token'))->first();
                if ($existingToken) {
                    if ($existingToken->status !== \App\Models\TokenEmergency::STATUS_AVAILABLE) {
                        // Clear invalid token from session
                        session()->forget('valid_emergency_token');

                        return redirect()->route('token-emergency.validate.form')
                            ->with('error', 'Token sudah digunakan atau kadaluarsa. Silakan masukkan token baru.');
                    } elseif (! $existingToken->canBeUsedBy($currentUserId)) {
                        // Clear invalid token from session
                        session()->forget('valid_emergency_token');

                        return redirect()->route('token-emergency.validate.form')
                            ->with('error', 'Token ini bukan milik Anda dan tidak dapat digunakan.');
                    }
                }

                // Clear invalid token from session
                session()->forget('valid_emergency_token');

                return redirect()->route('token-emergency.validate.form')
                    ->with('error', 'Token tidak valid. Silakan masukkan token kembali.');
            }

            // Check if token can be used by current user
            if (! $token->canBeUsedBy($currentUserId)) {
                // Clear invalid token from session
                session()->forget('valid_emergency_token');

                return redirect()->route('token-emergency.validate.form')
                    ->with('error', 'Token ini bukan milik Anda dan tidak dapat digunakan.');
            }

            // Use the token (mark as used)
            $token->status = \App\Models\TokenEmergency::STATUS_USED;
            $token->used_at = now();
            $token->used_by = $currentUserId;
            $token->save();

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

                // Dispatch event untuk mengurangi stok obat AFTER transaction commits
                event(new RekamMedisEmergencyCreated($rekamMedisEmergency));
            } catch (\Exception $e) {
                DB::rollback();
                // If there's an error, restore the token availability
                $token->status = 'available';
                $token->used_at = null;
                $token->id_user = null;
                $token->save();
                throw $e;
            }

            // Clear token from session after successful use
            session()->forget('valid_emergency_token');

            return redirect()->route('rekam-medis.index', ['tab' => 'emergency'])->with('success', 'Data rekam medis emergency berhasil ditambahkan! Token telah digunakan.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // On validation error, keep token in session so user can retry
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('error', 'Mohon perbaiki kesalahan pada form. Token Anda masih aktif.');
        } catch (\Exception $e) {
            // On other errors, keep token in session so user can retry
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan data: '.$e->getMessage().' Token Anda masih aktif, silakan coba lagi.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $rekamMedisEmergency = RekamMedisEmergency::with([
            'user:id_user,username,nama_lengkap',
            'externalEmployee:id,nik_employee,nama_employee,kode_rm,jenis_kelamin,alamat',
            'keluhans.diagnosaEmergency',
            'keluhans.obat:id_obat,nama_obat,id_satuan',
            'keluhans.obat.satuanObat:id_satuan,nama_satuan',
        ])->findOrFail($id);

        // Kelompokkan data berdasarkan diagnosa emergency untuk menghindari duplikasi
        $keluhanDikelompokkan = [];
        foreach ($rekamMedisEmergency->keluhans as $keluhan) {
            $diagnosaId = $keluhan->id_diagnosa_emergency;
            $diagnosaNama = $keluhan->diagnosaEmergency->nama_diagnosa_emergency ?? 'Tidak ada diagnosa';

            if (! isset($keluhanDikelompokkan[$diagnosaId])) {
                $keluhanDikelompokkan[$diagnosaId] = [
                    'diagnosa' => $diagnosaNama,
                    'terapi' => $keluhan->terapi,
                    'keterangan' => $keluhan->keterangan,
                    'obat_list' => [],
                ];
            }

            // Tambahkan obat jika ada
            if ($keluhan->obat) {
                $keluhanDikelompokkan[$diagnosaId]['obat_list'][] = [
                    'id_obat' => $keluhan->obat->id_obat,
                    'nama_obat' => $keluhan->obat->nama_obat,
                    'jumlah_obat' => $keluhan->jumlah_obat,
                    'aturan_pakai' => $keluhan->aturan_pakai,
                    'satuan' => $keluhan->obat->satuanObat->nama_satuan ?? '',
                ];
            }
        }

        // Konversi ke array untuk memudahkan iterasi di view
        $rekamMedisEmergency->keluhan_dikelompokkan = array_values($keluhanDikelompokkan);

        return view('rekam-medis-emergency.detail', compact('rekamMedisEmergency'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        // Check if user has valid token for editing emergency records
        if (! session('valid_emergency_token')) {
            return redirect()->route('token-emergency.validate.form')
                ->with('error', 'Token emergency diperlukan untuk mengedit rekam medis emergency.');
        }

        $rekamMedisEmergency = RekamMedisEmergency::with(['externalEmployee:id,nik_employee,nama_employee,kode_rm,jenis_kelamin,alamat', 'keluhans.diagnosaEmergency'])->findOrFail($id);

        // Get data for dropdowns with relationships
        $externalEmployees = ExternalEmployee::with(['vendor', 'kategori'])->aktif()->get();
        $diagnosaEmergency = DiagnosaEmergency::where('status', 'aktif')->with('obats')->get();
        $diagnosas = Diagnosa::where('status', 'aktif')->get();

        return view('rekam-medis-emergency.edit', compact('rekamMedisEmergency', 'externalEmployees', 'diagnosaEmergency', 'diagnosas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Check if user has valid token for editing emergency records
        if (! session('valid_emergency_token')) {
            return redirect()->route('token-emergency.validate.form')
                ->with('error', 'Token emergency diperlukan untuk mengedit rekam medis emergency.');
        }

        $rekamMedisEmergency = RekamMedisEmergency::findOrFail($id);

        $validated = $request->validate([
            'external_employee_id' => 'required|exists:external_employees,id',
            'tanggal_periksa' => 'required|date|before_or_equal:today',
            'waktu_periksa' => 'nullable|date_format:H:i',
            'status' => 'required|in:On Progress,Close',
            'keluhan' => 'required|string|min:10|max:1000',
            'id_diagnosa_emergency' => 'required|exists:diagnosa_emergency,id_diagnosa_emergency',
            'terapi' => 'required|in:Obat,Lab,Istirahat,Emergency',
            'catatan' => 'nullable|string|max:2000',
            // Obat list validation - for update
            'obat_list' => 'sometimes|array',
            'obat_list.*.id_obat' => 'required_with:obat_list|exists:obat,id_obat',
            'obat_list.*.jumlah_obat' => 'required_with:obat_list|integer|min:1|max:100',
            'obat_list.*.aturan_pakai' => 'nullable|string|max:500',
        ], [
            // External Employee
            'external_employee_id.required' => 'Karyawan emergency harus dipilih.',
            'external_employee_id.exists' => 'Karyawan emergency yang dipilih tidak valid atau tidak ditemukan dalam sistem.',

            // Tanggal Periksa
            'tanggal_periksa.required' => 'Tanggal periksa wajib diisi.',
            'tanggal_periksa.date' => 'Format tanggal periksa tidak valid. Harap gunakan format tanggal yang benar (YYYY-MM-DD).',
            'tanggal_periksa.before_or_equal' => 'Tanggal periksa tidak boleh melebihi tanggal hari ini.',

            // Waktu Periksa
            'waktu_periksa.date_format' => 'Format waktu periksa tidak valid. Harap gunakan format HH:MM (contoh: 14:30).',

            // Status
            'status.required' => 'Status rekam medis harus dipilih.',
            'status.in' => 'Status yang dipilih tidak valid. Pilihan yang tersedia: On Progress atau Close.',

            // Keluhan
            'keluhan.required' => 'Keluhan pasien wajib diisi untuk melanjutkan.',
            'keluhan.min' => 'Keluhan pasien terlalu singkat. Minimal 10 karakter untuk memberikan deskripsi yang memadai.',
            'keluhan.max' => 'Keluhan pasien terlalu panjang. Maksimal 1000 karakter.',

            // Diagnosa Emergency
            'id_diagnosa_emergency.required' => 'Diagnosa emergency harus dipilih.',
            'id_diagnosa_emergency.exists' => 'Diagnosa emergency yang dipilih tidak valid atau tidak ditemukan dalam sistem.',

            // Terapi
            'terapi.required' => 'Jenis terapi harus dipilih.',
            'terapi.in' => 'Jenis terapi yang dipilih tidak valid. Pilihan yang tersedia: Obat, Konsul Faskes Lanjutan, Istirahat, atau Emergency.',

            // Catatan
            'catatan.max' => 'Catatan terlalu panjang. Maksimal 2000 karakter.',

            // Obat List - for update
            'obat_list.array' => 'Format data obat tidak valid. Harap refresh halaman dan coba lagi.',
            'obat_list.*.id_obat.required_with' => 'Setiap baris obat harus memilih nama obat yang valid.',
            'obat_list.*.id_obat.exists' => 'Salah satu obat yang dipilih tidak valid atau tidak ditemukan dalam sistem.',
            'obat_list.*.jumlah_obat.required_with' => 'Jumlah obat wajib diisi untuk setiap obat yang dipilih.',
            'obat_list.*.jumlah_obat.integer' => 'Jumlah obat harus berupa angka bulat.',
            'obat_list.*.jumlah_obat.min' => 'Jumlah obat minimal adalah 1.',
            'obat_list.*.jumlah_obat.max' => 'Jumlah obat maksimal adalah 100 per item.',
            'obat_list.*.aturan_pakai.max' => 'Aturan pakai maksimal 500 karakter.',
        ]);

        try {
            // Verify token is still valid (but don't consume it for edits)
            $currentUserId = Auth::id();
            $token = \App\Models\TokenEmergency::where('token', session('valid_emergency_token'))->first();

            if (! $token || ! $token->canBeUsedBy($currentUserId)) {
                // Clear invalid token from session
                session()->forget('valid_emergency_token');

                return redirect()->route('token-emergency.validate.form')
                    ->with('error', 'Token tidak valid atau sudah kadaluarsa. Silakan masukkan token kembali.');
            }

            // Simpan old keluhans untuk listener BEFORE transaction
            $oldKeluhans = $rekamMedisEmergency->keluhans()
                ->whereNotNull('id_obat')
                ->where('jumlah_obat', '>', 0)
                ->get();

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

                // Delete old keluhans first
                $rekamMedisEmergency->keluhans()->delete();

                // Prepare keluhan base data
                $keluhanBaseData = [
                    'id_emergency' => $rekamMedisEmergency->id_emergency,
                    'id_rekam' => null, // Set to null for emergency records
                    'id_diagnosa' => null, // Set to null for emergency records
                    'id_diagnosa_emergency' => $validated['id_diagnosa_emergency'],
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
                            'jumlah_obat' => $obatData['jumlah_obat'] ?? 0,
                            'aturan_pakai' => $obatData['aturan_pakai'] ?? null,
                        ]));
                    }
                } else {
                    // No obat selected, save keluhan without obat
                    Keluhan::create(array_merge($keluhanBaseData, [
                        'id_obat' => null,
                        'jumlah_obat' => 0,
                        'aturan_pakai' => null,
                    ]));
                }

                DB::commit();

                // Dispatch event untuk menyesuaikan stok obat AFTER transaction commits
                // Refresh the model to get the latest keluhans
                $rekamMedisEmergency->refresh();
                event(new RekamMedisEmergencyUpdated($rekamMedisEmergency, $oldKeluhans));
            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }

            return redirect()->route('rekam-medis.index', ['tab' => 'emergency'])->with('success', 'Data rekam medis emergency berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui data: '.$e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $rekamMedisEmergency = RekamMedisEmergency::with('keluhans')->findOrFail($id);

            // Dispatch event SEBELUM delete untuk mengembalikan stok
            event(new RekamMedisEmergencyDeleted($rekamMedisEmergency));

            // Delete emergency medical record
            $rekamMedisEmergency->delete();

            return redirect()->route('rekam-medis.index', ['tab' => 'emergency'])->with('success', 'Data rekam medis emergency berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus data: '.$e->getMessage());
        }
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

        // Get obat IDs
        $obatIds = $diagnosa->obats->pluck('id_obat')->toArray();

        // Get sisa stok batch untuk semua obat sekaligus
        $stokMap = \App\Models\StokBulanan::getSisaStokSaatIniBatch($obatIds);

        $obats = $diagnosa->obats->map(function ($obat) use ($stokMap) {
            return [
                'id_obat' => $obat->id_obat,
                'nama_obat' => $obat->nama_obat,
                'stok_akhir' => $stokMap[$obat->id_obat] ?? 0,
            ];
        });

        return response()->json($obats);
    }

    /**
     * Get all diagnosa emergency with their obat
     */
    public function getDiagnosaWithObat()
    {
        $diagnosaEmergency = DiagnosaEmergency::where('status', 'aktif')->with('obats')->get();

        // Kumpulkan semua obat IDs
        $allObatIds = $diagnosaEmergency->flatMap(function ($diagnosa) {
            return $diagnosa->obats->pluck('id_obat');
        })->unique()->toArray();

        // Get sisa stok batch untuk semua obat sekaligus
        $stokMap = \App\Models\StokBulanan::getSisaStokSaatIniBatch($allObatIds);

        $result = $diagnosaEmergency->map(function ($diagnosa) use ($stokMap) {
            return [
                'id_diagnosa_emergency' => $diagnosa->id_diagnosa_emergency,
                'nama_diagnosa_emergency' => $diagnosa->nama_diagnosa_emergency,
                'obats' => $diagnosa->obats->map(function ($obat) use ($stokMap) {
                    return [
                        'id_obat' => $obat->id_obat,
                        'nama_obat' => $obat->nama_obat,
                        'stok_akhir' => $stokMap[$obat->id_obat] ?? 0,
                    ];
                }),
            ];
        });

        return response()->json($result);
    }

    /**
     * Export data rekam medis emergency ke Excel
     */
    public function export(Request $request)
    {
        // Get filter parameters
        $dariTanggal = $request->input('dari_tanggal');
        $sampaiTanggal = $request->input('sampai_tanggal');
        $search = $request->input('q');
        $status = $request->input('status');

        try {
            // Query for emergency medical records
            $query = RekamMedisEmergency::with([
                'user:id_user,username,nama_lengkap',
                'externalEmployee',
                'keluhans.diagnosaEmergency:id_diagnosa_emergency,nama_diagnosa_emergency',
                'keluhans.obat:id_obat,nama_obat',
            ]);

            // Apply filters
            if ($search) {
                $query->search($search);
            }

            if ($dariTanggal) {
                $query->where('tanggal_periksa', '>=', $dariTanggal);
            }

            if ($sampaiTanggal) {
                $query->where('tanggal_periksa', '<=', $sampaiTanggal);
            }

            if ($status) {
                $query->where('status', $status);
            }

            // Get all data (no pagination for export)
            $rekamMedisEmergency = $query->orderBy('id_emergency', 'desc')->get();

            // Create spreadsheet
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Data Rekam Medis Emergency');

            // Set document properties
            $spreadsheet->getProperties()
                ->setCreator('SIPO')
                ->setTitle('Export Data Rekam Medis Emergency')
                ->setSubject('Export Data Rekam Medis Emergency')
                ->setDescription('Export data rekam medis emergency');

            // Headers - Format sama dengan template import
            $headers = [
                'Hari / Tgl', 'Waktu Periksa', 'NIK', 'Nama Karyawan', 'Kode RM', 'Nama Pasien',
                'Diagnosa 1', 'Keluhan 1', 'Obat 1-1', 'Qty', 'Obat 1-2', 'Qty', 'Obat 1-3', 'Qty',
                'Diagnosa 2', 'Keluhan 2', 'Obat 2-1', 'Qty', 'Obat 2-2', 'Qty', 'Obat 2-3', 'Qty',
                'Diagnosa 3', 'Keluhan 3', 'Obat 3-1', 'Qty', 'Obat 3-2', 'Qty', 'Obat 3-3', 'Qty',
                'Petugas', 'Status',
            ];

            $column = 'A';
            foreach ($headers as $header) {
                $sheet->setCellValue($column . '1', $header);
                $column++;
            }

            // Style header
            $headerStyle = [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 12,
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'DC2626'],
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ];

            $sheet->getStyle('A1:AF1')->applyFromArray($headerStyle);

            // Data
            $row = 2;
            foreach ($rekamMedisEmergency as $index => $rm) {
                // Group data by diagnosa
                $diagnosaGroups = [];
                foreach ($rm->keluhans as $keluhan) {
                    $diagnosaId = $keluhan->id_diagnosa_emergency;
                    $diagnosaName = $keluhan->diagnosaEmergency->nama_diagnosa_emergency ?? '-';

                    if (!isset($diagnosaGroups[$diagnosaId])) {
                        $diagnosaGroups[$diagnosaId] = [
                            'diagnosa' => $diagnosaName,
                            'keluhan' => $keluhan->keterangan ?? '-',
                            'obats' => []
                        ];
                    }

                    if ($keluhan->obat) {
                        $diagnosaGroups[$diagnosaId]['obats'][] = [
                            'nama_obat' => $keluhan->obat->nama_obat,
                            'jumlah_obat' => $keluhan->jumlah_obat ?? 0
                        ];
                    }
                }

                // Convert to array and ensure we have 3 diagnosa groups
                $diagnosaArray = array_values($diagnosaGroups);
                while (count($diagnosaArray) < 3) {
                    $diagnosaArray[] = [
                        'diagnosa' => '-',
                        'keluhan' => '-',
                        'obats' => []
                    ];
                }

                $sheet->setCellValue('A' . $row, is_string($rm->tanggal_periksa) ? $rm->tanggal_periksa : $rm->tanggal_periksa->format('d/m/Y'));
                $sheet->setCellValue('B' . $row, $rm->waktu_periksa ? (is_string($rm->waktu_periksa) ? $rm->waktu_periksa : $rm->waktu_periksa->format('H:i')) : '-');
                $sheet->setCellValue('C' . $row, $rm->externalEmployee->nik_employee ?? '-');
                $sheet->setCellValue('D' . $row, $rm->externalEmployee->nama_employee ?? '-');
                $sheet->setCellValue('E' . $row, $rm->externalEmployee->kode_rm ?? '-');
                $sheet->setCellValue('F' . $row, $rm->externalEmployee->nama_employee ?? '-');

                // Diagnosa 1
                $sheet->setCellValue('G' . $row, $diagnosaArray[0]['diagnosa']);
                $sheet->setCellValue('H' . $row, $diagnosaArray[0]['keluhan']);

                // Obat 1-1, 1-2, 1-3
                $obat1 = $diagnosaArray[0]['obats'];
                $sheet->setCellValue('I' . $row, isset($obat1[0]) ? $obat1[0]['nama_obat'] : '-');
                $sheet->setCellValue('J' . $row, isset($obat1[0]) ? $obat1[0]['jumlah_obat'] : '-');
                $sheet->setCellValue('K' . $row, isset($obat1[1]) ? $obat1[1]['nama_obat'] : '-');
                $sheet->setCellValue('L' . $row, isset($obat1[1]) ? $obat1[1]['jumlah_obat'] : '-');
                $sheet->setCellValue('M' . $row, isset($obat1[2]) ? $obat1[2]['nama_obat'] : '-');
                $sheet->setCellValue('N' . $row, isset($obat1[2]) ? $obat1[2]['jumlah_obat'] : '-');

                // Diagnosa 2
                $sheet->setCellValue('O' . $row, $diagnosaArray[1]['diagnosa']);
                $sheet->setCellValue('P' . $row, $diagnosaArray[1]['keluhan']);

                // Obat 2-1, 2-2, 2-3
                $obat2 = $diagnosaArray[1]['obats'];
                $sheet->setCellValue('Q' . $row, isset($obat2[0]) ? $obat2[0]['nama_obat'] : '-');
                $sheet->setCellValue('R' . $row, isset($obat2[0]) ? $obat2[0]['jumlah_obat'] : '-');
                $sheet->setCellValue('S' . $row, isset($obat2[1]) ? $obat2[1]['nama_obat'] : '-');
                $sheet->setCellValue('T' . $row, isset($obat2[1]) ? $obat2[1]['jumlah_obat'] : '-');
                $sheet->setCellValue('U' . $row, isset($obat2[2]) ? $obat2[2]['nama_obat'] : '-');
                $sheet->setCellValue('V' . $row, isset($obat2[2]) ? $obat2[2]['jumlah_obat'] : '-');

                // Diagnosa 3
                $sheet->setCellValue('W' . $row, $diagnosaArray[2]['diagnosa']);
                $sheet->setCellValue('X' . $row, $diagnosaArray[2]['keluhan']);

                // Obat 3-1, 3-2, 3-3
                $obat3 = $diagnosaArray[2]['obats'];
                $sheet->setCellValue('Y' . $row, isset($obat3[0]) ? $obat3[0]['nama_obat'] : '-');
                $sheet->setCellValue('Z' . $row, isset($obat3[0]) ? $obat3[0]['jumlah_obat'] : '-');
                $sheet->setCellValue('AA' . $row, isset($obat3[1]) ? $obat3[1]['nama_obat'] : '-');
                $sheet->setCellValue('AB' . $row, isset($obat3[1]) ? $obat3[1]['jumlah_obat'] : '-');
                $sheet->setCellValue('AC' . $row, isset($obat3[2]) ? $obat3[2]['nama_obat'] : '-');
                $sheet->setCellValue('AD' . $row, isset($obat3[2]) ? $obat3[2]['jumlah_obat'] : '-');

                $sheet->setCellValue('AE' . $row, $rm->user->nama_lengkap ?? '-');
                $sheet->setCellValue('AF' . $row, $rm->status ?? '-');

                $row++;
            }

            // Style data
            $dataStyle = [
                'alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC'],
                    ],
                ],
            ];

            $sheet->getStyle('A2:AF' . ($row - 1))->applyFromArray($dataStyle);

            // Set column widths
            $sheet->getColumnDimension('A')->setWidth(15);
            $sheet->getColumnDimension('B')->setWidth(15);
            $sheet->getColumnDimension('C')->setWidth(10);
            $sheet->getColumnDimension('D')->setWidth(20);
            $sheet->getColumnDimension('E')->setWidth(15);
            $sheet->getColumnDimension('F')->setWidth(20);
            $sheet->getColumnDimension('G')->setWidth(20);
            $sheet->getColumnDimension('H')->setWidth(25);
            $sheet->getColumnDimension('I')->setWidth(15);
            $sheet->getColumnDimension('J')->setWidth(10);
            $sheet->getColumnDimension('K')->setWidth(15);
            $sheet->getColumnDimension('L')->setWidth(10);
            $sheet->getColumnDimension('M')->setWidth(15);
            $sheet->getColumnDimension('N')->setWidth(10);
            $sheet->getColumnDimension('O')->setWidth(15);
            $sheet->getColumnDimension('P')->setWidth(25);
            $sheet->getColumnDimension('Q')->setWidth(15);
            $sheet->getColumnDimension('R')->setWidth(10);
            $sheet->getColumnDimension('S')->setWidth(15);
            $sheet->getColumnDimension('T')->setWidth(10);
            $sheet->getColumnDimension('U')->setWidth(15);
            $sheet->getColumnDimension('V')->setWidth(10);
            $sheet->getColumnDimension('W')->setWidth(15);
            $sheet->getColumnDimension('X')->setWidth(25);
            $sheet->getColumnDimension('Y')->setWidth(15);
            $sheet->getColumnDimension('Z')->setWidth(10);
            $sheet->getColumnDimension('AA')->setWidth(15);
            $sheet->getColumnDimension('AB')->setWidth(10);
            $sheet->getColumnDimension('AC')->setWidth(15);
            $sheet->getColumnDimension('AD')->setWidth(10);
            $sheet->getColumnDimension('AE')->setWidth(20);
            $sheet->getColumnDimension('AF')->setWidth(15);

            // Set row height for header
            $sheet->getRowDimension(1)->setRowHeight(25);

            // Create Excel file
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $filename = 'export_rekam_medis_emergency_' . date('Y-m-d_H-i-s') . '.xlsx';

            // Set headers for download
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            $writer->save('php://output');
            exit;

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal export data: ' . $e->getMessage());
        }
    }
}
