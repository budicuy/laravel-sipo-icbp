<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class MedicalArchivesQueryOptimizer
{
    /**
     * Optimized version of getEmployeeMedicalRecords to avoid N+1 queries
     */
    public static function getEmployeeMedicalRecords($perPage = 50, $search = null, $departmentFilter = null, $statusFilter = null)
    {
        // Get all data in a single query with proper joins including medical check up data
        $query = DB::table('karyawan as k')
            ->select([
                'k.id_karyawan',
                'k.nik_karyawan',
                'k.nama_karyawan',
                'k.status as karyawan_status',
                'd.nama_departemen',
                'kl.id_keluarga',
                'kl.nama_keluarga',
                'kl.no_rm',
                'kl.kode_hubungan',
                'h.hubungan as hubungan_nama',
                // Get latest medical check up data - use periode field which contains year
                DB::raw('(SELECT periode FROM medical_check_up WHERE id_karyawan = k.id_karyawan ORDER BY tanggal DESC LIMIT 1) as periode_terakhir'),
                DB::raw('(SELECT bmi FROM medical_check_up WHERE id_karyawan = k.id_karyawan ORDER BY tanggal DESC LIMIT 1) as bmi'),
                DB::raw('(SELECT keterangan_bmi FROM medical_check_up WHERE id_karyawan = k.id_karyawan ORDER BY tanggal DESC LIMIT 1) as keterangan_bmi'),
                DB::raw('(SELECT catatan FROM medical_check_up WHERE id_karyawan = k.id_karyawan ORDER BY tanggal DESC LIMIT 1) as catatan')
            ])
            ->leftJoin('departemen as d', 'k.id_departemen', '=', 'd.id_departemen')
            ->leftJoin('keluarga as kl', function($join) {
                $join->on('k.id_karyawan', '=', 'kl.id_karyawan')
                     ->where('kl.kode_hubungan', '=', 'A');
            })
            ->leftJoin('hubungan as h', 'kl.kode_hubungan', '=', 'h.kode_hubungan')
            ->where('k.status', 'aktif')
            ->whereNotNull('kl.no_rm');
            
        // Apply search filter
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('k.nik_karyawan', 'like', "%{$search}%")
                  ->orWhere('k.nama_karyawan', 'like', "%{$search}%")
                  ->orWhere('kl.nama_keluarga', 'like', "%{$search}%")
                  ->orWhere('kl.no_rm', 'like', "%{$search}%");
            });
        }
        
        // Apply department filter
        if ($departmentFilter) {
            $query->where('d.id_departemen', $departmentFilter);
        }
        
        // Apply status filter (employee status)
        if ($statusFilter) {
            $query->where('k.status', $statusFilter);
        }
        
        // Order the results
        $query->orderBy('k.nama_karyawan')
              ->orderBy('kl.nama_keluarga');
        
        // Get all results
        $allResults = $query->get();
        
        // Get kondisi kesehatan for all karyawan (only from latest medical check up)
        $kondisiKesehatanMap = collect();
        
        foreach ($allResults->pluck('id_karyawan')->unique() as $idKaryawan) {
            // Get the latest medical check up date for this employee
            $latestMCU = DB::table('medical_check_up')
                ->where('id_karyawan', $idKaryawan)
                ->orderBy('tanggal', 'desc')
                ->first();
            
            if ($latestMCU) {
                // Get kondisi kesehatan for the latest medical check up
                $kondisiKesehatan = DB::table('medical_check_up_kondisi_kesehatan as mck')
                    ->join('kondisi_kesehatan as kk', 'mck.id_kondisi_kesehatan', '=', 'kk.id')
                    ->where('mck.id_medical_check_up', $latestMCU->id_medical_check_up)
                    ->pluck('kk.nama_kondisi')
                    ->toArray();
                
                if (!empty($kondisiKesehatan)) {
                    $kondisiKesehatanMap->put($idKaryawan, $kondisiKesehatan);
                }
            }
        }
        
        // Group by employee to avoid duplicates
        $groupedResults = $allResults->groupBy('id_karyawan');
        
        // Transform the results to match the required format
        $medicalArchives = collect();
        $counter = 1;
        
        foreach ($groupedResults as $employeeId => $familyMembers) {
            $employee = $familyMembers->first();
            
            // Skip if kode_hubungan is not 'A'
            if ($employee->kode_hubungan !== 'A') {
                continue;
            }
            
            // Get kondisi kesehatan for this employee (from latest medical check up)
            $kondisiKesehatan = $kondisiKesehatanMap->get($employeeId, []);
            
            $medicalArchives->push([
                'id' => $counter++,
                'nik_karyawan' => $employee->nik_karyawan,
                'nama_karyawan' => $employee->nama_karyawan,
                'nama_departemen' => $employee->nama_departemen,
                'periode_terakhir' => $employee->periode_terakhir, // This is already a year (integer)
                'bmi' => $employee->bmi,
                'keterangan_bmi' => $employee->keterangan_bmi,
                'kondisi_kesehatan' => $kondisiKesehatan,
                'catatan' => $employee->catatan,
                'status' => $employee->karyawan_status,
                'id_keluarga' => $employee->id_keluarga,
                'id_karyawan' => $employee->id_karyawan
            ]);
        }
        
        // Apply pagination
        $currentPage = request()->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        $total = $medicalArchives->count();
        $items = $medicalArchives->slice($offset, $perPage)->values();
        
        // Create a LengthAwarePaginator
        $paginator = new LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $currentPage,
            [
                'path' => request()->url(),
                'pageName' => 'page',
            ]
        );
        
        return $paginator;
    }
    
    /**
     * Optimized version of getEmployeeMedicalHistory to avoid N+1 queries
     */
    public static function getEmployeeMedicalHistory($id_karyawan)
    {
        return DB::table('karyawan as k')
            ->select([
                'k.id_karyawan',
                'k.nik_karyawan',
                'k.nama_karyawan',
                'k.status as karyawan_status',
                'd.nama_departemen',
                'kl.id_keluarga',
                'kl.nama_keluarga',
                'kl.no_rm',
                'kl.kode_hubungan',
                'h.hubungan as hubungan_nama',
                'rm.id_rekam',
                'rm.tanggal_periksa',
                'rm.status as rekam_status',
                'rm.jumlah_keluhan',
                'u.nama_lengkap as petugas',
                'rm.created_at'
            ])
            ->leftJoin('departemen as d', 'k.id_departemen', '=', 'd.id_departemen')
            ->leftJoin('keluarga as kl', 'k.id_karyawan', '=', 'kl.id_karyawan')
            ->leftJoin('hubungan as h', 'kl.kode_hubungan', '=', 'h.kode_hubungan')
            ->leftJoin('rekam_medis as rm', 'kl.id_keluarga', '=', 'rm.id_keluarga')
            ->leftJoin('user as u', 'rm.id_user', '=', 'u.id_user')
            ->where('k.id_karyawan', $id_karyawan)
            ->where('k.status', 'aktif')
            ->whereNotNull('kl.no_rm')
            ->where('kl.kode_hubungan', 'A') // Filter hanya pasien dengan kode hubungan A (Karyawan)
            ->orderBy('rm.tanggal_periksa', 'desc')
            ->orderBy('kl.nama_keluarga')
            ->get();
    }
    
    /**
     * Optimized version of getDetailedMedicalVisits to avoid N+1 queries
     */
    public static function getDetailedMedicalVisits($id_rekam)
    {
        // Get visit details in a single query
        $visitDetails = DB::table('rekam_medis as rm')
            ->select([
                'rm.id_rekam',
                'rm.tanggal_periksa',
                'rm.status',
                'rm.jumlah_keluhan',
                'rm.created_at',
                'u.nama_lengkap as petugas',
                'kl.nama_keluarga',
                'kl.no_rm',
                'k.nama_karyawan',
                'd.nama_departemen',
                'h.hubungan'
            ])
            ->join('user as u', 'rm.id_user', '=', 'u.id_user')
            ->join('keluarga as kl', 'rm.id_keluarga', '=', 'kl.id_keluarga')
            ->join('karyawan as k', 'kl.id_karyawan', '=', 'k.id_karyawan')
            ->leftJoin('departemen as d', 'k.id_departemen', '=', 'd.id_departemen')
            ->leftJoin('hubungan as h', 'kl.kode_hubungan', '=', 'h.kode_hubungan')
            ->where('rm.id_rekam', $id_rekam)
            ->first();
        
        if (!$visitDetails) {
            return null;
        }
        
        // Get all complaints and diagnoses for this visit in a single query
        $complaints = DB::table('keluhan as ke')
            ->select([
                'ke.id_keluhan',
                'ke.terapi',
                'ke.keterangan',
                'ke.jumlah_obat',
                'ke.aturan_pakai',
                'd.nama_diagnosa',
                'o.nama_obat',
                's.nama_satuan'
            ])
            ->leftJoin('diagnosa as d', 'ke.id_diagnosa', '=', 'd.id_diagnosa')
            ->leftJoin('obat as o', 'ke.id_obat', '=', 'o.id_obat')
            ->leftJoin('satuan_obat as s', 'o.id_satuan', '=', 's.id_satuan')
            ->where('ke.id_rekam', $id_rekam)
            ->orderBy('ke.id_diagnosa')
            ->orderBy('ke.id_obat')
            ->get();
        
        $visitDetails->complaints = $complaints;
        
        return $visitDetails;
    }
    
    /**
     * Get complete family member information with all related data in a single query
     */
    public static function getFamilyMemberCompleteInfo($id_keluarga)
    {
        return DB::table('keluarga as kl')
            ->select([
                'kl.id_keluarga',
                'kl.nama_keluarga',
                'kl.no_rm',
                'kl.kode_hubungan',
                'kl.tanggal_lahir',
                'kl.jenis_kelamin',
                'kl.alamat',
                'kl.bpjs_id',
                'k.id_karyawan',
                'k.nik_karyawan',
                'k.nama_karyawan',
                'd.nama_departemen',
                'h.hubungan as hubungan_nama'
            ])
            ->join('karyawan as k', 'kl.id_karyawan', '=', 'k.id_karyawan')
            ->leftJoin('departemen as d', 'k.id_departemen', '=', 'd.id_departemen')
            ->leftJoin('hubungan as h', 'kl.kode_hubungan', '=', 'h.kode_hubungan')
            ->where('kl.id_keluarga', $id_keluarga)
            ->where('kl.kode_hubungan', 'A') // Filter hanya pasien dengan kode hubungan A (Karyawan)
            ->first();
    }
    
    /**
     * Get departments for filter dropdown
     */
    public static function getDepartments()
    {
        return DB::table('departemen')
            ->select('id_departemen', 'nama_departemen')
            ->orderBy('nama_departemen')
            ->get();
    }
    
    /**
     * Get employee information with department in a single query
     */
    public static function getEmployeeInfo($id_karyawan)
    {
        return DB::table('karyawan as k')
            ->select([
                'k.id_karyawan',
                'k.nik_karyawan',
                'k.nama_karyawan',
                'k.status as karyawan_status',
                'd.nama_departemen'
            ])
            ->leftJoin('departemen as d', 'k.id_departemen', '=', 'd.id_departemen')
            ->where('k.id_karyawan', $id_karyawan)
            ->first();
    }
    
    /**
     * Search employees with department information in a single query
     */
    public static function searchEmployees($search, $limit = 10)
    {
        return DB::table('karyawan as k')
            ->select([
                'k.id_karyawan',
                'k.nik_karyawan',
                'k.nama_karyawan',
                'd.nama_departemen'
            ])
            ->leftJoin('departemen as d', 'k.id_departemen', '=', 'd.id_departemen')
            ->where('k.status', 'aktif')
            ->where(function($query) use ($search) {
                $query->where('k.nik_karyawan', 'like', "%{$search}%")
                      ->orWhere('k.nama_karyawan', 'like', "%{$search}%");
            })
            ->limit($limit)
            ->get();
    }
}