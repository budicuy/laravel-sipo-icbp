<?php

namespace App\Http\Controllers;

use App\Models\ExternalEmployee;
use App\Models\Vendor;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ExternalEmployeeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = ExternalEmployee::with(['vendor', 'kategori']);

        // Filter by nama (search)
        if ($request->has('search') && $request->search != '') {
            $query->where('nama_employee', 'like', '%' . $request->search . '%');
        }

        // Filter by jenis kelamin
        if ($request->has('jenis_kelamin') && $request->jenis_kelamin != '') {
            $query->where('jenis_kelamin', $request->jenis_kelamin);
        }

        // Filter by vendor
        if ($request->has('id_vendor') && $request->id_vendor != '') {
            $query->byVendor($request->id_vendor);
        }

        // Filter by kategori
        if ($request->has('id_kategori') && $request->id_kategori != '') {
            $query->byKategori($request->id_kategori);
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Apply sorting
        $sortField = $request->get('sort', 'id_external_employee');
        $sortDirection = $request->get('direction', 'asc');

        // Validate sort field to prevent SQL injection
        $allowedSortFields = ['id_external_employee', 'nik_employee', 'nama_employee', 'jenis_kelamin', 'id_vendor', 'id_kategori', 'no_hp', 'status', 'tanggal_lahir'];
        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortDirection);
        }

        $perPage = $request->get('per_page', 50);
        $externalEmployees = $query->paginate($perPage);
        $vendors = Vendor::all();
        $kategoris = Kategori::all();

        return view('external-employee.index', compact('externalEmployees', 'vendors', 'kategoris'));
    }

    public function create()
    {
        $vendors = Vendor::all();
        $kategoris = Kategori::all();
        return view('external-employee.create', compact('vendors', 'kategoris'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nik_employee' => 'required|string|max:20|unique:external_employees,nik_employee',
            'nama_employee' => 'required|string|max:255',
            'kode_rm' => 'required|string|max:50',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:L,P',
            'alamat' => 'required|string',
            'no_hp' => 'required|string|max:15',
            'id_vendor' => 'required|exists:vendors,id_vendor',
            'no_ktp' => 'nullable|string|max:20',
            'bpjs_id' => 'nullable|string|max:20',
            'id_kategori' => 'required|exists:kategoris,id_kategori',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();

        // Handle foto upload
        if ($request->hasFile('foto')) {
            $foto = $request->file('foto');
            $fotoPath = $foto->store('external-employee-foto', 'public');
            $data['foto'] = $fotoPath;
        }

        ExternalEmployee::create($data);

        return redirect()->route('external-employee.index')
            ->with('success', 'Data external employee berhasil ditambahkan');
    }

    public function show($id)
    {
        $externalEmployee = ExternalEmployee::with(['vendor', 'kategori'])->findOrFail($id);
        return view('external-employee.show', compact('externalEmployee'));
    }

    public function edit($id)
    {
        $externalEmployee = ExternalEmployee::findOrFail($id);
        $vendors = Vendor::all();
        $kategoris = Kategori::all();
        return view('external-employee.edit', compact('externalEmployee', 'vendors', 'kategoris'));
    }

    public function update(Request $request, $id)
    {
        $externalEmployee = ExternalEmployee::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nik_employee' => 'required|string|max:20|unique:external_employees,nik_employee,' . $id . ',id_external_employee',
            'nama_employee' => 'required|string|max:255',
            'kode_rm' => 'required|string|max:50',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:L,P',
            'alamat' => 'required|string',
            'no_hp' => 'required|string|max:15',
            'id_vendor' => 'required|exists:vendors,id_vendor',
            'no_ktp' => 'nullable|string|max:20',
            'bpjs_id' => 'nullable|string|max:20',
            'id_kategori' => 'required|exists:kategoris,id_kategori',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();

        // Handle foto upload
        if ($request->hasFile('foto')) {
            // Delete old foto
            if ($externalEmployee->foto) {
                Storage::disk('public')->delete($externalEmployee->foto);
            }

            $foto = $request->file('foto');
            $fotoPath = $foto->store('external-employee-foto', 'public');
            $data['foto'] = $fotoPath;
        }

        $externalEmployee->update($data);

        return redirect()->route('external-employee.index')
            ->with('success', 'Data external employee berhasil diperbarui');
    }

    public function destroy($id)
    {
        $externalEmployee = ExternalEmployee::findOrFail($id);

        // Delete foto if exists
        if ($externalEmployee->foto) {
            Storage::disk('public')->delete($externalEmployee->foto);
        }

        $externalEmployee->delete();

        return redirect()->route('external-employee.index')
            ->with('success', 'Data external employee berhasil dihapus');
    }

    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:csv,xlsx,xls|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first('file')
            ], 422);
        }

        try {
            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();

            // Create temporary directory if not exists
            $tempDir = storage_path('app/temp');
            if (!is_dir($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            // Save file to temporary location
            $filename = 'external_employee_import_' . time() . '.' . $extension;
            $file->move($tempDir, $filename);
            $filePath = $tempDir . '/' . $filename;

            $importCount = 0;
            $skipCount = 0;
            $errors = [];

            // Process file based on extension
            if ($extension === 'csv') {
                $importCount = $this->processCsvImport($filePath, $skipCount, $errors);
            } else {
                $importCount = $this->processExcelImport($filePath, $skipCount, $errors);
            }

            // Clean up temporary file
            unlink($filePath);

            $message = "Import selesai. {$importCount} data berhasil diimport.";
            if ($skipCount > 0) {
                $message .= " {$skipCount} data dilewati karena duplikasi.";
            }
            if (!empty($errors)) {
                $message .= " " . count($errors) . " data gagal diimport.";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'import_count' => $importCount,
                'skip_count' => $skipCount,
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    private function processCsvImport($filePath, &$skipCount, &$errors)
    {
        $importCount = 0;

        if (($handle = fopen($filePath, 'r')) !== FALSE) {
            // Skip header row
            fgetcsv($handle, 1000, ',');

            while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
                try {
                    // Skip empty rows
                    if (empty(array_filter($data))) {
                        continue;
                    }

                    // Map CSV columns to database fields
                    $importData = [
                        'nik_employee' => $data[0] ?? '',
                        'nama_employee' => $data[1] ?? '',
                        'kode_rm' => $data[2] ?? '',
                        'tanggal_lahir' => $data[3] ?? '',
                        'jenis_kelamin' => $data[4] ?? '',
                        'alamat' => $data[5] ?? '',
                        'no_hp' => $data[6] ?? '',
                        'nama_vendor' => $data[7] ?? '',
                        'no_ktp' => $data[8] ?? null,
                        'bpjs_id' => $data[9] ?? null,
                        'kategori' => $data[10] ?? '',
                        'status' => 'aktif'
                    ];

                    $result = $this->processImportData($importData);
                    if ($result['success']) {
                        $importCount++;
                    } elseif ($result['skipped']) {
                        $skipCount++;
                    } else {
                        $errors[] = "Baris " . ($importCount + $skipCount + count($errors) + 2) . ": " . $result['message'];
                    }
                } catch (\Exception $e) {
                    $errors[] = "Baris " . ($importCount + $skipCount + count($errors) + 2) . ": " . $e->getMessage();
                }
            }
            fclose($handle);
        }

        return $importCount;
    }

    private function processExcelImport($filePath, &$skipCount, &$errors)
    {
        $importCount = 0;

        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // Skip header row
            array_shift($rows);

            foreach ($rows as $index => $data) {
                try {
                    // Skip empty rows
                    if (empty(array_filter($data))) {
                        continue;
                    }

                    // Map Excel columns to database fields
                    $importData = [
                        'nik_employee' => $data[0] ?? '',
                        'nama_employee' => $data[1] ?? '',
                        'kode_rm' => $data[2] ?? '',
                        'tanggal_lahir' => $data[3] ?? '',
                        'jenis_kelamin' => $data[4] ?? '',
                        'alamat' => $data[5] ?? '',
                        'no_hp' => $data[6] ?? '',
                        'nama_vendor' => $data[7] ?? '',
                        'no_ktp' => $data[8] ?? null,
                        'bpjs_id' => $data[9] ?? null,
                        'kategori' => $data[10] ?? '',
                        'status' => 'aktif'
                    ];

                    $result = $this->processImportData($importData);
                    if ($result['success']) {
                        $importCount++;
                    } elseif ($result['skipped']) {
                        $skipCount++;
                    } else {
                        $errors[] = "Baris " . ($index + 2) . ": " . $result['message'];
                    }
                } catch (\Exception $e) {
                    $errors[] = "Baris " . ($index + 2) . ": " . $e->getMessage();
                }
            }
        } catch (\Exception $e) {
            throw new \Exception("Error processing Excel file: " . $e->getMessage());
        }

        return $importCount;
    }

    private function processImportData($data)
    {
        // Validate required fields
        if (empty($data['nik_employee']) || empty($data['nama_employee']) || empty($data['kode_rm'])) {
            return ['success' => false, 'skipped' => false, 'message' => 'Field NIK, Nama, dan Kode RM wajib diisi'];
        }

        // Check if NIK already exists
        if (ExternalEmployee::where('nik_employee', $data['nik_employee'])->exists()) {
            return ['success' => false, 'skipped' => true, 'message' => 'NIK sudah ada'];
        }

        // Find or create vendor
        $vendor = Vendor::where('nama_vendor', $data['nama_vendor'])->first();
        if (!$vendor) {
            $vendor = Vendor::create(['nama_vendor' => $data['nama_vendor']]);
        }

        // Process kategori
        $kategori = null;
        if (!empty($data['kategori'])) {
            // Extract kode kategori from format like "X - Guest"
            if (preg_match('/^([xyz])\s*-\s*(.+)$/i', $data['kategori'], $matches)) {
                $kodeKategori = strtolower($matches[1]);
                $namaKategori = $matches[2];

                $kategori = Kategori::where('kode_kategori', $kodeKategori)->first();
                if (!$kategori) {
                    $kategori = Kategori::create([
                        'kode_kategori' => $kodeKategori,
                        'nama_kategori' => $namaKategori
                    ]);
                }
            }
        }

        // Prepare data for insertion
        $insertData = [
            'nik_employee' => $data['nik_employee'],
            'nama_employee' => $data['nama_employee'],
            'kode_rm' => $data['kode_rm'],
            'tanggal_lahir' => $this->parseDate($data['tanggal_lahir']),
            'jenis_kelamin' => $data['jenis_kelamin'],
            'alamat' => $data['alamat'] ?? '',
            'no_hp' => $data['no_hp'],
            'id_vendor' => $vendor->id_vendor,
            'no_ktp' => $data['no_ktp'],
            'bpjs_id' => $data['bpjs_id'],
            'id_kategori' => $kategori ? $kategori->id_kategori : null,
            'status' => $data['status'] ?? 'aktif'
        ];

        // Create external employee
        ExternalEmployee::create($insertData);

        return ['success' => true, 'skipped' => false, 'message' => ''];
    }

    /**
     * Parse date from various formats (Excel serial, text, etc.)
     */
    private function parseDate($dateValue)
    {
        if (empty($dateValue)) {
            return null;
        }

        // If it's already a valid date string (YYYY-MM-DD format)
        if (is_string($dateValue) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateValue)) {
            return $dateValue;
        }

        // If it's a numeric value (Excel serial date)
        if (is_numeric($dateValue)) {
            try {
                // Excel stores dates as days since 1900-01-01 (with 1900 incorrectly considered a leap year)
                // PHP's base date is 1970-01-01
                $excelEpoch = new \DateTime('1899-12-30'); // Excel's epoch adjusted for the leap year bug
                $interval = new \DateInterval('P' . $dateValue . 'D');
                $date = clone $excelEpoch;
                $date->add($interval);
                return $date->format('Y-m-d');
            } catch (\Exception $e) {
                // If parsing fails, return null
                return null;
            }
        }

        // Try to parse as a regular date string
        try {
            $date = new \DateTime($dateValue);
            return $date->format('Y-m-d');
        } catch (\Exception $e) {
            // If parsing fails, return null
            return null;
        }
    }

    public function export(Request $request)
    {
        // Implementation for export functionality can be added here
        return redirect()->route('external-employee.index')
            ->with('info', 'Fitur export akan segera tersedia');
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->ids;

        if (empty($ids)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data yang dipilih'
            ], 400);
        }

        try {
            // Get employees to delete their photos
            $employees = ExternalEmployee::whereIn('id', $ids)->get();

            foreach ($employees as $employee) {
                // Delete photo if exists
                if ($employee->foto) {
                    Storage::disk('public')->delete($employee->foto);
                }
            }

            // Delete employees
            ExternalEmployee::whereIn('id', $ids)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Berhasil menghapus ' . count($ids) . ' data external employee'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function downloadTemplate()
    {
        // Create template directory if not exists
        $templateDir = public_path('templates');
        if (!is_dir($templateDir)) {
            mkdir($templateDir, 0755, true);
        }

        $filePath = $templateDir . '/external-employee-template.csv';

        // Create a simple CSV template
        $csvContent = "nik_employee,nama_employee,kode_rm,tanggal_lahir,jenis_kelamin,alamat,no_hp,nama_vendor,no_ktp,bpjs_id,kategori\n";
        $csvContent .= "80007053,John Doe,80007053-F,1993-09-09,L,Jakarta,082122,PT. Tropis Service,6372040909930005,0001547298944,X - Guest\n";
        $csvContent .= "80007054,Jane Smith,80007054-F,1995-05-15,P,Bandung,082123,PT. Tropis Service,6372051505950006,0001547298945,Y - Outsourcing\n";
        $csvContent .= "80007055,Robert Johnson,80007055-F,1990-12-20,L,Surabaya,082124,PT. Mitra Sejati,6372122009900007,0001547298946,Z - Supporting\n";

        file_put_contents($filePath, $csvContent);

        return response()->download($filePath, 'external-employee-template.csv')->deleteFileAfterSend(true);
    }
}
