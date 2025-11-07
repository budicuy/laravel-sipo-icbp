<?php

namespace App\Http\Controllers;

use App\Models\Diagnosa;
use App\Models\Obat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class DiagnosaController extends Controller
{
    public function index(Request $request)
    {
        $query = Diagnosa::with('obats:id_obat,nama_obat');

        // Search functionality - pencarian berdasarkan nama diagnosa
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('nama_diagnosa', 'like', '%' . $search . '%');
        }

        // Sorting
        $sortField = $request->get('sort', 'id_diagnosa');
        $sortDirection = $request->get('direction', 'desc');

        // Validasi field yang bisa diurutkan
        $allowedSortFields = ['id_diagnosa', 'nama_diagnosa', 'deskripsi', 'created_at', 'updated_at'];

        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->orderBy('id_diagnosa', 'desc');
        }

        // Pagination dengan opsi custom
        $perPage = $request->get('per_page', 50);
        $allowedPerPage = [50, 100, 150, 200];

        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 50;
        }

        $diagnosas = $query->paginate($perPage);

        return view('diagnosa.index', compact('diagnosas'));
    }

    public function create()
    {
        $obats = Cache::remember('obats_all', 60, function () {
            return Obat::orderBy('nama_obat', 'asc')->get();
        });
        return view('diagnosa.create', compact('obats'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_diagnosa' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'status' => 'required|in:aktif,non-aktif',
            'obat_ids' => 'nullable|array',
            'obat_ids.*' => 'exists:obat,id_obat'
        ], [
            'nama_diagnosa.required' => 'Nama diagnosa wajib diisi',
            'nama_diagnosa.max' => 'Nama diagnosa maksimal 100 karakter',
            'status.required' => 'Status wajib dipilih',
            'status.in' => 'Status harus aktif atau non-aktif',
        ]);

        $diagnosa = Diagnosa::create([
            'nama_diagnosa' => $validated['nama_diagnosa'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'status' => $validated['status'],
        ]);

        // Attach obat yang direkomendasikan
        if (isset($validated['obat_ids']) && count($validated['obat_ids']) > 0) {
            $diagnosa->obats()->attach($validated['obat_ids']);
        }

        // Clear cache
        Cache::forget('obats_all');

        return redirect()->route('diagnosa.index')->with('success', 'Data diagnosa berhasil ditambahkan');
    }

    public function edit($id)
    {
        $diagnosa = Diagnosa::with('obats')->findOrFail($id);
        $obats = Cache::remember('obats_all', 60, function () {
            return Obat::orderBy('nama_obat', 'asc')->get();
        });
        return view('diagnosa.edit', compact('diagnosa', 'obats'));
    }

    public function update(Request $request, $id)
    {
        $diagnosa = Diagnosa::findOrFail($id);

        $validated = $request->validate([
            'nama_diagnosa' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'status' => 'required|in:aktif,non-aktif',
            'obat_ids' => 'nullable|array',
            'obat_ids.*' => 'exists:obat,id_obat'
        ], [
            'nama_diagnosa.required' => 'Nama diagnosa wajib diisi',
            'nama_diagnosa.max' => 'Nama diagnosa maksimal 100 karakter',
            'status.required' => 'Status wajib dipilih',
            'status.in' => 'Status harus aktif atau non-aktif',
        ]);

        $diagnosa->update([
            'nama_diagnosa' => $validated['nama_diagnosa'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'status' => $validated['status'],
        ]);

        // Sync obat yang direkomendasikan
        // Jika obat_ids ada (bahkan jika array kosong), sync dengan nilai tersebut
        // Jika tidak ada sama sekali di request, tetap pertahankan relasi yang ada
        if ($request->has('obat_ids')) {
            $diagnosa->obats()->sync($validated['obat_ids'] ?? []);
        }

        // Clear cache
        Cache::forget('obats_all');

        return redirect()->route('diagnosa.index')->with('success', 'Data diagnosa berhasil diperbarui');
    }

    public function destroy($id)
    {
        Log::info('Destroy method called with id: ' . $id);

        try {
            $diagnosa = Diagnosa::findOrFail($id);
            Log::info('Diagnosa found: ' . $diagnosa->nama_diagnosa);

            // Detach relasi dengan obat sebelum menghapus
            $diagnosa->obats()->detach();
            Log::info('Relationships detached');

            // Hapus diagnosa
            $diagnosa->delete();
            Log::info('Diagnosa deleted');

            // Clear cache
            Cache::forget('obats_all');
            Log::info('Cache cleared');

            return response()->json(['success' => true, 'message' => 'Data diagnosa berhasil dihapus']);
        } catch (\Exception $e) {
            // Log error
            Log::error('Error deleting diagnosa: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json(['success' => false, 'message' => 'Gagal menghapus data diagnosa'], 500);
        }
    }

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Import');

        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator('SIPO ICBP')
            ->setTitle('Template Import Diagnosa')
            ->setSubject('Template Import Diagnosa')
            ->setDescription('Template untuk import data diagnosa');

        // Header columns
        $headers = ['Nama Diagnosa', 'Deskripsi', 'Status', 'Rekomendasi Obat'];
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
                'startColor' => ['rgb' => 'DC2626'],
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

        $sheet->getStyle('A1:D1')->applyFromArray($headerStyle);

        // Add sample data
        $sheet->setCellValue('A2', 'Demam Berdarah');
        $sheet->setCellValue('B2', 'Demam yang disertai ruam merah dan penurunan trombosit');
        $sheet->setCellValue('C2', 'aktif');
        $sheet->setCellValue('D2', 'Paracetamol, Vitamin C');

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

        $sheet->getStyle('A2:D2')->applyFromArray($dataStyle);

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(30);
        $sheet->getColumnDimension('B')->setWidth(50);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(40);

        // Set row heights
        $sheet->getRowDimension(1)->setRowHeight(25);
        $sheet->getRowDimension(2)->setRowHeight(20);

        // Add notes
        $sheet->setCellValue('A4', 'CATATAN:');
        $sheet->setCellValue('A5', '• Nama Diagnosa wajib diisi');
        $sheet->setCellValue('A6', '• Deskripsi bersifat opsional, bisa dikosongkan');
        $sheet->setCellValue('A7', '• Status: aktif atau non-aktif');
        $sheet->setCellValue('A8', '• Rekomendasi Obat: pisahkan dengan koma (,)');
        $sheet->setCellValue('A9', '• Format yang diharapkan: Nama Diagnosa | Deskripsi | Status | Rekomendasi Obat');

        $sheet->getStyle('A4')->getFont()->setBold(true);
        $sheet->getStyle('A5:A9')->getFont()->setItalic(true)->setSize(10);

        // Create Excel file
        $writer = new Xlsx($spreadsheet);
        $filename = 'template_diagnosa_' . date('Y-m-d') . '.xlsx';

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
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv'],
        ], [
            'file.required' => 'File harus dipilih',
            'file.mimes' => 'File harus berformat Excel (.xlsx, .xls) atau CSV (.csv)',
        ]);

        try {
            $file = $request->file('file');
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();

            // Get highest row number
            $highestRow = $sheet->getHighestRow();

            // Skip header row, start from row 2
            $created = 0;
            $updated = 0;
            $errors = [];

            for ($rowNumber = 2; $rowNumber <= $highestRow; $rowNumber++) {
                // Read cell values
                $namaDiagnosa = trim($sheet->getCell('A' . $rowNumber)->getValue() ?? '');
                $deskripsi = trim($sheet->getCell('B' . $rowNumber)->getValue() ?? '');
                $status = trim($sheet->getCell('C' . $rowNumber)->getValue() ?? 'aktif');
                $rekomendasiObat = trim($sheet->getCell('D' . $rowNumber)->getValue() ?? '');

                // Skip empty rows
                if (empty($namaDiagnosa)) {
                    continue;
                }

                // Validate required fields
                if (empty($namaDiagnosa)) {
                    $errors[] = "Baris $rowNumber: Nama Diagnosa tidak boleh kosong";
                    continue;
                }

                // Validate nama diagnosa length
                if (strlen($namaDiagnosa) > 100) {
                    $errors[] = "Baris $rowNumber: Nama Diagnosa maksimal 100 karakter";
                    continue;
                }

                // Validate status
                if (!empty($status) && !in_array($status, ['aktif', 'non-aktif'])) {
                    $errors[] = "Baris $rowNumber: Status harus 'aktif' atau 'non-aktif'";
                    continue;
                }

                // Check if update or create
                $exists = Diagnosa::where('nama_diagnosa', $namaDiagnosa)->exists();

                // Create or update diagnosa
                $diagnosa = Diagnosa::updateOrCreate(
                    ['nama_diagnosa' => $namaDiagnosa],
                    [
                        'deskripsi' => !empty($deskripsi) ? $deskripsi : null,
                        'status' => !empty($status) ? $status : 'aktif',
                    ]
                );

                // Handle rekomendasi obat
                if (!empty($rekomendasiObat)) {
                    // Split by comma and clean up
                    $obatNames = array_map('trim', explode(',', $rekomendasiObat));
                    $obatIds = [];
                    
                    foreach ($obatNames as $obatName) {
                        if (!empty($obatName)) {
                            $obat = Obat::where('nama_obat', 'like', '%' . $obatName . '%')->first();
                            if ($obat) {
                                $obatIds[] = $obat->id_obat;
                            }
                        }
                    }
                    
                    // Sync obat relationships
                    if (!empty($obatIds)) {
                        $diagnosa->obats()->sync($obatIds);
                    }
                } else {
                    // If no rekomendasi obat, clear existing relationships
                    $diagnosa->obats()->detach();
                }

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

    public function bulkDelete(Request $request)
    {
        Log::info('Bulk delete method called');
        Log::info('Request data: ' . json_encode($request->all()));

        try {
            $ids = $request->input('ids', []);
            Log::info('IDs to delete: ' . json_encode($ids));

            if (empty($ids)) {
                Log::info('No IDs provided');
                return response()->json(['success' => false, 'message' => 'Tidak ada data yang dipilih'], 400);
            }

            // Get diagnosas to delete for detaching relationships
            $diagnosas = Diagnosa::whereIn('id_diagnosa', $ids)->get();
            Log::info('Found ' . $diagnosas->count() . ' diagnosas to delete');

            // Detach relationships before deleting
            foreach ($diagnosas as $diagnosa) {
                $diagnosa->obats()->detach();
            }
            Log::info('Relationships detached');

            // Delete the diagnosas
            $count = Diagnosa::whereIn('id_diagnosa', $ids)->delete();
            Log::info('Deleted ' . $count . ' diagnosas');

            // Clear cache
            Cache::forget('obats_all');
            Log::info('Cache cleared');

            return response()->json(['success' => true, 'message' => "{$count} data diagnosa berhasil dihapus!"]);
        } catch (\Exception $e) {
            // Log error
            Log::error('Error bulk deleting diagnosa: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json(['success' => false, 'message' => 'Gagal menghapus data diagnosa'], 500);
        }
    }

    public function export(Request $request)
    {
        $query = Diagnosa::with('obats:id_obat,nama_obat');

        // Apply search filter if exists
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('nama_diagnosa', 'like', '%' . $search . '%');
        }

        // Apply sorting if exists
        $sortField = $request->get('sort', 'id_diagnosa');
        $sortDirection = $request->get('direction', 'desc');
        $allowedSortFields = ['id_diagnosa', 'nama_diagnosa', 'deskripsi', 'created_at', 'updated_at'];

        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->orderBy('id_diagnosa', 'desc');
        }

        // Get all data for export
        $diagnosas = $query->get();

        // Create temporary directory if not exists
        $tempDir = storage_path('app/temp');
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $filename = 'export_diagnosa_' . date('Y-m-d-H-i-s') . '.csv';
        $filePath = $tempDir . '/' . $filename;

        // Open file for writing
        $file = fopen($filePath, 'w');

        // Add UTF-8 BOM for proper Excel display
        fwrite($file, "\xEF\xBB\xBF");

        // CSV Header
        $headers = [
            'NO',
            'NAMA DIAGNOSA',
            'DESKRIPSI',
            'STATUS',
            'REKOMENDASI OBAT'
        ];
        fputcsv($file, $headers, ';');

        // Data rows
        $rowNumber = 1;
        foreach ($diagnosas as $diagnosa) {
            // Get obat names as comma-separated string
            $obatNames = $diagnosa->obats->pluck('nama_obat')->implode(', ');

            $rowData = [
                $rowNumber,
                $diagnosa->nama_diagnosa,
                $diagnosa->deskripsi ?? '',
                $diagnosa->status ?? 'aktif',
                $obatNames
            ];

            fputcsv($file, $rowData, ';');
            $rowNumber++;
        }

        // Add summary info at the bottom
        fputcsv($file, [], ';'); // Empty row
        fputcsv($file, ['SUMMARY:', 'Total Diagnosa: ' . $diagnosas->count(), 'Export Date: ' . date('d/m/Y H:i:s')], ';');

        fclose($file);

        // Download file and delete after
        return response()->download($filePath, $filename)->deleteFileAfterSend(true);
    }
}
