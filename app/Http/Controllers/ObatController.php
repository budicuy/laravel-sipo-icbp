<?php

namespace App\Http\Controllers;

use App\Models\Obat;
use App\Models\JenisObat;
use App\Models\SatuanObat;
use App\Models\StokBulanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
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
            'jumlah_per_kemasan' => 'required|integer|min:1',
            'harga_per_kemasan' => 'required|numeric|min:0',
            'harga_per_satuan' => 'required|numeric|min:0',
        ], [
            'nama_obat.required' => 'Nama obat wajib diisi',
            'nama_obat.unique' => 'Nama obat sudah terdaftar',
            'id_jenis_obat.required' => 'Jenis obat wajib dipilih',
            'id_satuan.required' => 'Satuan obat wajib dipilih',
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
            'jumlah_per_kemasan' => 'required|integer|min:1',
            'harga_per_kemasan' => 'required|numeric|min:0',
            'harga_per_satuan' => 'required|numeric|min:0',
        ], [
            'nama_obat.required' => 'Nama obat wajib diisi',
            'nama_obat.unique' => 'Nama obat sudah terdaftar',
            'id_jenis_obat.required' => 'Jenis obat wajib dipilih',
            'id_satuan.required' => 'Satuan obat wajib dipilih',
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
        $headers = ['Nama Obat', 'Satuan', 'Keterangan', 'Harga Satuan', 'Harga Perkemasan', 'Jenis Obat'];
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

        $sheet->getStyle('A1:F1')->applyFromArray($headerStyle);

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

        $sheet->getStyle('A2:F2')->applyFromArray($dataStyle);

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(30);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(50);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(18);
        $sheet->getColumnDimension('F')->setWidth(20);

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

                // Prepare data
                $data = [
                    'nama_obat' => $namaObat,
                    'keterangan' => !empty($keterangan) ? $keterangan : null,
                    'id_jenis_obat' => $jenisObatNames[$jenisObat],
                    'id_satuan' => $satuanObatNames[$satuan],
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

    /**
     * Import data stok bulanan dari CSV
     */
    public function importStokBulanan(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,xlsx,xls'],
        ], [
            'file.required' => 'File harus dipilih',
            'file.mimes' => 'File harus berformat CSV atau Excel (.csv, .xlsx, .xls)',
        ]);

        try {
            $file = $request->file('file');
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();

            // Get highest row number
            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();

            // Parse header untuk mendapatkan periode
            $headerRow = $sheet->rangeToArray('A1:' . $highestColumn . '1', NULL, TRUE, FALSE)[0];
            $periodes = [];

            // Ekstrak periode dari header (format: MM-YY)
            for ($i = 5; $i < count($headerRow); $i += 4) {
                if (isset($headerRow[$i]) && preg_match('/(\d{2}-\d{2})/', $headerRow[$i], $matches)) {
                    $periodes[] = $matches[1];
                }
            }

            // Get reference data
            $obats = Obat::pluck('id_obat', 'nama_obat')->toArray();

            // Process data
            $successCount = 0;
            $errorCount = 0;
            $errors = [];

            // Start transaction
            DB::beginTransaction();

            try {
                // Skip header rows, start from row 3
                for ($rowNumber = 3; $rowNumber <= $highestRow; $rowNumber++) {
                    $rowData = $sheet->rangeToArray('A' . $rowNumber . ':' . $highestColumn . $rowNumber, NULL, TRUE, FALSE)[0];

                    // Get nama obat dari kolom B
                    $namaObat = trim($rowData[1] ?? '');

                    if (empty($namaObat)) {
                        continue; // Skip empty rows
                    }

                    // Check if obat exists
                    if (!isset($obats[$namaObat])) {
                        $errors[] = "Baris $rowNumber: Obat '$namaObat' tidak ditemukan di database";
                        $errorCount++;
                        continue;
                    }

                    $idObat = $obats[$namaObat];

                    // Process each periode
                    $colIndex = 5; // Start from column F (index 5)
                    foreach ($periodes as $periode) {
                        if ($colIndex + 3 < count($rowData)) {
                            $stokAwal = $this->parseStokValue($rowData[$colIndex] ?? 0);
                            $stokPakai = $this->parseStokValue($rowData[$colIndex + 1] ?? 0);
                            $stokAkhir = $this->parseStokValue($rowData[$colIndex + 2] ?? 0);
                            $stokMasuk = $this->parseStokValue($rowData[$colIndex + 3] ?? 0);

                            // Insert or update stok bulanan
                            StokBulanan::updateOrCreate(
                                [
                                    'id_obat' => $idObat,
                                    'periode' => $periode,
                                ],
                                [
                                    'stok_awal' => $stokAwal,
                                    'stok_pakai' => $stokPakai,
                                    'stok_akhir' => $stokAkhir,
                                    'stok_masuk' => $stokMasuk,
                                ]
                            );
                        }
                        $colIndex += 4;
                    }
                    $successCount++;
                }

                DB::commit();

                $message = "Import stok bulanan selesai: $successCount data berhasil diproses";
                if ($errorCount > 0) {
                    $message .= ", $errorCount data gagal";
                    if (!empty($errors)) {
                        $message .= ". Error: " . implode(', ', array_slice($errors, 0, 5));
                        if (count($errors) > 5) {
                            $message .= ' ... dan ' . (count($errors) - 5) . ' error lainnya';
                        }
                    }
                }

                return back()->with('success', $message);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('Error importing stok bulanan: ' . $e->getMessage());
            return back()->with('error', 'Gagal import data stok bulanan: ' . $e->getMessage());
        }
    }

    /**
     * Parse nilai stok dari CSV
     */
    private function parseStokValue($value)
    {
        // Handle nilai dengan tanda kurung (60) = -60
        if (is_string($value) && preg_match('/^\((\d+)\)$/', $value, $matches)) {
            return -(int)$matches[1];
        }

        // Handle nilai dengan titik atau koma
        $value = str_replace(['.', ','], '', $value);

        // Handle nilai "-" atau kosong
        if ($value === '-' || $value === '' || $value === null) {
            return 0;
        }

        return (int)$value;
    }

    /**
     * Download template untuk import stok bulanan
     */
    public function downloadTemplateStokBulanan()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Import Stok Bulanan');

        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator('SIPO ICBP')
            ->setTitle('Template Import Stok Bulanan')
            ->setSubject('Template Import Stok Bulanan')
            ->setDescription('Template untuk import data stok bulanan');

        // Header columns
        $headers = [
            'No', 'Nama Obat', 'Satuan', 'Kegunaan', 'Jenis / Golongan Obat',
            '08-24 Awal', '08-24 Pakai', '08-24 Akhir', '08-24 Masuk',
            '09-24 Awal', '09-24 Pakai', '09-24 Akhir', '09-24 Masuk',
            '10-24 Awal', '10-24 Pakai', '10-24 Akhir', '10-24 Masuk'
        ];

        $column = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($column . '1', $header);
            $column++;
        }

        // Style header
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '059669']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ];

        $sheet->getStyle('A1:P1')->applyFromArray($headerStyle);

        // Get sample data
        $obats = Obat::with(['jenisObat', 'satuanObat'])->limit(3)->get();

        $row = 2;
        foreach ($obats as $index => $obat) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $obat->nama_obat);
            $sheet->setCellValue('C' . $row, $obat->satuanObat->nama_satuan ?? '');
            $sheet->setCellValue('D' . $row, $obat->keterangan ?? '');
            $sheet->setCellValue('E' . $row, $obat->jenisObat->nama_jenis_obat ?? '');

            // Sample stok data
            $sheet->setCellValue('F' . $row, $obat->stok_awal);
            $sheet->setCellValue('G' . $row, $obat->stok_keluar);
            $sheet->setCellValue('H' . $row, $obat->stok_akhir);
            $sheet->setCellValue('I' . $row, $obat->stok_masuk);

            $row++;
        }

        // Set column widths
        foreach (range('A', 'P') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Add notes
        $sheet->setCellValue('A' . ($row + 2), 'CATATAN:');
        $sheet->setCellValue('A' . ($row + 3), '• Format file: CSV atau Excel (.xlsx, .xls)');
        $sheet->setCellValue('A' . ($row + 4), '• Nama Obat harus sesuai dengan data di database');
        $sheet->setCellValue('A' . ($row + 5), '• Format periode: MM-YY (contoh: 08-24 untuk Agustus 2024)');
        $sheet->setCellValue('A' . ($row + 6), '• Nilai stok dalam format angka, gunakan - untuk nilai kosong');
        $sheet->setCellValue('A' . ($row + 7), '• Untuk nilai negatif, gunakan format (60) = -60');

        $sheet->getStyle('A' . ($row + 2))->getFont()->setBold(true);
        $sheet->getStyle('A' . ($row + 3) . ':A' . ($row + 7))->getFont()->setItalic(true)->setSize(10);

        // Create Excel file
        $writer = new Xlsx($spreadsheet);
        $filename = 'template_stok_bulanan_' . date('Y-m-d') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
}
