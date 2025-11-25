<?php

namespace App\Http\Controllers;

use App\Models\MedicalArchives;
use App\Models\RekamMedis;
use App\Models\Keluhan;
use App\Models\Keluarga;
use App\Models\Karyawan;
use App\Models\Departemen;
use App\Models\Hubungan;
use App\Models\User;
use App\Models\SuratRekomendasiMedis;
use App\Models\MedicalCheckUp;
use App\Models\KondisiKesehatan;
use App\Services\MedicalArchivesQueryOptimizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class MedicalArchivesController extends Controller
{
    // ========================================
    // MAIN MEDICAL ARCHIVES METHODS
    // ========================================

    /**
     * Display a listing of medical archives with filters
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 50);
        $search = $request->get('q');
        $departmentFilter = $request->get('department');
        $statusFilter = $request->get('status');
        $yearFilter = $request->get('year');
        
        // New chart-specific filters
        $kondisiKesehatanFilter = $request->get('kondisi_kesehatan');
        $keteranganBmiFilter = $request->get('keterangan_bmi');
        $catatanFilter = $request->get('catatan');
        
        // Get medical archives with filters using optimized query
        $medicalArchives = MedicalArchivesQueryOptimizer::getEmployeeMedicalRecords(
            $perPage,
            $search,
            $departmentFilter,
            $statusFilter,
            $yearFilter,
            $kondisiKesehatanFilter,
            $keteranganBmiFilter,
            $catatanFilter
        );
        
        // Get departments for filter dropdown using optimized query
        $departments = MedicalArchivesQueryOptimizer::getDepartments();
        
        // Get available years for filter dropdown
        $availableYears = MedicalArchivesQueryOptimizer::getAvailableYears();
        
        // Get chart data using optimized query
        $chartData = MedicalArchivesQueryOptimizer::getChartData(
            $search,
            $departmentFilter,
            $statusFilter,
            $yearFilter,
            $kondisiKesehatanFilter,
            $keteranganBmiFilter,
            $catatanFilter
        );
        
        // Validate and ensure chart data structure is correct
        if (!isset($chartData['kondisiKesehatan']) || !is_iterable($chartData['kondisiKesehatan'])) {
            $chartData['kondisiKesehatan'] = collect(['Tidak Ada Data' => 1]);
        }
        
        if (!isset($chartData['keteranganBmi']) || !is_iterable($chartData['keteranganBmi'])) {
            $chartData['keteranganBmi'] = collect(['Tidak Ada Data' => 1]);
        }
        
        if (!isset($chartData['catatan']) || !is_iterable($chartData['catatan'])) {
            $chartData['catatan'] = collect(['Tidak Ada Data' => 1]);
        }
        
        // Get status options (employee status)
        $statusOptions = [
            'aktif' => 'Aktif',
            'nonaktif' => 'Non-Aktif',
        ];
        
        return view('medical-archives.index', compact(
            'medicalArchives',
            'departments',
            'statusOptions',
            'availableYears',
            'perPage',
            'search',
            'departmentFilter',
            'statusFilter',
            'yearFilter',
            'kondisiKesehatanFilter',
            'keteranganBmiFilter',
            'catatanFilter',
            'chartData'
        ));
    }
    
    /**
     * Display detailed medical records for specific employee
     */
    public function show($id_karyawan)
    {
        // Get employee medical history using optimized query
        $medicalHistory = MedicalArchivesQueryOptimizer::getEmployeeMedicalHistory($id_karyawan);
        
        if ($medicalHistory->isEmpty()) {
            return redirect()->route('medical-archives.index')
                ->with('error', 'Data medis karyawan tidak ditemukan');
        }
        
        // Get employee info
        $employee = $medicalHistory->first();
        
        // Get complete employee information using optimized query
        $employeeInfo = MedicalArchivesQueryOptimizer::getEmployeeInfo($id_karyawan);
        
        // Group medical records by family member
        $groupedRecords = $medicalHistory->groupBy('id_keluarga');
        
        // Get detailed medical information for each visit
        $detailedRecords = [];
        foreach ($groupedRecords as $id_keluarga => $records) {
            $familyMember = $records->first();
            $medicalVisits = [];
            
            foreach ($records as $record) {
                if ($record->id_rekam) {
                    // Get detailed medical information with complaints and diagnoses using optimized query
                    $visitDetails = MedicalArchivesQueryOptimizer::getDetailedMedicalVisits($record->id_rekam);
                    
                    if ($visitDetails) {
                        $medicalVisits[] = $visitDetails;
                    }
                }
            }
            
            // Get complete family member information including birth date using optimized query
            $familyMemberInfo = MedicalArchivesQueryOptimizer::getFamilyMemberCompleteInfo($id_keluarga);
            
            $detailedRecords[] = [
                'family_member' => $familyMemberInfo ?: $familyMember,
                'visits' => $medicalVisits
            ];
        }
        
        // Get document counts
        $suratRekomendasiCount = SuratRekomendasiMedis::where('id_karyawan', $id_karyawan)->count();
        $medicalCheckUpCount = MedicalCheckUp::where('id_karyawan', $id_karyawan)->count();
        
        return view('medical-archives.show', compact(
            'employee',
            'employeeInfo',
            'detailedRecords',
            'suratRekomendasiCount',
            'medicalCheckUpCount'
        ));
    }

    // ========================================
    // REDIRECT METHODS (LEGACY SUPPORT)
    // ========================================

    /**
     * Redirect to rekam-medis create (medical archives are created through that system)
     */
    public function create()
    {
        return redirect()->route('rekam-medis.choose-type')
            ->with('info', 'Silakan pilih jenis rekam medis yang ingin dibuat');
    }
    
    /**
     * Redirect to rekam-medis store (medical archives are created through that system)
     */
    public function store(Request $request)
    {
        return redirect()->route('rekam-medis.choose-type')
            ->with('info', 'Silakan pilih jenis rekam medis yang ingin dibuat');
    }
    
    /**
     * Redirect to rekam-medis edit (medical archives are edited through that system)
     */
    public function edit($id_karyawan)
    {
        // Medical archives are edited through rekam-medis system using optimized query
        $medicalHistory = MedicalArchivesQueryOptimizer::getEmployeeMedicalHistory($id_karyawan);
        
        if ($medicalHistory->isEmpty()) {
            return redirect()->route('medical-archives.index')
                ->with('error', 'Data medis karyawan tidak ditemukan');
        }
        
        // Get the latest medical archive to edit
        $latestRecord = $medicalHistory
            ->where('id_rekam', '!==', null)
            ->sortByDesc('tanggal_periksa')
            ->first();
            
        if (!$latestRecord) {
            return redirect()->route('medical-archives.show', $id_karyawan)
                ->with('error', 'Tidak ada rekam medis yang dapat diedit');
        }
        
        return redirect()->route('rekam-medis.edit', $latestRecord->id_rekam);
    }
    
    /**
     * Redirect to medical archives show (medical archives are updated through rekam-medis system)
     */
    public function update(Request $request, $id_karyawan)
    {
        return redirect()->route('medical-archives.show', $id_karyawan)
            ->with('info', 'Data medis diperbarui melalui sistem rekam medis');
    }
    
    /**
     * Medical archives cannot be deleted through this method
     */
    public function destroy($id_karyawan)
    {
        return redirect()->route('medical-archives.index')
            ->with('error', 'Data medis tidak dapat dihapus melalui menu ini. Silakan gunakan menu Rekam Medis.');
    }

    // ========================================
    // API METHODS
    // ========================================

    /**
     * API endpoint for searching employees
     */
    public function searchEmployees(Request $request)
    {
        $search = $request->get('q');
        
        // Search employees using optimized query
        $employees = MedicalArchivesQueryOptimizer::searchEmployees($search);
            
        return response()->json($employees);
    }

    // ========================================
    // SURAT REKOMENDASI MEDIS METHODS
    // ========================================

    /**
     * Display Surat Rekomendasi Medis page for employee
     */
    public function suratRekomendasiMedis($id_karyawan)
    {
        // Get employee information using optimized query
        $employeeInfo = MedicalArchivesQueryOptimizer::getEmployeeInfo($id_karyawan);
            
        if (!$employeeInfo) {
            return redirect()->route('medical-archives.index')
                ->with('error', 'Data karyawan tidak ditemukan');
        }
        
        // Get family member (using first family member for demo)
        $familyMember = DB::table('keluarga as kl')
            ->select([
                'kl.id_keluarga',
                'kl.nama_keluarga',
                'kl.no_rm',
                'kl.kode_hubungan',
                'kl.tanggal_lahir',
                'kl.jenis_kelamin',
                'h.hubungan as hubungan_nama'
            ])
            ->leftJoin('hubungan as h', 'kl.kode_hubungan', '=', 'h.kode_hubungan')
            ->where('kl.id_karyawan', $id_karyawan)
            ->first();
            
        // Get surat rekomendasi medis data
        $suratRekomendasi = SuratRekomendasiMedis::where('id_karyawan', $id_karyawan)
            ->orderBy('tanggal', 'desc')
            ->get();
        
        // Get document count
        $suratRekomendasiCount = SuratRekomendasiMedis::where('id_karyawan', $id_karyawan)->count();
        
        return view('medical-archives.surat-rekomendasi-medis', compact(
            'employeeInfo',
            'familyMember',
            'suratRekomendasi',
            'suratRekomendasiCount',
            'id_karyawan'
        ));
    }

    /**
     * Upload new surat rekomendasi medis
     */
    public function uploadSuratRekomendasi(Request $request, $id_karyawan)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:pdf|max:5120', // Max 5MB
            'tanggal' => 'required|date',
            'penerbit_surat' => 'required|string|max:255',
            'catatan_medis' => 'nullable|string|max:1000',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            // Get family member
            $familyMember = DB::table('keluarga as kl')
                ->where('kl.id_karyawan', $id_karyawan)
                ->first();
                
            // Handle file upload
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('surat-rekomendasi-medis', $fileName, 'public');
            
            // Create surat rekomendasi record
            $suratRekomendasi = SuratRekomendasiMedis::create([
                'id_karyawan' => $id_karyawan,
                'id_keluarga' => $familyMember->id_keluarga ?? null,
                'tanggal' => $request->tanggal,
                'penerbit_surat' => $request->penerbit_surat,
                'catatan_medis' => $request->catatan_medis,
                'file_path' => $filePath,
                'file_name' => $fileName,
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'created_by' => auth()->id(),
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Surat rekomendasi medis berhasil diunggah',
                'data' => $suratRekomendasi
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengunggah file: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get surat rekomendasi medis data for editing
     */
    public function editSuratRekomendasi($id_karyawan, $id)
    {
        try {
            $suratRekomendasi = SuratRekomendasiMedis::where('id_karyawan', $id_karyawan)
                ->where('id', $id)
                ->firstOrFail();
                
            return response()->json([
                'success' => true,
                'data' => $suratRekomendasi
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data surat rekomendasi medis tidak ditemukan'
            ], 404);
        }
    }
    
    /**
     * Update existing surat rekomendasi medis
     */
    public function updateSuratRekomendasi(Request $request, $id_karyawan, $id)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'tanggal' => 'required|date',
            'penerbit_surat' => 'required|string|max:255',
            'catatan_medis' => 'nullable|string|max:1000',
            'file' => 'nullable|file|mimes:pdf|max:10240', // Max 10MB, optional for edit
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            $suratRekomendasi = SuratRekomendasiMedis::where('id_karyawan', $id_karyawan)
                ->where('id', $id)
                ->firstOrFail();
            
            // Update data
            $suratRekomendasi->tanggal = $request->tanggal;
            $suratRekomendasi->penerbit_surat = $request->penerbit_surat;
            $suratRekomendasi->catatan_medis = $request->catatan_medis;
            
            // Handle file upload if new file is provided
            if ($request->hasFile('file')) {
                // Delete old file
                Storage::disk('public')->delete($suratRekomendasi->file_path);
                
                // Upload new file
                $file = $request->file('file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('surat-rekomendasi-medis', $fileName, 'public');
                
                $suratRekomendasi->file_path = $filePath;
                $suratRekomendasi->file_name = $fileName;
                $suratRekomendasi->file_size = $file->getSize();
                $suratRekomendasi->mime_type = $file->getMimeType();
            }
            
            $suratRekomendasi->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Surat rekomendasi medis berhasil diperbarui',
                'data' => $suratRekomendasi
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui surat: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download surat rekomendasi medis file
     */
    public function downloadSuratRekomendasi($id_karyawan, $id)
    {
        $suratRekomendasi = SuratRekomendasiMedis::where('id_karyawan', $id_karyawan)
            ->where('id', $id)
            ->firstOrFail();
            
        $filePath = storage_path('app/public/' . $suratRekomendasi->file_path);
        
        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File tidak ditemukan');
        }
        
        return response()->download($filePath, $suratRekomendasi->file_name);
    }
    
    /**
     * Delete surat rekomendasi medis
     */
    public function deleteSuratRekomendasi($id_karyawan, $id)
    {
        try {
            $suratRekomendasi = SuratRekomendasiMedis::where('id_karyawan', $id_karyawan)
                ->where('id', $id)
                ->firstOrFail();
                
            // Delete file from storage
            Storage::disk('public')->delete($suratRekomendasi->file_path);
            
            // Delete record from database
            $suratRekomendasi->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Surat rekomendasi medis berhasil dihapus'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus surat: ' . $e->getMessage()
            ], 500);
        }
    }

    // ========================================
    // MEDICAL CHECK UP METHODS
    // ========================================

    /**
     * Display Medical Check Up page for employee
     */
    public function medicalCheckUp($id_karyawan)
    {
        // Get employee information using optimized query
        $employeeInfo = MedicalArchivesQueryOptimizer::getEmployeeInfo($id_karyawan);
            
        if (!$employeeInfo) {
            return redirect()->route('medical-archives.index')
                ->with('error', 'Data karyawan tidak ditemukan');
        }
        
        // Get family member (using first family member for demo)
        $familyMember = DB::table('keluarga as kl')
            ->select([
                'kl.id_keluarga',
                'kl.nama_keluarga',
                'kl.no_rm',
                'kl.kode_hubungan',
                'kl.tanggal_lahir',
                'kl.jenis_kelamin',
                'h.hubungan as hubungan_nama'
            ])
            ->leftJoin('hubungan as h', 'kl.kode_hubungan', '=', 'h.kode_hubungan')
            ->where('kl.id_karyawan', $id_karyawan)
            ->first();
            
        // Get medical check up data with optimized query to avoid N+1 issues
        $medicalCheckUp = DB::table('medical_check_up as mc')
            ->select([
                'mc.id_medical_check_up',
                'mc.id_karyawan',
                'mc.id_keluarga',
                'mc.periode',
                'mc.tanggal',
                'mc.dikeluarkan_oleh',
                'mc.bmi',
                'mc.keterangan_bmi',
                'mc.catatan',
                'mc.file_path',
                'mc.file_name',
                'mc.file_size',
                'mc.mime_type',
                'mc.id_user',
                'mc.created_at',
                'mc.updated_at',
                // Get kondisi kesehatan names as aggregated JSON string
                DB::raw("GROUP_CONCAT(DISTINCT kk.nama_kondisi SEPARATOR ', ') as kondisi_kesehatan_names"),
                // Get kondisi kesehatan IDs as aggregated string
                DB::raw("GROUP_CONCAT(DISTINCT kk.id SEPARATOR ',') as kondisi_kesehatan_ids")
            ])
            ->leftJoin('medical_check_up_kondisi_kesehatan as mck', 'mc.id_medical_check_up', '=', 'mck.id_medical_check_up')
            ->leftJoin('kondisi_kesehatan as kk', 'mck.id_kondisi_kesehatan', '=', 'kk.id')
            ->where('mc.id_karyawan', $id_karyawan)
            ->groupBy(
                'mc.id_medical_check_up',
                'mc.id_karyawan',
                'mc.id_keluarga',
                'mc.periode',
                'mc.tanggal',
                'mc.dikeluarkan_oleh',
                'mc.bmi',
                'mc.keterangan_bmi',
                'mc.catatan',
                'mc.file_path',
                'mc.file_name',
                'mc.file_size',
                'mc.mime_type',
                'mc.id_user',
                'mc.created_at',
                'mc.updated_at'
            )
            ->orderBy('mc.tanggal', 'desc')
            ->get();
            
        // Transform the results to include kondisiKesehatan relationship
        $medicalCheckUp->transform(function ($item) {
            // Parse kondisi kesehatan names and IDs
            $kondisiNames = $item->kondisi_kesehatan_names ? explode(', ', $item->kondisi_kesehatan_names) : [];
            $kondisiIds = $item->kondisi_kesehatan_ids ? explode(',', $item->kondisi_kesehatan_ids) : [];
            
            // Create kondisiKesehatan collection
            $kondisiKesehatan = collect();
            foreach ($kondisiNames as $index => $name) {
                if (!empty($name) && isset($kondisiIds[$index])) {
                    $kondisiKesehatan->push((object)[
                        'id' => $kondisiIds[$index],
                        'nama_kondisi' => $name
                    ]);
                }
            }
            
            // Add the relationship to the item
            $item->kondisiKesehatan = $kondisiKesehatan;
            
            // Remove the temporary fields
            unset($item->kondisi_kesehatan_names);
            unset($item->kondisi_kesehatan_ids);
            
            return $item;
        });
            
        // Get kondisi kesehatan list for dropdown
        $kondisiKesehatanList = KondisiKesehatan::orderBy('nama_kondisi')->get();
        
        return view('medical-archives.medical-check-up', compact(
            'employeeInfo',
            'familyMember',
            'medicalCheckUp',
            'kondisiKesehatanList',
            'id_karyawan'
        ));
    }

    /**
     * Upload new medical check up
     */
    public function uploadMedicalCheckUp(Request $request, $id_karyawan)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'file' => 'nullable|file|mimes:pdf|max:5120', // Max 5MB, optional
            'periode' => 'required|integer|min:2000|max:2100',
            'tanggal' => 'required|date',
            'dikeluarkan_oleh' => 'required|string|max:255',
            'bmi' => 'nullable|numeric|min:0|max:999',
            'keterangan_bmi' => ['nullable', Rule::in(['Underweight', 'Normal', 'Overweight', 'Obesitas Tk 1', 'Obesitas Tk 2', 'Obesitas Tk 3'])],
            'id_kondisi_kesehatan' => 'nullable|array',
            'id_kondisi_kesehatan.*' => 'nullable|integer|exists:kondisi_kesehatan,id',
            'catatan' => ['nullable', Rule::in(['Fit', 'Fit dengan Catatan', 'Fit dalam Pengawasan'])],
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            // Get family member
            $familyMember = DB::table('keluarga as kl')
                ->where('kl.id_karyawan', $id_karyawan)
                ->first();
                
            // Prepare data for creation
            $data = [
                'id_karyawan' => $id_karyawan,
                'id_keluarga' => $familyMember ? $familyMember->id_keluarga : null,
                'id_user' => auth()->id(),
                'periode' => $request->periode,
                'tanggal' => $request->tanggal,
                'dikeluarkan_oleh' => $request->dikeluarkan_oleh,
                'bmi' => $request->bmi,
                'keterangan_bmi' => $request->keterangan_bmi,
                'catatan' => $request->catatan,
            ];
                
            // Handle file upload if provided
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('medical-check-ups', $fileName, 'public');
                
                $data['file_path'] = $filePath;
                $data['file_name'] = $fileName;
                $data['file_size'] = $file->getSize();
                $data['mime_type'] = $file->getMimeType();
            }
            
            // Create medical check up record
            $medicalCheckUp = MedicalCheckUp::create($data);
            
            // Sync kondisi kesehatan relationships
            if ($request->id_kondisi_kesehatan && is_array($request->id_kondisi_kesehatan)) {
                // Filter out empty values
                $kondisiIds = array_filter($request->id_kondisi_kesehatan, function($value) {
                    return $value !== null && $value !== '';
                });
                
                if (!empty($kondisiIds)) {
                    $medicalCheckUp->kondisiKesehatan()->sync($kondisiIds);
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Medical check up berhasil diunggah',
                'data' => $medicalCheckUp
            ]);
            
        } catch (\Exception $e) {
            // Log error for debugging
            \Log::error('Medical Check Up Upload Error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            \Log::error('Request data: ' . json_encode(request()->all()));
            \Log::error('Session ID: ' . session()->getId());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengunggah file: ' . $e->getMessage(),
                'debug_info' => [
                    'error_message' => $e->getMessage(),
                    'error_code' => $e->getCode(),
                    'error_file' => $e->getFile(),
                    'error_line' => $e->getLine(),
                    'request_data' => request()->all(),
                    'session_id' => session()->getId()
                ]
            ], 500);
        }
    }

    /**
     * Get medical check up data for editing
     */
    public function editMedicalCheckUp($id_karyawan, $id_medical_check_up)
    {
        try {
            $medicalCheckUp = MedicalCheckUp::with('kondisiKesehatan')
                ->where('id_karyawan', $id_karyawan)
                ->where('id_medical_check_up', $id_medical_check_up)
                ->firstOrFail();
                
            // Add kondisi kesehatan IDs to the response
            $medicalCheckUp->kondisi_kesehatan_ids = $medicalCheckUp->kondisiKesehatan->pluck('id')->toArray();
                
            return response()->json([
                'success' => true,
                'data' => $medicalCheckUp
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Edit Medical Check Up Error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Data medical check up tidak ditemukan'
            ], 404);
        }
    }
    
    /**
     * Update existing medical check up
     */
    public function updateMedicalCheckUp(Request $request, $id_karyawan, $id)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'file' => 'nullable|file|mimes:pdf|max:5120', // Max 5MB, optional for edit
            'periode' => 'required|integer|min:2000|max:2100',
            'tanggal' => 'required|date',
            'dikeluarkan_oleh' => 'required|string|max:255',
            'bmi' => 'nullable|numeric|min:0|max:999',
            'keterangan_bmi' => ['nullable', Rule::in(['Underweight', 'Normal', 'Overweight', 'Obesitas Tk 1', 'Obesitas Tk 2', 'Obesitas Tk 3'])],
            'id_kondisi_kesehatan' => 'nullable|array',
            'id_kondisi_kesehatan.*' => 'nullable|integer|exists:kondisi_kesehatan,id',
            'catatan' => ['nullable', Rule::in(['Fit', 'Fit dengan Catatan', 'Fit dalam Pengawasan'])],
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            $medicalCheckUp = DB::table('medical_check_up')
                ->where('id_karyawan', $id_karyawan)
                ->where('id_medical_check_up', $id)
                ->first();
                
            if (!$medicalCheckUp) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data medical check up tidak ditemukan'
                ], 404);
            }
            
            // Handle file upload if new file is provided
            if ($request->hasFile('file')) {
                // Delete old file
                if ($medicalCheckUp->file_path) {
                    Storage::disk('public')->delete($medicalCheckUp->file_path);
                }
                
                // Upload new file
                $file = $request->file('file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('medical-check-ups', $fileName, 'public');
                
                $medicalCheckUp->file_path = $filePath;
                $medicalCheckUp->file_name = $fileName;
                $medicalCheckUp->file_size = $file->getSize();
                $medicalCheckUp->mime_type = $file->getMimeType();
            }
            
            // Update medical check up record using DB query for better performance
            DB::table('medical_check_up')
                ->where('id_medical_check_up', $id)
                ->update([
                    'periode' => $request->periode,
                    'tanggal' => $request->tanggal,
                    'dikeluarkan_oleh' => $request->dikeluarkan_oleh,
                    'bmi' => $request->bmi,
                    'keterangan_bmi' => $request->keterangan_bmi,
                    'catatan' => $request->catatan,
                    'file_path' => $medicalCheckUp->file_path,
                    'file_name' => $medicalCheckUp->file_name,
                    'file_size' => $medicalCheckUp->file_size,
                    'mime_type' => $medicalCheckUp->mime_type,
                    'updated_at' => now()
                ]);
            
            // Sync kondisi kesehatan relationships using direct DB operations
            // First, delete existing relationships
            DB::table('medical_check_up_kondisi_kesehatan')
                ->where('id_medical_check_up', $id)
                ->delete();
            
            // Then insert new relationships if provided
            if ($request->id_kondisi_kesehatan && is_array($request->id_kondisi_kesehatan)) {
                // Filter out empty values
                $kondisiIds = array_filter($request->id_kondisi_kesehatan, function($value) {
                    return $value !== null && $value !== '';
                });
                
                if (!empty($kondisiIds)) {
                    $insertData = [];
                    foreach ($kondisiIds as $kondisiId) {
                        $insertData[] = [
                            'id_medical_check_up' => $id,
                            'id_kondisi_kesehatan' => $kondisiId,
                            'created_at' => now(),
                            'updated_at' => now()
                        ];
                    }
                    
                    DB::table('medical_check_up_kondisi_kesehatan')->insert($insertData);
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Medical check up berhasil diperbarui',
                'data' => $medicalCheckUp
            ]);
            
        } catch (\Exception $e) {
            // Log error for debugging
            \Log::error('Medical Check Up Update Error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download medical check up file
     */
    public function downloadMedicalCheckUp($id_karyawan, $id_medical_check_up)
    {
        $medicalCheckUp = MedicalCheckUp::where('id_karyawan', $id_karyawan)
            ->where('id_medical_check_up', $id_medical_check_up)
            ->firstOrFail();
            
        $filePath = storage_path('app/public/' . $medicalCheckUp->file_path);
        
        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File tidak ditemukan');
        }
        
        return response()->download($filePath, $medicalCheckUp->file_name);
    }
    
    /**
     * Delete medical check up
     */
    public function deleteMedicalCheckUp($id_karyawan, $id_medical_check_up)
    {
        try {
            // Debug logging
            \Log::info('Delete Medical Check Up - ID: ' . $id_medical_check_up . ', Karyawan: ' . $id_karyawan);
            \Log::info('Session ID: ' . session()->getId());
            \Log::info('CSRF Token: ' . csrf_token());
            
            $medicalCheckUp = MedicalCheckUp::where('id_karyawan', $id_karyawan)
                ->where('id_medical_check_up', $id_medical_check_up)
                ->firstOrFail();
                
            // Delete kondisi kesehatan relationships first
            $medicalCheckUp->kondisiKesehatan()->detach();
            
            // Delete file from storage
            if ($medicalCheckUp->file_path) {
                Storage::disk('public')->delete($medicalCheckUp->file_path);
            }
            
            // Delete record from database
            $medicalCheckUp->delete();
            
            \Log::info('Medical Check Up deleted successfully');
            
            return response()->json([
                'success' => true,
                'message' => 'Medical check up berhasil dihapus'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Delete Medical Check Up Error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            \Log::error('Request data: ' . json_encode([
                'id_karyawan' => $id_karyawan,
                'id_medical_check_up' => $id_medical_check_up,
                'session_id' => session()->getId(),
                'csrf_token' => csrf_token()
            ]));
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage(),
                'debug_info' => [
                    'error_message' => $e->getMessage(),
                    'error_code' => $e->getCode(),
                    'error_file' => $e->getFile(),
                    'error_line' => $e->getLine(),
                    'request_data' => [
                        'id_karyawan' => $id_karyawan,
                        'id_medical_check_up' => $id_medical_check_up,
                        'session_id' => session()->getId(),
                        'csrf_token' => csrf_token()
                    ]
                ]
            ], 500);
        }
    }
}