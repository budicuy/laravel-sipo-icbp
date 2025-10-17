<?php

namespace App\Http\Controllers;

use App\Models\Obat;
use App\Models\JenisObat;
use App\Models\SatuanObat;
use App\Models\StokObat;
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

        if (in_array($sortField, ['nama_obat', 'jenis_obat', 'satuan_obat', 'jumlah_per_kemasan', 'harga_per_kemasan', 'harga_per_satuan', 'keterangan', 'tanggal_update'])) {
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

        // Start transaction
        DB::beginTransaction();

        try {
            // Create obat
            $obat = Obat::create($validated);

            // Create initial stok bulanan entry for current month
            $currentPeriode = now()->format('m-y');
            StokObat::create([
                'id_obat' => $obat->id_obat,
                'periode' => $currentPeriode,
                'stok_awal' => 0,
                'stok_pakai' => 0,
                'stok_masuk' => 0,
                'stok_akhir' => 0,
            ]);

            DB::commit();

            // Clear cache
            Cache::forget('jenis_obats_all');
            Cache::forget('satuan_obats_all');

            return redirect()->route('obat.index')->with('success', 'Data obat berhasil ditambahkan');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating obat: ' . $e->getMessage());
            return back()->with('error', 'Gagal menambahkan data obat: ' . $e->getMessage())->withInput();
        }
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

        $sheet->getStyle('A4')->getFont()->setBold(true);
        $sheet->getStyle('A5:A8')->getFont()->setItalic(true)->setSize(10);

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

    /**
     * Export data obat to Excel
     */
    public function export(Request $request)
    {
        // Build query with same filters as index
        $query = Obat::with([
            'jenisObat:id_jenis_obat,nama_jenis_obat',
            'satuanObat:id_satuan,nama_satuan'
        ]);

        // Apply search filter
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

        // Apply jenis obat filter
        if ($request->has('jenis_obat') && $request->jenis_obat != '') {
            $query->where('id_jenis_obat', $request->jenis_obat);
        }

        // Apply satuan obat filter
        if ($request->has('satuan_obat') && $request->satuan_obat != '') {
            $query->where('id_satuan', $request->satuan_obat);
        }

        // Apply sorting
        $sortField = $request->get('sort', 'nama_obat');
        $sortDirection = $request->get('direction', 'asc');

        if (in_array($sortField, ['nama_obat', 'jenis_obat', 'satuan_obat', 'jumlah_per_kemasan', 'harga_per_kemasan', 'harga_per_satuan', 'keterangan', 'tanggal_update'])) {
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
            $query->orderBy('nama_obat', 'asc');
        }

        // Get data
        $obats = $query->get();

        // Create spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Obat');

        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator('SIPO ICBP')
            ->setTitle('Data Obat')
            ->setSubject('Data Obat')
            ->setDescription('Data obat dan persediaan farmasi');

        // Header columns
        $headers = [
            'No', 'Nama Obat', 'Jenis Obat', 'Satuan', 'Jumlah per Kemasan',
            'Harga per Kemasan', 'Harga per Satuan', 'Keterangan', 'Tanggal Update'
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

        $lastColumn = chr(ord('A') + count($headers) - 1);
        $sheet->getStyle('A1:' . $lastColumn . '1')->applyFromArray($headerStyle);

        // Fill data
        $row = 2;
        $no = 1;

        foreach ($obats as $obat) {
            $sheet->setCellValue('A' . $row, $no);
            $sheet->setCellValue('B' . $row, $obat->nama_obat);
            $sheet->setCellValue('C' . $row, $obat->jenisObat->nama_jenis_obat ?? '-');
            $sheet->setCellValue('D' . $row, $obat->satuanObat->nama_satuan ?? '-');
            $sheet->setCellValue('E' . $row, $obat->jumlah_per_kemasan);
            $sheet->setCellValue('F' . $row, $obat->harga_per_kemasan);
            $sheet->setCellValue('G' . $row, $obat->harga_per_satuan);
            $sheet->setCellValue('H' . $row, $obat->keterangan ?? '-');
            $sheet->setCellValue('I' . $row, $obat->tanggal_update ? $obat->tanggal_update->format('d-m-Y') : '-');

            // Style data rows
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

            $sheet->getStyle('A' . $row . ':' . $lastColumn . $row)->applyFromArray($dataStyle);

            $row++;
            $no++;
        }

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(18);
        $sheet->getColumnDimension('F')->setWidth(18);
        $sheet->getColumnDimension('G')->setWidth(18);
        $sheet->getColumnDimension('H')->setWidth(50);
        $sheet->getColumnDimension('I')->setWidth(15);

        // Set row heights
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Add summary sheet
        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex(1);
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Ringkasan');

        // Add summary data
        $totalObat = $obats->count();
        $totalJenis = $obats->pluck('id_jenis_obat')->unique()->count();
        $totalSatuan = $obats->pluck('id_satuan')->unique()->count();
        $avgHargaKemasan = $obats->avg('harga_per_kemasan');
        $avgHargaSatuan = $obats->avg('harga_per_satuan');

        $sheet->setCellValue('A1', 'RINGKASAN DATA OBAT');
        $sheet->setCellValue('A3', 'Total Obat:');
        $sheet->setCellValue('B3', $totalObat);
        $sheet->setCellValue('A4', 'Total Jenis Obat:');
        $sheet->setCellValue('B4', $totalJenis);
        $sheet->setCellValue('A5', 'Total Satuan:');
        $sheet->setCellValue('B5', $totalSatuan);
        $sheet->setCellValue('A6', 'Rata-rata Harga per Kemasan:');
        $sheet->setCellValue('B6', 'Rp ' . number_format($avgHargaKemasan, 0, ',', '.'));
        $sheet->setCellValue('A7', 'Rata-rata Harga per Satuan:');
        $sheet->setCellValue('B7', 'Rp ' . number_format($avgHargaSatuan, 0, ',', '.'));
        $sheet->setCellValue('A9', 'Tanggal Export:');
        $sheet->setCellValue('B9', now()->format('d-m-Y H:i:s'));

        // Style summary
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A3:A9')->getFont()->setBold(true);
        $sheet->getColumnDimension('A')->setWidth(30);
        $sheet->getColumnDimension('B')->setWidth(20);

        // Set active sheet back to data
        $spreadsheet->setActiveSheetIndex(0);
        $sheet = $spreadsheet->getActiveSheet();

        // Create Excel file
        $writer = new Xlsx($spreadsheet);
        $filename = 'data_obat_' . date('Y-m-d_H-i-s') . '.xlsx';

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

        // Start transaction
        DB::beginTransaction();

        try {
            $file = $request->file('file');
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();

            // Get highest row number
            $highestRow = $sheet->getHighestRow();

            // Log informasi awal
            Log::info('Starting import obat process', [
                'file_name' => $file->getClientOriginalName(),
                'highest_row' => $highestRow
            ]);

            // Get reference data
            $jenisObats = JenisObat::pluck('nama_jenis_obat', 'id_jenis_obat')->toArray();
            $jenisObatNames = array_flip($jenisObats);
            $satuanObats = SatuanObat::pluck('nama_satuan', 'id_satuan')->toArray();
            $satuanObatNames = array_flip($satuanObats);

            // Log reference data
            Log::info('Reference data loaded', [
                'jenis_obat_count' => count($jenisObats),
                'satuan_obat_count' => count($satuanObats)
            ]);

            // Skip header row, start from row 2
            $created = 0;
            $updated = 0;
            $errors = [];
            $currentPeriode = now()->format('m-y');

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

                // Create or update obat with validation
                try {
                    $obat = Obat::updateOrCreate(
                        ['nama_obat' => $namaObat],
                        $data
                    );

                    // Log successful creation/update
                    Log::info('Obat processed', [
                        'row' => $rowNumber,
                        'nama_obat' => $namaObat,
                        'exists' => $exists,
                        'obat_id' => $obat->id_obat
                    ]);

                    // If this is a new obat, create initial stok bulanan entry
                    if (!$exists) {
                        $currentPeriode = now()->format('m-y');
                        StokObat::create([
                            'id_obat' => $obat->id_obat,
                            'periode' => $currentPeriode,
                            'stok_awal' => 0,
                            'stok_pakai' => 0,
                            'stok_masuk' => 0,
                            'stok_akhir' => 0,
                        ]);

                        Log::info('Stok bulanan created for new obat', [
                            'obat_id' => $obat->id_obat,
                            'periode' => $currentPeriode
                        ]);
                    }

                    if ($exists) {
                        $updated++;
                    } else {
                        $created++;
                    }
                } catch (\Exception $e) {
                    Log::error('Error processing obat at row ' . $rowNumber, [
                        'error' => $e->getMessage(),
                        'nama_obat' => $namaObat
                    ]);
                    $errors[] = "Baris $rowNumber: " . $e->getMessage();
                    continue;
                }
            }

            // Commit transaction
            DB::commit();

            Log::info('Import transaction committed', [
                'created' => $created,
                'updated' => $updated,
                'errors_count' => count($errors)
            ]);

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
            // Rollback transaction
            DB::rollBack();

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
