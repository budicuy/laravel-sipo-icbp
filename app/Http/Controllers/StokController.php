<?php

namespace App\Http\Controllers;

use App\Models\Obat;
use App\Models\StokBulanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class StokController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Query semua data Obat
        $query = Obat::with(['satuanObat:id_satuan,nama_satuan']);

        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_obat', 'like', '%'.$search.'%')
                    ->orWhere('keterangan', 'like', '%'.$search.'%');
            });
        }

        $obats = $query->get();

        // Optimasi N+1: Hitung sisa stok untuk semua obat sekaligus
        $obatIds = $obats->pluck('id_obat')->toArray();
        $sisaStokMap = StokBulanan::getSisaStokSaatIniBatch($obatIds);

        // Assign sisa stok ke setiap obat dengan setAttribute untuk menghindari N+1
        $obatsWithStok = $obats->map(function ($obat) use ($sisaStokMap) {
            // Set sebagai attribute agar accessor tidak memanggil query individual
            $obat->setAttribute('sisa_stok', $sisaStokMap->get($obat->id_obat, 0));

            return $obat;
        });

        // Filter berdasarkan status stok (setelah perhitungan)
        if ($request->has('stok_status') && $request->stok_status != '') {
            switch ($request->stok_status) {
                case 'habis':
                    $obatsWithStok = $obatsWithStok->filter(function ($obat) {
                        return $obat->sisa_stok <= 0;
                    });
                    break;
                case 'rendah':
                    $obatsWithStok = $obatsWithStok->filter(function ($obat) {
                        return $obat->sisa_stok > 0 && $obat->sisa_stok <= 10;
                    });
                    break;
                case 'tersedia':
                    $obatsWithStok = $obatsWithStok->filter(function ($obat) {
                        return $obat->sisa_stok > 10;
                    });
                    break;
            }
        }

        // Sorting
        $sortField = $request->get('sort', 'nama_obat');
        $sortDirection = $request->get('direction', 'asc');

        if ($sortField === 'sisa_stok') {
            $obatsWithStok = $sortDirection === 'asc'
                ? $obatsWithStok->sortBy('sisa_stok')
                : $obatsWithStok->sortByDesc('sisa_stok');
        } elseif ($sortField === 'nama_obat') {
            $obatsWithStok = $sortDirection === 'asc'
                ? $obatsWithStok->sortBy('nama_obat')
                : $obatsWithStok->sortByDesc('nama_obat');
        }

        return view('stok.index', compact('obatsWithStok'));
    }

    /**
     * Display the specified resource.
     */
    public function show($obat_id)
    {
        // Ambil data Obat berdasarkan $obat_id
        $obat = Obat::with(['satuanObat:id_satuan,nama_satuan'])
            ->findOrFail($obat_id);

        // Tampilkan riwayat stok bulanan
        $riwayatStok = StokBulanan::getRiwayatStok($obat_id, 24); // 24 bulan terakhir

        // Hitung sisa stok saat ini menggunakan batch approach (untuk konsistensi)
        $sisaStokMap = StokBulanan::getSisaStokSaatIniBatch([$obat_id]);
        $sisaStok = $sisaStokMap->get($obat_id, 0);

        // Set sisa_stok sebagai attribute untuk menghindari N+1 di accessor
        $obat->setAttribute('sisa_stok', $sisaStok);

        // Data untuk form stok masuk bulan ini
        $tahunSekarang = now()->year;
        $bulanSekarang = now()->month;

        // Cek apakah sudah ada stok bulanan untuk bulan ini
        $stokBulananIni = StokBulanan::where('obat_id', $obat_id)
            ->where('tahun', $tahunSekarang)
            ->where('bulan', $bulanSekarang)
            ->first();

        return view('stok.show', compact(
            'obat',
            'riwayatStok',
            'sisaStok',
            'tahunSekarang',
            'bulanSekarang',
            'stokBulananIni'
        ));
    }

    /**
     * Download template Excel untuk import stok obat
     */
    public function downloadTemplate()
    {
        $filename = 'template_import_stok_obat_'.date('Y-m-d').'.xlsx';

        // Create new Spreadsheet object
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Import Stok Masuk');

        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator('SIPO ICBP')
            ->setTitle('Template Import Stok Masuk')
            ->setSubject('Template Import Stok Masuk')
            ->setDescription('Template untuk import data stok masuk obat');

        // Header columns
        $headers = ['Nama Obat', '08-2025', '09-2025', '10-2025'];
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
                'startColor' => ['rgb' => '7c3aed'],
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
        $sheet->getStyle('A1:'.$lastColumn.'1')->applyFromArray($headerStyle);

        // Sample data
        $sampleData = [
            ['Allopurinol', '50', '0', '42'],
            ['Ambeven', '10', '30', '60'],
            ['Ambroxol', '70', '88', '30'],
            ['Amlodipin 10Mg', '150', '160', '90'],
            ['Amlodipin 5Mg', '190', '180', '170'],
        ];

        $row = 2;
        foreach ($sampleData as $data) {
            $column = 'A';
            foreach ($data as $value) {
                $sheet->setCellValue($column.$row, $value);
                $column++;
            }
            $row++;
        }

        // Style data
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

        $sheet->getStyle('A2:'.$lastColumn.($row - 1))->applyFromArray($dataStyle);

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(30);
        for ($i = 1; $i < count($headers); $i++) {
            $sheet->getColumnDimension(chr(ord('A') + $i))->setWidth(15);
        }

        // Set row heights
        $sheet->getRowDimension(1)->setRowHeight(25);
        for ($i = 2; $i < $row; $i++) {
            $sheet->getRowDimension($i)->setRowHeight(20);
        }

        // Add notes
        $sheet->setCellValue('A'.($row + 1), 'CATATAN:');
        $sheet->getStyle('A'.($row + 1))->getFont()->setBold(true);

        $notes = [
            '• Nama Obat harus sesuai dengan data di sistem',
            '• Format periode: MM-YYYY (contoh: 08-2025)',
            '• Isi dengan jumlah stok masuk untuk periode tersebut',
            '• Kosongkan jika tidak ada stok masuk di periode tersebut',
        ];

        $noteRow = $row + 2;
        foreach ($notes as $note) {
            $sheet->setCellValue('A'.$noteRow, $note);
            $sheet->getStyle('A'.$noteRow)->getFont()->setItalic(true)->setSize(10);
            $noteRow++;
        }

        // Create Excel file
        $writer = new Xlsx($spreadsheet);

        // Set headers for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    /**
     * Import stok obat dari Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx', 'max:5120'],
        ], [
            'file.required' => 'File harus dipilih',
            'file.mimes' => 'File harus berformat Excel (.xlsx)',
            'file.max' => 'Ukuran file maksimal 5MB',
        ]);

        try {
            $file = $request->file('file');
            $filePath = $file->getRealPath();

            // Read Excel file
            $spreadsheet = IOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();

            // Get highest row and column
            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();

            // Extract headers (first row)
            $headers = [];
            for ($col = 'A'; $col <= $highestColumn; $col++) {
                $cellValue = $sheet->getCell($col.'1')->getValue();
                if ($cellValue !== null) {
                    $headers[] = trim($cellValue);
                } else {
                    break; // Stop at first empty column
                }
            }

            if (empty($headers)) {
                throw new \Exception('File Excel kosong atau tidak valid');
            }

            // Extract periode columns (skip first column which is Nama Obat)
            $periodes = [];
            for ($i = 1; $i < count($headers); $i++) {
                $periode = trim($headers[$i]);
                if (! empty($periode)) {
                    // Validate periode format (MM-YYYY)
                    if (preg_match('/^(\d{2})-(\d{4})$/', $periode, $matches)) {
                        $bulan = (int) $matches[1];
                        $tahun = (int) $matches[2];

                        if ($bulan >= 1 && $bulan <= 12 && $tahun >= 2000 && $tahun <= 2100) {
                            $periodes[] = [
                                'column' => $i,
                                'bulan' => $bulan,
                                'tahun' => $tahun,
                                'text' => $periode,
                            ];
                        }
                    }
                }
            }

            if (empty($periodes)) {
                throw new \Exception('Tidak ada periode valid yang ditemukan. Format periode harus MM-YYYY (contoh: 08-2025)');
            }

            // Read data rows (start from row 2)
            $rowNumber = 2;
            $imported = 0;
            $errors = [];

            for ($row = 2; $row <= $highestRow; $row++) {
                // Get nama obat from first column
                $namaObat = trim($sheet->getCell('A'.$row)->getValue() ?? '');

                // Skip empty rows
                if (empty($namaObat)) {
                    continue;
                }

                // Find obat by name
                $obat = Obat::where('nama_obat', $namaObat)->first();

                if (! $obat) {
                    $errors[] = "Baris $row: Obat '$namaObat' tidak ditemukan di sistem";

                    continue;
                }

                // Process each periode
                foreach ($periodes as $periode) {
                    $columnName = chr(ord('A') + $periode['column']);
                    $stokMasuk = trim($sheet->getCell($columnName.$row)->getValue() ?? '');

                    // Convert to integer, allow empty values
                    $stokMasuk = (empty($stokMasuk) || $stokMasuk === '0') ? 0 : (int) $stokMasuk;

                    if ($stokMasuk > 0) {
                        // Add stok masuk using StokBulanan model
                        StokBulanan::tambahStokMasuk(
                            $obat->id_obat,
                            $periode['tahun'],
                            $periode['bulan'],
                            $stokMasuk
                        );

                        $imported++;
                    }
                }
            }

            $message = "Import stok obat berhasil: $imported data stok masuk ditambahkan";
            $hasErrors = count($errors) > 0;

            if ($hasErrors) {
                $errorMessage = implode(', ', array_slice($errors, 0, 5));
                if (count($errors) > 5) {
                    $errorMessage .= ' ... dan '.(count($errors) - 5).' error lainnya';
                }
                $message .= '. Beberapa error: '.$errorMessage;
            }

            // Return JSON response for AJAX requests
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'data' => [
                        'imported' => $imported,
                        'errors' => $errors,
                    ],
                ]);
            }

            if ($hasErrors) {
                return back()->with('warning', $message);
            }

            return back()->with('success', $message);

        } catch (\Exception $e) {
            Log::error('Import stok error: '.$e->getMessage());

            // Return JSON response for AJAX requests
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal import data stok: '.$e->getMessage(),
                ], 500);
            }

            return back()->with('error', 'Gagal import data stok: '.$e->getMessage());
        }
    }

    /**
     * Download template Excel untuk import stok pakai obat
     */
    public function downloadTemplatePakai()
    {
        $filename = 'template_import_stok_pakai_obat_'.date('Y-m-d').'.xlsx';

        // Create new Spreadsheet object
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Import Stok Pakai');

        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator('SIPO ICBP')
            ->setTitle('Template Import Stok Pakai')
            ->setSubject('Template Import Stok Pakai')
            ->setDescription('Template untuk import data stok pakai obat');

        // Header columns
        $headers = ['Nama Obat', '08-2025', '09-2025', '10-2025'];
        $column = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($column.'1', $header);
            $column++;
        }

        // Style Header
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 12,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'ea580c'],
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
        $sheet->getStyle('A1:'.$lastColumn.'1')->applyFromArray($headerStyle);

        // Sample data
        $sampleData = [
            ['Allopurinol', '25', '15', '30'],
            ['Ambeven', '5', '12', '8'],
            ['Ambroxol', '20', '35', '25'],
            ['Amlodipin 10Mg', '45', '60', '40'],
            ['Amlodipin 5Mg', '30', '25', '35'],
        ];

        $row = 2;
        foreach ($sampleData as $data) {
            $column = 'A';
            foreach ($data as $value) {
                $sheet->setCellValue($column.$row, $value);
                $column++;
            }
            $row++;
        }

        // Style data
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

        $sheet->getStyle('A2:'.$lastColumn.($row - 1))->applyFromArray($dataStyle);

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(30);
        for ($i = 1; $i < count($headers); $i++) {
            $sheet->getColumnDimension(chr(ord('A') + $i))->setWidth(15);
        }

        // Set row heights
        $sheet->getRowDimension(1)->setRowHeight(25);
        for ($i = 2; $i < $row; $i++) {
            $sheet->getRowDimension($i)->setRowHeight(20);
        }

        // Add notes
        $sheet->setCellValue('A'.($row + 1), 'CATATAN:');
        $sheet->getStyle('A'.($row + 1))->getFont()->setBold(true);

        $notes = [
            '• Nama Obat harus sesuai dengan data di sistem',
            '• Format periode: MM-YYYY (contoh: 08-2025)',
            '• Isi dengan jumlah stok pakai untuk periode tersebut',
            '• Kosongkan jika tidak ada stok pakai di periode tersebut',
        ];

        $noteRow = $row + 2;
        foreach ($notes as $note) {
            $sheet->setCellValue('A'.$noteRow, $note);
            $sheet->getStyle('A'.$noteRow)->getFont()->setItalic(true)->setSize(10);
            $noteRow++;
        }

        // Create Excel file
        $writer = new Xlsx($spreadsheet);

        // Set headers for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    /**
     * Import stok pakai obat dari Excel
     */
    public function importPakai(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx', 'max:5120'],
        ], [
            'file.required' => 'File harus dipilih',
            'file.mimes' => 'File harus berformat Excel (.xlsx)',
            'file.max' => 'Ukuran file maksimal 5MB',
        ]);

        try {
            $file = $request->file('file');
            $filePath = $file->getRealPath();

            // Read Excel file
            $spreadsheet = IOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();

            // Get highest row and column
            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();

            // Extract headers (first row)
            $headers = [];
            for ($col = 'A'; $col <= $highestColumn; $col++) {
                $cellValue = $sheet->getCell($col.'1')->getValue();
                if ($cellValue !== null) {
                    $headers[] = trim($cellValue);
                } else {
                    break; // Stop at first empty column
                }
            }

            if (empty($headers)) {
                throw new \Exception('File Excel kosong atau tidak valid');
            }

            // Extract periode columns (skip first column which is Nama Obat)
            $periodes = [];
            for ($i = 1; $i < count($headers); $i++) {
                $periode = trim($headers[$i]);
                if (! empty($periode)) {
                    // Validate periode format (MM-YYYY)
                    if (preg_match('/^(\d{2})-(\d{4})$/', $periode, $matches)) {
                        $bulan = (int) $matches[1];
                        $tahun = (int) $matches[2];

                        if ($bulan >= 1 && $bulan <= 12 && $tahun >= 2000 && $tahun <= 2100) {
                            $periodes[] = [
                                'column' => $i,
                                'bulan' => $bulan,
                                'tahun' => $tahun,
                                'text' => $periode,
                            ];
                        }
                    }
                }
            }

            if (empty($periodes)) {
                throw new \Exception('Tidak ada periode valid yang ditemukan. Format periode harus MM-YYYY (contoh: 08-2025)');
            }

            // Read data rows (start from row 2)
            $rowNumber = 2;
            $imported = 0;
            $errors = [];

            for ($row = 2; $row <= $highestRow; $row++) {
                // Get nama obat from first column
                $namaObat = trim($sheet->getCell('A'.$row)->getValue() ?? '');

                // Skip empty rows
                if (empty($namaObat)) {
                    continue;
                }

                // Find obat by name
                $obat = Obat::where('nama_obat', $namaObat)->first();

                if (! $obat) {
                    $errors[] = "Baris $row: Obat '$namaObat' tidak ditemukan di sistem";

                    continue;
                }

                // Process each periode
                foreach ($periodes as $periode) {
                    $columnName = chr(ord('A') + $periode['column']);
                    $stokPakai = trim($sheet->getCell($columnName.$row)->getValue() ?? '');

                    // Convert to integer, allow empty values
                    $stokPakai = (empty($stokPakai) || $stokPakai === '0') ? 0 : (int) $stokPakai;

                    if ($stokPakai > 0) {
                        // Add stok pakai using StokBulanan model
                        StokBulanan::tambahStokPakai(
                            $obat->id_obat,
                            $periode['tahun'],
                            $periode['bulan'],
                            $stokPakai
                        );

                        $imported++;
                    }
                }
            }

            $message = "Import stok pakai obat berhasil: $imported data stok pakai ditambahkan";
            $hasErrors = count($errors) > 0;

            if ($hasErrors) {
                $errorMessage = implode(', ', array_slice($errors, 0, 5));
                if (count($errors) > 5) {
                    $errorMessage .= ' ... dan '.(count($errors) - 5).' error lainnya';
                }
                $message .= '. Beberapa error: '.$errorMessage;
            }

            // Return JSON response for AJAX requests
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'data' => [
                        'imported' => $imported,
                        'errors' => $errors,
                    ],
                ]);
            }

            if ($hasErrors) {
                return back()->with('warning', $message);
            }

            return back()->with('success', $message);

        } catch (\Exception $e) {
            Log::error('Import stok pakai error: '.$e->getMessage());

            // Return JSON response for AJAX requests
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal import data stok pakai: '.$e->getMessage(),
                ], 500);
            }

            return back()->with('error', 'Gagal import data stok pakai: '.$e->getMessage());
        }
    }
}
