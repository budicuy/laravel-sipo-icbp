<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class MedicalArchivesQueryOptimizer
{
    public static function getEmployeeMedicalRecords($perPage = 50, $search = null, $departmentFilter = null, $statusFilter = null, $yearFilter = null, $kondisiKesehatanFilter = null, $keteranganBmiFilter = null, $catatanFilter = null)
    {
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
                DB::raw('(SELECT periode FROM medical_check_up WHERE id_karyawan = k.id_karyawan ORDER BY tanggal DESC LIMIT 1) as periode_terakhir'),
                DB::raw('(SELECT bmi FROM medical_check_up WHERE id_karyawan = k.id_karyawan ORDER BY tanggal DESC LIMIT 1) as bmi'),
                DB::raw('(SELECT keterangan_bmi FROM medical_check_up WHERE id_karyawan = k.id_karyawan ORDER BY tanggal DESC LIMIT 1) as keterangan_bmi'),
                DB::raw('(SELECT catatan FROM medical_check_up WHERE id_karyawan = k.id_karyawan ORDER BY tanggal DESC LIMIT 1) as catatan'),
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
        
        if ($yearFilter) {
            $query->whereExists(function($q) use ($yearFilter) {
                $q->select(DB::raw(1))
                  ->from('medical_check_up')
                  ->whereRaw('id_karyawan = k.id_karyawan')
                  ->where('periode', $yearFilter)
                  ->orderBy('tanggal', 'desc')
                  ->limit(1);
            });
        }
        
        if ($kondisiKesehatanFilter) {
            $query->whereExists(function($q) use ($kondisiKesehatanFilter) {
                $q->select(DB::raw(1))
                  ->from('medical_check_up as mcu')
                  ->join('medical_check_up_kondisi_kesehatan as mck', 'mcu.id_medical_check_up', '=', 'mck.id_medical_check_up')
                  ->join('kondisi_kesehatan as kk', 'mck.id_kondisi_kesehatan', '=', 'kk.id')
                  ->whereRaw('mcu.id_karyawan = k.id_karyawan')
                  ->where('kk.nama_kondisi', 'like', "%{$kondisiKesehatanFilter}%")
                  ->orderBy('mcu.tanggal', 'desc')
                  ->limit(1);
            });
        }
        
        if ($keteranganBmiFilter) {
            $query->whereExists(function($q) use ($keteranganBmiFilter) {
                $q->select(DB::raw(1))
                  ->from('medical_check_up')
                  ->whereRaw('id_karyawan = k.id_karyawan')
                  ->where('keterangan_bmi', $keteranganBmiFilter)
                  ->orderBy('tanggal', 'desc')
                  ->limit(1);
            });
        }
        
        if ($catatanFilter) {
            $query->whereExists(function($q) use ($catatanFilter) {
                $q->select(DB::raw(1))
                  ->from('medical_check_up')
                  ->whereRaw('id_karyawan = k.id_karyawan')
                  ->where('catatan', $catatanFilter)
                  ->orderBy('tanggal', 'desc')
                  ->limit(1);
            });
        }
        
        $query->orderBy('nama_karyawan')->orderBy('nama_keluarga');
        
        $results = $query->get();
        
        $medicalArchives = collect();
        $counter = 1;
        
        foreach ($results as $employee) {
            if ($employee->kode_hubungan !== 'A' || !$employee->id_keluarga) {
                continue;
            }
            
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
        
        $currentPage = request()->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        $total = $medicalArchives->count();
        $items = $medicalArchives->slice($offset, $perPage)->values();
        
        return new LengthAwarePaginator($items, $total, $perPage, $currentPage, [
            'path' => request()->url(),
            'pageName' => 'page',
        ]);
    }
    
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
            ->whereNotNull('kl.no_rm')
            ->where('kl.kode_hubungan', 'A')
            ->orderBy('rm.tanggal_periksa', 'desc')
            ->orderBy('kl.nama_keluarga')
            ->get();
    }
    
    public static function getDetailedMedicalVisits($id_rekam)
    {
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
                'k.status as karyawan_status',
                'd.nama_departemen',
                'h.hubungan as hubungan_nama'
            ])
            ->join('karyawan as k', 'kl.id_karyawan', '=', 'k.id_karyawan')
            ->leftJoin('departemen as d', 'k.id_departemen', '=', 'd.id_departemen')
            ->leftJoin('hubungan as h', 'kl.kode_hubungan', '=', 'h.kode_hubungan')
            ->where('kl.id_keluarga', $id_keluarga)
            ->where('kl.kode_hubungan', 'A')
            ->first();
    }
    
    public static function getDepartments()
    {
        return DB::table('departemen')
            ->select('id_departemen', 'nama_departemen')
            ->orderBy('nama_departemen')
            ->get();
    }
    
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
            ->where(function($query) use ($search) {
                $query->where('k.nik_karyawan', 'like', "%{$search}%")
                      ->orWhere('k.nama_karyawan', 'like', "%{$search}%");
            })
            ->limit($limit)
            ->get();
    }
    
    public static function getChartData($search = null, $departmentFilter = null, $statusFilter = null, $yearFilter = null, $kondisiKesehatanFilter = null, $keteranganBmiFilter = null, $catatanFilter = null)
    {
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
                DB::raw('(SELECT periode FROM medical_check_up WHERE id_karyawan = k.id_karyawan ORDER BY tanggal DESC LIMIT 1) as periode_terakhir'),
                DB::raw('(SELECT bmi FROM medical_check_up WHERE id_karyawan = k.id_karyawan ORDER BY tanggal DESC LIMIT 1) as bmi'),
                DB::raw('(SELECT keterangan_bmi FROM medical_check_up WHERE id_karyawan = k.id_karyawan ORDER BY tanggal DESC LIMIT 1) as keterangan_bmi'),
                DB::raw('(SELECT catatan FROM medical_check_up WHERE id_karyawan = k.id_karyawan ORDER BY tanggal DESC LIMIT 1) as catatan'),
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
        
        if ($yearFilter) {
            $query->whereExists(function($q) use ($yearFilter) {
                $q->select(DB::raw(1))
                  ->from('medical_check_up')
                  ->whereRaw('id_karyawan = k.id_karyawan')
                  ->where('periode', $yearFilter)
                  ->orderBy('tanggal', 'desc')
                  ->limit(1);
            });
        }
        
        if ($kondisiKesehatanFilter) {
            $query->whereExists(function($q) use ($kondisiKesehatanFilter) {
                $q->select(DB::raw(1))
                  ->from('medical_check_up as mcu')
                  ->join('medical_check_up_kondisi_kesehatan as mck', 'mcu.id_medical_check_up', '=', 'mck.id_medical_check_up')
                  ->join('kondisi_kesehatan as kk', 'mck.id_kondisi_kesehatan', '=', 'kk.id')
                  ->whereRaw('mcu.id_karyawan = k.id_karyawan')
                  ->where('kk.nama_kondisi', 'like', "%{$kondisiKesehatanFilter}%")
                  ->orderBy('mcu.tanggal', 'desc')
                  ->limit(1);
            });
        }
        
        if ($keteranganBmiFilter) {
            $query->whereExists(function($q) use ($keteranganBmiFilter) {
                $q->select(DB::raw(1))
                  ->from('medical_check_up')
                  ->whereRaw('id_karyawan = k.id_karyawan')
                  ->where('keterangan_bmi', $keteranganBmiFilter)
                  ->orderBy('tanggal', 'desc')
                  ->limit(1);
            });
        }
        
        if ($catatanFilter) {
            $query->whereExists(function($q) use ($catatanFilter) {
                $q->select(DB::raw(1))
                  ->from('medical_check_up')
                  ->whereRaw('id_karyawan = k.id_karyawan')
                  ->where('catatan', $catatanFilter)
                  ->orderBy('tanggal', 'desc')
                  ->limit(1);
            });
        }
        
        $allResults = $query->get();
        
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
        
        foreach ($allResults as $employee) {
            if ($employee->kode_hubungan && $employee->kode_hubungan !== 'A') {
                continue;
            }
            
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
            
            if ($employee->keterangan_bmi && $keteranganBmiChart->has($employee->keterangan_bmi)) {
                $currentValue = $keteranganBmiChart->get($employee->keterangan_bmi);
                $keteranganBmiChart->put($employee->keterangan_bmi, $currentValue + 1);
            }
            
            if ($employee->catatan && $catatanChart->has($employee->catatan)) {
                $currentValue = $catatanChart->get($employee->catatan);
                $catatanChart->put($employee->catatan, $currentValue + 1);
            }
        }
        
        if ($kondisiKesehatanChart->isEmpty()) {
            $kondisiKesehatanChart->put('Tidak Ada Data', 1);
        } else {
            $kondisiKesehatanChart = $kondisiKesehatanChart->sortDesc()->take(10);
        }
        
        $keteranganBmiChart = $keteranganBmiChart->filter(function($value) {
            return $value > 0;
        });
        
        if ($keteranganBmiChart->isEmpty()) {
            $keteranganBmiChart->put('Tidak Ada Data', 1);
        }
        
        $catatanChart = $catatanChart->filter(function($value) {
            return $value > 0;
        });
        
        if ($catatanChart->isEmpty()) {
            $catatanChart->put('Tidak Ada Data', 1);
        }
        
        return [
            'kondisiKesehatan' => $kondisiKesehatanChart,
            'keteranganBmi' => $keteranganBmiChart,
            'catatan' => $catatanChart
        ];
    }
    
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