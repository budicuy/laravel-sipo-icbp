<?php

namespace App\Http\Controllers;

use App\Events\RekamMedisCreated;
use App\Events\RekamMedisDeleted;
use App\Http\Requests\RekamMedisStoreRequest;
use App\Http\Requests\RekamMedisUpdateRequest;
use App\Models\Diagnosa;
use App\Models\Karyawan;
use App\Models\Keluarga;
use App\Models\Keluhan;
use App\Models\Obat;
use App\Models\RekamMedis;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class RekamMedisController extends Controller
{
    public function index(Request $request)
    {
        // Query for regular medical records
        $query = RekamMedis::with([
            'keluarga.karyawan:id_karyawan,nik_karyawan,nama_karyawan',
            'keluarga.hubungan:kode_hubungan,hubungan',
            'user:id_user,username,nama_lengkap',
            'keluhans.diagnosa:id_diagnosa,nama_diagnosa',
            'keluhans.obat:id_obat,nama_obat',
        ]);

        // Filter pencarian for regular records
        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function ($sub) use ($q) {
                $sub->whereHas('keluarga', function ($keluarga) use ($q) {
                    $keluarga->where('nama_keluarga', 'like', "%$q%")
                        ->orWhere('no_rm', 'like', "%$q%")
                        ->orWhere('bpjs_id', 'like', "%$q%")
                        ->orWhereHas('karyawan', function ($karyawan) use ($q) {
                            $karyawan->where('nik_karyawan', 'like', "%$q%");
                        });
                });
            });
        }

        // Filter tanggal for regular records
        if ($request->filled('dari_tanggal')) {
            $query->where('tanggal_periksa', '>=', $request->dari_tanggal);
        }

        if ($request->filled('sampai_tanggal')) {
            $query->where('tanggal_periksa', '<=', $request->sampai_tanggal);
        }

        // Filter status for regular records
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Pagination for regular records
        $perPage = $request->input('per_page', 50);
        if (! in_array($perPage, [50, 100, 200])) {
            $perPage = 50;
        }

        $rekamMedis = $query->orderBy('id_rekam', 'desc')->paginate($perPage)->appends($request->except('page'));

        // Query for emergency medical records
        $queryEmergency = \App\Models\RekamMedisEmergency::with([
            'user:id_user,username,nama_lengkap',
            'externalEmployee',
            'keluhans.diagnosaEmergency',
        ]);

        // Filter pencarian for emergency records
        if ($request->filled('q')) {
            $q = $request->input('q');
            $queryEmergency->search($q);
        }

        // Filter tanggal for emergency records
        if ($request->filled('dari_tanggal')) {
            $queryEmergency->where('tanggal_periksa', '>=', $request->dari_tanggal);
        }

        if ($request->filled('sampai_tanggal')) {
            $queryEmergency->where('tanggal_periksa', '<=', $request->sampai_tanggal);
        }

        // Filter status for emergency records
        if ($request->filled('status')) {
            $queryEmergency->where('status', $request->status);
        }

        $rekamMedisEmergency = $queryEmergency->orderBy('id_emergency', 'desc')->paginate($perPage)->appends($request->except('page'));

        return view('rekam-medis.index', compact('rekamMedis', 'rekamMedisEmergency'));
    }

    public function chooseType()
    {
        return view('rekam-medis.choose-type');
    }

    public function create()
    {
        // Get all diagnosa and obat for keluhan inputs
        $diagnosas = Diagnosa::orderBy('nama_diagnosa')->get();
        $obats = Obat::orderBy('nama_obat')->get();

        return view('rekam-medis.create', compact('diagnosas', 'obats'));
    }

    public function indexEmergency(Request $request)
    {
        // Query untuk mendapatkan data dari tabel rekam_medis_emergency
        $query = \App\Models\RekamMedisEmergency::with(['user:id_user,username,nama_lengkap']);

        // Filter pencarian
        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function ($sub) use ($q) {
                $sub->where('nama_pasien', 'like', "%$q%")
                    ->orWhere('nik_pasien', 'like', "%$q%")
                    ->orWhere('no_rm', 'like', "%$q%");
            });
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
            $query->where('status_rekam_medis', $request->status);
        }

        // Pagination
        $perPage = $request->input('per_page', 50);
        if (! in_array($perPage, [50, 100, 200])) {
            $perPage = 50;
        }

        $rekamMedisEmergency = $query->orderBy('id_emergency', 'desc')->paginate($perPage)->appends($request->except('page'));

        return view('rekam-medis-emergency.index', compact('rekamMedisEmergency'));
    }

    public function createEmergency()
    {
        return view('rekam-medis-emergency.create-emergency');
    }

    public function storeEmergency(Request $request)
    {
        // Check if user has valid token
        if (! session('valid_emergency_token')) {
            return redirect()->route('token-emergency.validate')
                ->with('error', 'Token emergency diperlukan untuk membuat rekam medis emergency.');
        }

        $validated = $request->validate([
            'nik_pasien' => 'required|digits_between:1,16|numeric',
            'nama_pasien' => 'required|string|max:255',
            'no_rm' => 'required|string|max:30',
            'jenis_kelamin' => 'required|in:L,P',
            'tanggal_periksa' => 'required|date',
            'waktu_periksa' => 'nullable|date_format:H:i',
            'status_rekam_medis' => 'required|in:On Progress,Close',
            'keluhan' => 'required|string',
            'diagnosa' => 'nullable|string',
            'catatan' => 'nullable|string',
        ], [
            'nik_pasien.required' => 'NIK pasien harus diisi',
            'nik_pasien.digits_between' => 'NIK pasien harus terdiri dari 1-16 digit angka',
            'nik_pasien.numeric' => 'NIK pasien harus berupa angka',
            'nama_pasien.required' => 'Nama pasien harus diisi',
            'nama_pasien.string' => 'Nama pasien harus berupa teks',
            'nama_pasien.max' => 'Nama pasien maksimal 255 karakter',
            'no_rm.required' => 'No. RM harus diisi',
            'no_rm.string' => 'No. RM harus berupa teks',
            'no_rm.max' => 'No. RM maksimal 30 karakter',
            'jenis_kelamin.required' => 'Jenis kelamin harus dipilih',
            'jenis_kelamin.in' => 'Jenis kelamin harus "Laki-laki" atau "Perempuan"',
            'tanggal_periksa.required' => 'Tanggal periksa harus diisi',
            'tanggal_periksa.date' => 'Format tanggal tidak valid. Gunakan format YYYY-MM-DD',
            'waktu_periksa.date_format' => 'Format waktu tidak valid. Gunakan format HH:MM',
            'status_rekam_medis.required' => 'Status rekam medis harus dipilih',
            'status_rekam_medis.in' => 'Status rekam medis harus "On Progress" atau "Close"',
            'keluhan.required' => 'Keluhan harus diisi',
            'keluhan.string' => 'Keluhan harus berupa teks',
            'diagnosa.string' => 'Diagnosa harus berupa teks',
            'catatan.string' => 'Catatan harus berupa teks',
        ]);

        try {
            // Using Laravel 12's transaction method with automatic retry for better reliability
            $rekamMedisEmergency = \Illuminate\Support\Facades\DB::transaction(function () use ($validated) {
                // Get and use the token
                $token = \App\Models\TokenEmergency::where('token', session('valid_emergency_token'))
                    ->where('status', 'available')
                    ->first();

                if (! $token) {
                    throw new \Exception('Token tidak valid atau sudah digunakan.');
                }

                // Use the token (mark as used)
                $token->useToken(Auth::id());

                // Simpan data rekam medis emergency langsung ke tabel rekam_medis_emergency
                $rekamMedisEmergency = \App\Models\RekamMedisEmergency::create([
                    'nik_pasien' => $validated['nik_pasien'],
                    'nama_pasien' => $validated['nama_pasien'],
                    'no_rm' => $validated['no_rm'],
                    'jenis_kelamin' => $validated['jenis_kelamin'],
                    'tanggal_periksa' => $validated['tanggal_periksa'],
                    'waktu_periksa' => $validated['waktu_periksa'] ?? null,
                    'status_rekam_medis' => $validated['status_rekam_medis'],
                    'keluhan' => $validated['keluhan'],
                    'diagnosa' => $validated['diagnosa'],
                    'catatan' => $validated['catatan'],
                    'id_user' => Auth::id(),
                    'hubungan' => 'Emergency',
                ]);

                // Clear token from session after successful use
                session()->forget('valid_emergency_token');

                return $rekamMedisEmergency;
            }, 3); // Retry up to 3 times on deadlock

            return redirect()->route('rekam-medis-emergency.index')->with('success', 'Data rekam medis emergency berhasil ditambahkan! Token telah digunakan.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data: '.$e->getMessage());
        }
    }

    public function store(RekamMedisStoreRequest $request)
    {
<<<<<<< HEAD
        $validated = $request->validate([
            'id_keluarga' => 'required|exists:keluarga,id_keluarga',
            'tanggal_periksa' => 'required|date',
            'waktu_periksa' => 'nullable|date_format:H:i:s',
            'status' => 'required|in:On Progress,Close',
            'jumlah_keluhan' => 'required|integer|min:1|max:3',

            // Validasi untuk setiap keluhan
            'keluhan.*.id_diagnosa' => 'required|exists:diagnosa,id_diagnosa',
            'keluhan.*.terapi' => 'required|in:Obat,Lab,Istirahat',
            'keluhan.*.keterangan' => 'nullable|string',
            'keluhan.*.obat_list' => 'nullable|array',
            'keluhan.*.obat_list.*.id_obat' => 'required|exists:obat,id_obat',
            'keluhan.*.obat_list.*.jumlah_obat' => 'nullable|integer|min:1|max:10000',
            'keluhan.*.obat_list.*.aturan_pakai' => 'nullable|string',
        ]);
=======
        $validated = $request->validated();
>>>>>>> f4794cd429d33da2afdec023af14015e3c34f646

        // Using Laravel 12's transaction method with automatic retry for better reliability
        try {
            $rekamMedis = \Illuminate\Support\Facades\DB::transaction(function () use ($validated, $request) {
                // Simpan data rekam medis
                $rekamMedis = RekamMedis::create([
                    'id_keluarga' => $validated['id_keluarga'],
                    'tanggal_periksa' => $validated['tanggal_periksa'],
                    'waktu_periksa' => $validated['waktu_periksa'] ?? null,
                    'id_user' => Auth::id(),
                    'jumlah_keluhan' => $validated['jumlah_keluhan'],
                    'status' => $validated['status'],
                ]);

                // Simpan data keluhan sesuai jumlah
                if (isset($request->keluhan)) {
                    foreach ($request->keluhan as $keluhanData) {
                        // Check if there are obat_list (multiple obat)
                        if (isset($keluhanData['obat_list']) && is_array($keluhanData['obat_list'])) {
                            // Save multiple keluhan entries, one for each obat
                            foreach ($keluhanData['obat_list'] as $obatData) {
                                Keluhan::create([
                                    'id_rekam' => $rekamMedis->id_rekam,
                                    'id_keluarga' => $validated['id_keluarga'],
                                    'id_diagnosa' => $keluhanData['id_diagnosa'],
                                    'terapi' => $keluhanData['terapi'],
                                    'keterangan' => $keluhanData['keterangan'] ?? null,
                                    'id_obat' => $obatData['id_obat'],
                                    'jumlah_obat' => $obatData['jumlah_obat'] ?? 0, // Default to 0
                                    'aturan_pakai' => $obatData['aturan_pakai'] ?? null,
                                ]);
                            }
                        } else {
                            // No obat selected, save keluhan without obat
                            Keluhan::create([
                                'id_rekam' => $rekamMedis->id_rekam,
                                'id_keluarga' => $validated['id_keluarga'],
                                'id_diagnosa' => $keluhanData['id_diagnosa'],
                                'terapi' => $keluhanData['terapi'],
                                'keterangan' => $keluhanData['keterangan'] ?? null,
                                'id_obat' => null,
                                'jumlah_obat' => 0, // Default to 0
                                'aturan_pakai' => null,
                            ]);
                        }
                    }
                }

                return $rekamMedis;
            }, 3); // Retry up to 3 times on deadlock

            // Dispatch event untuk mengurangi stok obat otomatis
            RekamMedisCreated::dispatch($rekamMedis);

            return redirect()->route('rekam-medis.index')->with('success', 'Data rekam medis berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data: '.$e->getMessage());
        }
    }

    public function show($id)
    {
        $rekamMedis = RekamMedis::with([
            'keluarga.karyawan:id_karyawan,nik_karyawan,nama_karyawan',
            'keluarga.hubungan:kode_hubungan,hubungan',
            'user:id_user,username,nama_lengkap',
            'keluhans.diagnosa:id_diagnosa,nama_diagnosa',
            'keluhans.obat:id_obat,nama_obat',
        ])->findOrFail($id);

        // Optimized query for riwayat kunjungan - select only needed columns
        $riwayatKunjungan = RekamMedis::with([
            'user:id_user,username,nama_lengkap',
            'keluhans.diagnosa:id_diagnosa,nama_diagnosa',
            'keluhans.obat:id_obat,nama_obat',
        ])
            ->select('id_rekam', 'id_keluarga', 'tanggal_periksa', 'status', 'id_user')
            ->where('id_keluarga', $rekamMedis->id_keluarga)
            ->orderBy('tanggal_periksa', 'desc')
            ->get();

        return view('rekam-medis.detail', compact('rekamMedis', 'riwayatKunjungan'));
    }

    public function edit($id)
    {
        $rekamMedis = RekamMedis::with([
            'keluarga.karyawan.departemen',  // relasi keluarga dengan karyawan dan departemen
            'keluarga.hubungan',             // relasi hubungan
            'keluhans.diagnosa',             // relasi diagnosa di tabel keluhans
            'keluhans.obat',                  // relasi obat (pivot diagnosa_obat)
        ])->findOrFail($id);

        $diagnosas = Diagnosa::orderBy('nama_diagnosa')->get();
        $obats = Obat::orderBy('nama_obat')->get();

        return view('rekam-medis.edit', compact('rekamMedis', 'diagnosas', 'obats'));
    }

    public function update(RekamMedisUpdateRequest $request, $id)
    {
        $rekamMedis = RekamMedis::findOrFail($id);
<<<<<<< HEAD

        $validated = $request->validate([
            'id_keluarga' => 'required|exists:keluarga,id_keluarga',
            'tanggal_periksa' => 'required|date',
            'waktu_periksa' => 'nullable|date_format:H:i:s',
            'status' => 'required|in:On Progress,Close',
            'jumlah_keluhan' => 'required|integer|min:1|max:3',

            // Validasi untuk setiap keluhan
            'keluhan.*.id_diagnosa' => 'required|exists:diagnosa,id_diagnosa',
            'keluhan.*.terapi' => 'required|in:Obat,Lab,Istirahat',
            'keluhan.*.keterangan' => 'nullable|string',
            'keluhan.*.obat_list' => 'nullable|array',
            'keluhan.*.obat_list.*.id_obat' => 'required|exists:obat,id_obat',
            'keluhan.*.obat_list.*.jumlah_obat' => 'nullable|integer|min:1|max:10000',
            'keluhan.*.obat_list.*.aturan_pakai' => 'nullable|string',
        ]);
=======
        $validated = $request->validated();
>>>>>>> f4794cd429d33da2afdec023af14015e3c34f646

        // Using Laravel 12's transaction method with automatic retry for better reliability
        try {
            // Simpan keluhan lama untuk perbandingan stok
            $oldKeluhans = $rekamMedis->keluhans()->get();

            \Illuminate\Support\Facades\DB::transaction(function () use ($rekamMedis, $validated, $request) {
                // Update data rekam medis
                $rekamMedis->update([
                    'id_keluarga' => $validated['id_keluarga'],
                    'tanggal_periksa' => $validated['tanggal_periksa'],
                    'waktu_periksa' => $validated['waktu_periksa'] ?? null,
                    'jumlah_keluhan' => $validated['jumlah_keluhan'],
                    'status' => $validated['status'],
                ]);

                // Hapus keluhan lama
                $rekamMedis->keluhans()->delete();

                // Simpan keluhan baru
                if (isset($request->keluhan)) {
                    foreach ($request->keluhan as $keluhanData) {
                        // Check if there are obat_list (multiple obat)
                        if (isset($keluhanData['obat_list']) && is_array($keluhanData['obat_list'])) {
                            // Save multiple keluhan entries, one for each obat
                            foreach ($keluhanData['obat_list'] as $obatData) {
                                Keluhan::create([
                                    'id_rekam' => $rekamMedis->id_rekam,
                                    'id_keluarga' => $validated['id_keluarga'],
                                    'id_diagnosa' => $keluhanData['id_diagnosa'],
                                    'terapi' => $keluhanData['terapi'],
                                    'keterangan' => $keluhanData['keterangan'] ?? null,
                                    'id_obat' => $obatData['id_obat'],
                                    'jumlah_obat' => $obatData['jumlah_obat'] ?? 0, // Default to 0
                                    'aturan_pakai' => $obatData['aturan_pakai'] ?? null,
                                ]);
                            }
                        } else {
                            // No obat selected, save keluhan without obat
                            Keluhan::create([
                                'id_rekam' => $rekamMedis->id_rekam,
                                'id_keluarga' => $validated['id_keluarga'],
                                'id_diagnosa' => $keluhanData['id_diagnosa'],
                                'terapi' => $keluhanData['terapi'],
                                'keterangan' => $keluhanData['keterangan'] ?? null,
                                'id_obat' => null,
                                'jumlah_obat' => 0, // Default to 0
                                'aturan_pakai' => null,
                            ]);
                        }
                    }
                }
            }, 3); // Retry up to 3 times on deadlock

            // Dispatch event untuk menyesuaikan stok obat otomatis
            event(new \App\Events\RekamMedisUpdated($rekamMedis, $oldKeluhans));

            return redirect()->route('rekam-medis.index')->with('success', 'Data rekam medis berhasil diperbarui! Stok obat telah disesuaikan.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui data: '.$e->getMessage());
        }
    }

    public function destroy($id)
    {
        $rekamMedis = RekamMedis::findOrFail($id);

        // Dispatch event untuk mengembalikan stok obat otomatis
        RekamMedisDeleted::dispatch($rekamMedis);

        $rekamMedis->delete();

        return redirect()->route('rekam-medis.index')->with('success', 'Data rekam medis berhasil dihapus! Stok obat telah dikembalikan.');
    }

    // API untuk pencarian karyawan (AJAX)
    public function searchKaryawan(Request $request)
    {
        $search = $request->input('q');

        $karyawans = Karyawan::with(['departemen:id_departemen,nama_departemen'])
            ->select('id_karyawan', 'nik_karyawan', 'nama_karyawan', 'id_departemen', 'foto')
            ->where(function ($query) use ($search) {
                $query->where('nik_karyawan', 'like', "%{$search}%")
                    ->orWhere('nama_karyawan', 'like', "%{$search}%");
            })
            ->limit(10)
            ->get()
            ->map(function ($karyawan) {
                return [
                    'id_karyawan' => $karyawan->id_karyawan,
                    'nik_karyawan' => $karyawan->nik_karyawan,
                    'nama_karyawan' => $karyawan->nama_karyawan,
                    'nama_departemen' => $karyawan->departemen->nama_departemen ?? '',
                    'foto' => $karyawan->foto,
                ];
            });

        return response()->json($karyawans);
    }

    // API untuk mendapatkan anggota keluarga berdasarkan karyawan (AJAX)
    public function getFamilyMembers(Request $request)
    {
        $karyawanId = $request->input('karyawan_id');

        $familyMembers = Keluarga::with(['hubungan:kode_hubungan,hubungan'])
            ->where('id_karyawan', $karyawanId)
            ->select('id_keluarga', 'nama_keluarga', 'jenis_kelamin', 'kode_hubungan')
            ->get()
            ->map(function ($keluarga) {
                return [
                    'id_keluarga' => $keluarga->id_keluarga,
                    'nama_keluarga' => $keluarga->nama_keluarga,
                    'kode_hubungan' => $keluarga->kode_hubungan, // <- INI jadi sumber nilai NO RM
                    'hubungan' => $keluarga->hubungan->hubungan ?? '-',
                    'jenis_kelamin' => $keluarga->jenis_kelamin,
                ];
            });

        return response()->json($familyMembers);
    }

    // API untuk pencarian pasien (AJAX) - deprecated
    public function searchPasien(Request $request)
    {
        $search = $request->input('q');

        $pasiens = Keluarga::with([
            'karyawan:id_karyawan,nik_karyawan',
            'hubungan:kode_hubungan,hubungan',
        ])
            ->select('id_keluarga', 'id_karyawan', 'nama_keluarga', 'no_rm', 'bpjs_id', 'kode_hubungan', 'jenis_kelamin', 'tanggal_lahir')
            ->where(function ($query) use ($search) {
                $query->where('nama_keluarga', 'like', "%{$search}%")
                    ->orWhere('bpjs_id', 'like', "%{$search}%")
                    ->orWhere('no_rm', 'like', "%{$search}%")
                    ->orWhereHas('karyawan', function ($karyawan) use ($search) {
                        $karyawan->where('nik_karyawan', 'like', "%{$search}%");
                    });
            })
            ->limit(10)
            ->get()
            ->map(function ($keluarga) {
                return [
                    'id' => $keluarga->id_keluarga,
                    'no_rm' => $keluarga->no_rm,
                    'nama' => $keluarga->nama_keluarga,
                    'bpjs_id' => $keluarga->bpjs_id,
                    'nik_karyawan' => $keluarga->karyawan->nik_karyawan ?? '',
                    'kode_hubungan' => $keluarga->kode_hubungan,
                    'hubungan' => $keluarga->hubungan->hubungan ?? '',
                    'jenis_kelamin' => $keluarga->jenis_kelamin,
                    'tanggal_lahir' => $keluarga->tanggal_lahir ? $keluarga->tanggal_lahir->format('d/m/Y') : '',
                ];
            });

        return response()->json($pasiens);
    }

    /**
     * Get obat by diagnosa ID
     */
    public function getObatByDiagnosa(Request $request)
    {
        $diagnosaId = $request->get('diagnosa_id');

        if (! $diagnosaId) {
            return response()->json([]);
        }

        $diagnosa = Diagnosa::with('obats')->find($diagnosaId);

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
     * Update status rekam medis via AJAX
     */
    public function updateStatus(Request $request, $id)
    {
        $rekamMedis = RekamMedis::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:On Progress,Close',
        ], [
            'status.required' => 'Status harus dipilih',
            'status.in' => 'Status harus "On Progress" atau "Close"',
        ]);

        try {
            $rekamMedis->update([
                'status' => $validated['status'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Status berhasil diperbarui',
                'status' => $rekamMedis->status,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui status: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Download template untuk import data rekam medis
     */
    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Import Rekam Medis');

        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator('SIPO ICBP')
            ->setTitle('Template Import Rekam Medis')
            ->setSubject('Template Import Rekam Medis')
            ->setDescription('Template untuk import data rekam medis');

        // Header columns - Updated for new format with AD column
        $headers = [
<<<<<<< HEAD
            'Hari / Tgl', 'Time', 'NIK', 'Nama Karyawan', 'Kode RM', 'Nama Pasien',
            'Diagnosa 1', 'Keluhan', 'Obat 1', 'Qty', 'Obat 2', 'Qty', 'Obat 3', 'Qty',
            'Diagnosa 2', 'Keluhan', 'Obat 1', 'Qty', 'Obat 2', 'Qty', 'Obat 3', 'Qty',
            'Diagnosa 3', 'Keluhan', 'Obat 1', 'Qty', 'Obat 2', 'Qty', 'Obat 3', 'Qty',
            'Petugas Klinik', 'Status'
=======
            'Hari / Tgl', 'Time', 'Shift', 'No', 'NIK', 'Nama Karyawan', 'Kode RM', 'Nama Pasien',
            'Diagnosa 1', 'Keluhan 1', 'Obat 1-1', 'Qyt', 'Obat 1-2', 'Qyt', 'Obat 1-3', 'Qyt',
            'Diagnosa 2', 'Keluhan 2', 'Obat 2-1', 'Qyt', 'Obat 2-2', 'Qyt',
            'Diagnosa 3', 'Keluhan 3', 'Obat 3-1', 'Qyt', 'Obat 3-2', 'Qyt', 'Petugas', 'Status',
>>>>>>> f4794cd429d33da2afdec023af14015e3c34f646
        ];

        $column = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($column.'1', $header);
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
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '059669'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];

        $sheet->getStyle('A1:AF1')->applyFromArray($headerStyle);

        // Add sample data - Updated for multiple diagnoses format
        // Sample 1: Single Diagnosa
        $sheet->setCellValue('A2', '01/08/2025');
        $sheet->setCellValue('B2', '09:22');
        $sheet->setCellValue('C2', '50173241');
        $sheet->setCellValue('D2', 'M. K. Ronggo Warsito');
        $sheet->setCellValue('E2', '50173241-A');
        $sheet->setCellValue('F2', 'M. K. Ronggo Warsito');
        $sheet->setCellValue('G2', 'Sakit Gigi');
        $sheet->setCellValue('H2', 'Nyeri gigi geraham');
        $sheet->setCellValue('I2', 'Natrium Diklofenak');
        $sheet->setCellValue('J2', '10');
        $sheet->setCellValue('K2', 'Amoxicilin');
        $sheet->setCellValue('L2', '15');
        $sheet->setCellValue('M2', '-');
        $sheet->setCellValue('N2', '-');
        $sheet->setCellValue('O2', '-');
        $sheet->setCellValue('P2', '-');
        $sheet->setCellValue('Q2', '-');
        $sheet->setCellValue('R2', '-');
        $sheet->setCellValue('S2', '-');
        $sheet->setCellValue('T2', '-');
        $sheet->setCellValue('U2', '-');
        $sheet->setCellValue('V2', '-');
        $sheet->setCellValue('W2', '-');
        $sheet->setCellValue('X2', '-');
        $sheet->setCellValue('Y2', '-');
        $sheet->setCellValue('Z2', '-');
        $sheet->setCellValue('AA2', '-');
        $sheet->setCellValue('AB2', '-');
        $sheet->setCellValue('AC2', '-');
        $sheet->setCellValue('AD2', '-');
        $sheet->setCellValue('AE2', 'Farid Wajidi');
        $sheet->setCellValue('AF2', 'Close');

        // Sample 2: Double Diagnosa
        $sheet->setCellValue('A3', '01/08/2025');
        $sheet->setCellValue('B3', '10:30');
        $sheet->setCellValue('C3', '50172104');
        $sheet->setCellValue('D3', 'Adam Azhari');
        $sheet->setCellValue('E3', '50172104-A');
        $sheet->setCellValue('F3', 'Adam Azhari');
        $sheet->setCellValue('G3', 'ISPA');
        $sheet->setCellValue('H3', 'Batuk, Pilek, Sakit Tenggorokan');
        $sheet->setCellValue('I3', 'Paracetamol');
        $sheet->setCellValue('J3', '10');
        $sheet->setCellValue('K3', 'Methylprednisolone');
        $sheet->setCellValue('L3', '5');
        $sheet->setCellValue('M3', '-');
        $sheet->setCellValue('N3', '-');
        $sheet->setCellValue('O3', 'Demam Berdarah');
        $sheet->setCellValue('P3', 'Pusing, Mual');
        $sheet->setCellValue('Q3', 'Paracetamol');
        $sheet->setCellValue('R3', '10');
        $sheet->setCellValue('S3', '-');
        $sheet->setCellValue('T3', '-');
        $sheet->setCellValue('U3', '-');
        $sheet->setCellValue('V3', '-');
        $sheet->setCellValue('W3', '-');
        $sheet->setCellValue('X3', '-');
        $sheet->setCellValue('Y3', '-');
        $sheet->setCellValue('Z3', '-');
        $sheet->setCellValue('AA3', '-');
        $sheet->setCellValue('AB3', '-');
        $sheet->setCellValue('AC3', '-');
        $sheet->setCellValue('AD3', '-');
        $sheet->setCellValue('AE3', 'Didi Suryadi');
        $sheet->setCellValue('AF3', 'Close');

        // Sample 3: Triple Diagnosa
        $sheet->setCellValue('A4', '01/08/2025');
        $sheet->setCellValue('B4', '14:15');
        $sheet->setCellValue('C4', '1200337');
        $sheet->setCellValue('D4', 'Suparjo');
        $sheet->setCellValue('E4', '1200337-A');
        $sheet->setCellValue('F4', 'Suparjo');
        $sheet->setCellValue('G4', 'Hipertensi');
        $sheet->setCellValue('H4', 'Pusing');
        $sheet->setCellValue('I4', 'Amlodipin 5Mg');
        $sheet->setCellValue('J4', '10');
        $sheet->setCellValue('K4', '-');
        $sheet->setCellValue('L4', '-');
        $sheet->setCellValue('M4', '-');
        $sheet->setCellValue('N4', '-');
        $sheet->setCellValue('O4', 'Diabetes');
        $sheet->setCellValue('P4', 'Lemas');
        $sheet->setCellValue('Q4', 'Metformin');
        $sheet->setCellValue('R4', '10');
        $sheet->setCellValue('S4', '-');
        $sheet->setCellValue('T4', '-');
        $sheet->setCellValue('U4', '-');
        $sheet->setCellValue('V4', '-');
        $sheet->setCellValue('W4', 'Asam Urat');
        $sheet->setCellValue('X4', 'Nyeri Sendi');
        $sheet->setCellValue('Y4', 'Allopurinol');
        $sheet->setCellValue('Z4', '5');
        $sheet->setCellValue('AA4', '-');
        $sheet->setCellValue('AB4', '-');
        $sheet->setCellValue('AC4', '-');
        $sheet->setCellValue('AD4', '-');
        $sheet->setCellValue('AE4', 'Ellien M');
        $sheet->setCellValue('AF4', 'On Progress');

        // Style sample data
        $dataStyle = [
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC'],
                ],
            ],
        ];

        $sheet->getStyle('A2:AF4')->applyFromArray($dataStyle);

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(15);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(10);
        $sheet->getColumnDimension('D')->setWidth(10);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(15);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(25);
        $sheet->getColumnDimension('I')->setWidth(15);
        $sheet->getColumnDimension('J')->setWidth(20);
        $sheet->getColumnDimension('K')->setWidth(15);
        $sheet->getColumnDimension('L')->setWidth(10);
        $sheet->getColumnDimension('M')->setWidth(15);
        $sheet->getColumnDimension('N')->setWidth(10);
        $sheet->getColumnDimension('O')->setWidth(15);
        $sheet->getColumnDimension('P')->setWidth(10);
        $sheet->getColumnDimension('Q')->setWidth(15);
        $sheet->getColumnDimension('R')->setWidth(20);
        $sheet->getColumnDimension('S')->setWidth(15);
        $sheet->getColumnDimension('T')->setWidth(10);
        $sheet->getColumnDimension('U')->setWidth(15);
        $sheet->getColumnDimension('V')->setWidth(10);
        $sheet->getColumnDimension('W')->setWidth(15);
        $sheet->getColumnDimension('X')->setWidth(20);
        $sheet->getColumnDimension('Y')->setWidth(15);
        $sheet->getColumnDimension('Z')->setWidth(10);
        $sheet->getColumnDimension('AA')->setWidth(15);
        $sheet->getColumnDimension('AB')->setWidth(10);
        $sheet->getColumnDimension('AC')->setWidth(15);
        $sheet->getColumnDimension('AD')->setWidth(10);
        $sheet->getColumnDimension('AE')->setWidth(20);
        $sheet->getColumnDimension('AF')->setWidth(15);

        // Set row heights
        $sheet->getRowDimension(1)->setRowHeight(25);
        $sheet->getRowDimension(2)->setRowHeight(20);
        $sheet->getRowDimension(3)->setRowHeight(20);
        $sheet->getRowDimension(4)->setRowHeight(20);

        // Add notes
        $sheet->setCellValue('A6', 'CATATAN:');
        $sheet->setCellValue('A7', '• Format Tanggal: DD/MM/YYYY (contoh: 01/08/2025)');
<<<<<<< HEAD
        $sheet->setCellValue('A8', '• Format Waktu: HH:MM atau HH:MM:SS (contoh: 09:22 atau 09:22:00)');
        $sheet->setCellValue('A9', '• NIK Karyawan harus ada di tabel karyawan');
        $sheet->setCellValue('A10', '• Kode RM format: NIK-KodeHubungan (contoh: 1200929-A)');
        $sheet->setCellValue('A11', '• Nama Pasien sesuai dengan data di tabel keluarga');
        $sheet->setCellValue('A12', '• Diagnosa 1-3: diagnosa penyakit (opsional, isi dengan "-" jika tidak ada)');
        $sheet->setCellValue('A13', '• Keluhan 1-3: keluhan pasien (opsional, isi dengan "-" jika tidak ada)');
        $sheet->setCellValue('A14', '• Obat 1 hingga Obat 3: isi dengan nama obat yang ada di tabel obat, jika tidak ada isi dengan "-"');
        $sheet->setCellValue('A15', '• Qty: jumlah obat, jika tidak ada isi dengan "-"');
        $sheet->setCellValue('A16', '• Petugas Klinik: nama petugas yang melakukan pemeriksaan');
        $sheet->setCellValue('A17', '• Status: "Close", "On Progress", atau "Reguler"');
        $sheet->setCellValue('A18', '• Setiap diagnosa dapat memiliki hingga 3 obat');
        $sheet->setCellValue('A19', '• Untuk diagnosa tunggal: isi hanya Diagnosa 1, Keluhan 1, dan Obat 1-1 hingga Obat 1-3');
        $sheet->setCellValue('A20', '• Untuk diagnosa double: isi Diagnosa 1 & 2 beserta keluhan dan obatnya');
        $sheet->setCellValue('A21', '• Untuk diagnosa triple: isi semua Diagnosa 1, 2, & 3 beserta keluhan dan obatnya');
        $sheet->setCellValue('A22', '• Lihat daftar karyawan, diagnosa, dan obat di sheet referensi');
=======
        $sheet->setCellValue('A8', '• Format Waktu: HH:MM (contoh: 09:22)');
        $sheet->setCellValue('A9', '• Shift: Pagi, Siang, atau Malam');
        $sheet->setCellValue('A10', '• NIK Karyawan harus ada di tabel karyawan');
        $sheet->setCellValue('A11', '• Kode RM format: NIK-KodeHubungan (contoh: 1200929-A)');
        $sheet->setCellValue('A12', '• Nama Pasien sesuai dengan data di tabel keluarga');
        $sheet->setCellValue('A13', '• Diagnosa 1-3: diagnosa penyakit (opsional, isi dengan "-" jika tidak ada)');
        $sheet->setCellValue('A14', '• Keluhan 1-3: keluhan pasien (opsional, isi dengan "-" jika tidak ada)');
        $sheet->setCellValue('A15', '• Obat 1-1 hingga Obat 3-2: isi dengan nama obat yang ada di tabel obat, jika tidak ada isi dengan "-"');
        $sheet->setCellValue('A16', '• Qyt: jumlah obat, jika tidak ada isi dengan "-"');
        $sheet->setCellValue('A17', '• Petugas: nama petugas yang melakukan pemeriksaan');
        $sheet->setCellValue('A18', '• Status: "Close", "On Progress", atau "Reguler"');
        $sheet->setCellValue('A19', '• Setiap diagnosa dapat memiliki hingga 3 obat');
        $sheet->setCellValue('A20', '• Lihat daftar karyawan, diagnosa, dan obat di sheet referensi');
>>>>>>> f4794cd429d33da2afdec023af14015e3c34f646

        $sheet->getStyle('A6')->getFont()->setBold(true);
        $sheet->getStyle('A7:A17')->getFont()->setItalic(true)->setSize(10);

        // ===== CREATE SECOND SHEET FOR REFERENCE =====
        $referenceSheet = $spreadsheet->createSheet();
        $referenceSheet->setTitle('Referensi');

        // Get reference data
        $karyawans = Karyawan::orderBy('nik_karyawan')->limit(20)->get();
        $diagnosas = Diagnosa::orderBy('nama_diagnosa')->get();
        $obats = Obat::orderBy('nama_obat')->get();

        // Karyawan section
        $referenceSheet->setCellValue('A1', 'DAFTAR KARYAWAN');
        $referenceSheet->mergeCells('A1:C1');
        $referenceSheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $referenceSheet->getStyle('A1:C1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E0F2FE');

        $referenceSheet->setCellValue('A2', 'NIK');
        $referenceSheet->setCellValue('B2', 'Nama Karyawan');
        $referenceSheet->setCellValue('C2', 'Departemen');

        $headerRefStyle = [
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '059669'],
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ];
        $referenceSheet->getStyle('A2:C2')->applyFromArray($headerRefStyle);

        $row = 3;
        foreach ($karyawans as $karyawan) {
            $referenceSheet->setCellValue('A'.$row, $karyawan->nik_karyawan);
            $referenceSheet->setCellValue('B'.$row, $karyawan->nama_karyawan);
            $referenceSheet->setCellValue('C'.$row, $karyawan->departemen->nama_departemen ?? '');
            $row++;
        }

        // Diagnosa section
        $referenceSheet->setCellValue('E1', 'DAFTAR DIAGNOSA');
        $referenceSheet->mergeCells('E1:F1');
        $referenceSheet->getStyle('E1')->getFont()->setBold(true)->setSize(14);
        $referenceSheet->getStyle('E1:F1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FEF3C7');

        $referenceSheet->setCellValue('E2', 'ID Diagnosa');
        $referenceSheet->setCellValue('F2', 'Nama Diagnosa');
        $referenceSheet->getStyle('E2:F2')->applyFromArray($headerRefStyle);

        $row = 3;
        foreach ($diagnosas as $diagnosa) {
            $referenceSheet->setCellValue('E'.$row, $diagnosa->id_diagnosa);
            $referenceSheet->setCellValue('F'.$row, $diagnosa->nama_diagnosa);
            $row++;
        }

        // Obat section
        $referenceSheet->setCellValue('H1', 'DAFTAR OBAT');
        $referenceSheet->mergeCells('H1:I1');
        $referenceSheet->getStyle('H1')->getFont()->setBold(true)->setSize(14);
        $referenceSheet->getStyle('H1:I1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F3E8FF');

        $referenceSheet->setCellValue('H2', 'ID Obat');
        $referenceSheet->setCellValue('I2', 'Nama Obat');
        $referenceSheet->getStyle('H2:I2')->applyFromArray($headerRefStyle);

        $row = 3;
        foreach ($obats as $obat) {
            $referenceSheet->setCellValue('H'.$row, $obat->id_obat);
            $referenceSheet->setCellValue('I'.$row, $obat->nama_obat);
            $row++;
        }

        // Set column widths for reference sheet
        $referenceSheet->getColumnDimension('A')->setWidth(15);
        $referenceSheet->getColumnDimension('B')->setWidth(25);
        $referenceSheet->getColumnDimension('C')->setWidth(20);
        $referenceSheet->getColumnDimension('E')->setWidth(15);
        $referenceSheet->getColumnDimension('F')->setWidth(25);
        $referenceSheet->getColumnDimension('H')->setWidth(15);
        $referenceSheet->getColumnDimension('I')->setWidth(25);

        // Set active sheet back to first sheet
        $spreadsheet->setActiveSheetIndex(0);

        // Create Excel file
        $writer = new Xlsx($spreadsheet);
<<<<<<< HEAD
        $filename = 'TEMPLATE_REKAM_MEDIS_' . date('Y-m-d') . '.xlsx';
=======
        $filename = 'template_rekam_medis_'.date('Y-m-d').'.xlsx';
>>>>>>> f4794cd429d33da2afdec023af14015e3c34f646

        // Set headers for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    /**
     * Helper function to read cell value with support for different Excel data types
     */
    private function getCellValue($sheet, $column, $row, $dataType = 'string')
    {
        $cell = $sheet->getCell($column.$row);
        $value = null;

        try {
            switch ($dataType) {
                case 'date':
                    if (\PhpOffice\PhpSpreadsheet\Shared\Date::isDateTime($cell)) {
                        $value = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($cell->getValue())->format('Y-m-d');
                    } else {
                        $rawValue = trim($cell->getValue() ?? '');
                        if (! empty($rawValue)) {
                            // Handle DD/MM/YYYY or DD-MM-YYYY format
                            if (preg_match('/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/', $rawValue, $matches)) {
                                $day = $matches[1];
                                $month = $matches[2];
                                $year = $matches[3];
                                $value = $year.'-'.str_pad($month, 2, '0', STR_PAD_LEFT).'-'.str_pad($day, 2, '0', STR_PAD_LEFT);
                            }
                            // Handle Excel serial date format
                            elseif (is_numeric($rawValue)) {
                                $excelDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($rawValue);
                                $value = $excelDate->format('Y-m-d');
                            }
                        }
                    }
                    break;

                case 'time':
                    $excelValue = $cell->getValue();
                    $formattedValue = $cell->getFormattedValue();

                    // Method 1: Check if it's a datetime object (for Excel time format)
                    if (\PhpOffice\PhpSpreadsheet\Shared\Date::isDateTime($cell)) {
                        // Excel stores time as a fraction of a day (e.g., 0.395833 for 09:30)
                        $timeObject = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($excelValue);
                        $value = $timeObject->format('H:i');
                    }
                    // Method 2: Check if it's a numeric value (for raw Excel time format)
                    elseif (is_numeric($excelValue)) {
                        // Excel menyimpan waktu sebagai pecahan hari (misal 0.572916 = 13:45)
                        $seconds = (float) $excelValue * 24 * 60 * 60;
                        $value = gmdate('H:i', $seconds);
                    }
                    // Method 3: Handle text format time (e.g., "09:15:00", "9.30", "09:30")
                    else {
                        // Try to get the formatted value first
                        $rawTime = trim((string) $formattedValue ?? '');

                        // If formatted value is empty, try the raw value
                        if (empty($rawTime)) {
                            $rawTime = trim((string) $excelValue ?? '');
                        }

                        // Replace various separators with colon
                        $rawTime = str_replace(['.', ',', ' '], ':', $rawTime);

                        if (! empty($rawTime)) {
                            $timeObject = \Carbon\Carbon::parse($rawTime);
                            $value = $timeObject->format('H:i');
                        }
                    }
                    break;

                case 'number':
                    $rawValue = $cell->getValue();
                    if (is_numeric($rawValue)) {
                        $value = $rawValue;
                    } else {
                        $formattedValue = $cell->getFormattedValue();
                        $value = is_numeric($formattedValue) ? $formattedValue : null;
                    }
                    break;

                case 'string':
                default:
                    // Try to get the formatted value first
                    $value = trim((string) $cell->getFormattedValue() ?? '');

                    // If formatted value is empty, try the raw value
                    if (empty($value)) {
                        $value = trim((string) $cell->getValue() ?? '');
                    }
                    break;
            }
        } catch (\Exception $e) {
            Log::error("Error reading cell {$column}{$row}: ".$e->getMessage());
            $value = null;
        }

        return $value;
    }

    /**
     * Import data rekam medis dari Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls'],
        ], [
            'file.required' => 'File harus dipilih',
            'file.mimes' => 'File harus berformat Excel (.xlsx atau .xls)',
        ]);

        try {
            $file = $request->file('file');

            // Configure reader to preserve date formats
            $reader = IOFactory::createReaderForFile($file->getRealPath());

            // If it's an Excel file, configure date handling
            if (method_exists($reader, 'setReadDataOnly')) {
                $reader->setReadDataOnly(false);
            }

            $spreadsheet = $reader->load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();

            // Get highest row number
            $highestRow = $sheet->getHighestRow();

            // Skip header row, start from row 2
            $created = 0;
            $errors = [];

            // Detect Excel format by checking the headers
            $highestColumn = $sheet->getHighestColumn();
            $isMultiDiagnosaFormat = false;

            // Check if the file has the multi-diagnosa format (up to column AD)
            if ($highestColumn >= 'AD') {
                // Check if the headers match the multi-diagnosa format
                $headerG = $this->getCellValue($sheet, 'G', 1, 'string');
                $headerO = $this->getCellValue($sheet, 'O', 1, 'string');
                $headerW = $this->getCellValue($sheet, 'W', 1, 'string');

                // If headers contain "Diagnosa 1", "Diagnosa 2", "Diagnosa 3", it's multi-diagnosa format
                if (stripos($headerG, 'diagnosa') !== false &&
                    stripos($headerO, 'diagnosa') !== false &&
                    stripos($headerW, 'diagnosa') !== false) {
                    $isMultiDiagnosaFormat = true;
                }
            }

            // Debug: Log the detected format
            Log::info('Detected Excel format: '.($isMultiDiagnosaFormat ? 'Multiple Diagnoses' : 'Single Diagnosa'));

            for ($rowNumber = 2; $rowNumber <= $highestRow; $rowNumber++) {
                if ($isMultiDiagnosaFormat) {
                    // Process multi-diagnosa format
                    // A: Hari / Tgl
                    // B: Time
                    // C: NIK
                    // D: Nama Karyawan
                    // E: Kode RM
                    // F: Nama Pasien
                    // G: Diagnosa 1
                    // H: Keluhan 1
                    // I: Obat 1-1
                    // J: Qyt
                    // K: Obat 1-2
                    // L: Qyt
                    // M: Obat 1-3
                    // N: Qyt
                    // O: Diagnosa 2
                    // P: Keluhan 2
                    // Q: Obat 2-1
                    // R: Qyt
                    // S: Obat 2-2
                    // T: Qyt
                    // U: Obat 2-3
                    // V: QTY
                    // W: Diagnosa 3
                    // X: Keluhan 3
                    // Y: Obat 3-1
                    // Z: Qyt
                    // AA: Obat 3-2
                    // AB: Qyt
                    // AC: Obat 3-3
                    // AD: Qyt
                    // AE: Petugas
                    // AF: Status
                    
                    // Read all columns using the helper function
                    $tanggalPeriksa = $this->getCellValue($sheet, 'A', $rowNumber, 'date');
                    $waktuPeriksa = $this->getCellValue($sheet, 'B', $rowNumber, 'time');
                    $nikKaryawan = $this->getCellValue($sheet, 'C', $rowNumber, 'string');
                    $namaKaryawan = $this->getCellValue($sheet, 'D', $rowNumber, 'string');
                    $kodeRM = $this->getCellValue($sheet, 'E', $rowNumber, 'string');
                    $namaPasien = $this->getCellValue($sheet, 'F', $rowNumber, 'string');
                    
                    // Diagnosa 1 data
                    $diagnosa1 = $this->getCellValue($sheet, 'G', $rowNumber, 'string');
                    $keluhan1 = $this->getCellValue($sheet, 'H', $rowNumber, 'string');
                    $obat1_1 = $this->getCellValue($sheet, 'I', $rowNumber, 'string');
                    $jumlahObat1_1 = $this->getCellValue($sheet, 'J', $rowNumber, 'number');
                    $obat1_2 = $this->getCellValue($sheet, 'K', $rowNumber, 'string');
                    $jumlahObat1_2 = $this->getCellValue($sheet, 'L', $rowNumber, 'number');
                    $obat1_3 = $this->getCellValue($sheet, 'M', $rowNumber, 'string');
                    $jumlahObat1_3 = $this->getCellValue($sheet, 'N', $rowNumber, 'number');
                    
                    // Diagnosa 2 data
                    $diagnosa2 = $this->getCellValue($sheet, 'O', $rowNumber, 'string');
                    $keluhan2 = $this->getCellValue($sheet, 'P', $rowNumber, 'string');
                    $obat2_1 = $this->getCellValue($sheet, 'Q', $rowNumber, 'string');
                    $jumlahObat2_1 = $this->getCellValue($sheet, 'R', $rowNumber, 'number');
                    $obat2_2 = $this->getCellValue($sheet, 'S', $rowNumber, 'string');
                    $jumlahObat2_2 = $this->getCellValue($sheet, 'T', $rowNumber, 'number');
                    $obat2_3 = $this->getCellValue($sheet, 'U', $rowNumber, 'string');
                    $jumlahObat2_3 = $this->getCellValue($sheet, 'V', $rowNumber, 'number');
                    
                    // Diagnosa 3 data
                    $diagnosa3 = $this->getCellValue($sheet, 'W', $rowNumber, 'string');
                    $keluhan3 = $this->getCellValue($sheet, 'X', $rowNumber, 'string');
                    $obat3_1 = $this->getCellValue($sheet, 'Y', $rowNumber, 'string');
                    $jumlahObat3_1 = $this->getCellValue($sheet, 'Z', $rowNumber, 'number');
                    $obat3_2 = $this->getCellValue($sheet, 'AA', $rowNumber, 'string');
                    $jumlahObat3_2 = $this->getCellValue($sheet, 'AB', $rowNumber, 'number');
                    $obat3_3 = $this->getCellValue($sheet, 'AC', $rowNumber, 'string');
                    $jumlahObat3_3 = $this->getCellValue($sheet, 'AD', $rowNumber, 'number');
                    
                    $petugasKlinik = $this->getCellValue($sheet, 'AE', $rowNumber, 'string');
                    $status = $this->getCellValue($sheet, 'AF', $rowNumber, 'string');

                    // Debug: Log the values
                    Log::info("Row {$rowNumber}: Tanggal={$tanggalPeriksa}, Waktu={$waktuPeriksa}, NIK={$nikKaryawan}, Nama={$namaPasien}");
                } else {
                    // Process single-diagnosa format
                    // A: Hari / Tgl
                    // B: Time
                    // C: NIK
                    // D: Nama Karyawan
                    // E: Kode RM
                    // F: Nama Pasien
                    // G: Diagnosa 1
                    // H: Keluhan 1
                    // I: Obat 1-1
                    // J: Qyt
                    // K: Obat 1-2
                    // L: Qyt
                    // M: Obat 1-3
                    // N: Qyt
                    // O: Diagnosa 2
                    // P: Keluhan 2
                    // Q: Obat 2-1
                    // R: Qyt
                    // S: Obat 2-2
                    // T: Qyt
                    // U: Obat 2-3
                    // V: QTY
                    // W: Diagnosa 3
                    // X: Keluhan 3
                    // Y: Obat 3-1
                    // Z: Qyt
                    // AA: Obat 3-2
                    // AB: Qyt
                    // AC: Obat 3-3
                    // AD: Qyt
                    // AE: Petugas
                    // AF: Status
                    
                    // Read all columns using the helper function
                    $tanggalPeriksa = $this->getCellValue($sheet, 'A', $rowNumber, 'date');
                    $waktuPeriksa = $this->getCellValue($sheet, 'B', $rowNumber, 'time');
                    $nikKaryawan = $this->getCellValue($sheet, 'C', $rowNumber, 'string');
                    $namaKaryawan = $this->getCellValue($sheet, 'D', $rowNumber, 'string');
                    $kodeRM = $this->getCellValue($sheet, 'E', $rowNumber, 'string');
                    $namaPasien = $this->getCellValue($sheet, 'F', $rowNumber, 'string');
                    
                    // Diagnosa 1 data
                    $diagnosa1 = $this->getCellValue($sheet, 'G', $rowNumber, 'string');
                    $keluhan1 = $this->getCellValue($sheet, 'H', $rowNumber, 'string');
                    $obat1_1 = $this->getCellValue($sheet, 'I', $rowNumber, 'string');
                    $jumlahObat1_1 = $this->getCellValue($sheet, 'J', $rowNumber, 'number');
                    $obat1_2 = $this->getCellValue($sheet, 'K', $rowNumber, 'string');
                    $jumlahObat1_2 = $this->getCellValue($sheet, 'L', $rowNumber, 'number');
                    $obat1_3 = $this->getCellValue($sheet, 'M', $rowNumber, 'string');
                    $jumlahObat1_3 = $this->getCellValue($sheet, 'N', $rowNumber, 'number');
                    
                    // Diagnosa 2 data
                    $diagnosa2 = $this->getCellValue($sheet, 'O', $rowNumber, 'string');
                    $keluhan2 = $this->getCellValue($sheet, 'P', $rowNumber, 'string');
                    $obat2_1 = $this->getCellValue($sheet, 'Q', $rowNumber, 'string');
                    $jumlahObat2_1 = $this->getCellValue($sheet, 'R', $rowNumber, 'number');
                    $obat2_2 = $this->getCellValue($sheet, 'S', $rowNumber, 'string');
                    $jumlahObat2_2 = $this->getCellValue($sheet, 'T', $rowNumber, 'number');
                    $obat2_3 = $this->getCellValue($sheet, 'U', $rowNumber, 'string');
                    $jumlahObat2_3 = $this->getCellValue($sheet, 'V', $rowNumber, 'number');
                    
                    // Diagnosa 3 data
                    $diagnosa3 = $this->getCellValue($sheet, 'W', $rowNumber, 'string');
                    $keluhan3 = $this->getCellValue($sheet, 'X', $rowNumber, 'string');
                    $obat3_1 = $this->getCellValue($sheet, 'Y', $rowNumber, 'string');
                    $jumlahObat3_1 = $this->getCellValue($sheet, 'Z', $rowNumber, 'number');
                    $obat3_2 = $this->getCellValue($sheet, 'AA', $rowNumber, 'string');
                    $jumlahObat3_2 = $this->getCellValue($sheet, 'AB', $rowNumber, 'number');
                    $obat3_3 = $this->getCellValue($sheet, 'AC', $rowNumber, 'string');
                    $jumlahObat3_3 = $this->getCellValue($sheet, 'AD', $rowNumber, 'number');
                    
                    $petugasKlinik = $this->getCellValue($sheet, 'AE', $rowNumber, 'string');
                    $status = $this->getCellValue($sheet, 'AF', $rowNumber, 'string');

                    // Debug: Log the values
                    Log::info("Row {$rowNumber}: Tanggal={$tanggalPeriksa}, Waktu={$waktuPeriksa}, NIK={$nikKaryawan}, Nama={$namaPasien}");
                }

                // Skip empty rows
                if (empty($tanggalPeriksa) && empty($nikKaryawan)) {
                    continue;
                }

                // Validate required fields
                if (empty($tanggalPeriksa)) {
                    $errors[] = "Baris $rowNumber: Tanggal periksa tidak boleh kosong";

                    continue;
                }

                if (empty($nikKaryawan)) {
                    $errors[] = "Baris $rowNumber: NIK karyawan tidak boleh kosong";

                    continue;
                }

                if (empty($namaPasien)) {
                    $errors[] = "Baris $rowNumber: Nama pasien tidak boleh kosong";

                    continue;
                }

                if (empty($petugasKlinik)) {
                    $errors[] = "Baris $rowNumber: Petugas klinik tidak boleh kosong";

                    continue;
                }

                // Convert tanggal format to YYYY-MM-DD
                if (! empty($tanggalPeriksa)) {
                    // Remove any extra spaces
                    $tanggalPeriksa = trim($tanggalPeriksa);

                    // Handle Excel serial date format (e.g., 45870)
                    if (is_numeric($tanggalPeriksa)) {
                        // Excel serial date conversion
                        $excelDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($tanggalPeriksa);
                        $tanggalPeriksa = $excelDate->format('Y-m-d');
                    }
                    // Check if the format is DD/MM/YYYY
                    elseif (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $tanggalPeriksa, $matches)) {
                        $day = $matches[1];
                        $month = $matches[2];
                        $year = $matches[3];

                        // Validate date
                        if (! checkdate($month, $day, $year)) {
                            $errors[] = "Baris $rowNumber: Tanggal '$tanggalPeriksa' tidak valid. Gunakan format DD/MM/YYYY";

                            continue;
                        }

                        // Convert to YYYY-MM-DD format
                        $tanggalPeriksa = $year.'-'.str_pad($month, 2, '0', STR_PAD_LEFT).'-'.str_pad($day, 2, '0', STR_PAD_LEFT);
                    }
                    // Check if the format is YYYY-MM-DD (already in database format)
                    elseif (preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2})$/', $tanggalPeriksa, $matches)) {
                        $year = $matches[1];
                        $month = $matches[2];
                        $day = $matches[3];

                        // Validate date
                        if (! checkdate($month, $day, $year)) {
                            $errors[] = "Baris $rowNumber: Tanggal '$tanggalPeriksa' tidak valid";

                            continue;
                        }

                        // Already in correct format, just ensure proper padding
                        $tanggalPeriksa = $year.'-'.str_pad($month, 2, '0', STR_PAD_LEFT).'-'.str_pad($day, 2, '0', STR_PAD_LEFT);
                    }
                    // Check if the format is D/M/YYYY (with single digit day/month)
                    elseif (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $tanggalPeriksa, $matches)) {
                        $day = $matches[1];
                        $month = $matches[2];
                        $year = $matches[3];

                        // Validate date
                        if (! checkdate($month, $day, $year)) {
                            $errors[] = "Baris $rowNumber: Tanggal '$tanggalPeriksa' tidak valid. Gunakan format DD/MM/YYYY";

                            continue;
                        }

                        // Convert to YYYY-MM-DD format
                        $tanggalPeriksa = $year.'-'.str_pad($month, 2, '0', STR_PAD_LEFT).'-'.str_pad($day, 2, '0', STR_PAD_LEFT);
                    } else {
                        $errors[] = "Baris $rowNumber: Format tanggal '$tanggalPeriksa' tidak valid. Gunakan format DD/MM/YYYY";

                        continue;
                    }
                }

                // Validate status
                if (! in_array($status, ['Close', 'On Progress', 'Reguler'])) {
                    $errors[] = "Baris $rowNumber: Status harus 'Close', 'On Progress', atau 'Reguler'";

                    continue;
                }

                // Convert status to database format
                if ($status === 'Reguler') {
                    $status = 'Close'; // Convert 'Reguler' to 'Close' for database
                }

                // Find karyawan
                $karyawan = Karyawan::where('nik_karyawan', $nikKaryawan)->first();
                if (! $karyawan) {
                    $errors[] = "Baris $rowNumber: Karyawan dengan NIK $nikKaryawan tidak ditemukan";

                    continue;
                }

                // Find user based on petugas klinik name
                $user = User::where('nama_lengkap', $petugasKlinik)->first();
                if (! $user) {
                    $errors[] = "Baris $rowNumber: User dengan nama '$petugasKlinik' tidak ditemukan";

                    continue;
                }

                // Find keluarga
                $keluarga = Keluarga::where('id_karyawan', $karyawan->id_karyawan)
                    ->where('nama_keluarga', $namaPasien)
                    ->first();
                if (! $keluarga) {
                    $errors[] = "Baris $rowNumber: Pasien $namaPasien tidak ditemukan untuk karyawan $nikKaryawan";

                    continue;
                }

                // Create rekam medis
                $rekamMedis = RekamMedis::create([
                    'id_keluarga' => $keluarga->id_keluarga,
                    'tanggal_periksa' => $tanggalPeriksa,
                    'waktu_periksa' => $waktuPeriksa,
                    'id_user' => $user->id_user, // Use the user found from petugas klinik
                    'jumlah_keluhan' => 0, // Will be updated later
                    'status' => $status,
                ]);

                // Debug: Log the waktu_periksa value
                Log::info('Row '.$rowNumber.': waktu_periksa = '.$waktuPeriksa);

                // Process data based on format
                $keluhanCount = 0;

                if ($isMultiDiagnosaFormat) {
                    // Process multiple diagnoses format

                    // Process Diagnosa 1
                    if (! empty($diagnosa1) && $diagnosa1 !== '-') {
                        $diagnosa1Model = Diagnosa::firstOrCreate(['nama_diagnosa' => $diagnosa1]);
                        $idDiagnosa1 = $diagnosa1Model->id_diagnosa;

                        // Create keluhan entries for each obat in Diagnosa 1
                        $obatList1 = [
                            ['nama' => $obat1_1, 'jumlah' => $jumlahObat1_1],
                            ['nama' => $obat1_2, 'jumlah' => $jumlahObat1_2],
                            ['nama' => $obat1_3, 'jumlah' => $jumlahObat1_3],
                        ];

                        $hasObat = false;
                        foreach ($obatList1 as $obatData) {
                            if (! empty($obatData['nama']) && $obatData['nama'] !== '-') {
                                $obatModel = Obat::where('nama_obat', $obatData['nama'])->first();
                                if ($obatModel) {
                                    Keluhan::create([
                                        'id_rekam' => $rekamMedis->id_rekam,
                                        'id_keluarga' => $keluarga->id_keluarga,
                                        'id_diagnosa' => $idDiagnosa1,
                                        'terapi' => 'Obat',
                                        'keterangan' => $keluhan1,
                                        'id_obat' => $obatModel->id_obat,
                                        'jumlah_obat' => is_numeric($obatData['jumlah']) ? $obatData['jumlah'] : null,
                                        'aturan_pakai' => null,
                                    ]);
                                    $keluhanCount++;
                                    $hasObat = true;
                                } else {
                                    $errors[] = "Baris $rowNumber: Obat '{$obatData['nama']}' tidak ditemukan";
                                }
                            }
                        }

                        // If no obat found but there's diagnosa, create keluhan without obat
                        if (! $hasObat) {
                            Keluhan::create([
                                'id_rekam' => $rekamMedis->id_rekam,
                                'id_keluarga' => $keluarga->id_keluarga,
                                'id_diagnosa' => $idDiagnosa1,
                                'terapi' => 'Istirahat',
                                'keterangan' => $keluhan1,
                                'id_obat' => null,
                                'jumlah_obat' => null,
                                'aturan_pakai' => null,
                            ]);
                            $keluhanCount++;
                        }
                    }

                    // Process Diagnosa 2
                    if (! empty($diagnosa2) && $diagnosa2 !== '-') {
                        $diagnosa2Model = Diagnosa::firstOrCreate(['nama_diagnosa' => $diagnosa2]);
                        $idDiagnosa2 = $diagnosa2Model->id_diagnosa;

                        // Create keluhan entries for each obat in Diagnosa 2
                        $obatList2 = [
                            ['nama' => $obat2_1, 'jumlah' => $jumlahObat2_1],
                            ['nama' => $obat2_2, 'jumlah' => $jumlahObat2_2],
                        ];

                        $hasObat = false;
                        foreach ($obatList2 as $obatData) {
                            if (! empty($obatData['nama']) && $obatData['nama'] !== '-') {
                                $obatModel = Obat::where('nama_obat', $obatData['nama'])->first();
                                if ($obatModel) {
                                    Keluhan::create([
                                        'id_rekam' => $rekamMedis->id_rekam,
                                        'id_keluarga' => $keluarga->id_keluarga,
                                        'id_diagnosa' => $idDiagnosa2,
                                        'terapi' => 'Obat',
                                        'keterangan' => $keluhan2,
                                        'id_obat' => $obatModel->id_obat,
                                        'jumlah_obat' => is_numeric($obatData['jumlah']) ? $obatData['jumlah'] : null,
                                        'aturan_pakai' => null,
                                    ]);
                                    $keluhanCount++;
                                    $hasObat = true;
                                } else {
                                    $errors[] = "Baris $rowNumber: Obat '{$obatData['nama']}' tidak ditemukan";
                                }
                            }
                        }

                        // If no obat found but there's diagnosa, create keluhan without obat
                        if (! $hasObat) {
                            Keluhan::create([
                                'id_rekam' => $rekamMedis->id_rekam,
                                'id_keluarga' => $keluarga->id_keluarga,
                                'id_diagnosa' => $idDiagnosa2,
                                'terapi' => 'Istirahat',
                                'keterangan' => $keluhan2,
                                'id_obat' => null,
                                'jumlah_obat' => null,
                                'aturan_pakai' => null,
                            ]);
                            $keluhanCount++;
                        }
                    }

                    // Process Diagnosa 3
                    if (! empty($diagnosa3) && $diagnosa3 !== '-') {
                        $diagnosa3Model = Diagnosa::firstOrCreate(['nama_diagnosa' => $diagnosa3]);
                        $idDiagnosa3 = $diagnosa3Model->id_diagnosa;

                        // Create keluhan entries for each obat in Diagnosa 3
                        $obatList3 = [
                            ['nama' => $obat3_1, 'jumlah' => $jumlahObat3_1],
                            ['nama' => $obat3_2, 'jumlah' => $jumlahObat3_2],
                        ];

                        $hasObat = false;
                        foreach ($obatList3 as $obatData) {
                            if (! empty($obatData['nama']) && $obatData['nama'] !== '-') {
                                $obatModel = Obat::where('nama_obat', $obatData['nama'])->first();
                                if ($obatModel) {
                                    Keluhan::create([
                                        'id_rekam' => $rekamMedis->id_rekam,
                                        'id_keluarga' => $keluarga->id_keluarga,
                                        'id_diagnosa' => $idDiagnosa3,
                                        'terapi' => 'Obat',
                                        'keterangan' => $keluhan3,
                                        'id_obat' => $obatModel->id_obat,
                                        'jumlah_obat' => is_numeric($obatData['jumlah']) ? $obatData['jumlah'] : null,
                                        'aturan_pakai' => null,
                                    ]);
                                    $keluhanCount++;
                                    $hasObat = true;
                                } else {
                                    $errors[] = "Baris $rowNumber: Obat '{$obatData['nama']}' tidak ditemukan";
                                }
                            }
                        }

                        // If no obat found but there's diagnosa, create keluhan without obat
                        if (! $hasObat) {
                            Keluhan::create([
                                'id_rekam' => $rekamMedis->id_rekam,
                                'id_keluarga' => $keluarga->id_keluarga,
                                'id_diagnosa' => $idDiagnosa3,
                                'terapi' => 'Istirahat',
                                'keterangan' => $keluhan3,
                                'id_obat' => null,
                                'jumlah_obat' => null,
                                'aturan_pakai' => null,
                            ]);
                            $keluhanCount++;
                        }
                    }
                } else {
                    // Process single-diagnosa format (using only Diagnosa 1 columns)
                    
                    // Find or create diagnosa
                    $idDiagnosa = null;
<<<<<<< HEAD
                    if (!empty($diagnosa1) && $diagnosa1 !== '-') {
                        $diagnosaModel = Diagnosa::firstOrCreate(['nama_diagnosa' => $diagnosa1]);
=======
                    if (! empty($diagnosa) && $diagnosa !== '-') {
                        $diagnosaModel = Diagnosa::firstOrCreate(['nama_diagnosa' => $diagnosa]);
>>>>>>> f4794cd429d33da2afdec023af14015e3c34f646
                        $idDiagnosa = $diagnosaModel->id_diagnosa;
                    }

                    // Create keluhan entries for each obat from Diagnosa 1 only
                    $obatList = [
                        ['nama' => $obat1_1, 'jumlah' => $jumlahObat1_1],
                        ['nama' => $obat1_2, 'jumlah' => $jumlahObat1_2],
                        ['nama' => $obat1_3, 'jumlah' => $jumlahObat1_3],
                    ];

                    $hasObat = false;
                    foreach ($obatList as $obatData) {
                        if (! empty($obatData['nama']) && $obatData['nama'] !== '-') {
                            $obatModel = Obat::where('nama_obat', $obatData['nama'])->first();
                            if ($obatModel) {
                                Keluhan::create([
                                    'id_rekam' => $rekamMedis->id_rekam,
                                    'id_keluarga' => $keluarga->id_keluarga,
                                    'id_diagnosa' => $idDiagnosa,
                                    'terapi' => 'Obat',
                                    'keterangan' => $keluhan1,
                                    'id_obat' => $obatModel->id_obat,
                                    'jumlah_obat' => is_numeric($obatData['jumlah']) ? $obatData['jumlah'] : null,
                                    'aturan_pakai' => null,
                                ]);
                                $keluhanCount++;
                                $hasObat = true;
                            } else {
                                $errors[] = "Baris $rowNumber: Obat '{$obatData['nama']}' tidak ditemukan";
                            }
                        }
                    }

                    // If no obat found but there's diagnosa, create keluhan without obat
                    if (!$hasObat && $idDiagnosa) {
                        Keluhan::create([
                            'id_rekam' => $rekamMedis->id_rekam,
                            'id_keluarga' => $keluarga->id_keluarga,
                            'id_diagnosa' => $idDiagnosa,
                            'terapi' => 'Istirahat',
                            'keterangan' => $keluhan1,
                            'id_obat' => null,
                            'jumlah_obat' => null,
                            'aturan_pakai' => null,
                        ]);
                        $keluhanCount = 1;
                    }
                }

                // Update jumlah_keluhan
                $rekamMedis->update(['jumlah_keluhan' => $keluhanCount]);

                $created++;
            }

            $message = "Import selesai: $created data rekam medis berhasil ditambahkan";
            $hasErrors = count($errors) > 0;

            if ($hasErrors) {
                $errorMessage = implode('<br>', array_slice($errors, 0, 10)); // Show first 10 errors
                if (count($errors) > 10) {
                    $errorMessage .= '<br>... dan '.(count($errors) - 10).' error lainnya';
                }
                $message .= '<br><br>Error:<br>'.$errorMessage;
            }

            // Return JSON response for AJAX requests
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'data' => [
                        'created' => $created,
                        'errors' => $errors,
                    ],
                ]);
            }

            if ($hasErrors) {
                return back()->with('warning', $message);
            }

            return back()->with('success', $message);

        } catch (\Exception $e) {
            // Return JSON response for AJAX requests
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal import data: '.$e->getMessage(),
                ], 500);
            }

            return back()->with('error', 'Gagal import data: '.$e->getMessage());
        }
    }
}
