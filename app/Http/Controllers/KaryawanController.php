<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Departemen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class KaryawanController extends Controller
{
    public function index(Request $request)
    {
        // Cache departemen data for better performance
        $departemens = Cache::remember('departemens_all', 60, function () {
            return Departemen::orderBy('nama_departemen')->get();
        });
        $query = Karyawan::with('departemen:id_departemen,nama_departemen');

        if ($request->filled('departemen')) {
            $query->where('id_departemen', $request->input('departemen'));
        }
        if ($request->filled('jenis_kelamin')) {
            $query->where('jenis_kelamin', $request->input('jenis_kelamin'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function ($sub) use ($q) {
                $sub->where('nik_karyawan', 'like', "%$q%")
                    ->orWhere('nama_karyawan', 'like', "%$q%");
            });
        }

        // Handle sorting
        $allowedSorts = ['id_karyawan', 'nik_karyawan', 'nama_karyawan', 'jenis_kelamin', 'id_departemen', 'no_hp', 'tanggal_lahir', 'alamat'];
        $sortField = $request->input('sort', 'id_karyawan');
        $sortDirection = $request->input('direction', 'asc');

        // Validate sort field and direction
        if (!in_array($sortField, $allowedSorts)) {
            $sortField = 'id_karyawan';
        }
        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'asc';
        }

        $query->orderBy($sortField, $sortDirection);

        // Get per_page from request, default to 50
        $perPage = $request->input('per_page', 50);

        // Validate per_page to only allow specific values
        if (!in_array($perPage, [50, 100, 150, 200])) {
            $perPage = 50;
        }

        $karyawans = $query->paginate($perPage)->appends($request->except('page'));
        return view('karyawan.index', compact('karyawans', 'departemens'));
    }

    public function create()
    {
        $departemens = Cache::remember('departemens_all', 60, function () {
            return Departemen::orderBy('nama_departemen')->get();
        });
        return view('karyawan.create', compact('departemens'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nik' => ['required','numeric','min:1','max:999999999999999','unique:karyawan,nik_karyawan'],
            'nama' => ['required','string','max:100'],
            'tanggal_lahir' => ['required','date'],
            'jenis_kelamin' => ['required', Rule::in(['L', 'P', 'J', 'Laki - Laki','Perempuan'])],
            'alamat' => ['required','string'],
            'no_hp' => ['required','regex:/^08\d+$/'],
            'departemen' => ['required','integer','exists:departemen,id_departemen'],
            'foto' => ['nullable','image','max:30'],
            'email' => ['nullable','email','max:100'],
            'bpjs_id' => ['nullable','string','max:50','regex:/^[0-9]+$/'],
        ], [
            'nik.required' => 'NIK karyawan wajib diisi',
            'nik.numeric' => 'NIK karyawan hanya boleh berisi angka',
            'nik.min' => 'NIK karyawan minimal 1 digit',
            'nik.max' => 'NIK karyawan maksimal 15 digit',
            'nik.unique' => 'NIK karyawan sudah terdaftar',
            'nama.required' => 'Nama karyawan wajib diisi',
            'nama.max' => 'Nama karyawan maksimal 100 karakter',
            'tanggal_lahir.required' => 'Tanggal lahir wajib diisi',
            'tanggal_lahir.date' => 'Format tanggal lahir tidak valid',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih',
            'jenis_kelamin.in' => 'Jenis kelamin tidak valid',
            'alamat.required' => 'Alamat wajib diisi',
            'no_hp.required' => 'Nomor HP wajib diisi',
            'no_hp.regex' => 'Nomor HP harus diawali dengan 08',
            'departemen.required' => 'Departemen wajib dipilih',
            'departemen.exists' => 'Departemen tidak valid',
            'foto.image' => 'File harus berupa gambar',
            'foto.max' => 'Ukuran foto maksimal 30KB',
            'email.email' => 'Format email tidak valid',
            'email.max' => 'Email maksimal 100 karakter',
            'bpjs_id.regex' => 'BPJS ID hanya boleh berisi angka',
            'bpjs_id.max' => 'BPJS ID maksimal 50 karakter',
        ]);

        $path = null;
        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('karyawan', 'public');
        }

        Karyawan::create([
            'nik_karyawan' => $validated['nik'],
            'nama_karyawan' => $validated['nama'],
            'tanggal_lahir' => $validated['tanggal_lahir'],
            'jenis_kelamin' => $validated['jenis_kelamin'],
            'alamat' => $validated['alamat'],
            'no_hp' => $validated['no_hp'],
            'id_departemen' => $validated['departemen'],
            'foto' => $path,
            'email' => $validated['email'] ?? null,
            'bpjs_id' => $validated['bpjs_id'] ?? null,
            'status' => 'aktif', // Default status aktif
        ]);

        // Clear cache
        Cache::forget('departemens_all');

        return redirect()->route('karyawan.index')->with('success', 'Karyawan berhasil ditambahkan');
    }

    public function edit(Karyawan $karyawan)
    {
        $departemens = Cache::remember('departemens_all', 60, function () {
            return Departemen::orderBy('nama_departemen')->get();
        });
        return view('karyawan.edit', compact('karyawan','departemens'));
    }

    public function update(Request $request, Karyawan $karyawan)
    {
        $validated = $request->validate([
            'nik' => ['required','numeric','min:1','max:999999999999999', Rule::unique('karyawan','nik_karyawan')->ignore($karyawan->id_karyawan, 'id_karyawan')],
            'nama' => ['required','string','max:100'],
            'tanggal_lahir' => ['required','date'],
            'jenis_kelamin' => ['required', Rule::in(['L', 'P', 'J', 'Laki - Laki','Perempuan'])],
            'alamat' => ['required','string'],
            'no_hp' => ['required','regex:/^08\d+$/'],
            'departemen' => ['required','integer','exists:departemen,id_departemen'],
            'foto' => ['nullable','image','max:30'],
            'email' => ['nullable','email','max:100'],
            'bpjs_id' => ['nullable','string','max:50','regex:/^[0-9]+$/'],
            'status' => ['required', Rule::in(['aktif', 'nonaktif'])],
        ], [
            'nik.required' => 'NIK karyawan wajib diisi',
            'nik.numeric' => 'NIK karyawan hanya boleh berisi angka',
            'nik.min' => 'NIK karyawan minimal 1 digit',
            'nik.max' => 'NIK karyawan maksimal 15 digit',
            'nik.unique' => 'NIK karyawan sudah terdaftar',
            'nama.required' => 'Nama karyawan wajib diisi',
            'nama.max' => 'Nama karyawan maksimal 100 karakter',
            'tanggal_lahir.required' => 'Tanggal lahir wajib diisi',
            'tanggal_lahir.date' => 'Format tanggal lahir tidak valid',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih',
            'jenis_kelamin.in' => 'Jenis kelamin tidak valid',
            'alamat.required' => 'Alamat wajib diisi',
            'no_hp.required' => 'Nomor HP wajib diisi',
            'no_hp.regex' => 'Nomor HP harus diawali dengan 08',
            'departemen.required' => 'Departemen wajib dipilih',
            'departemen.exists' => 'Departemen tidak valid',
            'foto.image' => 'File harus berupa gambar',
            'foto.max' => 'Ukuran foto maksimal 30KB',
            'email.email' => 'Format email tidak valid',
            'email.max' => 'Email maksimal 100 karakter',
            'bpjs_id.regex' => 'BPJS ID hanya boleh berisi angka',
            'bpjs_id.max' => 'BPJS ID maksimal 50 karakter',
            'status.required' => 'Status wajib dipilih',
            'status.in' => 'Status harus aktif atau nonaktif',
        ]);

        $data = [
            'nik_karyawan' => $validated['nik'],
            'nama_karyawan' => $validated['nama'],
            'tanggal_lahir' => $validated['tanggal_lahir'],
            'jenis_kelamin' => $validated['jenis_kelamin'],
            'alamat' => $validated['alamat'],
            'no_hp' => $validated['no_hp'],
            'id_departemen' => $validated['departemen'],
            'email' => $validated['email'] ?? null,
            'bpjs_id' => $validated['bpjs_id'] ?? null,
            'status' => $validated['status'],
        ];

        if ($request->hasFile('foto')) {
            if ($karyawan->foto) {
                Storage::disk('public')->delete($karyawan->foto);
            }
            $data['foto'] = $request->file('foto')->store('karyawan', 'public');
        }

        $karyawan->update($data);

        // Clear cache
        Cache::forget('departemens_all');

        return redirect()->route('karyawan.index')->with('success', 'Karyawan berhasil diperbarui');
    }

    public function destroy(Karyawan $karyawan)
    {
        if ($karyawan->foto) {
            Storage::disk('public')->delete($karyawan->foto);
        }
        $karyawan->delete();

        // Clear cache
        Cache::forget('departemens_all');

        return back()->with('success', 'Karyawan dihapus');
    }

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Import');

        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator('SIPO ICBP')
            ->setTitle('Template Import Karyawan')
            ->setSubject('Template Import Karyawan')
            ->setDescription('Template untuk import data karyawan');

        // Header columns
        $headers = ['NIK', 'Nama', 'Tanggal Lahir', 'Jenis Kelamin', 'Alamat', 'No HP', 'Departemen', 'Email', 'BPJS ID'];
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
                'startColor' => ['rgb' => '4F46E5'],
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

        // Add sample data
        $sheet->setCellValue('A2', '123456789012345');
        $sheet->setCellValue('B2', 'John Doe');
        $sheet->setCellValue('C2', '1990-01-01');
        $sheet->setCellValue('D2', 'L');
        $sheet->setCellValue('E2', 'Jl. Contoh No. 123, Jakarta');
        $sheet->setCellValue('F2', '081234567890');
        $sheet->setCellValue('G2', 'IT');
        $sheet->setCellValue('H2', 'john.doe@email.com');
        // Set BPJS ID as explicit string to preserve leading zeros
        $sheet->setCellValueExplicit('I2', '0001234567890', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

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
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(35);
        $sheet->getColumnDimension('F')->setWidth(15);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(25);
        $sheet->getColumnDimension('I')->setWidth(20);

        // Format kolom C (Tanggal Lahir) dan I (BPJS ID) sebagai Text untuk menjaga format
        $sheet->getStyle('C:C')
            ->getNumberFormat()
            ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);

        $sheet->getStyle('I:I')
            ->getNumberFormat()
            ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);

        // Set row heights
        $sheet->getRowDimension(1)->setRowHeight(25);
        $sheet->getRowDimension(2)->setRowHeight(20);

        // Add notes
        $sheet->setCellValue('A4', 'CATATAN:');
        $sheet->setCellValue('A5', '• NIK minimal 1 karakter dan maksimal 15 karakter');
        $sheet->setCellValue('A6', '• Format Tanggal Lahir: YYYY-MM-DD (contoh: 1990-01-01)');
        $sheet->setCellValue('A7', '• PENTING: Format kolom Tanggal Lahir sebagai TEXT di Excel untuk menjaga format tanggal');
        $sheet->setCellValue('A8', '• Jenis Kelamin: "L" (Laki-laki), "J" (Laki-laki), atau "P" (Perempuan)');
        $sheet->setCellValue('A9', '• No HP harus diawali dengan 08');
        $sheet->setCellValue('A10', '• Email format: contoh@email.com (opsional)');
        $sheet->setCellValue('A11', '• BPJS ID hanya boleh angka, maksimal 50 karakter (opsional)');
        $sheet->setCellValue('A12', '• PENTING: Format kolom BPJS ID sebagai TEXT di Excel untuk menjaga angka 0 di depan');
        $sheet->setCellValue('A13', '• Lihat daftar departemen di sheet "Daftar Departemen"');

        $sheet->getStyle('A4')->getFont()->setBold(true);
        $sheet->getStyle('A5:A12')->getFont()->setItalic(true)->setSize(10);

        // ===== CREATE SECOND SHEET FOR DEPARTMENTS =====
        $departemenSheet = $spreadsheet->createSheet();
        $departemenSheet->setTitle('Daftar Departemen');

        // Get all departments from database
        $departemens = Departemen::orderBy('nama_departemen')->get();

        // Header for department sheet
        $departemenSheet->setCellValue('A1', 'No');
        $departemenSheet->setCellValue('B1', 'Nama Departemen');

        $deptHeaderStyle = [
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

        $departemenSheet->getStyle('A1:B1')->applyFromArray($deptHeaderStyle);

        // Add departments data
        $row = 2;
        foreach ($departemens as $index => $dept) {
            $departemenSheet->setCellValue('A' . $row, $index + 1);
            $departemenSheet->setCellValue('B' . $row, $dept->nama_departemen);

            // Style data rows
            $departemenSheet->getStyle('A' . $row . ':B' . $row)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC'],
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);

            $row++;
        }

        // Set column widths for department sheet
        $departemenSheet->getColumnDimension('A')->setWidth(10);
        $departemenSheet->getColumnDimension('B')->setWidth(30);

        // Set row height
        $departemenSheet->getRowDimension(1)->setRowHeight(25);

        // Add note in department sheet
        $departemenSheet->setCellValue('A' . ($row + 1), 'CATATAN:');
        $departemenSheet->setCellValue('A' . ($row + 2), '• Salin nama departemen yang sesuai ke kolom Departemen di sheet "Template Import"');
        $departemenSheet->setCellValue('A' . ($row + 3), '• Nama departemen harus sama persis dengan yang ada di daftar');

        $departemenSheet->getStyle('A' . ($row + 1))->getFont()->setBold(true);
        $departemenSheet->getStyle('A' . ($row + 2) . ':A' . ($row + 3))->getFont()->setItalic(true)->setSize(10);

        // Set active sheet back to first sheet
        $spreadsheet->setActiveSheetIndex(0);

        // Create Excel file
        $writer = new Xlsx($spreadsheet);
        $filename = 'template_karyawan_' . date('Y-m-d') . '.xlsx';

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

                // Read cell values - use getValue() then convert to string to preserve leading zeros
                $nikCell = $sheet->getCell('A' . $rowNumber)->getValue();
                $nik = $nikCell !== null ? trim((string)$nikCell) : '';

                $nama = trim($sheet->getCell('B' . $rowNumber)->getValue() ?? '');
                // Handle tanggal lahir - konversi dari format Excel ke database format
                $tanggalLahirCell = $sheet->getCell('C' . $rowNumber);
                $tanggalLahirValue = $tanggalLahirCell->getValue();
                $tanggalLahir = null;

                if ($tanggalLahirValue !== null && $tanggalLahirValue !== '') {
                    // Jika ini adalah objek DateTime dari Excel
                    if ($tanggalLahirValue instanceof \DateTime) {
                        $tanggalLahir = $tanggalLahirValue->format('Y-m-d');
                    }
                    // Jika ini adalah string yang sudah dalam format yang benar
                    elseif (is_string($tanggalLahirValue) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggalLahirValue)) {
                        $tanggalLahir = $tanggalLahirValue;
                    }
                    // Jika ini adalah angka serial dari Excel
                    elseif (is_numeric($tanggalLahirValue)) {
                        try {
                            $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($tanggalLahirValue);
                            $tanggalLahir = $date->format('Y-m-d');
                        } catch (\Exception $e) {
                            // Jika konversi gagal, catat error
                            $errors[] = "Baris $rowNumber: Format tanggal lahir tidak valid";
                            continue;
                        }
                    }
                    // Jika ini adalah string dalam format lain, coba konversi
                    elseif (is_string($tanggalLahirValue)) {
                        try {
                            // Coba parsing dengan berbagai format
                            $date = new \DateTime($tanggalLahirValue);
                            $tanggalLahir = $date->format('Y-m-d');
                        } catch (\Exception $e) {
                            // Jika konversi gagal, catat error
                            $errors[] = "Baris $rowNumber: Format tanggal lahir tidak valid. Gunakan format YYYY-MM-DD";
                            continue;
                        }
                    }

                    $tanggalLahir = trim($tanggalLahir ?? '');
                }
                $jenisKelamin = strtoupper(trim($sheet->getCell('D' . $rowNumber)->getValue() ?? ''));
                $alamat = trim($sheet->getCell('E' . $rowNumber)->getValue() ?? '');

                $noHpCell = $sheet->getCell('F' . $rowNumber)->getValue();
                $noHp = $noHpCell !== null ? trim((string)$noHpCell) : '';

                $departemenName = trim($sheet->getCell('G' . $rowNumber)->getValue() ?? '');
                $email = trim($sheet->getCell('H' . $rowNumber)->getValue() ?? '');

                // For BPJS ID, convert to string to preserve leading zeros
                $bpjsIdCell = $sheet->getCell('I' . $rowNumber)->getValue();
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

                // Validate required fields
                if (empty($nama)) {
                    $errors[] = "Baris $rowNumber: Nama tidak boleh kosong";
                    continue;
                }

                // Validate jenis kelamin
                if (!in_array($jenisKelamin, ['L', 'J', 'P', 'LAKI - LAKI', 'PEREMPUAN'])) {
                    $errors[] = "Baris $rowNumber: Jenis kelamin harus 'L', 'J', atau 'P'";
                    continue;
                }

                // Validate no HP
                if (!preg_match('/^08\d+$/', $noHp)) {
                    $errors[] = "Baris $rowNumber: No HP harus diawali dengan 08";
                    continue;
                }

                // Validate email
                if (!empty($email) && strlen($email) > 100) {
                    $errors[] = "Baris $rowNumber: Email maksimal 100 karakter";
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

                // Validate tanggal lahir
                if (empty($tanggalLahir)) {
                    $errors[] = "Baris $rowNumber: Tanggal lahir tidak boleh kosong";
                    continue;
                }

                // Pastikan format tanggal valid
                if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggalLahir)) {
                    $errors[] = "Baris $rowNumber: Format tanggal lahir harus YYYY-MM-DD";
                    continue;
                }

                // Validasi tanggal dengan checkdate
                $dateParts = explode('-', $tanggalLahir);
                if (!checkdate($dateParts[1], $dateParts[2], $dateParts[0])) {
                    $errors[] = "Baris $rowNumber: Tanggal lahir tidak valid";
                    continue;
                }

                // Get or create departemen
                if (empty($departemenName)) {
                    $errors[] = "Baris $rowNumber: Departemen tidak boleh kosong";
                    continue;
                }

                $departemen = Departemen::firstOrCreate(['nama_departemen' => $departemenName]);

                // Check if update or create
                $exists = Karyawan::where('nik_karyawan', $nik)->exists();

                // Create or update karyawan
                Karyawan::updateOrCreate(
                    ['nik_karyawan' => $nik],
                    [
                        'nama_karyawan' => $nama,
                        'tanggal_lahir' => $tanggalLahir,
                        'jenis_kelamin' => $jenisKelamin,
                        'alamat' => $alamat,
                        'no_hp' => $noHp,
                        'id_departemen' => $departemen->id_departemen,
                        'foto' => null,
                        'email' => !empty($email) ? $email : null,
                        'bpjs_id' => !empty($bpjsId) ? $bpjsId : null,
                        'status' => $exists ? Karyawan::where('nik_karyawan', $nik)->first()->status : 'aktif',
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
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'exists:karyawan,id_karyawan']
        ]);

        $ids = $request->input('ids');

        // Get karyawan records to delete their photos
        $karyawans = Karyawan::whereIn('id_karyawan', $ids)->get();

        // Delete photos from storage
        foreach ($karyawans as $karyawan) {
            if ($karyawan->foto) {
                Storage::disk('public')->delete($karyawan->foto);
            }
        }

        // Delete karyawan records
        $deleted = Karyawan::whereIn('id_karyawan', $ids)->delete();

        // Clear cache
        Cache::forget('departemens_all');

        return back()->with('success', "$deleted karyawan berhasil dihapus");
    }

    /**
     * Remove photo from karyawan
     */
    public function removePhoto(Request $request, Karyawan $karyawan)
    {
        if ($karyawan->foto) {
            Storage::disk('public')->delete($karyawan->foto);
            $karyawan->update(['foto' => null]);

            return response()->json([
                'success' => true,
                'message' => 'Foto berhasil dihapus'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Tidak ada foto untuk dihapus'
        ], 404);
    }


}


