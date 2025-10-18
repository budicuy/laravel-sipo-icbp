<?php

namespace App\Http\Controllers;

use App\Models\HargaObatPerBulan;
use App\Models\Obat;
use App\Models\StokObat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class HargaObatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = HargaObatPerBulan::with([
            'obat:id_obat,nama_obat,keterangan,id_jenis_obat,id_satuan',
            'obat.jenisObat:id_jenis_obat,nama_jenis_obat',
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

        // Filter by jenis obat
        if ($request->has('jenis_obat') && $request->jenis_obat != '') {
            $query->whereHas('obat', function ($q) use ($request) {
                $q->where('id_jenis_obat', $request->jenis_obat);
            });
        }

        // Sorting
        $sortField = $request->get('sort', 'id_harga_obat');
        $sortDirection = $request->get('direction', 'desc');

        if (in_array($sortField, ['periode', 'nama_obat', 'jenis_obat', 'jumlah_per_kemasan', 'harga_per_kemasan', 'harga_per_satuan'])) {
            if ($sortField === 'nama_obat') {
                $query->join('obat', 'harga_obat_per_bulan.id_obat', '=', 'obat.id_obat')
                      ->orderBy('obat.nama_obat', $sortDirection)
                      ->select('harga_obat_per_bulan.*');
            } elseif ($sortField === 'jenis_obat') {
                $query->join('obat', 'harga_obat_per_bulan.id_obat', '=', 'obat.id_obat')
                      ->join('jenis_obat', 'obat.id_jenis_obat', '=', 'jenis_obat.id_jenis_obat')
                      ->orderBy('jenis_obat.nama_jenis_obat', $sortDirection)
                      ->select('harga_obat_per_bulan.*');
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
                  ->join('obat', 'harga_obat_per_bulan.id_obat', '=', 'obat.id_obat')
                  ->orderBy('obat.nama_obat', 'asc')
                  ->select('harga_obat_per_bulan.*');
        }

        // Pagination
        $perPage = $request->get('per_page', 50);
        $perPage = in_array($perPage, [50, 100, 150, 200]) ? $perPage : 50;

        $hargaObats = $query->paginate($perPage);

        // Get available periodes for filter
        $availablePeriodes = HargaObatPerBulan::getAvailablePeriodes();

        // Get reference data
        $jenisObats = Cache::remember('jenis_obats_all', 60, function () {
            return \App\Models\JenisObat::get();
        });

        return view('harga-obat.index', compact(
            'hargaObats',
            'availablePeriodes',
            'jenisObats',
            'request'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $obats = Obat::with(['jenisObat', 'satuanObat'])->get();
        $jenisObats = Cache::remember('jenis_obats_all', 60, function () {
            return \App\Models\JenisObat::get();
        });
        $availablePeriodes = StokObat::getAvailablePeriodes();

        return view('harga-obat.create', compact('obats', 'jenisObats', 'availablePeriodes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_obat' => 'required|exists:obat,id_obat',
            'periode' => [
                'required',
                'string',
                'regex:/^\d{2}-\d{2}$/',
                function ($attribute, $value, $fail) {
                    if (preg_match('/^(\d{2})-(\d{2})$/', $value, $matches)) {
                        $month = (int) $matches[1];
                        $year = (int) $matches[2];

                        if ($month < 1 || $month > 12) {
                            $fail('Bulan harus antara 01-12');
                        }

                        if ($year < 1 || $year > 99) {
                            $fail('Tahun harus antara 01-99');
                        }
                    }
                }
            ],
            'jumlah_per_kemasan' => 'required|integer|min:1',
            'harga_per_satuan' => 'required|numeric|min:0',
            'harga_per_kemasan' => 'required|numeric|min:0',
        ], [
            'id_obat.required' => 'Obat wajib dipilih',
            'periode.required' => 'Periode wajib diisi',
            'periode.regex' => 'Format periode harus MM-YY (contoh: 10-25)',
            'periode.string' => 'Periode harus berupa string',
            'jumlah_per_kemasan.required' => 'Jumlah per kemasan wajib diisi',
            'harga_per_satuan.required' => 'Harga per satuan wajib diisi',
            'harga_per_kemasan.required' => 'Harga per kemasan wajib diisi',
        ]);

        // Check if harga obat already exists for this obat and periode
        $existingHarga = HargaObatPerBulan::where('id_obat', $validated['id_obat'])
                                       ->where('periode', $validated['periode'])
                                       ->first();

        if ($existingHarga) {
            // Convert periode (MM-YY) to readable format
            $bulanNama = [
                '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
                '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
                '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
            ];

            if (preg_match('/^(\d{2})-(\d{2})$/', $validated['periode'], $matches)) {
                $bulan = $bulanNama[$matches[1]] ?? $matches[1];
                $tahun = '20' . $matches[2]; // Convert YY to 20YY
                $periodeFormat = "{$bulan} {$tahun}";
            } else {
                $periodeFormat = $validated['periode'];
            }

            return redirect()->route('harga-obat.edit', $existingHarga->id_harga_obat)
                           ->with('error', "Data harga obat untuk bulan {$periodeFormat} sudah ada")
                           ->withInput();
        }

        // Start transaction
        DB::beginTransaction();

        try {
            // Create harga obat
            $hargaObat = HargaObatPerBulan::create($validated);

            DB::commit();

            return redirect()->route('harga-obat.index')->with('success', 'Data harga obat berhasil ditambahkan');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating harga obat: ' . $e->getMessage());
            return back()->with('error', 'Gagal menambahkan data harga obat: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $hargaObat = HargaObatPerBulan::findOrFail($id);
        $obats = Obat::with(['jenisObat', 'satuanObat'])->get();
        $jenisObats = Cache::remember('jenis_obats_all', 60, function () {
            return \App\Models\JenisObat::get();
        });
        $availablePeriodes = StokObat::getAvailablePeriodes();

        return view('harga-obat.edit', compact('hargaObat', 'obats', 'jenisObats', 'availablePeriodes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $hargaObat = HargaObatPerBulan::findOrFail($id);

        $validated = $request->validate([
            'id_obat' => 'required|exists:obat,id_obat',
            'periode' => [
                'required',
                'string',
                'regex:/^\d{2}-\d{2}$/',
                function ($attribute, $value, $fail) {
                    if (preg_match('/^(\d{2})-(\d{2})$/', $value, $matches)) {
                        $month = (int) $matches[1];
                        $year = (int) $matches[2];

                        if ($month < 1 || $month > 12) {
                            $fail('Bulan harus antara 01-12');
                        }

                        if ($year < 1 || $year > 99) {
                            $fail('Tahun harus antara 01-99');
                        }
                    }
                }
            ],
            'jumlah_per_kemasan' => 'required|integer|min:1',
            'harga_per_satuan' => 'required|numeric|min:0',
            'harga_per_kemasan' => 'required|numeric|min:0',
        ], [
            'id_obat.required' => 'Obat wajib dipilih',
            'periode.required' => 'Periode wajib diisi',
            'periode.regex' => 'Format periode harus MM-YY (contoh: 10-25)',
            'periode.string' => 'Periode harus berupa string',
            'jumlah_per_kemasan.required' => 'Jumlah per kemasan wajib diisi',
            'harga_per_satuan.required' => 'Harga per satuan wajib diisi',
            'harga_per_kemasan.required' => 'Harga per kemasan wajib diisi',
        ]);

        // Check if harga obat already exists for this obat and periode (excluding current record)
        $existingHarga = HargaObatPerBulan::where('id_obat', $validated['id_obat'])
                                       ->where('periode', $validated['periode'])
                                       ->where('id_harga_obat', '!=', $id)
                                       ->first();

        if ($existingHarga) {
            // Convert periode (MM-YY) to readable format
            $bulanNama = [
                '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
                '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
                '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
            ];

            if (preg_match('/^(\d{2})-(\d{2})$/', $validated['periode'], $matches)) {
                $bulan = $bulanNama[$matches[1]] ?? $matches[1];
                $tahun = '20' . $matches[2]; // Convert YY to 20YY
                $periodeFormat = "{$bulan} {$tahun}";
            } else {
                $periodeFormat = $validated['periode'];
            }

            return redirect()->route('harga-obat.edit', $existingHarga->id_harga_obat)
                           ->with('error', "Data harga obat untuk bulan {$periodeFormat} sudah ada")
                           ->withInput();
        }

        $hargaObat->update($validated);

        return redirect()->route('harga-obat.index')->with('success', 'Data harga obat berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $hargaObat = HargaObatPerBulan::findOrFail($id);
        $hargaObat->delete();

        return response()->json(['success' => true, 'message' => 'Data harga obat berhasil dihapus']);
    }

    /**
     * Bulk delete harga obat
     */
    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'Tidak ada data yang dipilih'], 400);
        }

        HargaObatPerBulan::whereIn('id_harga_obat', $ids)->delete();

        return response()->json(['success' => true, 'message' => count($ids) . ' data harga obat berhasil dihapus']);
    }

    /**
     * Generate harga obat for all obat in a specific periode
     */
    public function generateForPeriode(Request $request)
    {
        // Handle AJAX request
        if ($request->ajax() || $request->wantsJson()) {
            $request->validate([
                'periode' => [
                    'required',
                    'string',
                    'regex:/^\d{2}-\d{2}$/',
                    function ($attribute, $value, $fail) {
                        if (preg_match('/^(\d{2})-(\d{2})$/', $value, $matches)) {
                            $month = (int) $matches[1];
                            $year = (int) $matches[2];

                            if ($month < 1 || $month > 12) {
                                $fail('Bulan harus antara 01-12');
                            }

                            if ($year < 1 || $year > 99) {
                                $fail('Tahun harus antara 01-99');
                            }
                        }
                    }
                ],
            ], [
                'periode.required' => 'Periode wajib diisi',
                'periode.regex' => 'Format periode harus MM-YY (contoh: 10-25)',
                'periode.string' => 'Periode harus berupa string',
            ]);

            $periode = $request->periode;

            // Check if any data already exists for this periode
            $existingDataCount = HargaObatPerBulan::where('periode', $periode)->count();
            if ($existingDataCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data untuk periode ini sudah ada'
                ], 400);
            }

            $createdCount = 0;
            $errors = [];

            try {
                // Get all obat
                $obats = Obat::all();

                // Start transaction
                DB::beginTransaction();

                foreach ($obats as $obat) {
                    try {
                        // Get harga from previous periode
                        $hargaPrevious = HargaObatPerBulan::getHargaObat($obat->id_obat, $periode);

                        if ($hargaPrevious) {
                            // Copy harga from previous periode
                            HargaObatPerBulan::create([
                                'id_obat' => $obat->id_obat,
                                'periode' => $periode,
                                'jumlah_per_kemasan' => $hargaPrevious->jumlah_per_kemasan,
                                'harga_per_satuan' => $hargaPrevious->harga_per_satuan,
                                'harga_per_kemasan' => $hargaPrevious->harga_per_kemasan,
                            ]);
                            $createdCount++;
                        } else {
                            // Create with default values
                            HargaObatPerBulan::create([
                                'id_obat' => $obat->id_obat,
                                'periode' => $periode,
                                'jumlah_per_kemasan' => 1,
                                'harga_per_satuan' => 0,
                                'harga_per_kemasan' => 0,
                            ]);
                            $createdCount++;
                        }

                    } catch (\Exception $e) {
                        $errors[] = "Gagal memproses obat {$obat->nama_obat}: " . $e->getMessage();
                        Log::error('Error generating harga obat for periode', [
                            'id_obat' => $obat->id_obat,
                            'nama_obat' => $obat->nama_obat,
                            'periode' => $periode,
                            'error' => $e->getMessage()
                        ]);
                    }
                }

                DB::commit();

                $message = "Generate harga obat untuk periode $periode selesai: $createdCount data baru dibuat";
                if (!empty($errors)) {
                    $message .= ". Error: " . implode(', ', array_slice($errors, 0, 3));
                    if (count($errors) > 3) {
                        $message .= ' ... dan ' . (count($errors) - 3) . ' error lainnya';
                    }
                }

                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'data' => [
                        'created_count' => $createdCount,
                        'errors' => $errors
                    ]
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error generating harga obat for periode: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal generate harga obat: ' . $e->getMessage()
                ], 500);
            }
        }

        // Handle regular form submission
        $request->validate([
            'periode' => [
                'required',
                'string',
                'regex:/^\d{2}-\d{2}$/',
                function ($attribute, $value, $fail) {
                    if (preg_match('/^(\d{2})-(\d{2})$/', $value, $matches)) {
                        $month = (int) $matches[1];
                        $year = (int) $matches[2];

                        if ($month < 1 || $month > 12) {
                            $fail('Bulan harus antara 01-12');
                        }

                        if ($year < 1 || $year > 99) {
                            $fail('Tahun harus antara 01-99');
                        }
                    }
                }
            ],
        ], [
            'periode.required' => 'Periode wajib diisi',
            'periode.regex' => 'Format periode harus MM-YY (contoh: 10-25)',
            'periode.string' => 'Periode harus berupa string',
        ]);

        $periode = $request->periode;

        // Check if any data already exists for this periode
        $existingDataCount = HargaObatPerBulan::where('periode', $periode)->count();
        if ($existingDataCount > 0) {
            return back()->with('error', 'Data untuk periode ini sudah ada')->withInput();
        }

        $createdCount = 0;
        $errors = [];

        try {
            // Get all obat
            $obats = Obat::all();

            // Start transaction
            DB::beginTransaction();

            foreach ($obats as $obat) {
                try {
                    // Get harga from previous periode
                    $hargaPrevious = HargaObatPerBulan::getHargaObat($obat->id_obat, $periode);

                    if ($hargaPrevious) {
                        // Copy harga from previous periode
                        HargaObatPerBulan::create([
                            'id_obat' => $obat->id_obat,
                            'periode' => $periode,
                            'jumlah_per_kemasan' => $hargaPrevious->jumlah_per_kemasan,
                            'harga_per_satuan' => $hargaPrevious->harga_per_satuan,
                            'harga_per_kemasan' => $hargaPrevious->harga_per_kemasan,
                        ]);
                        $createdCount++;
                    } else {
                        // Create with default values
                        HargaObatPerBulan::create([
                            'id_obat' => $obat->id_obat,
                            'periode' => $periode,
                            'jumlah_per_kemasan' => 1,
                            'harga_per_satuan' => 0,
                            'harga_per_kemasan' => 0,
                        ]);
                        $createdCount++;
                    }

                } catch (\Exception $e) {
                    $errors[] = "Gagal memproses obat {$obat->nama_obat}: " . $e->getMessage();
                    Log::error('Error generating harga obat for periode', [
                        'id_obat' => $obat->id_obat,
                        'nama_obat' => $obat->nama_obat,
                        'periode' => $periode,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            DB::commit();

            $message = "Generate harga obat untuk periode $periode selesai: $createdCount data baru dibuat";
            if (!empty($errors)) {
                $message .= ". Error: " . implode(', ', array_slice($errors, 0, 3));
                if (count($errors) > 3) {
                    $message .= ' ... dan ' . (count($errors) - 3) . ' error lainnya';
                }
            }

            return back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error generating harga obat for periode: ' . $e->getMessage());
            return back()->with('error', 'Gagal generate harga obat: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Export data harga obat to Excel
     */
    public function export(Request $request)
    {
        // Build query with same filters as index
        $query = HargaObatPerBulan::with([
            'obat:id_obat,nama_obat,keterangan,id_jenis_obat,id_satuan',
            'obat.jenisObat:id_jenis_obat,nama_jenis_obat',
            'obat.satuanObat:id_satuan,nama_satuan'
        ]);

        // Apply same filters as index
        if ($request->has('periode') && $request->periode != '') {
            $query->where('periode', $request->periode);
        }
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
        if ($request->has('obat') && $request->obat != '') {
            $query->whereHas('obat', function ($q) use ($request) {
                $q->where('nama_obat', 'like', '%' . $request->obat . '%');
            });
        }
        if ($request->has('jenis_obat') && $request->jenis_obat != '') {
            $query->whereHas('obat', function ($q) use ($request) {
                $q->where('id_jenis_obat', $request->jenis_obat);
            });
        }

        // Apply sorting
        $sortField = $request->get('sort', 'periode');
        $sortDirection = $request->get('direction', 'desc');

        if (in_array($sortField, ['periode', 'nama_obat', 'jenis_obat', 'jumlah_per_kemasan', 'harga_per_kemasan', 'harga_per_satuan'])) {
            if ($sortField === 'nama_obat') {
                $query->join('obat', 'harga_obat_per_bulan.id_obat', '=', 'obat.id_obat')
                      ->orderBy('obat.nama_obat', $sortDirection)
                      ->select('harga_obat_per_bulan.*');
            } elseif ($sortField === 'jenis_obat') {
                $query->join('obat', 'harga_obat_per_bulan.id_obat', '=', 'obat.id_obat')
                      ->join('jenis_obat', 'obat.id_jenis_obat', '=', 'jenis_obat.id_jenis_obat')
                      ->orderBy('jenis_obat.nama_jenis_obat', $sortDirection)
                      ->select('harga_obat_per_bulan.*');
            } elseif ($sortField === 'periode') {
                if ($sortDirection === 'asc') {
                    $query->orderByRaw("SUBSTRING(periode, 4, 2) ASC, SUBSTRING(periode, 1, 2) ASC");
                } else {
                    $query->orderByRaw("SUBSTRING(periode, 4, 2) DESC, SUBSTRING(periode, 1, 2) DESC");
                }
            } else {
                $query->orderBy($sortField, $sortDirection);
            }
        } else {
            $query->orderByRaw("SUBSTRING(periode, 4, 2) DESC, SUBSTRING(periode, 1, 2) DESC")
                  ->join('obat', 'harga_obat_per_bulan.id_obat', '=', 'obat.id_obat')
                  ->orderBy('obat.nama_obat', 'asc')
                  ->select('harga_obat_per_bulan.*');
        }

        // Get data
        $hargaObats = $query->get();

        // Create spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Harga Obat');

        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator('SIPO ICBP')
            ->setTitle('Data Harga Obat')
            ->setSubject('Data Harga Obat')
            ->setDescription('Data harga obat perbulan');

        // Header columns
        $headers = [
            'No', 'Nama Obat', 'Jenis Obat', 'Satuan', 'Periode',
            'Jumlah per Kemasan', 'Harga per Kemasan', 'Harga per Satuan', 'Tanggal Update'
        ];

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

        $lastColumn = chr(ord('A') + count($headers) - 1);
        $sheet->getStyle('A1:' . $lastColumn . '1')->applyFromArray($headerStyle);

        // Fill data
        $row = 2;
        $no = 1;

        foreach ($hargaObats as $hargaObat) {
            $sheet->setCellValue('A' . $row, $no);
            $sheet->setCellValue('B' . $row, $hargaObat->obat->nama_obat);
            $sheet->setCellValue('C' . $row, $hargaObat->obat->jenisObat->nama_jenis_obat ?? '-');
            $sheet->setCellValue('D' . $row, $hargaObat->obat->satuanObat->nama_satuan ?? '-');
            $sheet->setCellValue('E' . $row, $hargaObat->periode);
            $sheet->setCellValue('F' . $row, $hargaObat->jumlah_per_kemasan);
            $sheet->setCellValue('G' . $row, $hargaObat->harga_per_kemasan);
            $sheet->setCellValue('H' . $row, $hargaObat->harga_per_satuan);
            $sheet->setCellValue('I' . $row, $hargaObat->updated_at ? $hargaObat->updated_at->format('d-m-Y') : '-');

            // Style data rows
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

            $sheet->getStyle('A' . $row . ':' . $lastColumn . $row)->applyFromArray($dataStyle);

            $row++;
            $no++;
        }

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(12);
        $sheet->getColumnDimension('F')->setWidth(18);
        $sheet->getColumnDimension('G')->setWidth(18);
        $sheet->getColumnDimension('H')->setWidth(18);
        $sheet->getColumnDimension('I')->setWidth(15);

        // Set row heights
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Create Excel file
        $writer = new Xlsx($spreadsheet);
        $filename = 'data_harga_obat_' . date('Y-m-d_H-i-s') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
}
