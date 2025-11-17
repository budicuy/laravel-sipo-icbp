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
use App\Services\MedicalArchivesQueryOptimizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class MedicalArchivesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 50);
        $search = $request->get('q');
        $departmentFilter = $request->get('department');
        $statusFilter = $request->get('status');
        
        // Get medical archives with filters using optimized query
        $medicalArchives = MedicalArchivesQueryOptimizer::getEmployeeMedicalRecords($perPage, $search, $departmentFilter, $statusFilter);
        
        // Get departments for filter dropdown using optimized query
        $departments = MedicalArchivesQueryOptimizer::getDepartments();
        
        // Get status options (employee status)
        $statusOptions = [
            'aktif' => 'Aktif',
            'tidak aktif' => 'Tidak Aktif'
        ];
        
        return view('medical-archives.index', compact(
            'medicalArchives',
            'departments',
            'statusOptions',
            'perPage',
            'search',
            'departmentFilter',
            'statusFilter'
        ));
    }
    
    /**
     * Display the specified resource.
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
        
        return view('medical-archives.show', compact(
            'employee',
            'employeeInfo',
            'detailedRecords'
        ));
    }
    
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // This will redirect to rekam-medis create since medical archives are created through that system
        return redirect()->route('rekam-medis.choose-type')
            ->with('info', 'Silakan pilih jenis rekam medis yang ingin dibuat');
    }
    
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // This will redirect to rekam-medis store since medical archives are created through that system
        return redirect()->route('rekam-medis.choose-type')
            ->with('info', 'Silakan pilih jenis rekam medis yang ingin dibuat');
    }
    
    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id_karyawan)
    {
        // Medical archives are edited through the rekam-medis system using optimized query
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
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id_karyawan)
    {
        // Medical archives are updated through the rekam-medis system
        return redirect()->route('medical-archives.show', $id_karyawan)
            ->with('info', 'Data medis diperbarui melalui sistem rekam medis');
    }
    
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id_karyawan)
    {
        // Medical archives are deleted through the rekam-medis system
        // This method is not applicable for medical archives
        return redirect()->route('medical-archives.index')
            ->with('error', 'Data medis tidak dapat dihapus melalui menu ini. Silakan gunakan menu Rekam Medis.');
    }
    
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
            
        // Get surat rekomendasi medis data (empty for now)
        $suratRekomendasi = collect([]);
        
        return view('medical-archives.surat-rekomendasi-medis', compact(
            'employeeInfo',
            'familyMember',
            'suratRekomendasi',
            'id_karyawan'
        ));
    }
    
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
            
        // Get medical check up data (empty for now)
        $medicalCheckUp = collect([]);
        
        return view('medical-archives.medical-check-up', compact(
            'employeeInfo',
            'familyMember',
            'medicalCheckUp',
            'id_karyawan'
        ));
    }
    
}