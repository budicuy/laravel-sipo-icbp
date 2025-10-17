<?php

namespace App\Http\Controllers;

use App\Models\StokObat;
use App\Models\Obat;
use App\Models\JenisObat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class StokObatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = StokObat::with([
            'obat:id_obat,nama_obat,keterangan,id_jenis_obat,id_satuan',
            'obat.jenisObat:id_jenis_obat,nama_jenis_obat',
            'obat.satuanObat:id_satuan,nama_satuan'
        ]);

        // Filter by periode
        if ($request->has('periode') && $request->periode != '') {
            $query->where('periode', $request->periode);
        }

        // Filter by range periode
        if ($request->has('periode_start') && $request->periode_start != '') {
            $query->where(function($q) use ($request) {
                $startYear = '20' . substr($request->periode_start, 3, 2);
                $startMonth = substr($request->periode_start, 0, 2);
                $q->whereRaw("CONCAT('20', SUBSTRING(periode, 4, 2)) > '$startYear'")
                  ->orWhere(function($subQ) use ($startYear, $startMonth) {
                      $subQ->whereRaw("CONCAT('20', SUBSTRING(periode, 4, 2)) = '$startYear'")
                           ->whereRaw("SUBSTRING(periode, 1, 2) >= '$startMonth'");
                  });
            });
        }
        if ($request->has('periode_end') && $request->periode_end != '') {
            $query->where(function($q) use ($request) {
                $endYear = '20' . substr($request->periode_end, 3, 2);
                $endMonth = substr($request->periode_end, 0, 2);
                $q->whereRaw("CONCAT('20', SUBSTRING(periode, 4, 2)) < '$endYear'")
                  ->orWhere(function($subQ) use ($endYear, $endMonth) {
                      $subQ->whereRaw("CONCAT('20', SUBSTRING(periode, 4, 2)) = '$endYear'")
                           ->whereRaw("SUBSTRING(periode, 1, 2) <= '$endMonth'");
                  });
            });
        }

        // Filter by obat
        if ($request->has('obat') && $request->obat != '') {
            $query->whereHas('obat', function ($q) use ($request) {
                $q->where('nama_obat', 'like', '%' . $request->obat . '%');
            });
        }

        // Filter by jenis obat
        if ($request->has('jenis_obat') && $request->jenis_obat != '') {
            $query->whereHas('obat', function ($q) use ($request) {
                $q->where('id_jenis_obat', $request->jenis_obat);
            });
        }

        // Filter by stok status
        if ($request->has('stok_status') && $request->stok_status != '') {
            switch ($request->stok_status) {
                case 'habis':
                    $query->where('stok_akhir', '<=', 0);
                    break;
                case 'rendah':
                    $query->where('stok_akhir', '>', 0)->where('stok_akhir', '<=', 10);
                    break;
                case 'tersedia':
                    $query->where('stok_akhir', '>', 10);
                    break;
            }
        }

        // Sorting
        $sortField = $request->get('sort', 'id_stok_bulanan');
        $sortDirection = $request->get('direction', 'desc');

        if (in_array($sortField, ['periode', 'nama_obat', 'jenis_obat', 'stok_awal', 'stok_pakai', 'stok_akhir', 'stok_masuk'])) {
            if ($sortField === 'nama_obat') {
                $query->join('obat', 'stok_obat.id_obat', '=', 'obat.id_obat')
                      ->orderBy('obat.nama_obat', $sortDirection)
                      ->select('stok_obat.*');
            } elseif ($sortField === 'jenis_obat') {
                $query->join('obat', 'stok_obat.id_obat', '=', 'obat.id_obat')
                      ->join('jenis_obat', 'obat.id_jenis_obat', '=', 'jenis_obat.id_jenis_obat')
                      ->orderBy('jenis_obat.nama_jenis_obat', $sortDirection)
                      ->select('stok_obat.*');
            } elseif ($sortField === 'periode') {
                // Custom sorting for MM-YY format to sort by year then month
                if ($sortDirection === 'asc') {
                    $query->orderByRaw("SUBSTRING(periode, 4, 2) ASC, SUBSTRING(periode, 1, 2) ASC");
                } else {
                    $query->orderByRaw("SUBSTRING(periode, 4, 2) DESC, SUBSTRING(periode, 1, 2) DESC");
                }
            } else {
                $query->orderBy($sortField, $sortDirection);
            }
        } else {
            // Custom sorting for MM-YY format to sort by year then month (newest first)
            $query->orderByRaw("SUBSTRING(periode, 4, 2) DESC, SUBSTRING(periode, 1, 2) DESC")
                  ->join('obat', 'stok_obat.id_obat', '=', 'obat.id_obat')
                  ->orderBy('obat.nama_obat', 'asc')
                  ->select('stok_obat.*');
        }

        // Pagination
        $perPage = $request->get('per_page', 50);
        $perPage = in_array($perPage, [50, 100, 150, 200]) ? $perPage : 50;

        $stokObats = $query->paginate($perPage);

        // Get available periodes for filter
        $availablePeriodes = StokObat::getAvailablePeriodes();

        // Get reference data
        $jenisObats = Cache::remember('jenis_obats_all', 60, function () {
            return JenisObat::get();
        });

        return view('stok-obat.index', compact(
            'stokObats',
            'availablePeriodes',
            'jenisObats',
            'request'
        ));
    }

    /**
     * Export data stok obat to Excel dengan format periode horizontal
     */
    public function export(Request $request)
    {
        $query = StokObat::with([
            'obat:id_obat,nama_obat,keterangan,id_jenis_obat,id_satuan',
            'obat.jenisObat:id_jenis_obat,nama_jenis_obat',
            'obat.satuanObat:id_satuan,nama_satuan'
        ]);

        // Apply same filters as index
        if ($request->has('periode') && $request->periode != '') {
            $query->where('periode', $request->periode);
        }
        if ($request->has('periode_start') && $request->periode_start != '') {
            $query->where(function($q) use ($request) {
                $startYear = '20' . substr($request->periode_start, 3, 2);
                $startMonth = substr($request->periode_start, 0, 2);
                $q->whereRaw("CONCAT('20', SUBSTRING(periode, 4, 2)) > '$startYear'")
                  ->orWhere(function($subQ) use ($startYear, $startMonth) {
                      $subQ->whereRaw("CONCAT('20', SUBSTRING(periode, 4, 2)) = '$startYear'")
                           ->whereRaw("SUBSTRING(periode, 1, 2) >= '$startMonth'");
                  });
            });
        }
        if ($request->has('periode_end') && $request->periode_end != '') {
            $query->where(function($q) use ($request) {
                $endYear = '20' . substr($request->periode_end, 3, 2);
                $endMonth = substr($request->periode_end, 0, 2);
                $q->whereRaw("CONCAT('20', SUBSTRING(periode, 4, 2)) < '$endYear'")
                  ->orWhere(function($subQ) use ($endYear, $endMonth) {
                      $subQ->whereRaw("CONCAT('20', SUBSTRING(periode, 4, 2)) = '$endYear'")
                           ->whereRaw("SUBSTRING(periode, 1, 2) <= '$endMonth'");
                  });
            });
        }
        if ($request->has('obat') && $request->obat != '') {
            $query->whereHas('obat', function ($q) use ($request) {
                $q->where('nama_obat', 'like', '%' . $request->obat . '%');
            });
        }
        if ($request->has('jenis_obat') && $request->jenis_obat != '') {
            $query->whereHas('obat', function ($q) use ($request) {
                $q->where('id_jenis_obat', $request->jenis_obat);
            });
        }
        if ($request->has('stok_status') && $request->stok_status != '') {
            switch ($request->stok_status) {
                case 'habis':
                    $query->where('stok_akhir', '<=', 0);
                    break;
                case 'rendah':
                    $query->where('stok_akhir', '>', 0)->where('stok_akhir', '<=', 10);
                    break;
                case 'tersedia':
                    $query->where('stok_akhir', '>', 10);
                    break;
            }
        }

        // Get data
        $data = $query->join('obat', 'stok_obat.id_obat', '=', 'obat.id_obat')
                     ->orderBy('obat.nama_obat', 'asc')
                     ->orderByRaw("SUBSTRING(stok_obat.periode, 4, 2) ASC, SUBSTRING(stok_obat.periode, 1, 2) ASC")
                     ->select('stok_obat.*')
                     ->get();

        // Group data by obat
        $groupedData = $data->groupBy('id_obat');

        // Get all unique periodes and sort them chronologically
        $periodes = $data->pluck('periode')->unique()->sort(function($a, $b) {
            // Custom sort for MM-YY format
            $yearA = '20' . substr($a, 3, 2);
            $monthA = substr($a, 0, 2);
            $yearB = '20' . substr($b, 3, 2);
            $monthB = substr($b, 0, 2);

            if ($yearA != $yearB) {
                return $yearA <=> $yearB;
            }
            return $monthA <=> $monthB;
        })->values()->toArray();

        // Create spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Stok Obat');

        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator('SIPO ICBP')
            ->setTitle('Data Stok Obat')
            ->setSubject('Data Stok Obat')
            ->setDescription('Data stok obat perbulan');

        // Header columns - Format dengan periode horizontal
        $headers = [
            'No', 'Nama Obat', 'Satuan'
        ];

        // Add periode headers
        foreach ($periodes as $periode) {
            $headers[] = $periode . ' Awal';
            $headers[] = $periode . ' Pakai';
            $headers[] = $periode . ' Akhir';
            $headers[] = $periode . ' Masuk';
        }

        $column = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($column . '1', $header);
            $column++;
        }

        // Style header
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '059669']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ];

        // Calculate last column index properly
        $lastColumnIndex = count($headers) - 1;
        $lastColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($lastColumnIndex + 1);
        $sheet->getStyle('A1:' . $lastColumn . '1')->applyFromArray($headerStyle);

        // Fill data
        $row = 2;
        $no = 1;

        foreach ($groupedData as $obatId => $items) {
            $obat = $items->first()->obat;

            // Basic info
            $sheet->setCellValue('A' . $row, $no);
            $sheet->setCellValue('B' . $row, $obat->nama_obat);
            $sheet->setCellValue('C' . $row, $obat->satuanObat->nama_satuan ?? '');

            // Create a map of periode to stok data for this obat
            $stokByPeriode = [];
            foreach ($items as $item) {
                $stokByPeriode[$item->periode] = $item;
            }

            // Fill stok data for each periode
            $colIndex = 3; // Start from column D (index 3)
            foreach ($periodes as $periode) {
                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1);
                $colLetter1 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 2);
                $colLetter2 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 3);
                $colLetter3 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 4);

                if (isset($stokByPeriode[$periode])) {
                    $stok = $stokByPeriode[$periode];
                    $sheet->setCellValue($colLetter . $row, $stok->stok_awal);
                    $sheet->setCellValue($colLetter1 . $row, $stok->stok_pakai);
                    $sheet->setCellValue($colLetter2 . $row, $stok->stok_akhir);
                    $sheet->setCellValue($colLetter3 . $row, $stok->stok_masuk);
                } else {
                    // Empty if no data for this periode
                    $sheet->setCellValue($colLetter . $row, '-');
                    $sheet->setCellValue($colLetter1 . $row, '-');
                    $sheet->setCellValue($colLetter2 . $row, '-');
                    $sheet->setCellValue($colLetter3 . $row, '-');
                }
                $colIndex += 4;
            }

            // Style data rows
            $dataStyle = [
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ];

            $sheet->getStyle('A' . $row . ':' . $lastColumn . $row)->applyFromArray($dataStyle);

            $row++;
            $no++;
        }

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(12);

        // Set width for periode columns
        for ($i = 3; $i < count($headers); $i++) {
            $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
            $sheet->getColumnDimension($columnLetter)->setWidth(12);
        }

        // Set row heights for headers
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Add instructions sheet
        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex(1);
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Petunjuk Import');

        // Add instructions
        $instructions = [
            'Petunjuk Import Data Stok Obat:',
            '',
            '1. Format File:',
            '   - Gunakan file Excel (.xlsx, .xls)',
            '   - Pastikan format kolom sesuai dengan template',
            '',
            '2. Struktur Kolom:',
            '   - No: Nomor urut',
            '   - Nama Obat: Nama obat yang sudah ada di sistem',
            '   - Satuan: Satuan obat',
            '   - Periode: Format MM-YY (contoh: 01-25 untuk Januari 2025)',
            '   - Setiap periode memiliki 4 kolom: Awal, Pakai, Akhir, Masuk',
            '',
            '3. Ketentuan:',
            '   - Pastikan nama obat sudah terdaftar di sistem',
            '   - Format periode harus MM-YY',
            '   - Isi hanya dengan angka pada kolom stok',
            '   - Gunakan - untuk nilai kosong',
            '   - Untuk nilai negatif, gunakan format (60) = -60',
            '',
            '4. Contoh Data:',
            '   1, Paracetamol, Tablet, 100, 20, 80, 50, 01-25 Awal, 01-25 Pakai, 01-25 Akhir, 01-25 Masuk',
        ];

        $row = 1;
        foreach ($instructions as $instruction) {
            $sheet->setCellValue('A' . $row, $instruction);
            $row++;
        }

        // Style instructions
        $sheet->getStyle('A1:A' . ($row - 1))->getFont()->setSize(11);
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getColumnDimension('A')->setWidth(80);

        // Set active sheet back to data
        $spreadsheet->setActiveSheetIndex(0);
        $sheet = $spreadsheet->getActiveSheet();

        // Create Excel file
        $writer = new Xlsx($spreadsheet);
        $filename = 'data_stok_obat_' . date('Y-m_d_H-i-s') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $stokObat = StokObat::findOrFail($id);
        $stokObat->delete();

        return response()->json(['success' => true, 'message' => 'Data stok obat berhasil dihapus']);
    }

    /**
     * Bulk delete stok obat
     */
    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'Tidak ada data yang dipilih'], 400);
        }

        StokObat::whereIn('id_stok_obat', $ids)->delete();

        return response()->json(['success' => true, 'message' => count($ids) . ' data stok obat berhasil dihapus']);
    }

    /**
     * Download template untuk import stok obat dengan format horizontal
     */
    public function downloadTemplateStokObat()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Import Stok Obat');

        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator('SIPO ICBP')
            ->setTitle('Template Import Stok Obat')
            ->setSubject('Template Import Stok Obat')
            ->setDescription('Template untuk import data stok obat');

        // Header columns - Format dengan periode horizontal
        $headers = [
            'No', 'Nama Obat', 'Satuan'
        ];

        // Add periode headers (sample 3 months)
        $periodes = ['08-24', '09-24', '10-24'];
        foreach ($periodes as $periode) {
            $headers[] = $periode . ' Awal';
            $headers[] = $periode . ' Pakai';
            $headers[] = $periode . ' Akhir';
            $headers[] = $periode . ' Masuk';
        }

        $column = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($column . '1', $header);
            $column++;
        }

        // Style header
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '059669']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ];

        $lastColumn = chr(ord('A') + count($headers) - 1);
        $sheet->getStyle('A1:' . $lastColumn . '1')->applyFromArray($headerStyle);

        // Get sample data
        $obats = Obat::with(['jenisObat', 'satuanObat'])->limit(5)->get();

        $row = 2;
        $no = 1;
        foreach ($obats as $obat) {
            // Basic info
            $sheet->setCellValue('A' . $row, $no);
            $sheet->setCellValue('B' . $row, $obat->nama_obat);
            $sheet->setCellValue('C' . $row, $obat->satuanObat->nama_satuan ?? '');

            // Fill stok data for each periode
            $colIndex = 3; // Start from column D (index 3)
            foreach ($periodes as $periode) {
                $sheet->setCellValue(chr(ord('A') + $colIndex) . $row, 100); // Sample stok awal
                $sheet->setCellValue(chr(ord('A') + $colIndex + 1) . $row, 20); // Sample stok pakai
                $sheet->setCellValue(chr(ord('A') + $colIndex + 2) . $row, 80); // Sample stok akhir
                $sheet->setCellValue(chr(ord('A') + $colIndex + 3) . $row, 50); // Sample stok masuk
                $colIndex += 4;
            }

            // Style data rows
            $dataStyle = [
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ];

            $sheet->getStyle('A' . $row . ':' . $lastColumn . $row)->applyFromArray($dataStyle);

            $row++;
            $no++;
        }

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(12);

        // Set width for periode columns
        for ($i = 3; $i < count($headers); $i++) {
            $sheet->getColumnDimension(chr(ord('A') + $i))->setWidth(12);
        }

        // Set row heights for headers
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Add instructions sheet
        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex(1);
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Petunjuk Import');

        // Add instructions
        $instructions = [
            'Petunjuk Import Data Stok Obat:',
            '',
            '1. Format File:',
            '   - Gunakan file Excel (.xlsx, .xls)',
            '   - Pastikan format kolom sesuai dengan template',
            '',
            '2. Struktur Kolom:',
            '   - No: Nomor urut',
            '   - Nama Obat: Nama obat yang sudah ada di sistem',
            '   - Satuan: Satuan obat',
            '   - Periode: Format MM-YY (contoh: 01-25 untuk Januari 2025)',
            '   - Setiap periode memiliki 4 kolom: Awal, Pakai, Akhir, Masuk',
            '',
            '3. Ketentuan:',
            '   - Pastikan nama obat sudah terdaftar di sistem',
            '   - Format periode harus MM-YY',
            '   - Isi hanya dengan angka pada kolom stok',
            '   - Gunakan - untuk nilai kosong',
            '   - Untuk nilai negatif, gunakan format (60) = -60',
            '',
            '4. Contoh Data:',
            '   1, Paracetamol, Tablet, 100, 20, 80, 50, 01-25 Awal, 01-25 Pakai, 01-25 Akhir, 01-25 Masuk',
        ];

        $row = 1;
        foreach ($instructions as $instruction) {
            $sheet->setCellValue('A' . $row, $instruction);
            $row++;
        }

        // Style instructions
        $sheet->getStyle('A1:A' . ($row - 1))->getFont()->setSize(11);
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getColumnDimension('A')->setWidth(80);

        // Set active sheet back to data
        $spreadsheet->setActiveSheetIndex(0);
        $sheet = $spreadsheet->getActiveSheet();

        // Create Excel file
        $writer = new Xlsx($spreadsheet);
        $filename = 'template_stok_obat_' . date('Y-m-d') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    /**
     * Import data stok obat dari Excel dengan format horizontal
     */
    public function importStokObat(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls'],
        ], [
            'file.required' => 'File harus dipilih',
            'file.mimes' => 'File harus berformat Excel (.xlsx, .xls)',
        ]);

        try {
            $file = $request->file('file');
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();

            // Get highest row and column
            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();
            $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);

            // Get reference data
            $obats = Obat::pluck('id_obat', 'nama_obat')->toArray();

            // Process data
            $successCount = 0;
            $errorCount = 0;
            $errors = [];

            // Start transaction
            DB::beginTransaction();

            try {
                // Check if this is horizontal format (by checking if there are more than 6 columns)
                $isHorizontalFormat = $highestColumnIndex > 6;

                if ($isHorizontalFormat) {
                    // Process horizontal format
                    $this->processHorizontalFormat($sheet, $highestRow, $highestColumnIndex, $obats, $successCount, $errorCount, $errors);
                } else {
                    // Process vertical format (old format)
                    $this->processVerticalFormat($sheet, $highestRow, $obats, $successCount, $errorCount, $errors);
                }

                DB::commit();

                $message = "Import stok obat selesai: $successCount data berhasil diproses";
                if ($errorCount > 0) {
                    $message .= ", $errorCount data gagal";
                    if (!empty($errors)) {
                        $message .= ". Error: " . implode(', ', array_slice($errors, 0, 5));
                        if (count($errors) > 5) {
                            $message .= ' ... dan ' . (count($errors) - 5) . ' error lainnya';
                        }
                    }
                }

                // Return JSON response for AJAX requests
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => $message,
                        'data' => [
                            'created' => $successCount,
                            'errors' => $errors
                        ]
                    ]);
                }

                return back()->with('success', $message);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('Error importing stok bulanan: ' . $e->getMessage());

            // Return JSON response for AJAX requests
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal import data stok obat: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Gagal import data stok obat: ' . $e->getMessage());
        }
    }

    /**
     * Process horizontal format for stok obat import
     */
    private function processHorizontalFormat($sheet, $highestRow, $highestColumnIndex, $obats, &$successCount, &$errorCount, &$errors)
    {
        // Extract periode headers from row 1 (starting from column D)
        $periodes = [];
        for ($col = 4; $col <= $highestColumnIndex; $col += 4) {
            $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
            $cellValue = $sheet->getCell($columnLetter . '1')->getValue();
            if ($cellValue) {
                // Extract periode from header like "08-24 Awal"
                if (preg_match('/(\d{2}-\d{2})/', $cellValue, $matches)) {
                    $periodes[] = $matches[1];
                }
            }
        }

        // Process each row
        for ($rowNumber = 2; $rowNumber <= $highestRow; $rowNumber++) {
            // Read basic info
            $no = trim($sheet->getCell('A' . $rowNumber)->getValue() ?? '');
            $namaObat = trim($sheet->getCell('B' . $rowNumber)->getValue() ?? '');
            $satuan = trim($sheet->getCell('C' . $rowNumber)->getValue() ?? '');

            // Skip empty rows
            if (empty($namaObat)) {
                continue;
            }

            // Check if obat exists
            if (!isset($obats[$namaObat])) {
                $errors[] = "Baris $rowNumber: Obat '$namaObat' tidak ditemukan di database";
                $errorCount++;
                continue;
            }

            $idObat = $obats[$namaObat];

            // Process stok data for each periode
            $colIndex = 4; // Start from column D (index 4)
            foreach ($periodes as $periode) {
                if ($colIndex + 3 <= $highestColumnIndex) {
                    $colLetter1 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
                    $colLetter2 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1);
                    $colLetter3 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 2);
                    $colLetter4 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 3);

                    $stokAwal = trim($sheet->getCell($colLetter1 . $rowNumber)->getValue() ?? '');
                    $stokPakai = trim($sheet->getCell($colLetter2 . $rowNumber)->getValue() ?? '');
                    $stokAkhir = trim($sheet->getCell($colLetter3 . $rowNumber)->getValue() ?? '');
                    $stokMasuk = trim($sheet->getCell($colLetter4 . $rowNumber)->getValue() ?? '');

                    // Parse stok values
                    $stokAwal = $this->parseStokValue($stokAwal);
                    $stokPakai = $this->parseStokValue($stokPakai);
                    $stokMasuk = $this->parseStokValue($stokMasuk);
                    $stokAkhir = $this->parseStokValue($stokAkhir);

                    // Insert or update stok obat
                    StokObat::updateOrCreate(
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

                    $successCount++;
                }
                $colIndex += 4;
            }
        }
    }

    /**
     * Process vertical format for stok obat import (old format)
     */
    private function processVerticalFormat($sheet, $highestRow, $obats, &$successCount, &$errorCount, &$errors)
    {
        // Skip header row, start from row 2
        for ($rowNumber = 2; $rowNumber <= $highestRow; $rowNumber++) {
            // Read cell values
            $namaObat = trim($sheet->getCell('A' . $rowNumber)->getValue() ?? '');
            $periode = trim($sheet->getCell('B' . $rowNumber)->getValue() ?? '');
            $stokAwal = trim($sheet->getCell('C' . $rowNumber)->getValue() ?? '');
            $stokPakai = trim($sheet->getCell('D' . $rowNumber)->getValue() ?? '');
            $stokMasuk = trim($sheet->getCell('E' . $rowNumber)->getValue() ?? '');
            $stokAkhir = trim($sheet->getCell('F' . $rowNumber)->getValue() ?? '');

            // Skip empty rows
            if (empty($namaObat)) {
                continue;
            }

            // Validate required fields
            if (empty($namaObat)) {
                $errors[] = "Baris $rowNumber: Nama Obat tidak boleh kosong";
                $errorCount++;
                continue;
            }

            if (empty($periode)) {
                $errors[] = "Baris $rowNumber: Periode tidak boleh kosong";
                $errorCount++;
                continue;
            }

            // Validate periode format
            if (!preg_match('/^\d{2}-\d{2}$/', $periode)) {
                $errors[] = "Baris $rowNumber: Format periode salah. Gunakan format MM-YY (contoh: 01-25)";
                $errorCount++;
                continue;
            }

            // Check if obat exists
            if (!isset($obats[$namaObat])) {
                $errors[] = "Baris $rowNumber: Obat '$namaObat' tidak ditemukan di database";
                $errorCount++;
                continue;
            }

            $idObat = $obats[$namaObat];

            // Parse stok values
            $stokAwal = $this->parseStokValue($stokAwal);
            $stokPakai = $this->parseStokValue($stokPakai);
            $stokMasuk = $this->parseStokValue($stokMasuk);
            $stokAkhir = $this->parseStokValue($stokAkhir);

            // Insert or update stok obat
            StokObat::updateOrCreate(
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

            $successCount++;
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
}
