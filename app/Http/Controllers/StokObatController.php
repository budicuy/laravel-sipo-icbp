<?php

namespace App\Http\Controllers;

use App\Models\StokObat;
use App\Models\Obat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class StokObatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = StokObat::with([
            'obat:id_obat,nama_obat,keterangan,id_satuan',
            'obat.satuanObat:id_satuan,nama_satuan'
        ]);

        // Filter by periode
        if ($request->has('periode') && $request->periode != '') {
            $query->where('periode', $request->periode);
        }

        // Filter by range periode
        if ($request->has('periode_start') && $request->periode_start != '') {
            $query->where(function($q) use ($request) {
                $startYear = '20' . substr($request->periode_start, 3, 2);
                $startMonth = substr($request->periode_start, 0, 2);
                $q->whereRaw("CONCAT('20', SUBSTRING(periode, 4, 2)) > '$startYear'")
                  ->orWhere(function($subQ) use ($startYear, $startMonth) {
                      $subQ->whereRaw("CONCAT('20', SUBSTRING(periode, 4, 2)) = '$startYear'")
                           ->whereRaw("SUBSTRING(periode, 1, 2) >= '$startMonth'");
                  });
            });
        }
        if ($request->has('periode_end') && $request->periode_end != '') {
            $query->where(function($q) use ($request) {
                $endYear = '20' . substr($request->periode_end, 3, 2);
                $endMonth = substr($request->periode_end, 0, 2);
                $q->whereRaw("CONCAT('20', SUBSTRING(periode, 4, 2)) < '$endYear'")
                  ->orWhere(function($subQ) use ($endYear, $endMonth) {
                      $subQ->whereRaw("CONCAT('20', SUBSTRING(periode, 4, 2)) = '$endYear'")
                           ->whereRaw("SUBSTRING(periode, 1, 2) <= '$endMonth'");
                  });
            });
        }

        // Filter by obat
        if ($request->has('obat') && $request->obat != '') {
            $query->whereHas('obat', function ($q) use ($request) {
                $q->where('nama_obat', 'like', '%' . $request->obat . '%');
            });
        }

        // Filter by stok status
        if ($request->has('stok_status') && $request->stok_status != '') {
            switch ($request->stok_status) {
                case 'habis':
                    $query->where('stok_akhir', '<=', 0);
                    break;
                case 'rendah':
                    $query->where('stok_akhir', '>', 0)->where('stok_akhir', '<=', 10);
                    break;
                case 'tersedia':
                    $query->where('stok_akhir', '>', 10);
                    break;
            }
        }

        // Sorting
        $sortField = $request->get('sort', 'id_stok_obat');
        $sortDirection = $request->get('direction', 'desc');

        if (in_array($sortField, ['periode', 'nama_obat', 'stok_awal', 'stok_pakai', 'stok_akhir', 'stok_masuk'])) {
            if ($sortField === 'nama_obat') {
                $query->join('obat', 'stok_obat.id_obat', '=', 'obat.id_obat')
                      ->orderBy('obat.nama_obat', $sortDirection)
                      ->select('stok_obat.*');
            } elseif ($sortField === 'periode') {
                // Custom sorting for MM-YY format to sort by year then month
                if ($sortDirection === 'asc') {
                    $query->orderByRaw("SUBSTRING(periode, 4, 2) ASC, SUBSTRING(periode, 1, 2) ASC");
                } else {
                    $query->orderByRaw("SUBSTRING(periode, 4, 2) DESC, SUBSTRING(periode, 1, 2) DESC");
                }
            } else {
                $query->orderBy($sortField, $sortDirection);
            }
        } else {
            // Custom sorting for MM-YY format to sort by year then month (newest first)
            $query->orderByRaw("SUBSTRING(periode, 4, 2) DESC, SUBSTRING(periode, 1, 2) DESC")
                  ->join('obat', 'stok_obat.id_obat', '=', 'obat.id_obat')
                  ->orderBy('obat.nama_obat', 'asc')
                  ->select('stok_obat.*');
        }

        // Pagination
        $perPage = $request->get('per_page', 50);
        $perPage = in_array($perPage, [50, 100, 150, 200]) ? $perPage : 50;

        $stokObats = $query->paginate($perPage);

        // Get available periodes for filter
        $availablePeriodes = StokObat::getAvailablePeriodes();

        return view('stok-obat.index', compact(
            'stokObats',
            'availablePeriodes',
            'request'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $obats = Obat::with('satuanObat')->orderBy('nama_obat')->get();
        $availablePeriodes = StokObat::getAvailablePeriodes();
        
        return view('stok-obat.create', compact('obats', 'availablePeriodes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_obat' => 'required|exists:obat,id_obat',
            'periode' => 'required|string|regex:/^\d{2}-\d{2}$/',
            'stok_masuk' => 'required|integer|min:0',
            'keterangan' => 'nullable|string|max:255',
        ], [
            'id_obat.required' => 'Obat harus dipilih',
            'id_obat.exists' => 'Obat tidak ditemukan',
            'periode.required' => 'Periode wajib diisi',
            'periode.regex' => 'Format periode harus MM-YY (contoh: 10-25)',
            'stok_masuk.required' => 'Stok masuk wajib diisi',
            'stok_masuk.integer' => 'Stok masuk harus berupa angka',
            'stok_masuk.min' => 'Stok masuk tidak boleh negatif',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $idObat = $request->id_obat;
            $periode = $request->periode;
            $stokMasuk = $request->stok_masuk;

            // Cek apakah ini stok awal pertama kali
            $isInitialStok = !StokObat::hasInitialStok($idObat);

            if ($isInitialStok) {
                // Buat stok awal pertama kali
                $stokObat = StokObat::buatStokAwalPertama($idObat, $periode, $stokMasuk);
                $message = 'Stok awal pertama berhasil ditambahkan';
            } else {
                // Tambah stok masuk biasa
                $stokObat = StokObat::tambahStokMasuk($idObat, $periode, $stokMasuk, $request->keterangan);
                $message = 'Stok masuk berhasil ditambahkan';
            }

            // Update stok pakai otomatis
            $stokPakai = StokObat::hitungStokPakaiDariKeluhan($idObat, $periode);
            $stokAkhir = StokObat::hitungStokAkhir($stokObat->stok_awal, $stokPakai, $stokObat->stok_masuk);
            
            $stokObat->update([
                'stok_pakai' => $stokPakai,
                'stok_akhir' => $stokAkhir
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $stokObat
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating stok obat: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal menambah stok obat: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $stokObat = StokObat::with(['obat', 'obat.satuanObat'])->findOrFail($id);
        $obats = Obat::with('satuanObat')->orderBy('nama_obat')->get();
        
        return view('stok-obat.edit', compact('stokObat', 'obats'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'stok_masuk' => 'required|integer|min:0',
            'keterangan' => 'nullable|string|max:255',
        ], [
            'stok_masuk.required' => 'Stok masuk wajib diisi',
            'stok_masuk.integer' => 'Stok masuk harus berupa angka',
            'stok_masuk.min' => 'Stok masuk tidak boleh negatif',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $stokObat = StokObat::findOrFail($id);
            
            // Update stok masuk
            $stokObat->stok_masuk = $request->stok_masuk;
            $stokObat->keterangan = $request->keterangan;
            
            // Update stok pakai otomatis
            $stokPakai = StokObat::hitungStokPakaiDariKeluhan($stokObat->id_obat, $stokObat->periode);
            $stokAkhir = StokObat::hitungStokAkhir($stokObat->stok_awal, $stokPakai, $stokObat->stok_masuk);
            
            $stokObat->stok_pakai = $stokPakai;
            $stokObat->stok_akhir = $stokAkhir;
            $stokObat->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Stok obat berhasil diperbarui',
                'data' => $stokObat
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating stok obat: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui stok obat: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $stokObat = StokObat::findOrFail($id);
            
            // Cek apakah ini stok awal pertama
            if ($stokObat->is_initial_stok) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok awal pertama tidak dapat dihapus'
                ], 400);
            }
            
            $stokObat->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data stok obat berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting stok obat: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data stok obat: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk delete stok obat
     */
    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'Tidak ada data yang dipilih'], 400);
        }

        try {
            // Cek apakah ada stok awal pertama yang akan dihapus
            $hasInitialStok = StokObat::whereIn('id_stok_obat', $ids)
                                   ->where('is_initial_stok', true)
                                   ->exists();

            if ($hasInitialStok) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menghapus stok awal pertama'
                ], 400);
            }

            StokObat::whereIn('id_stok_obat', $ids)->delete();

            return response()->json([
                'success' => true, 
                'message' => count($ids) . ' data stok obat berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            Log::error('Error bulk deleting stok obat: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data stok obat: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update stok pakai otomatis untuk periode tertentu
     */
    public function updateStokPakai(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'periode' => 'required|string|regex:/^\d{2}-\d{2}$/',
        ], [
            'periode.required' => 'Periode wajib diisi',
            'periode.regex' => 'Format periode harus MM-YY (contoh: 10-25)',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $updatedCount = StokObat::updateStokPakaiPerPeriode($request->periode);

            return back()->with('success', "Stok pakai berhasil diperbarui untuk $updatedCount obat pada periode {$request->periode}");

        } catch (\Exception $e) {
            Log::error('Error updating stok pakai: ' . $e->getMessage());

            return back()->with('error', 'Gagal memperbarui stok pakai: ' . $e->getMessage());
        }
    }

    /**
     * Generate stok awal untuk periode baru
     */
    public function generateStokAwal(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'periode' => 'required|string|regex:/^\d{2}-\d{2}$/',
        ], [
            'periode.required' => 'Periode wajib diisi',
            'periode.regex' => 'Format periode harus MM-YY (contoh: 10-25)',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $periode = $request->periode;
            $obats = Obat::all();
            $createdCount = 0;

            foreach ($obats as $obat) {
                // Cek apakah sudah ada stok untuk periode ini
                $existingStok = StokObat::where('id_obat', $obat->id_obat)
                                       ->where('periode', $periode)
                                       ->first();

                if (!$existingStok) {
                    // Cek apakah ini stok awal pertama kali untuk obat ini
                    $isFirstStok = !StokObat::hasInitialStok($obat->id_obat);
                    
                    // Buat stok awal dari stok akhir bulan sebelumnya
                    $stokAwal = StokObat::getStokAkhirBulanSebelumnya($obat->id_obat, $periode);
                    
                    StokObat::create([
                        'id_obat' => $obat->id_obat,
                        'periode' => $periode,
                        'stok_awal' => $stokAwal,
                        'stok_masuk' => 0,
                        'stok_pakai' => 0,
                        'stok_akhir' => $stokAwal,
                        'is_initial_stok' => $isFirstStok, // Jika ini stok pertama, tandai sebagai initial stok
                        'keterangan' => $isFirstStok 
                            ? 'Stok awal pertama kali' 
                            : 'Stok awal periode ' . $periode,
                    ]);

                    $createdCount++;
                }
            }

            return back()->with('success', "Stok awal berhasil dibuat untuk $createdCount obat pada periode $periode");

        } catch (\Exception $e) {
            Log::error('Error generating stok awal: ' . $e->getMessage());

            return back()->with('error', 'Gagal membuat stok awal: ' . $e->getMessage());
        }
    }

    /**
     * Preview stok data untuk AJAX
     */
    public function previewStok(Request $request)
    {
        try {
            $idObat = $request->query('id_obat');
            $periode = $request->query('periode');

            if (!$idObat || !$periode) {
                return response()->json([
                    'success' => false,
                    'message' => 'ID obat dan periode wajib diisi'
                ], 400);
            }

            // Validasi format periode
            if (!preg_match('/^\d{2}-\d{2}$/', $periode)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Format periode harus MM-YY (contoh: 10-25)'
                ], 400);
            }

            // Ambil stok awal dari bulan sebelumnya
            $stokAwal = StokObat::getStokAkhirBulanSebelumnya($idObat, $periode);

            // Hitung stok pakai dari data keluhan
            $stokPakai = StokObat::hitungStokPakaiDariKeluhan($idObat, $periode);

            return response()->json([
                'success' => true,
                'stok_awal' => $stokAwal,
                'stok_pakai' => $stokPakai
            ]);

        } catch (\Exception $e) {
            Log::error('Error previewing stok: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat preview stok: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export data stok obat to Excel
     */
    public function export(Request $request)
    {
        // TODO: Implement export functionality
        return back()->with('info', 'Fitur export akan segera tersedia');
    }

    /**
     * Download template untuk import stok obat
     */
    public function downloadTemplate()
    {
        // TODO: Implement template download functionality
        return back()->with('info', 'Fitur template akan segera tersedia');
    }

    /**
     * Import data stok obat dari Excel
     */
    public function import(Request $request)
    {
        // TODO: Implement import functionality
        return back()->with('info', 'Fitur import akan segera tersedia');
    }
}