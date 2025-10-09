<?php

namespace App\Http\Controllers;

use App\Models\Kunjungan;
use App\Models\RekamMedis;
use App\Models\Keluarga;
use Illuminate\Http\Request;

class KunjunganController extends Controller
{
    public function index(Request $request)
    {
        // Ambil semua data rekam medis untuk dijadikan kunjungan
        $query = RekamMedis::with(['keluarga.karyawan', 'keluarga.hubungan', 'user']);

        // Filter pencarian
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

        // Filter tanggal
        if ($request->filled('dari_tanggal')) {
            $query->where('tanggal_periksa', '>=', $request->dari_tanggal);
        }

        if ($request->filled('sampai_tanggal')) {
            $query->where('tanggal_periksa', '<=', $request->sampai_tanggal);
        }

        // Pagination
        $perPage = $request->input('per_page', 50);
        if (!in_array($perPage, [50, 100, 200])) {
            $perPage = 50;
        }

        $rekamMedis = $query->orderBy('tanggal_periksa', 'desc')->paginate($perPage)->appends($request->except('page'));

        // Transform data ke format kunjungan
        $kunjungans = $rekamMedis->map(function($rm) {
            // Generate nomor registrasi format: 1(No Running)/NDL/BJM/08/2025
            $noRunning = str_pad($rm->id_rekam, 1, '0', STR_PAD_LEFT);
            $bulan = $rm->tanggal_periksa->format('m');
            $tahun = $rm->tanggal_periksa->format('Y');
            $nomorRegistrasi = "1{$noRunning}/NDL/BJM/{$bulan}/{$tahun}";

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
                'keluhans' => $rm->keluhans ?? []
            ];
        });

        // Buat paginator manual untuk data yang sudah di-transform
        $kunjunganCollection = new \Illuminate\Pagination\LengthAwarePaginator(
            $kunjungans,
            $rekamMedis->total(),
            $rekamMedis->perPage(),
            $rekamMedis->currentPage(),
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('kunjungan.index', compact('kunjunganCollection'));
    }

    public function show($id)
    {
        // Ambil data rekam medis sebagai detail kunjungan
        $rekamMedis = RekamMedis::with(['keluarga.karyawan', 'keluarga.hubungan', 'user', 'keluhans.diagnosa', 'keluhans.obat'])
            ->findOrFail($id);

        // Generate nomor registrasi format: 1(No Running)/NDL/BJM/08/2025
        $noRunning = str_pad($rekamMedis->id_rekam, 1, '0', STR_PAD_LEFT);
        $bulan = $rekamMedis->tanggal_periksa->format('m');
        $tahun = $rekamMedis->tanggal_periksa->format('Y');
        $nomorRegistrasi = "1{$noRunning}/NDL/BJM/{$bulan}/{$tahun}";

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
            'keluhans' => $rekamMedis->keluhans ?? []
        ];

        // Ambil semua riwayat kunjungan pasien ini (semua rekam medis dengan id_keluarga yang sama)
        $riwayatKunjungan = RekamMedis::with(['user', 'keluhans.diagnosa', 'keluhans.obat'])
            ->where('id_keluarga', $rekamMedis->id_keluarga)
            ->orderBy('tanggal_periksa', 'desc')
            ->get()
            ->map(function($rm) {
                // Generate nomor registrasi format: 1(No Running)/NDL/BJM/08/2025
                $noRunning = str_pad($rm->id_rekam, 1, '0', STR_PAD_LEFT);
                $bulan = $rm->tanggal_periksa->format('m');
                $tahun = $rm->tanggal_periksa->format('Y');
                $nomorRegistrasi = "1{$noRunning}/NDL/BJM/{$bulan}/{$tahun}";

                return (object) [
                    'id_kunjungan' => $rm->id_rekam,
                    'nomor_registrasi' => $nomorRegistrasi,
                    'tanggal_kunjungan' => $rm->tanggal_periksa,
                    'status' => $rm->status ?? 'On Progress',
                    'user' => $rm->user,
                    'keluhans' => $rm->keluhans ?? []
                ];
            });

        return view('kunjungan.detail', compact('kunjungan', 'riwayatKunjungan'));
    }
}
