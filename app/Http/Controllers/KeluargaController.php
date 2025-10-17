<?php

namespace App\Http\Controllers;

use App\Models\Keluarga;
use App\Models\Karyawan;
use App\Models\Hubungan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class KeluargaController extends Controller
{
    public function index(Request $request)
    {
        $query = Keluarga::with([
            'karyawan:id_karyawan,nik_karyawan,nama_karyawan',
            'hubungan:kode_hubungan,hubungan'
        ]);

        // Filter pencarian
        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function ($sub) use ($q) {
                $sub->where('nama_keluarga', 'like', "%$q%")
                    ->orWhereHas('karyawan', function($karyawan) use ($q) {
                        $karyawan->where('nik_karyawan', 'like', "%$q%")
                                ->orWhere('nama_karyawan', 'like', "%$q%");
                    });
            });
        }

        // Filter jenis kelamin
        if ($request->filled('jenis_kelamin')) {
            $jenisKelamin = $request->input('jenis_kelamin');
            if ($jenisKelamin === 'L') {
                $query->where('jenis_kelamin', 'Laki - Laki');
            } elseif ($jenisKelamin === 'P') {
                $query->where('jenis_kelamin', 'Perempuan');
            }
        }

        // Filter hubungan
        if ($request->filled('kode_hubungan')) {
            $query->where('kode_hubungan', $request->input('kode_hubungan'));
        }

        // Handle sorting
        $allowedSorts = ['id_keluarga', 'id_karyawan', 'nama_keluarga', 'tanggal_lahir', 'jenis_kelamin', 'kode_hubungan', 'alamat'];
        $sortField = $request->input('sort', 'id_keluarga');
        $sortDirection = $request->input('direction', 'asc');

        if (!in_array($sortField, $allowedSorts)) {
            $sortField = 'id_keluarga';
        }
        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'asc';
        }

        $query->orderBy($sortField, $sortDirection);

        // Pagination
        $perPage = $request->input('per_page', 50);
        if (!in_array($perPage, [50, 100, 150, 200])) {
            $perPage = 50;
        }

        $keluargas = $query->paginate($perPage)->appends($request->except('page'));

        // Get data for filters
        $hubungans = Hubungan::all();

        return view('keluarga.index', compact('keluargas', 'hubungans'));
    }

    public function create()
    {
        $hubungans = Hubungan::all();
        return view('keluarga.create', compact('hubungans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_karyawan' => 'required|exists:karyawan,id_karyawan',
            'nama_keluarga' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:L,P,Laki - Laki,Perempuan',
            'alamat' => 'required|string',
            'kode_hubungan' => 'required|exists:hubungan,kode_hubungan',
            'tanggal_daftar' => 'nullable|date',
            'bpjs_id' => 'nullable|max:50|regex:/^[0-9]+$/',
        ], [
            'id_karyawan.required' => 'Karyawan wajib dipilih',
            'id_karyawan.exists' => 'Karyawan tidak valid',
            'nama_keluarga.required' => 'Nama keluarga wajib diisi',
            'nama_keluarga.max' => 'Nama keluarga maksimal 100 karakter',
            'tanggal_lahir.required' => 'Tanggal lahir wajib diisi',
            'tanggal_lahir.date' => 'Format tanggal lahir tidak valid',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih',
            'jenis_kelamin.in' => 'Jenis kelamin tidak valid',
            'alamat.required' => 'Alamat wajib diisi',
            'kode_hubungan.required' => 'Hubungan keluarga wajib dipilih',
            'kode_hubungan.exists' => 'Hubungan keluarga tidak valid',
            'tanggal_daftar.date' => 'Format tanggal daftar tidak valid',
            'bpjs_id.regex' => 'BPJS ID hanya boleh berisi angka',
            'bpjs_id.max' => 'BPJS ID maksimal 50 karakter',
        ]);

        Keluarga::create($validated);

        return redirect()->route('keluarga.index')->with('success', 'Data keluarga berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $keluarga = Keluarga::findOrFail($id);
        $hubungans = Hubungan::all();

        return view('keluarga.edit', compact('keluarga', 'hubungans'));
    }

    public function update(Request $request, $id)
    {
        $keluarga = Keluarga::findOrFail($id);

        $validated = $request->validate([
            'id_karyawan' => 'required|exists:karyawan,id_karyawan',
            'nama_keluarga' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:L,P,Laki - Laki,Perempuan',
            'alamat' => 'required|string',
            'kode_hubungan' => 'required|exists:hubungan,kode_hubungan',
            'tanggal_daftar' => 'nullable|date',
            'bpjs_id' => 'nullable|max:50|regex:/^[0-9]+$/',
        ], [
            'id_karyawan.required' => 'Karyawan wajib dipilih',
            'id_karyawan.exists' => 'Karyawan tidak valid',
            'nama_keluarga.required' => 'Nama keluarga wajib diisi',
            'nama_keluarga.max' => 'Nama keluarga maksimal 100 karakter',
            'tanggal_lahir.required' => 'Tanggal lahir wajib diisi',
            'tanggal_lahir.date' => 'Format tanggal lahir tidak valid',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih',
            'jenis_kelamin.in' => 'Jenis kelamin tidak valid',
            'alamat.required' => 'Alamat wajib diisi',
            'kode_hubungan.required' => 'Hubungan keluarga wajib dipilih',
            'kode_hubungan.exists' => 'Hubungan keluarga tidak valid',
            'tanggal_daftar.date' => 'Format tanggal daftar tidak valid',
            'bpjs_id.regex' => 'BPJS ID hanya boleh berisi angka',
            'bpjs_id.max' => 'BPJS ID maksimal 50 karakter',
        ]);

        $keluarga->update($validated);

        return redirect()->route('keluarga.index')->with('success', 'Data keluarga berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $keluarga = Keluarga::findOrFail($id);
        $keluarga->delete();

        return redirect()->route('keluarga.index')->with('success', 'Data keluarga berhasil dihapus!');
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:keluarga,id_keluarga'
        ]);

        $count = Keluarga::whereIn('id_keluarga', $request->ids)->delete();

        return redirect()->route('keluarga.index')
            ->with('success', "{$count} data keluarga berhasil dihapus!");
    }

    // API untuk pencarian karyawan (AJAX)
    public function searchKaryawan(Request $request)
    {
        $search = $request->input('q');

        $karyawans = Karyawan::select('id_karyawan', 'nik_karyawan', 'nama_karyawan', 'jenis_kelamin', 'tanggal_lahir', 'alamat')
            ->where(function($query) use ($search) {
                $query->where('nik_karyawan', 'like', "%{$search}%")
                      ->orWhere('nama_karyawan', 'like', "%{$search}%");
            })
            ->limit(10)
            ->get();

        return response()->json($karyawans);
    }

    // Download template Excel untuk import
    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set header
        $headers = ['NIK', 'Nama', 'Tanggal Lahir', 'Kode Hubungan', 'Family Relationship', 'Alamat', 'JK', 'BPJS ID'];
        $sheet->fromArray($headers, null, 'A1');

        // Style header
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ];
        $sheet->getStyle('A1:H1')->applyFromArray($headerStyle);

        // Set column width
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(18);
        $sheet->getColumnDimension('E')->setWidth(25);
        $sheet->getColumnDimension('F')->setWidth(40);
        $sheet->getColumnDimension('G')->setWidth(10);
        $sheet->getColumnDimension('H')->setWidth(20);

        // Set BPJS ID column to TEXT format
        $sheet->getStyle('H2:H1000')->getNumberFormat()->setFormatCode('@');

        // Add sample data
        $sampleData = [
            ['200032', 'Martin Hardja', '1968-03-24', '200032-A', 'Karyawan', 'KEL. PONDOK JAGUNG KEC. SERPONG UTARA JL. SUTERA GARDENIA RT 001/005', 'L', '0001602990617'],
            ['200032', 'Lenawati Sulina', '1968-05-24', '200032-B', 'Spouse', 'KEL. PONDOK JAGUNG KEC. SERPONG UTARA JL. SUTERA GARDENIA RT 001/005', 'P', '0002032192361'],
        ];
        $sheet->fromArray($sampleData, null, 'A2');

        // Add notes
        $sheet->setCellValue('A' . ($sheet->getHighestRow() + 2), 'Catatan:');
        $sheet->setCellValue('A' . ($sheet->getHighestRow() + 1), '1. NIK: Nomor Induk Karyawan (wajib diisi, minimal 1 maksimal 15 karakter)');
        $sheet->setCellValue('A' . ($sheet->getHighestRow() + 1), '2. Nama: Nama lengkap anggota keluarga (wajib diisi)');
        $sheet->setCellValue('A' . ($sheet->getHighestRow() + 1), '3. Tanggal Lahir: Format YYYY-MM-DD contoh: 1990-01-15 (wajib diisi)');
        $sheet->setCellValue('A' . ($sheet->getHighestRow() + 1), '4. Kode Hubungan: NIK-Kode, contoh: 200032-A (wajib diisi)');
        $sheet->setCellValue('A' . ($sheet->getHighestRow() + 1), '5. Family Relationship: Karyawan/Spouse/Anak 1/Anak 2/Anak 3 (opsional, hanya informasi)');
        $sheet->setCellValue('A' . ($sheet->getHighestRow() + 1), '6. Alamat: Alamat lengkap (wajib diisi)');
        $sheet->setCellValue('A' . ($sheet->getHighestRow() + 1), '7. JK: L (Laki-laki) atau P (Perempuan) (wajib diisi)');
        $sheet->setCellValue('A' . ($sheet->getHighestRow() + 1), '8. BPJS ID: Hanya angka, maksimal 50 karakter (opsional, kolom sudah diformat sebagai TEXT untuk mempertahankan leading zeros)');
        $sheet->setCellValue('A' . ($sheet->getHighestRow() + 1), '9. Kode Hubungan menggunakan format: A=Karyawan, B=Suami/Istri, C=Anak Ke-1, D=Anak Ke-2, E=Anak Ke-3');

        $writer = new Xlsx($spreadsheet);
        $filename = 'template_import_keluarga_' . date('Y-m-d_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    // Import data dari Excel
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:5120'
        ]);

        try {
            set_time_limit(300); // 5 minutes for large files

            $file = $request->file('file');
            $spreadsheet = IOFactory::load($file->getPathname());
            $sheet = $spreadsheet->getActiveSheet();
            $highestRow = $sheet->getHighestRow();

            // Skip header row, start from row 2
            $created = 0;
            $updated = 0;
            $errors = [];

            for ($rowNumber = 2; $rowNumber <= $highestRow; $rowNumber++) {
                try {
                    // Read cell values - use getValue() then convert to string to preserve leading zeros
                    $nikCell = $sheet->getCell('A' . $rowNumber)->getValue();
                    $nik = $nikCell !== null ? trim((string)$nikCell) : '';

                    $nama = trim($sheet->getCell('B' . $rowNumber)->getValue() ?? '');

                    $tanggalLahirRaw = $sheet->getCell('C' . $rowNumber)->getValue();
                    $tanggalLahir = $tanggalLahirRaw !== null ? trim((string)$tanggalLahirRaw) : '';

                    $kodeHubungan = trim($sheet->getCell('D' . $rowNumber)->getValue() ?? '');
                    // Column E (Family Relationship) is skipped - just for information
                    $alamat = trim($sheet->getCell('F' . $rowNumber)->getValue() ?? '');
                    $jenisKelamin = strtoupper(trim($sheet->getCell('G' . $rowNumber)->getValue() ?? ''));

                    // For BPJS ID, convert to string to preserve leading zeros
                    $bpjsIdCell = $sheet->getCell('H' . $rowNumber)->getValue();
                    $bpjsId = $bpjsIdCell !== null ? trim((string)$bpjsIdCell) : '';

                    // Skip empty rows
                    if (empty($nik) && empty($nama)) {
                        continue;
                    }

                    // Validate NIK
                    if (strlen($nik) < 1 || strlen($nik) > 15) {
                        $errors[] = "Baris $rowNumber: NIK minimal 1 dan maksimal 15 karakter";
                        continue;
                    }

                    // Find karyawan by NIK
                    $karyawan = Karyawan::where('nik_karyawan', $nik)->first();
                    if (!$karyawan) {
                        $errors[] = "Baris $rowNumber: Karyawan dengan NIK $nik tidak ditemukan";
                        continue;
                    }

                    // Validate required fields
                    if (empty($nama)) {
                        $errors[] = "Baris $rowNumber: Nama tidak boleh kosong";
                        continue;
                    }

                    if (empty($tanggalLahir)) {
                        $errors[] = "Baris $rowNumber: Tanggal lahir tidak boleh kosong";
                        continue;
                    }

                    // Validate jenis kelamin
                    if (!in_array($jenisKelamin, ['L', 'P'])) {
                        $errors[] = "Baris $rowNumber: Jenis kelamin harus 'L' atau 'P'";
                        continue;
                    }

                    if (empty($alamat)) {
                        $errors[] = "Baris $rowNumber: Alamat tidak boleh kosong";
                        continue;
                    }

                    // Parse kode hubungan (format: NIK-Kode, e.g., 200032-A)
                    $hubunganCode = null;
                    if (!empty($kodeHubungan)) {
                        $parts = explode('-', $kodeHubungan);
                        if (count($parts) >= 2) {
                            $hubunganCode = strtoupper(trim($parts[count($parts) - 1])); // Get last part (A, B, C, D, E)
                        }
                    }

                    if (empty($hubunganCode) || !in_array($hubunganCode, ['A', 'B', 'C', 'D', 'E'])) {
                        $errors[] = "Baris $rowNumber: Kode hubungan tidak valid. Format: NIK-Kode (contoh: 200032-A). Kode: A/B/C/D/E";
                        continue;
                    }

                    // Validate BPJS ID
                    if (!empty($bpjsId) && strlen($bpjsId) > 50) {
                        $errors[] = "Baris $rowNumber: BPJS ID maksimal 50 karakter";
                        continue;
                    }

                    // Validate BPJS ID harus angka
                    if (!empty($bpjsId) && !preg_match('/^[0-9]+$/', $bpjsId)) {
                        $errors[] = "Baris $rowNumber: BPJS ID hanya boleh berisi angka";
                        continue;
                    }

                    // Parse tanggal lahir with better error handling
                    $parsedDate = null;

                    // Try different date formats
                    if (is_numeric($tanggalLahir)) {
                        // Excel numeric date format
                        try {
                            $parsedDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($tanggalLahir)->format('Y-m-d');
                        } catch (\Exception $e) {
                            $errors[] = "Baris $rowNumber: Format tanggal lahir Excel tidak valid";
                            continue;
                        }
                    } elseif (preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggalLahir)) {
                        // Already in Y-m-d format
                        $parsedDate = $tanggalLahir;
                    } else {
                        // Try to parse string date
                        try {
                            $timestamp = strtotime($tanggalLahir);
                            if ($timestamp === false) {
                                throw new \Exception('Invalid date');
                            }
                            $parsedDate = date('Y-m-d', $timestamp);
                        } catch (\Exception $e) {
                            $errors[] = "Baris $rowNumber: Format tanggal lahir tidak valid (gunakan format: YYYY-MM-DD atau DD/MM/YYYY)";
                            continue;
                        }
                    }

                    // Prepare data
                    $data = [
                        'id_karyawan' => $karyawan->id_karyawan,
                        'nama_keluarga' => $nama,
                        'tanggal_lahir' => $parsedDate,
                        'jenis_kelamin' => $jenisKelamin === 'L' ? 'Laki - Laki' : 'Perempuan',
                        'alamat' => $alamat,
                        'kode_hubungan' => $hubunganCode,
                        'bpjs_id' => $bpjsId ?: null,
                    ];

                    // Check if keluarga already exists (by karyawan + hubungan)
                    $existing = Keluarga::where('id_karyawan', $karyawan->id_karyawan)
                        ->where('kode_hubungan', $hubunganCode)
                        ->first();

                    if ($existing) {
                        $existing->update($data);
                        $updated++;
                    } else {
                        Keluarga::create($data);
                        $created++;
                    }
                } catch (\Exception $e) {
                    $errors[] = "Baris $rowNumber: " . $e->getMessage();
                    Log::error("Import error at row $rowNumber: " . $e->getMessage());
                    continue;
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

            return redirect()->route('keluarga.index')->with('success', $message);

        } catch (\Exception $e) {
            Log::error('Import failed: ' . $e->getMessage());

            // Return JSON response for AJAX requests
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal import data: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('keluarga.index')
                ->with('error', 'Gagal import data: ' . $e->getMessage());
        }
    }
}
