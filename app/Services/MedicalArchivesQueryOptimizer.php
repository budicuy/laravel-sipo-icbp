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
    public static function getEmployeeMedicalRecords($perPage = 50, $search = null, $departmentFilter = null, $statusFilter = null, $yearFilter = null, $kondisiKesehatanFilter = null, $keteranganBmiFilter = null, $catatanFilter = null)
    {
        // Optimized query to get all data in a single query with proper joins including medical check up data and kondisi kesehatan
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
                // Get latest medical check up data using subquery for better performance
                DB::raw('(SELECT periode FROM medical_check_up WHERE id_karyawan = k.id_karyawan ORDER BY tanggal DESC LIMIT 1) as periode_terakhir'),
                DB::raw('(SELECT bmi FROM medical_check_up WHERE id_karyawan = k.id_karyawan ORDER BY tanggal DESC LIMIT 1) as bmi'),
                DB::raw('(SELECT keterangan_bmi FROM medical_check_up WHERE id_karyawan = k.id_karyawan ORDER BY tanggal DESC LIMIT 1) as keterangan_bmi'),
                DB::raw('(SELECT catatan FROM medical_check_up WHERE id_karyawan = k.id_karyawan ORDER BY tanggal DESC LIMIT 1) as catatan'),
                // Get kondisi kesehatan names as aggregated string using subquery
                DB::raw("(SELECT GROUP_CONCAT(DISTINCT kk.nama_kondisi SEPARATOR ', ')
                    FROM medical_check_up_kondisi_kesehatan mck
                    JOIN kondisi_kesehatan kk ON mck.id_kondisi_kesehatan = kk.id
                    WHERE mck.id_medical_check_up = (
                        SELECT id_medical_check_up FROM medical_check_up
                        WHERE id_karyawan = k.id_karyawan
                        ORDER BY tanggal DESC LIMIT 1
                    )
                ) as kondisi_kesehatan")
            ])
            ->leftJoin('departemen as d', 'k.id_departemen', '=', 'd.id_departemen')
            ->leftJoin('keluarga as kl', function($join) {
                $join->on('k.id_karyawan', '=', 'kl.id_karyawan')
                     ->where('kl.kode_hubungan', '=', 'A');
            })
            ->leftJoin('hubungan as h', 'kl.kode_hubungan', '=', 'h.kode_hubungan');
            
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
        
        // Apply year filter
        if ($yearFilter) {
            $query->havingRaw('periode_terakhir = ?', [$yearFilter]);
        }
        
        // Apply kondisi kesehatan filter
        if ($kondisiKesehatanFilter) {
            $query->havingRaw("kondisi_kesehatan LIKE ?", ["%{$kondisiKesehatanFilter}%"]);
        }
        
        // Apply keterangan BMI filter - menggunakan having karena ini adalah subquery
        if ($keteranganBmiFilter) {
            $query->havingRaw('(SELECT keterangan_bmi FROM medical_check_up WHERE id_karyawan = k.id_karyawan ORDER BY tanggal DESC LIMIT 1) = ?', [$keteranganBmiFilter]);
        }
        
        // Apply catatan filter - menggunakan having karena ini adalah subquery
        if ($catatanFilter) {
            $query->havingRaw('(SELECT catatan FROM medical_check_up WHERE id_karyawan = k.id_karyawan ORDER BY tanggal DESC LIMIT 1) = ?', [$catatanFilter]);
        }
        
        // Order the results
        $query->orderBy('nama_karyawan')
              ->orderBy('nama_keluarga');
        
        // Get paginated results directly from database
        $results = $query->get();
        
        // Transform the results to match the required format
        $medicalArchives = collect();
        $counter = 1;
        
        foreach ($results as $employee) {
            // Skip if kode_hubungan is not 'A' or if no family record exists
            if ($employee->kode_hubungan !== 'A' || !$employee->id_keluarga) {
                continue;
            }
            
            // Convert kondisi_kesehatan from string to array
            $kondisiKesehatan = [];
            if ($employee->kondisi_kesehatan) {
                $kondisiKesehatan = array_filter(explode(', ', $employee->kondisi_kesehatan));
            }
            
            $medicalArchives->push([
                'id' => $counter++,
                'nik_karyawan' => $employee->nik_karyawan,
                'nama_karyawan' => $employee->nama_karyawan,
                'nama_departemen' => $employee->nama_departemen,
                'periode_terakhir' => $employee->periode_terakhir,
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
    
    /**
     * Get statistics data for charts
     */
    public static function getChartData($search = null, $departmentFilter = null, $statusFilter = null, $yearFilter = null, $kondisiKesehatanFilter = null, $keteranganBmiFilter = null, $catatanFilter = null)
    {
        // Optimized query to get all data in a single query with proper joins including medical check up data and kondisi kesehatan
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
                // Get latest medical check up data using subqueries for better performance
                DB::raw('(SELECT periode FROM medical_check_up WHERE id_karyawan = k.id_karyawan ORDER BY tanggal DESC LIMIT 1) as periode_terakhir'),
                DB::raw('(SELECT bmi FROM medical_check_up WHERE id_karyawan = k.id_karyawan ORDER BY tanggal DESC LIMIT 1) as bmi'),
                DB::raw('(SELECT keterangan_bmi FROM medical_check_up WHERE id_karyawan = k.id_karyawan ORDER BY tanggal DESC LIMIT 1) as keterangan_bmi'),
                DB::raw('(SELECT catatan FROM medical_check_up WHERE id_karyawan = k.id_karyawan ORDER BY tanggal DESC LIMIT 1) as catatan'),
                // Get kondisi kesehatan names as aggregated string using subquery
                DB::raw("(SELECT GROUP_CONCAT(DISTINCT kk.nama_kondisi SEPARATOR ', ')
                    FROM medical_check_up_kondisi_kesehatan mck
                    JOIN kondisi_kesehatan kk ON mck.id_kondisi_kesehatan = kk.id
                    WHERE mck.id_medical_check_up = (
                        SELECT id_medical_check_up FROM medical_check_up
                        WHERE id_karyawan = k.id_karyawan
                        ORDER BY tanggal DESC LIMIT 1
                    )
                ) as kondisi_kesehatan")
            ])
            ->leftJoin('departemen as d', 'k.id_departemen', '=', 'd.id_departemen')
            ->leftJoin('keluarga as kl', function($join) {
                $join->on('k.id_karyawan', '=', 'kl.id_karyawan')
                     ->where('kl.kode_hubungan', '=', 'A');
            })
            ->leftJoin('hubungan as h', 'kl.kode_hubungan', '=', 'h.kode_hubungan');
            
        // Apply the same filters as the main query
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('k.nik_karyawan', 'like', "%{$search}%")
                  ->orWhere('k.nama_karyawan', 'like', "%{$search}%")
                  ->orWhere('kl.nama_keluarga', 'like', "%{$search}%")
                  ->orWhere('kl.no_rm', 'like', "%{$search}%");
            });
        }
        
        if ($departmentFilter) {
            $query->where('d.id_departemen', $departmentFilter);
        }
        
        if ($statusFilter) {
            $query->where('k.status', $statusFilter);
        }
        
        // Apply year filter
        if ($yearFilter) {
            $query->havingRaw('(SELECT periode FROM medical_check_up WHERE id_karyawan = k.id_karyawan ORDER BY tanggal DESC LIMIT 1) = ?', [$yearFilter]);
        }
        
        // Apply kondisi kesehatan filter
        if ($kondisiKesehatanFilter) {
            $query->havingRaw("(SELECT GROUP_CONCAT(DISTINCT kk.nama_kondisi SEPARATOR ', ')
                FROM medical_check_up_kondisi_kesehatan mck
                JOIN kondisi_kesehatan kk ON mck.id_kondisi_kesehatan = kk.id
                WHERE mck.id_medical_check_up = (
                    SELECT id_medical_check_up FROM medical_check_up
                    WHERE id_karyawan = k.id_karyawan
                    ORDER BY tanggal DESC LIMIT 1
                )
            ) LIKE ?", ["%{$kondisiKesehatanFilter}%"]);
        }
        
        // Apply keterangan BMI filter
        if ($keteranganBmiFilter) {
            $query->havingRaw('(SELECT keterangan_bmi FROM medical_check_up WHERE id_karyawan = k.id_karyawan ORDER BY tanggal DESC LIMIT 1) = ?', [$keteranganBmiFilter]);
        }
        
        // Apply catatan filter
        if ($catatanFilter) {
            $query->havingRaw('(SELECT catatan FROM medical_check_up WHERE id_karyawan = k.id_karyawan ORDER BY tanggal DESC LIMIT 1) = ?', [$catatanFilter]);
        }
        
        // Get all results
        $allResults = $query->get();
        
        // Initialize chart data with default values
        $kondisiKesehatanChart = collect();
        $keteranganBmiChart = collect([
            'Underweight' => 0,
            'Normal' => 0,
            'Overweight' => 0,
            'Obesitas Tk 1' => 0,
            'Obesitas Tk 2' => 0,
            'Obesitas Tk 3' => 0
        ]);
        $catatanChart = collect([
            'Fit' => 0,
            'Fit dengan Catatan' => 0,
            'Fit dalam Pengawasan' => 0
        ]);
        
        // Process each employee
        foreach ($allResults as $employee) {
            // Skip if kode_hubungan is not 'A' (but still show if no family record exists)
            if ($employee->kode_hubungan && $employee->kode_hubungan !== 'A') {
                continue;
            }
            
            // Process Kondisi Kesehatan
            $kondisiKesehatan = [];
            if ($employee->kondisi_kesehatan) {
                $kondisiKesehatan = array_filter(explode(', ', $employee->kondisi_kesehatan));
            }
            
            foreach ($kondisiKesehatan as $kondisi) {
                if ($kondisiKesehatanChart->has($kondisi)) {
                    $currentValue = $kondisiKesehatanChart->get($kondisi);
                    $kondisiKesehatanChart->put($kondisi, $currentValue + 1);
                } else {
                    $kondisiKesehatanChart->put($kondisi, 1);
                }
            }
            
            // Process Keterangan BMI
            if ($employee->keterangan_bmi && $keteranganBmiChart->has($employee->keterangan_bmi)) {
                $currentValue = $keteranganBmiChart->get($employee->keterangan_bmi);
                $keteranganBmiChart->put($employee->keterangan_bmi, $currentValue + 1);
            }
            
            // Process Catatan
            if ($employee->catatan && $catatanChart->has($employee->catatan)) {
                $currentValue = $catatanChart->get($employee->catatan);
                $catatanChart->put($employee->catatan, $currentValue + 1);
            }
        }
        
        // Ensure all chart data have at least some default values if empty
        if ($kondisiKesehatanChart->isEmpty()) {
            $kondisiKesehatanChart->put('Tidak Ada Data', 1);
        }
        
        // Remove zero values from keterangan BMI chart for cleaner display
        $keteranganBmiChart = $keteranganBmiChart->filter(function($value, $key) {
            return $value > 0;
        });
        
        // If all values are zero, add a default entry
        if ($keteranganBmiChart->isEmpty()) {
            $keteranganBmiChart->put('Tidak Ada Data', 1);
        }
        
        // Remove zero values from catatan chart for cleaner display
        $catatanChart = $catatanChart->filter(function($value, $key) {
            return $value > 0;
        });
        
        // If all values are zero, add a default entry
        if ($catatanChart->isEmpty()) {
            $catatanChart->put('Tidak Ada Data', 1);
        }
        
        return [
            'kondisiKesehatan' => $kondisiKesehatanChart->sortKeys(),
            'keteranganBmi' => $keteranganBmiChart,
            'catatan' => $catatanChart
        ];
    }
    
    /**
     * Get available years for filter dropdown
     */
    public static function getAvailableYears()
    {
        return DB::table('medical_check_up')
            ->select('periode')
            ->distinct()
            ->orderBy('periode', 'desc')
            ->pluck('periode')
            ->toArray();
    }
}