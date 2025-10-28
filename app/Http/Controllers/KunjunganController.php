<?php

namespace App\Http\Controllers;

use App\Models\Kunjungan;
use App\Models\RekamMedis;
use App\Models\RekamMedisEmergency;
use App\Models\Keluarga;
use Illuminate\Http\Request;

class KunjunganController extends Controller
{
    public function index(Request $request)
    {
        // Ambil semua data rekam medis reguler untuk dijadikan kunjungan - OPTIMIZED
        $query = RekamMedis::with([
            'keluarga.karyawan:id_karyawan,nik_karyawan,nama_karyawan',
            'keluarga.hubungan:kode_hubungan,hubungan',
            'user:id_user,username,nama_lengkap',
            'keluhans:id_keluhan,id_rekam,id_diagnosa,terapi,keterangan,id_obat,jumlah_obat,aturan_pakai,id_keluarga' // Eager loading dengan select specific columns untuk keluhans
        ])->select('id_rekam', 'id_keluarga', 'tanggal_periksa', 'id_user', 'status'); // Select only needed columns

        // Ambil semua data rekam medis emergency untuk dijadikan kunjungan - OPTIMIZED
        $queryEmergency = RekamMedisEmergency::with([
            'externalEmployee:id,nik_employee,nama_employee,kode_rm',
            'user:id_user,username,nama_lengkap',
            'keluhans:id_keluhan,id_emergency,id_diagnosa_emergency,terapi,keterangan,id_obat,jumlah_obat,aturan_pakai' // Eager loading dengan select specific columns untuk keluhans
        ])->select('id_emergency', 'id_external_employee', 'tanggal_periksa', 'id_user', 'status'); // Select only needed columns

        // Filter pencarian untuk rekam medis reguler
        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function ($sub) use ($q) {
                $sub->whereHas('keluarga', function($keluarga) use ($q) {
                    $keluarga->where('nama_keluarga', 'like', "%$q%")
                            ->orWhere('no_rm', 'like', "%$q%")
                            ->orWhere('bpjs_id', 'like', "%$q%")
                            ->orWhereHas('karyawan', function($karyawan) use ($q) {
                                $karyawan->where('nik_karyawan', 'like', "%$q%");
                            });
                });
            });
        }

        // Filter pencarian untuk rekam medis emergency
        if ($request->filled('q')) {
            $q = $request->input('q');
            $queryEmergency->where(function ($sub) use ($q) {
                $sub->whereHas('externalEmployee', function($employee) use ($q) {
                    $employee->where('nama_employee', 'like', "%$q%")
                            ->orWhere('nik_employee', 'like', "%$q%")
                            ->orWhere('kode_rm', 'like', "%$q%");
                });
            });
        }

        // Filter tanggal untuk rekam medis reguler
        if ($request->filled('dari_tanggal')) {
            $query->where('tanggal_periksa', '>=', $request->dari_tanggal);
        }

        if ($request->filled('sampai_tanggal')) {
            $query->where('tanggal_periksa', '<=', $request->sampai_tanggal);
        }

        // Filter tanggal untuk rekam medis emergency
        if ($request->filled('dari_tanggal')) {
            $queryEmergency->where('tanggal_periksa', '>=', $request->dari_tanggal);
        }

        if ($request->filled('sampai_tanggal')) {
            $queryEmergency->where('tanggal_periksa', '<=', $request->sampai_tanggal);
        }

        // Pagination
        $perPage = $request->input('per_page', 50);
        if (!in_array($perPage, [50, 100, 200])) {
            $perPage = 50;
        }

        // Urutkan berdasarkan No RM (nik_karyawan + kode_hubungan) lalu tanggal descending
        $rekamMedis = $query->get()->sortBy(function($rm) {
            $noRM = ($rm->keluarga->karyawan->nik_karyawan ?? '') . '-' . ($rm->keluarga->kode_hubungan ?? '');
            return $noRM . '_' . $rm->tanggal_periksa->format('Y-m-d');
        })->values();

        // Urutkan berdasarkan No RM emergency lalu tanggal descending
        $rekamMedisEmergency = $queryEmergency->get()->sortBy(function($rm) {
            $noRM = $rm->externalEmployee->kode_rm ?? '';
            return $noRM . '_' . $rm->tanggal_periksa->format('Y-m-d');
        })->values();

        // Transform data rekam medis reguler ke format kunjungan
        $kunjungansReguler = $rekamMedis->map(function($rm) {
            // Generate nomor registrasi format: [urutan_kunjungan_pasien_per_bulan]/NDL/BJM/[bulan]/[tahun]
            $bulan = $rm->tanggal_periksa->format('m');
            $tahun = $rm->tanggal_periksa->format('Y');
            
            // Hitung urutan kunjungan untuk pasien ini pada bulan dan tahun yang sama
            $visitCount = RekamMedis::where('id_keluarga', $rm->id_keluarga)
                ->whereMonth('tanggal_periksa', $bulan)
                ->whereYear('tanggal_periksa', $tahun)
                ->where('tanggal_periksa', '<=', $rm->tanggal_periksa)
                ->count();
            
            $nomorRegistrasi = "{$visitCount}/NDL/BJM/{$bulan}/{$tahun}";

            return (object) [
                'id_kunjungan' => $rm->id_rekam,
                'nomor_registrasi' => $nomorRegistrasi,
                'no_rm' => ($rm->keluarga->karyawan->nik_karyawan ?? '') . '-' . ($rm->keluarga->kode_hubungan ?? ''),
                'nama_pasien' => $rm->keluarga->nama_keluarga ?? '-',
                'hubungan' => $rm->keluarga->hubungan->hubungan ?? '-',
                'tanggal_kunjungan' => $rm->tanggal_periksa,
                'status' => $rm->status ?? 'On Progress',
                'keluarga' => $rm->keluarga,
                'user' => $rm->user,
                'keluhans' => $rm->keluhans ?? [],
                'tipe' => 'reguler'
            ];
        });

        // Transform data rekam medis emergency ke format kunjungan
        $kunjungansEmergency = $rekamMedisEmergency->map(function($rm) {
            // Generate nomor registrasi format: [urutan_kunjungan_pasien_per_bulan]/NDL/BJM/[bulan]/[tahun]
            $bulan = $rm->tanggal_periksa->format('m');
            $tahun = $rm->tanggal_periksa->format('Y');
            
            // Hitung urutan kunjungan untuk pasien ini pada bulan dan tahun yang sama
            $visitCount = RekamMedisEmergency::where('id_external_employee', $rm->id_external_employee)
                ->whereMonth('tanggal_periksa', $bulan)
                ->whereYear('tanggal_periksa', $tahun)
                ->where('tanggal_periksa', '<=', $rm->tanggal_periksa)
                ->count();
            
            $nomorRegistrasi = "{$visitCount}/NDL/BJM/{$bulan}/{$tahun}";

            return (object) [
                'id_kunjungan' => 'EMR-' . $rm->id_emergency, // Prefix EMR untuk emergency
                'nomor_registrasi' => $nomorRegistrasi,
                'no_rm' => $rm->externalEmployee->kode_rm ?? '-',
                'nama_pasien' => $rm->externalEmployee->nama_employee ?? '-',
                'hubungan' => 'External',
                'tanggal_kunjungan' => $rm->tanggal_periksa,
                'status' => $rm->status ?? 'On Progress',
                'externalEmployee' => $rm->externalEmployee,
                'user' => $rm->user,
                'keluhans' => $rm->keluhans ?? [],
                'tipe' => 'emergency',
                'id_emergency' => $rm->id_emergency
            ];
        });

        // Gabungkan kedua koleksi
        $kunjungans = $kunjungansReguler->concat($kunjungansEmergency);

        // Urutkan ulang berdasarkan No RM lalu tanggal descending untuk tampilan
        $kunjungans = $kunjungans->sortByDesc(function($kunjungan) {
            return $kunjungan->no_rm . '_' . $kunjungan->tanggal_kunjungan->format('Y-m-d');
        })->values();

        // Buat paginator manual untuk data yang sudah di-transform
        $currentPage = \Illuminate\Pagination\Paginator::resolveCurrentPage();
        $offset = ($currentPage - 1) * $perPage;
        $itemsForCurrentPage = $kunjungans->slice($offset, $perPage)->values();
        
        $kunjunganCollection = new \Illuminate\Pagination\LengthAwarePaginator(
            $itemsForCurrentPage,
            $kunjungans->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('kunjungan.index', compact('kunjunganCollection'));
    }

    public function show($id)
    {
        // Check if this is an emergency record (starts with EMR-)
        if (strpos($id, 'EMR-') === 0) {
            // Extract emergency ID
            $emergencyId = str_replace('EMR-', '', $id);
            
            // Ambil data rekam medis emergency sebagai detail kunjungan - OPTIMIZED
            $rekamMedisEmergency = RekamMedisEmergency::with([
                'externalEmployee:id,nik_employee,nama_employee,kode_rm,jenis_kelamin,alamat,no_hp',
                'user:id_user,username,nama_lengkap',
                'keluhans:id_keluhan,id_emergency,id_diagnosa_emergency,terapi,keterangan,id_obat,jumlah_obat,aturan_pakai',
                'keluhans.diagnosaEmergency:id_diagnosa_emergency,nama_diagnosa',
                'keluhans.obat:id_obat,nama_obat'
            ])->findOrFail($emergencyId);

            // Generate nomor registrasi format: [urutan_kunjungan_pasien_per_bulan]/NDL/BJM/[bulan]/[tahun]
            $bulan = $rekamMedisEmergency->tanggal_periksa->format('m');
            $tahun = $rekamMedisEmergency->tanggal_periksa->format('Y');
            
            // Hitung urutan kunjungan untuk pasien ini pada bulan dan tahun yang sama
            $visitCount = RekamMedisEmergency::where('id_external_employee', $rekamMedisEmergency->id_external_employee)
                ->whereMonth('tanggal_periksa', $bulan)
                ->whereYear('tanggal_periksa', $tahun)
                ->where('tanggal_periksa', '<=', $rekamMedisEmergency->tanggal_periksa)
                ->count();
            
            $nomorRegistrasi = "{$visitCount}/NDL/BJM/{$bulan}/{$tahun}";

            // Transform ke format kunjungan
            $kunjungan = (object) [
                'id_kunjungan' => 'EMR-' . $rekamMedisEmergency->id_emergency,
                'nomor_registrasi' => $nomorRegistrasi,
                'no_rm' => $rekamMedisEmergency->externalEmployee->kode_rm ?? '-',
                'nama_pasien' => $rekamMedisEmergency->externalEmployee->nama_employee ?? '-',
                'hubungan' => 'External',
                'tanggal_kunjungan' => $rekamMedisEmergency->tanggal_periksa,
                'status' => $rekamMedisEmergency->status ?? 'On Progress',
                'externalEmployee' => $rekamMedisEmergency->externalEmployee,
                'user' => $rekamMedisEmergency->user,
                'keluhans' => $rekamMedisEmergency->keluhans ?? [],
                'tipe' => 'emergency',
                'id_emergency' => $rekamMedisEmergency->id_emergency,
                'keluhan' => $rekamMedisEmergency->keluhan,
                'catatan' => $rekamMedisEmergency->catatan,
                'waktu_periksa' => $rekamMedisEmergency->waktu_periksa
            ];

            // Ambil semua riwayat kunjungan emergency pasien ini - OPTIMIZED
            $riwayatKunjungan = RekamMedisEmergency::with([
                'user:id_user,username,nama_lengkap',
                'keluhans:id_keluhan,id_emergency,id_diagnosa_emergency,terapi,keterangan,id_obat,jumlah_obat,aturan_pakai',
                'keluhans.diagnosaEmergency:id_diagnosa_emergency,nama_diagnosa',
                'keluhans.obat:id_obat,nama_obat'
            ])
            ->select('id_emergency', 'id_external_employee', 'tanggal_periksa', 'status', 'id_user')
            ->where('id_external_employee', $rekamMedisEmergency->id_external_employee)
            ->orderBy('tanggal_periksa', 'desc')
            ->get()
            ->map(function($rm) {
                // Generate nomor registrasi format: [urutan_kunjungan_pasien_per_bulan]/NDL/BJM/[bulan]/[tahun]
                $bulan = $rm->tanggal_periksa->format('m');
                $tahun = $rm->tanggal_periksa->format('Y');
                
                // Hitung urutan kunjungan untuk pasien ini pada bulan dan tahun yang sama
                $visitCount = RekamMedisEmergency::where('id_external_employee', $rm->id_external_employee)
                    ->whereMonth('tanggal_periksa', $bulan)
                    ->whereYear('tanggal_periksa', $tahun)
                    ->where('tanggal_periksa', '<=', $rm->tanggal_periksa)
                    ->count();
                
                $nomorRegistrasi = "{$visitCount}/NDL/BJM/{$bulan}/{$tahun}";

                return (object) [
                    'id_kunjungan' => 'EMR-' . $rm->id_emergency,
                    'nomor_registrasi' => $nomorRegistrasi,
                    'tanggal_kunjungan' => $rm->tanggal_periksa,
                    'status' => $rm->status ?? 'On Progress',
                    'user' => $rm->user,
                    'keluhans' => $rm->keluhans ?? []
                ];
            });

        } else {
            // Handle regular medical record
            // Ambil data rekam medis sebagai detail kunjungan - OPTIMIZED
            $rekamMedis = RekamMedis::with([
                'keluarga.karyawan:id_karyawan,nik_karyawan,nama_karyawan',
                'keluarga.hubungan:kode_hubungan,hubungan',
                'user:id_user,username,nama_lengkap',
                'keluhans:id_keluhan,id_rekam,id_diagnosa,terapi,keterangan,id_obat,jumlah_obat,aturan_pakai,id_keluarga',
                'keluhans.diagnosa:id_diagnosa,nama_diagnosa',
                'keluhans.obat:id_obat,nama_obat'
            ])->findOrFail($id);

            // Generate nomor registrasi format: [urutan_kunjungan_pasien_per_bulan]/NDL/BJM/[bulan]/[tahun]
            $bulan = $rekamMedis->tanggal_periksa->format('m');
            $tahun = $rekamMedis->tanggal_periksa->format('Y');
            
            // Hitung urutan kunjungan untuk pasien ini pada bulan dan tahun yang sama
            $visitCount = RekamMedis::where('id_keluarga', $rekamMedis->id_keluarga)
                ->whereMonth('tanggal_periksa', $bulan)
                ->whereYear('tanggal_periksa', $tahun)
                ->where('tanggal_periksa', '<=', $rekamMedis->tanggal_periksa)
                ->count();
            
            $nomorRegistrasi = "{$visitCount}/NDL/BJM/{$bulan}/{$tahun}";

            // Transform ke format kunjungan
            $kunjungan = (object) [
                'id_kunjungan' => $rekamMedis->id_rekam,
                'nomor_registrasi' => $nomorRegistrasi,
                'no_rm' => ($rekamMedis->keluarga->karyawan->nik_karyawan ?? '') . '-' . ($rekamMedis->keluarga->kode_hubungan ?? ''),
                'nama_pasien' => $rekamMedis->keluarga->nama_keluarga ?? '-',
                'hubungan' => $rekamMedis->keluarga->hubungan->hubungan ?? '-',
                'tanggal_kunjungan' => $rekamMedis->tanggal_periksa,
                'status' => $rekamMedis->status ?? 'On Progress',
                'keluarga' => $rekamMedis->keluarga,
                'user' => $rekamMedis->user,
                'keluhans' => $rekamMedis->keluhans ?? [],
                'tipe' => 'reguler'
            ];

            // Ambil semua riwayat kunjungan pasien ini - OPTIMIZED
            $riwayatKunjungan = RekamMedis::with([
                'user:id_user,username,nama_lengkap',
                'keluhans:id_keluhan,id_rekam,id_diagnosa,terapi,keterangan,id_obat,jumlah_obat,aturan_pakai,id_keluarga',
                'keluhans.diagnosa:id_diagnosa,nama_diagnosa',
                'keluhans.obat:id_obat,nama_obat'
            ])
            ->select('id_rekam', 'id_keluarga', 'tanggal_periksa', 'status', 'id_user')
            ->where('id_keluarga', $rekamMedis->id_keluarga)
            ->orderBy('tanggal_periksa', 'desc')
            ->get()
            ->map(function($rm) {
                // Generate nomor registrasi format: [urutan_kunjungan_pasien_per_bulan]/NDL/BJM/[bulan]/[tahun]
                $bulan = $rm->tanggal_periksa->format('m');
                $tahun = $rm->tanggal_periksa->format('Y');
                
                // Hitung urutan kunjungan untuk pasien ini pada bulan dan tahun yang sama
                $visitCount = RekamMedis::where('id_keluarga', $rm->id_keluarga)
                    ->whereMonth('tanggal_periksa', $bulan)
                    ->whereYear('tanggal_periksa', $tahun)
                    ->where('tanggal_periksa', '<=', $rm->tanggal_periksa)
                    ->count();
                
                $nomorRegistrasi = "{$visitCount}/NDL/BJM/{$bulan}/{$tahun}";

                return (object) [
                    'id_kunjungan' => $rm->id_rekam,
                    'nomor_registrasi' => $nomorRegistrasi,
                    'tanggal_kunjungan' => $rm->tanggal_periksa,
                    'status' => $rm->status ?? 'On Progress',
                    'user' => $rm->user,
                    'keluhans' => $rm->keluhans ?? []
                ];
            });
        }

        return view('kunjungan.detail', compact('kunjungan', 'riwayatKunjungan'));
    }
}
