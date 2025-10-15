<?php

namespace App\Http\Controllers;

use App\Models\RekamMedis;
use App\Models\Keluarga;
use App\Models\Karyawan;
use App\Models\Keluhan;
use App\Models\Diagnosa;
use App\Models\Obat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class RekamMedisController extends Controller
{
    public function index(Request $request)
    {
        $query = RekamMedis::with([
            'keluarga.karyawan:id_karyawan,nik_karyawan,nama_karyawan',
            'keluarga.hubungan:kode_hubungan,hubungan',
            'user:id_user,username,nama_lengkap',
            'keluhans.diagnosa:id_diagnosa,nama_diagnosa',
            'keluhans.obat:id_obat,nama_obat,harga_per_satuan'
        ]);

        // Filter pencarian
        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function ($sub) use ($q) {
                $sub->whereHas('keluarga', function($keluarga) use ($q) {
                    $keluarga->where('nama_keluarga', 'like', "%$q%")
                            ->orWhere('no_rm', 'like', "%$q%")
                            ->orWhere('bpjs_id', 'like', "%$q%")
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

        // Using Laravel 12's transaction method with automatic retry for better reliability
        try {
            $rekamMedis = \Illuminate\Support\Facades\DB::transaction(function () use ($validated, $request) {
                // Simpan data rekam medis
                $rekamMedis = RekamMedis::create([
                    'id_keluarga' => $validated['id_keluarga'],
                    'tanggal_periksa' => $validated['tanggal_periksa'],
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
                                    'jumlah_obat' => $obatData['jumlah_obat'] ?? null,
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
                                'jumlah_obat' => null,
                                'aturan_pakai' => null,
                            ]);
                        }
                    }
                }

                return $rekamMedis;
            }, 3); // Retry up to 3 times on deadlock

            return redirect()->route('rekam-medis.index')->with('success', 'Data rekam medis berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $rekamMedis = RekamMedis::with([
            'keluarga.karyawan:id_karyawan,nik_karyawan,nama_karyawan',
            'keluarga.hubungan:kode_hubungan,hubungan',
            'user:id_user,username,nama_lengkap',
            'keluhans.diagnosa:id_diagnosa,nama_diagnosa',
            'keluhans.obat:id_obat,nama_obat,harga_per_satuan'
        ])->findOrFail($id);

        // Optimized query for riwayat kunjungan - select only needed columns
        $riwayatKunjungan = RekamMedis::with([
            'user:id_user,username,nama_lengkap',
            'keluhans.diagnosa:id_diagnosa,nama_diagnosa',
            'keluhans.obat:id_obat,nama_obat,harga_per_satuan'
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
            'keluhans.diagnosa',   // relasi diagnosa di tabel keluhans
            'keluhans.obat'        // relasi obat (pivot diagnosa_obat)
        ])->findOrFail($id);

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

        // Using Laravel 12's transaction method with automatic retry for better reliability
        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($rekamMedis, $validated, $request) {
                // Update data rekam medis
                $rekamMedis->update([
                    'id_keluarga' => $validated['id_keluarga'],
                    'tanggal_periksa' => $validated['tanggal_periksa'],
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
                                    'jumlah_obat' => $obatData['jumlah_obat'] ?? null,
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
                                'jumlah_obat' => null,
                                'aturan_pakai' => null,
                            ]);
                        }
                    }
                }
            }, 3); // Retry up to 3 times on deadlock

            return redirect()->route('rekam-medis.index')->with('success', 'Data rekam medis berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $rekamMedis = RekamMedis::findOrFail($id);
        $rekamMedis->delete();

        return redirect()->route('rekam-medis.index')->with('success', 'Data rekam medis berhasil dihapus!');
    }

    // API untuk pencarian karyawan (AJAX)
    public function searchKaryawan(Request $request)
    {
        $search = $request->input('q');

        $karyawans = Karyawan::with(['departemen:id_departemen,nama_departemen'])
            ->select('id_karyawan', 'nik_karyawan', 'nama_karyawan', 'id_departemen')
            ->where(function($query) use ($search) {
                $query->where('nik_karyawan', 'like', "%{$search}%")
                      ->orWhere('nama_karyawan', 'like', "%{$search}%");
            })
            ->limit(10)
            ->get()
            ->map(function($karyawan) {
                return [
                    'id_karyawan' => $karyawan->id_karyawan,
                    'nik_karyawan' => $karyawan->nik_karyawan,
                    'nama_karyawan' => $karyawan->nama_karyawan,
                    'nama_departemen' => $karyawan->departemen->nama_departemen ?? '',
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
                    'id_keluarga'    => $keluarga->id_keluarga,
                    'nama_keluarga'  => $keluarga->nama_keluarga,
                    'kode_hubungan'  => $keluarga->kode_hubungan, // <- INI jadi sumber nilai NO RM
                    'hubungan'       => $keluarga->hubungan->hubungan ?? '-',
                    'jenis_kelamin'  => $keluarga->jenis_kelamin,
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
            'hubungan:kode_hubungan,hubungan'
        ])
        ->select('id_keluarga', 'id_karyawan', 'nama_keluarga', 'no_rm', 'bpjs_id', 'kode_hubungan', 'jenis_kelamin', 'tanggal_lahir')
        ->where(function($query) use ($search) {
            $query->where('nama_keluarga', 'like', "%{$search}%")
                  ->orWhere('bpjs_id', 'like', "%{$search}%")
                  ->orWhere('no_rm', 'like', "%{$search}%")
                  ->orWhereHas('karyawan', function($karyawan) use ($search) {
                      $karyawan->where('nik_karyawan', 'like', "%{$search}%");
                  });
        })
        ->limit(10)
        ->get()
        ->map(function($keluarga) {
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

        if (!$diagnosaId) {
            return response()->json([]);
        }

        $diagnosa = Diagnosa::with('obats')->find($diagnosaId);

        if (!$diagnosa) {
            return response()->json([]);
        }

        $obats = $diagnosa->obats->map(function($obat) {
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
                'message' => 'Gagal memperbarui status: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Download template untuk import data rekam medis
     */
    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Import Rekam Medis');

        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator('SIPO ICBP')
            ->setTitle('Template Import Rekam Medis')
            ->setSubject('Template Import Rekam Medis')
            ->setDescription('Template untuk import data rekam medis');

        // Header columns
        $headers = [
            'Hari / Tgl Periksa', 'NIK Karyawan', 'Nama Karyawan', 'Kode RM', 'Nama Pasien',
            'Keluhan', 'Diagnosa', 'Obat 1', 'jumlah Obat 1', 'Obat 2', 'jumlah obat 2',
            'Obat 3', 'jumlah Obat 3', 'Obat 4', 'jumlah Obat 4', 'Obat 5', 'jumlah Obat 5', 'Status'
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

        $sheet->getStyle('A1:Q1')->applyFromArray($headerStyle);

        // Add sample data
        $sheet->setCellValue('A2', '01/08/2025');
        $sheet->setCellValue('B2', '1200929');
        $sheet->setCellValue('C2', 'Purnomo');
        $sheet->setCellValue('D2', '1200929-A');
        $sheet->setCellValue('E2', 'Purnomo');
        $sheet->setCellValue('F2', 'S.Gigi PPJP');
        $sheet->setCellValue('G2', 'Natrium Diklofenak');
        $sheet->setCellValue('H2', 'Amoxicilin');
        $sheet->setCellValue('I2', '1');
        $sheet->setCellValue('J2', '-');
        $sheet->setCellValue('K2', '-');
        $sheet->setCellValue('L2', '-');
        $sheet->setCellValue('M2', '-');
        $sheet->setCellValue('N2', '-');
        $sheet->setCellValue('O2', '-');
        $sheet->setCellValue('P2', '-');
        $sheet->setCellValue('Q2', '-');
        $sheet->setCellValue('R2', 'Close');

        $sheet->setCellValue('A3', '01/08/2025');
        $sheet->setCellValue('B3', '50172104');
        $sheet->setCellValue('C3', 'Adam Azhari');
        $sheet->setCellValue('D3', '50172104-A');
        $sheet->setCellValue('E3', 'Adam Azhari');
        $sheet->setCellValue('F3', 'Batuk,Pilek,S.Tenggorakan');
        $sheet->setCellValue('G3', 'ISPA');
        $sheet->setCellValue('H3', '-');
        $sheet->setCellValue('I3', '-');
        $sheet->setCellValue('J3', 'Methylprednisolone');
        $sheet->setCellValue('K3', '-');
        $sheet->setCellValue('L3', 'Paracetamol');
        $sheet->setCellValue('M3', '1');
        $sheet->setCellValue('N3', 'Bodrex');
        $sheet->setCellValue('O3', '5');
        $sheet->setCellValue('P3', '-');
        $sheet->setCellValue('Q3', '-');
        $sheet->setCellValue('R3', 'Close');

        $sheet->setCellValue('A4', '01/08/2025');
        $sheet->setCellValue('B4', '1200337');
        $sheet->setCellValue('C4', 'Suparjo');
        $sheet->setCellValue('D4', '1200337-A');
        $sheet->setCellValue('E4', 'Suparjo');
        $sheet->setCellValue('F4', 'Pusing');
        $sheet->setCellValue('G4', '-');
        $sheet->setCellValue('H4', 'Amlodipin 5Mg');
        $sheet->setCellValue('I4', '-');
        $sheet->setCellValue('J4', '-');
        $sheet->setCellValue('K4', '-');
        $sheet->setCellValue('L4', '-');
        $sheet->setCellValue('M4', '-');
        $sheet->setCellValue('N4', '-');
        $sheet->setCellValue('O4', '-');
        $sheet->setCellValue('P4', '-');
        $sheet->setCellValue('Q4', '-');
        $sheet->setCellValue('R4', 'On Progress');

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

        $sheet->getStyle('A2:R4')->applyFromArray($dataStyle);

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(15);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(25);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(20);
        $sheet->getColumnDimension('I')->setWidth(15);
        $sheet->getColumnDimension('J')->setWidth(20);
        $sheet->getColumnDimension('K')->setWidth(15);
        $sheet->getColumnDimension('L')->setWidth(20);
        $sheet->getColumnDimension('M')->setWidth(15);
        $sheet->getColumnDimension('N')->setWidth(20);
        $sheet->getColumnDimension('O')->setWidth(15);
        $sheet->getColumnDimension('P')->setWidth(20);
        $sheet->getColumnDimension('Q')->setWidth(15);
        $sheet->getColumnDimension('R')->setWidth(15);

        // Set row heights
        $sheet->getRowDimension(1)->setRowHeight(25);
        $sheet->getRowDimension(2)->setRowHeight(20);
        $sheet->getRowDimension(3)->setRowHeight(20);
        $sheet->getRowDimension(4)->setRowHeight(20);

        // Add notes
        $sheet->setCellValue('A6', 'CATATAN:');
        $sheet->setCellValue('A7', '• Format Tanggal: DD/MM/YYYY (contoh: 01/08/2025)');
        $sheet->setCellValue('A8', '• NIK Karyawan harus ada di tabel karyawan');
        $sheet->setCellValue('A9', '• Kode RM format: NIK-KodeHubungan (contoh: 1200929-A)');
        $sheet->setCellValue('A10', '• Nama Pasien sesuai dengan data di tabel keluarga');
        $sheet->setCellValue('A11', '• Keluhan bisa multiple, pisahkan dengan koma (,)');
        $sheet->setCellValue('A12', '• Obat 1-5: isi dengan nama obat yang ada di tabel obat, jika tidak ada isi dengan "-"');
        $sheet->setCellValue('A13', '• jumlah Obat 1-5: isi dengan jumlah obat, jika tidak ada isi dengan "-"');
        $sheet->setCellValue('A14', '• Status: "Close" atau "On Progress"');
        $sheet->setCellValue('A15', '• Lihat daftar karyawan, diagnosa, dan obat di sheet referensi');

        $sheet->getStyle('A6')->getFont()->setBold(true);
        $sheet->getStyle('A7:A15')->getFont()->setItalic(true)->setSize(10);

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
            $referenceSheet->setCellValue('A' . $row, $karyawan->nik_karyawan);
            $referenceSheet->setCellValue('B' . $row, $karyawan->nama_karyawan);
            $referenceSheet->setCellValue('C' . $row, $karyawan->departemen->nama_departemen ?? '');
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
            $referenceSheet->setCellValue('E' . $row, $diagnosa->id_diagnosa);
            $referenceSheet->setCellValue('F' . $row, $diagnosa->nama_diagnosa);
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
            $referenceSheet->setCellValue('H' . $row, $obat->id_obat);
            $referenceSheet->setCellValue('I' . $row, $obat->nama_obat);
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
        $filename = 'template_rekam_medis_' . date('Y-m-d') . '.xlsx';

        // Set headers for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
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

            for ($rowNumber = 2; $rowNumber <= $highestRow; $rowNumber++) {
                // Read cell values with proper date handling
                $cellA = $sheet->getCell('A' . $rowNumber);
                if (\PhpOffice\PhpSpreadsheet\Shared\Date::isDateTime($cellA)) {
                    $tanggalPeriksa = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($cellA->getValue())->format('Y-m-d');
                } else {
                    $tanggalPeriksa = trim($cellA->getValue() ?? '');
                }

                $nikKaryawan = trim($sheet->getCell('B' . $rowNumber)->getValue() ?? '');
                $namaKaryawan = trim($sheet->getCell('C' . $rowNumber)->getValue() ?? '');
                $kodeRM = trim($sheet->getCell('D' . $rowNumber)->getValue() ?? '');
                $namaPasien = trim($sheet->getCell('E' . $rowNumber)->getValue() ?? '');
                $keluhan = trim($sheet->getCell('F' . $rowNumber)->getValue() ?? '');
                $diagnosa = trim($sheet->getCell('G' . $rowNumber)->getValue() ?? '');
                $obat1 = trim($sheet->getCell('H' . $rowNumber)->getValue() ?? '');
                $jumlahObat1 = trim($sheet->getCell('I' . $rowNumber)->getValue() ?? '');
                $obat2 = trim($sheet->getCell('J' . $rowNumber)->getValue() ?? '');
                $jumlahObat2 = trim($sheet->getCell('K' . $rowNumber)->getValue() ?? '');
                $obat3 = trim($sheet->getCell('L' . $rowNumber)->getValue() ?? '');
                $jumlahObat3 = trim($sheet->getCell('M' . $rowNumber)->getValue() ?? '');
                $obat4 = trim($sheet->getCell('N' . $rowNumber)->getValue() ?? '');
                $jumlahObat4 = trim($sheet->getCell('O' . $rowNumber)->getValue() ?? '');
                $obat5 = trim($sheet->getCell('P' . $rowNumber)->getValue() ?? '');
                $jumlahObat5 = trim($sheet->getCell('Q' . $rowNumber)->getValue() ?? '');
                $status = trim($sheet->getCell('R' . $rowNumber)->getValue() ?? '');

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

                // Convert tanggal format to YYYY-MM-DD
                if (!empty($tanggalPeriksa)) {
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
                        if (!checkdate($month, $day, $year)) {
                            $errors[] = "Baris $rowNumber: Tanggal '$tanggalPeriksa' tidak valid. Gunakan format DD/MM/YYYY";
                            continue;
                        }

                        // Convert to YYYY-MM-DD format
                        $tanggalPeriksa = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-' . str_pad($day, 2, '0', STR_PAD_LEFT);
                    }
                    // Check if the format is YYYY-MM-DD (already in database format)
                    elseif (preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2})$/', $tanggalPeriksa, $matches)) {
                        $year = $matches[1];
                        $month = $matches[2];
                        $day = $matches[3];

                        // Validate date
                        if (!checkdate($month, $day, $year)) {
                            $errors[] = "Baris $rowNumber: Tanggal '$tanggalPeriksa' tidak valid";
                            continue;
                        }

                        // Already in correct format, just ensure proper padding
                        $tanggalPeriksa = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-' . str_pad($day, 2, '0', STR_PAD_LEFT);
                    }
                    // Check if the format is D/M/YYYY (with single digit day/month)
                    elseif (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $tanggalPeriksa, $matches)) {
                        $day = $matches[1];
                        $month = $matches[2];
                        $year = $matches[3];

                        // Validate date
                        if (!checkdate($month, $day, $year)) {
                            $errors[] = "Baris $rowNumber: Tanggal '$tanggalPeriksa' tidak valid. Gunakan format DD/MM/YYYY";
                            continue;
                        }

                        // Convert to YYYY-MM-DD format
                        $tanggalPeriksa = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-' . str_pad($day, 2, '0', STR_PAD_LEFT);
                    }
                    else {
                        $errors[] = "Baris $rowNumber: Format tanggal '$tanggalPeriksa' tidak valid. Gunakan format DD/MM/YYYY";
                        continue;
                    }
                }

                // Validate status
                if (!in_array($status, ['Close', 'On Progress'])) {
                    $errors[] = "Baris $rowNumber: Status harus 'Close' atau 'On Progress'";
                    continue;
                }

                // Find karyawan
                $karyawan = Karyawan::where('nik_karyawan', $nikKaryawan)->first();
                if (!$karyawan) {
                    $errors[] = "Baris $rowNumber: Karyawan dengan NIK $nikKaryawan tidak ditemukan";
                    continue;
                }

                // Find keluarga
                $keluarga = Keluarga::where('id_karyawan', $karyawan->id_karyawan)
                                    ->where('nama_keluarga', $namaPasien)
                                    ->first();
                if (!$keluarga) {
                    $errors[] = "Baris $rowNumber: Pasien $namaPasien tidak ditemukan untuk karyawan $nikKaryawan";
                    continue;
                }

                // Find or create diagnosa
                $idDiagnosa = null;
                if (!empty($diagnosa) && $diagnosa !== '-') {
                    $diagnosaModel = Diagnosa::firstOrCreate(['nama_diagnosa' => $diagnosa]);
                    $idDiagnosa = $diagnosaModel->id_diagnosa;
                }

                // Create rekam medis
                $rekamMedis = RekamMedis::create([
                    'id_keluarga' => $keluarga->id_keluarga,
                    'tanggal_periksa' => $tanggalPeriksa,
                    'id_user' => Auth::id(),
                    'jumlah_keluhan' => 1, // Default
                    'status' => $status,
                ]);

                // Create keluhan entries for each obat
                $obatList = [
                    ['nama' => $obat1, 'jumlah' => $jumlahObat1],
                    ['nama' => $obat2, 'jumlah' => $jumlahObat2],
                    ['nama' => $obat3, 'jumlah' => $jumlahObat3],
                    ['nama' => $obat4, 'jumlah' => $jumlahObat4],
                    ['nama' => $obat5, 'jumlah' => $jumlahObat5],
                ];

                $keluhanCount = 0;
                foreach ($obatList as $obatData) {
                    if (!empty($obatData['nama']) && $obatData['nama'] !== '-') {
                        $obatModel = Obat::where('nama_obat', $obatData['nama'])->first();
                        if ($obatModel) {
                            Keluhan::create([
                                'id_rekam' => $rekamMedis->id_rekam,
                                'id_keluarga' => $keluarga->id_keluarga,
                                'id_diagnosa' => $idDiagnosa,
                                'terapi' => 'Obat',
                                'keterangan' => $keluhan,
                                'id_obat' => $obatModel->id_obat,
                                'jumlah_obat' => is_numeric($obatData['jumlah']) ? $obatData['jumlah'] : null,
                                'aturan_pakai' => null,
                            ]);
                            $keluhanCount++;
                        } else {
                            $errors[] = "Baris $rowNumber: Obat '{$obatData['nama']}' tidak ditemukan";
                        }
                    }
                }

                // If no obat found but there's diagnosa, create keluhan without obat
                if ($keluhanCount === 0 && $idDiagnosa) {
                    Keluhan::create([
                        'id_rekam' => $rekamMedis->id_rekam,
                        'id_keluarga' => $keluarga->id_keluarga,
                        'id_diagnosa' => $idDiagnosa,
                        'terapi' => 'Istirahat',
                        'keterangan' => $keluhan,
                        'id_obat' => null,
                        'jumlah_obat' => null,
                        'aturan_pakai' => null,
                    ]);
                    $keluhanCount = 1;
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
                    $errorMessage .= '<br>... dan ' . (count($errors) - 10) . ' error lainnya';
                }
                $message .= '<br><br>Error:<br>' . $errorMessage;
            }

            // Return JSON response for AJAX requests
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'data' => [
                        'created' => $created,
                        'errors' => $errors
                    ]
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
                    'message' => 'Gagal import data: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Gagal import data: ' . $e->getMessage());
        }
    }
}
