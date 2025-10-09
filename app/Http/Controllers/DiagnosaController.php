<?php

namespace App\Http\Controllers;

use App\Models\Diagnosa;
use App\Models\Obat;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class DiagnosaController extends Controller
{
    public function index(Request $request)
    {
        $query = Diagnosa::with('obats');

        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_diagnosa', 'like', '%' . $search . '%')
                    ->orWhere('deskripsi', 'like', '%' . $search . '%');
            });
        }

        // Sorting
        $sortField = $request->get('sort', 'id_diagnosa');
        $sortDirection = $request->get('direction', 'desc');

        if (in_array($sortField, ['nama_diagnosa', 'created_at', 'updated_at'])) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->orderBy('id_diagnosa', 'desc');
        }

        $diagnosas = $query->paginate(10);

        return view('diagnosa.index', compact('diagnosas'));
    }

    public function create()
    {
        $obats = Obat::orderBy('nama_obat', 'asc')->get();
        return view('diagnosa.create', compact('obats'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_diagnosa' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'obat_ids' => 'nullable|array',
            'obat_ids.*' => 'exists:obat,id_obat'
        ], [
            'nama_diagnosa.required' => 'Nama diagnosa wajib diisi',
            'nama_diagnosa.max' => 'Nama diagnosa maksimal 100 karakter',
        ]);

        $diagnosa = Diagnosa::create([
            'nama_diagnosa' => $validated['nama_diagnosa'],
            'deskripsi' => $validated['deskripsi'] ?? null,
        ]);

        // Attach obat yang direkomendasikan
        if (isset($validated['obat_ids']) && count($validated['obat_ids']) > 0) {
            $diagnosa->obats()->attach($validated['obat_ids']);
        }

        return redirect()->route('diagnosa.index')->with('success', 'Data diagnosa berhasil ditambahkan');
    }

    public function edit($id)
    {
        $diagnosa = Diagnosa::with('obats')->findOrFail($id);
        $obats = Obat::orderBy('nama_obat', 'asc')->get();
        return view('diagnosa.edit', compact('diagnosa', 'obats'));
    }

    public function update(Request $request, $id)
    {
        $diagnosa = Diagnosa::findOrFail($id);

        $validated = $request->validate([
            'nama_diagnosa' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'obat_ids' => 'nullable|array',
            'obat_ids.*' => 'exists:obat,id_obat'
        ], [
            'nama_diagnosa.required' => 'Nama diagnosa wajib diisi',
            'nama_diagnosa.max' => 'Nama diagnosa maksimal 100 karakter',
        ]);

        $diagnosa->update([
            'nama_diagnosa' => $validated['nama_diagnosa'],
            'deskripsi' => $validated['deskripsi'] ?? null,
        ]);

        // Sync obat yang direkomendasikan
        // Jika obat_ids ada (bahkan jika array kosong), sync dengan nilai tersebut
        // Jika tidak ada sama sekali di request, tetap pertahankan relasi yang ada
        if ($request->has('obat_ids')) {
            $diagnosa->obats()->sync($validated['obat_ids'] ?? []);
        }

        return redirect()->route('diagnosa.index')->with('success', 'Data diagnosa berhasil diperbarui');
    }

    public function destroy($id)
    {
        $diagnosa = Diagnosa::findOrFail($id);
        $diagnosa->obats()->detach(); // Hapus relasi dengan obat
        $diagnosa->delete();

        return redirect()->route('diagnosa.index')->with('success', 'Data diagnosa berhasil dihapus');
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
        $headers = ['Nama Diagnosa', 'Deskripsi'];
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

        $sheet->getStyle('A1:B1')->applyFromArray($headerStyle);

        // Add sample data
        $sheet->setCellValue('A2', 'Demam Berdarah');
        $sheet->setCellValue('B2', 'Demam yang disertai ruam merah dan penurunan trombosit');

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

        $sheet->getStyle('A2:B2')->applyFromArray($dataStyle);

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(30);
        $sheet->getColumnDimension('B')->setWidth(50);

        // Set row heights
        $sheet->getRowDimension(1)->setRowHeight(25);
        $sheet->getRowDimension(2)->setRowHeight(20);

        // Add notes
        $sheet->setCellValue('A4', 'CATATAN:');
        $sheet->setCellValue('A5', '• Nama Diagnosa wajib diisi');
        $sheet->setCellValue('A6', '• Deskripsi bersifat opsional, bisa dikosongkan');
        $sheet->setCellValue('A7', '• Format yang diharapkan: Nama Diagnosa | Deskripsi');

        $sheet->getStyle('A4')->getFont()->setBold(true);
        $sheet->getStyle('A5:A7')->getFont()->setItalic(true)->setSize(10);

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

            // Skip header row, start from row 2
            $created = 0;
            $updated = 0;
            $errors = [];

            for ($rowNumber = 2; $rowNumber <= $highestRow; $rowNumber++) {
                // Read cell values
                $namaDiagnosa = trim($sheet->getCell('A' . $rowNumber)->getValue() ?? '');
                $deskripsi = trim($sheet->getCell('B' . $rowNumber)->getValue() ?? '');

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

                // Check if update or create
                $exists = Diagnosa::where('nama_diagnosa', $namaDiagnosa)->exists();

                // Create or update diagnosa
                Diagnosa::updateOrCreate(
                    ['nama_diagnosa' => $namaDiagnosa],
                    [
                        'deskripsi' => !empty($deskripsi) ? $deskripsi : null,
                    ]
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
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:diagnosa,id_diagnosa'
        ]);

        $count = Diagnosa::whereIn('id_diagnosa', $request->ids)->delete();

        return redirect()->route('diagnosa.index')
            ->with('success', "{$count} data diagnosa berhasil dihapus!");
    }
}
