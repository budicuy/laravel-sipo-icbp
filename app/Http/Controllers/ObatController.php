<?php

namespace App\Http\Controllers;

use App\Models\Obat;
use App\Models\JenisObat;
use App\Models\SatuanObat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ObatController extends Controller
{
    public function index(Request $request)
    {
        $query = Obat::with([
            'jenisObat:id_jenis_obat,nama_jenis_obat',
            'satuanObat:id_satuan,nama_satuan'
        ]);

        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_obat', 'like', '%' . $search . '%')
                    ->orWhere('keterangan', 'like', '%' . $search . '%')
                    ->orWhereHas('jenisObat', function ($q) use ($search) {
                        $q->where('nama_jenis_obat', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('satuanObat', function ($q) use ($search) {
                        $q->where('nama_satuan', 'like', '%' . $search . '%');
                    });
            });
        }

        // Filter by jenis obat
        if ($request->has('jenis_obat') && $request->jenis_obat != '') {
            $query->where('id_jenis_obat', $request->jenis_obat);
        }

        // Filter by satuan obat
        if ($request->has('satuan_obat') && $request->satuan_obat != '') {
            $query->where('id_satuan', $request->satuan_obat);
        }

        // Sorting
        $sortField = $request->get('sort', 'id_obat');
        $sortDirection = $request->get('direction', 'desc');

        if (in_array($sortField, ['nama_obat', 'jenis_obat', 'satuan_obat', 'jumlah_per_kemasan', 'stok_awal', 'stok_masuk', 'stok_keluar', 'stok_akhir', 'harga_per_kemasan', 'harga_per_satuan', 'keterangan', 'tanggal_update'])) {
            // Handle sorting for related fields
            if ($sortField === 'jenis_obat') {
                $query->join('jenis_obat', 'obat.id_jenis_obat', '=', 'jenis_obat.id_jenis_obat')
                      ->orderBy('jenis_obat.nama_jenis_obat', $sortDirection)
                      ->select('obat.*');
            } elseif ($sortField === 'satuan_obat') {
                $query->join('satuan_obat', 'obat.id_satuan', '=', 'satuan_obat.id_satuan')
                      ->orderBy('satuan_obat.nama_satuan', $sortDirection)
                      ->select('obat.*');
            } else {
                $query->orderBy($sortField, $sortDirection);
            }
        } else {
            $query->orderBy('id_obat', 'desc');
        }

        // Pagination dengan nilai dinamis
        $perPage = $request->get('per_page', 50);
        $perPage = in_array($perPage, [50, 100, 150, 200]) ? $perPage : 50;

        $obats = $query->paginate($perPage);
        // Cache reference data for better performance
        $jenisObats = Cache::remember('jenis_obats_all', 60, function () {
            return JenisObat::get();
        });
        $satuanObats = Cache::remember('satuan_obats_all', 60, function () {
            return SatuanObat::get();
        });

        return view('obat.index', compact('obats', 'jenisObats', 'satuanObats'));
    }

    public function create()
    {
        $jenisObats = Cache::remember('jenis_obats_all', 60, function () {
            return JenisObat::get();
        });
        $satuanObats = Cache::remember('satuan_obats_all', 60, function () {
            return SatuanObat::get();
        });
        return view('obat.create', compact('jenisObats', 'satuanObats'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_obat' => 'required|string|max:100|unique:obat,nama_obat',
            'keterangan' => 'nullable|string',
            'id_jenis_obat' => 'required|exists:jenis_obat,id_jenis_obat',
            'id_satuan' => 'required|exists:satuan_obat,id_satuan',
            'stok_awal' => 'required|integer|min:0',
            'stok_masuk' => 'required|integer|min:0',
            'stok_keluar' => 'required|integer|min:0',
            'jumlah_per_kemasan' => 'required|integer|min:1',
            'harga_per_kemasan' => 'required|numeric|min:0',
            'harga_per_satuan' => 'required|numeric|min:0',
        ], [
            'nama_obat.required' => 'Nama obat wajib diisi',
            'nama_obat.unique' => 'Nama obat sudah terdaftar',
            'id_jenis_obat.required' => 'Jenis obat wajib dipilih',
            'id_satuan.required' => 'Satuan obat wajib dipilih',
            'stok_awal.required' => 'Stok awal wajib diisi',
            'stok_masuk.required' => 'Stok masuk wajib diisi',
            'stok_keluar.required' => 'Stok keluar wajib diisi',
            'jumlah_per_kemasan.required' => 'Jumlah per kemasan wajib diisi',
            'harga_per_kemasan.required' => 'Harga per kemasan wajib diisi',
            'harga_per_satuan.required' => 'Harga per satuan wajib diisi',
        ]);

        Obat::create($validated);

        // Clear cache
        Cache::forget('jenis_obats_all');
        Cache::forget('satuan_obats_all');

        return redirect()->route('obat.index')->with('success', 'Data obat berhasil ditambahkan');
    }

    public function edit($id)
    {
        $obat = Obat::findOrFail($id);
        $jenisObats = Cache::remember('jenis_obats_all', 60, function () {
            return JenisObat::get();
        });
        $satuanObats = Cache::remember('satuan_obats_all', 60, function () {
            return SatuanObat::get();
        });
        return view('obat.edit', compact('obat', 'jenisObats', 'satuanObats'));
    }

    public function update(Request $request, $id)
    {
        $obat = Obat::findOrFail($id);

        $validated = $request->validate([
            'nama_obat' => 'required|string|max:100|unique:obat,nama_obat,' . $id . ',id_obat',
            'keterangan' => 'nullable|string',
            'id_jenis_obat' => 'required|exists:jenis_obat,id_jenis_obat',
            'id_satuan' => 'required|exists:satuan_obat,id_satuan',
            'stok_awal' => 'required|integer|min:0',
            'stok_masuk' => 'required|integer|min:0',
            'stok_keluar' => 'required|integer|min:0',
            'jumlah_per_kemasan' => 'required|integer|min:1',
            'harga_per_kemasan' => 'required|numeric|min:0',
            'harga_per_satuan' => 'required|numeric|min:0',
        ], [
            'nama_obat.required' => 'Nama obat wajib diisi',
            'nama_obat.unique' => 'Nama obat sudah terdaftar',
            'id_jenis_obat.required' => 'Jenis obat wajib dipilih',
            'id_satuan.required' => 'Satuan obat wajib dipilih',
            'stok_awal.required' => 'Stok awal wajib diisi',
            'stok_masuk.required' => 'Stok masuk wajib diisi',
            'stok_keluar.required' => 'Stok keluar wajib diisi',
            'jumlah_per_kemasan.required' => 'Jumlah per kemasan wajib diisi',
            'harga_per_kemasan.required' => 'Harga per kemasan wajib diisi',
            'harga_per_satuan.required' => 'Harga per satuan wajib diisi',
        ]);

        $obat->update($validated);

        // Clear cache
        Cache::forget('jenis_obats_all');
        Cache::forget('satuan_obats_all');

        return redirect()->route('obat.index')->with('success', 'Data obat berhasil diperbarui');
    }

    public function destroy($id)
    {
        $obat = Obat::findOrFail($id);
        $obat->delete();

        // Clear cache
        Cache::forget('jenis_obats_all');
        Cache::forget('satuan_obats_all');

        return response()->json(['success' => true, 'message' => 'Data obat berhasil dihapus']);
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'Tidak ada data yang dipilih'], 400);
        }

        Obat::whereIn('id_obat', $ids)->delete();

        // Clear cache
        Cache::forget('jenis_obats_all');
        Cache::forget('satuan_obats_all');

        return response()->json(['success' => true, 'message' => count($ids) . ' data obat berhasil dihapus']);
    }

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Import Obat');

        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator('SIPO ICBP')
            ->setTitle('Template Import Obat')
            ->setSubject('Template Import Obat')
            ->setDescription('Template untuk import data obat');

        // Header columns
        $headers = ['Nama Obat', 'Satuan', 'Keterangan', 'Harga Satuan', 'Harga Perkemasan', 'Jenis Obat', 'Stok Awal', 'Stok Masuk', 'Stok Keluar'];
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

        $sheet->getStyle('A1:I1')->applyFromArray($headerStyle);

        // Get reference data for dropdowns
        $jenisObats = JenisObat::pluck('nama_jenis_obat')->toArray();
        $satuanObats = SatuanObat::pluck('nama_satuan')->toArray();

        // Add sample data
        $sheet->setCellValue('A2', 'Paracetamol');
        $sheet->setCellValue('B2', 'Tablet');
        $sheet->setCellValue('C2', 'Obat untuk menurunkan demam dan meredakan nyeri');
        $sheet->setCellValue('D2', '500');
        $sheet->setCellValue('E2', '10000');
        $sheet->setCellValue('F2', 'Tablet');
        $sheet->setCellValue('G2', '100');
        $sheet->setCellValue('H2', '50');
        $sheet->setCellValue('I2', '10');

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

        $sheet->getStyle('A2:I2')->applyFromArray($dataStyle);

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(30);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(50);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(18);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(12);
        $sheet->getColumnDimension('H')->setWidth(12);
        $sheet->getColumnDimension('I')->setWidth(12);

        // Set row heights
        $sheet->getRowDimension(1)->setRowHeight(25);
        $sheet->getRowDimension(2)->setRowHeight(20);

        // Add notes
        $sheet->setCellValue('A4', 'CATATAN:');
        $sheet->setCellValue('A5', '• Nama Obat wajib diisi dan harus unik');
        $sheet->setCellValue('A6', '• Satuan: ' . implode(', ', $satuanObats));
        $sheet->setCellValue('A7', '• Jenis Obat: ' . implode(', ', $jenisObats));
        $sheet->setCellValue('A8', '• Harga dalam format angka (tanpa titik/koma)');
        $sheet->setCellValue('A9', '• Stok Awal, Masuk, Keluar dalam format angka');
        $sheet->setCellValue('A10', '• Stok Akhir akan dihitung otomatis (Stok Awal + Stok Masuk - Stok Keluar)');

        $sheet->getStyle('A4')->getFont()->setBold(true);
        $sheet->getStyle('A5:A10')->getFont()->setItalic(true)->setSize(10);

        // Create Excel file
        $writer = new Xlsx($spreadsheet);
        $filename = 'template_obat_' . date('Y-m-d') . '.xlsx';

        // Set headers for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

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
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();

            // Get highest row number
            $highestRow = $sheet->getHighestRow();

            // Get reference data
            $jenisObats = JenisObat::pluck('nama_jenis_obat', 'id_jenis_obat')->toArray();
            $jenisObatNames = array_flip($jenisObats);
            $satuanObats = SatuanObat::pluck('nama_satuan', 'id_satuan')->toArray();
            $satuanObatNames = array_flip($satuanObats);

            // Skip header row, start from row 2
            $created = 0;
            $updated = 0;
            $errors = [];

            for ($rowNumber = 2; $rowNumber <= $highestRow; $rowNumber++) {
                // Read cell values
                $namaObat = trim($sheet->getCell('A' . $rowNumber)->getValue() ?? '');
                $satuan = trim($sheet->getCell('B' . $rowNumber)->getValue() ?? '');
                $keterangan = trim($sheet->getCell('C' . $rowNumber)->getValue() ?? '');
                $hargaSatuan = trim($sheet->getCell('D' . $rowNumber)->getValue() ?? '');
                $hargaPerkemasan = trim($sheet->getCell('E' . $rowNumber)->getValue() ?? '');
                $jenisObat = trim($sheet->getCell('F' . $rowNumber)->getValue() ?? '');
                $stokAwal = trim($sheet->getCell('G' . $rowNumber)->getValue() ?? '');
                $stokMasuk = trim($sheet->getCell('H' . $rowNumber)->getValue() ?? '');
                $stokKeluar = trim($sheet->getCell('I' . $rowNumber)->getValue() ?? '');

                // Skip empty rows
                if (empty($namaObat)) {
                    continue;
                }

                // Validate required fields
                if (empty($namaObat)) {
                    $errors[] = "Baris $rowNumber: Nama Obat tidak boleh kosong";
                    continue;
                }

                if (empty($satuan)) {
                    $errors[] = "Baris $rowNumber: Satuan tidak boleh kosong";
                    continue;
                }

                if (empty($jenisObat)) {
                    $errors[] = "Baris $rowNumber: Jenis Obat tidak boleh kosong";
                    continue;
                }

                // Validate nama obat length
                if (strlen($namaObat) > 100) {
                    $errors[] = "Baris $rowNumber: Nama Obat maksimal 100 karakter";
                    continue;
                }

                // Validate satuan exists in database
                if (!isset($satuanObatNames[$satuan])) {
                    $errors[] = "Baris $rowNumber: Satuan '$satuan' tidak valid. Pilihan yang tersedia: " . implode(', ', array_keys($satuanObatNames));
                    continue;
                }

                // Validate jenis obat exists in database
                if (!isset($jenisObatNames[$jenisObat])) {
                    $errors[] = "Baris $rowNumber: Jenis Obat '$jenisObat' tidak valid. Pilihan yang tersedia: " . implode(', ', array_keys($jenisObatNames));
                    continue;
                }

                // Validate and convert numeric fields
                $hargaSatuan = is_numeric($hargaSatuan) ? (float)$hargaSatuan : 0;
                $hargaPerkemasan = is_numeric($hargaPerkemasan) ? (float)$hargaPerkemasan : 0;
                $stokAwal = is_numeric($stokAwal) ? (int)$stokAwal : 0;
                $stokMasuk = is_numeric($stokMasuk) ? (int)$stokMasuk : 0;
                $stokKeluar = is_numeric($stokKeluar) ? (int)$stokKeluar : 0;

                // Calculate stok akhir
                $stokAkhir = $stokAwal + $stokMasuk - $stokKeluar;

                // Prepare data
                $data = [
                    'nama_obat' => $namaObat,
                    'keterangan' => !empty($keterangan) ? $keterangan : null,
                    'id_jenis_obat' => $jenisObatNames[$jenisObat],
                    'id_satuan' => $satuanObatNames[$satuan],
                    'stok_awal' => $stokAwal,
                    'stok_masuk' => $stokMasuk,
                    'stok_keluar' => $stokKeluar,
                    'stok_akhir' => $stokAkhir,
                    'harga_per_satuan' => $hargaSatuan,
                    'harga_per_kemasan' => $hargaPerkemasan,
                    'jumlah_per_kemasan' => 1, // Default value
                    'tanggal_update' => now(),
                ];

                // Check if update or create
                $exists = Obat::where('nama_obat', $namaObat)->exists();

                // Create or update obat
                Obat::updateOrCreate(
                    ['nama_obat' => $namaObat],
                    $data
                );

                if ($exists) {
                    $updated++;
                } else {
                    $created++;
                }
            }

            $message = "Import selesai: $created data baru ditambahkan, $updated data diperbarui";
            $hasErrors = count($errors) > 0;

            if ($hasErrors) {
                $errorMessage = implode(', ', array_slice($errors, 0, 10));
                if (count($errors) > 10) {
                    $errorMessage .= ' ... dan ' . (count($errors) - 10) . ' error lainnya';
                }
                $message .= '. Error: ' . $errorMessage;
            }

            // Clear cache
            Cache::forget('jenis_obats_all');
            Cache::forget('satuan_obats_all');

            // Return JSON response for AJAX requests
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'data' => [
                        'created' => $created,
                        'updated' => $updated,
                        'errors' => $errors
                    ]
                ]);
            }

            if ($hasErrors) {
                return back()->with('warning', $message);
            }

            return back()->with('success', $message);

        } catch (\Exception $e) {
            Log::error('Error importing obat: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

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
