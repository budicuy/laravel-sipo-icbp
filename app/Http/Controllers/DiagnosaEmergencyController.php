<?php

namespace App\Http\Controllers;

use App\Models\DiagnosaEmergency;
use App\Models\Obat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DiagnosaEmergencyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = DiagnosaEmergency::with('obats');
        
        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $query->where('nama_diagnosa_emergency', 'like', '%' . $request->search . '%')
                  ->orWhere('deskripsi', 'like', '%' . $request->search . '%');
        }
        
        // Sort functionality
        if ($request->has('sort') && !empty($request->sort)) {
            $direction = $request->has('direction') && $request->direction === 'desc' ? 'desc' : 'asc';
            $query->orderBy($request->sort, $direction);
        } else {
            $query->orderBy('id_diagnosa_emergency', 'desc');
        }
        
        $diagnosaEmergencies = $query->paginate($request->get('per_page', 50));
        
        return view('diagnosa-emergency.index', compact('diagnosaEmergencies'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $obats = Obat::orderBy('nama_obat')->get();
        return view('diagnosa-emergency.create', compact('obats'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_diagnosa_emergency' => 'required|string|max:255|unique:diagnosa_emergency,nama_diagnosa_emergency',
            'deskripsi' => 'nullable|string',
            'obat_rekomendasi' => 'nullable|array',
            'obat_rekomendasi.*' => 'exists:obat,id_obat',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $diagnosaEmergency = DiagnosaEmergency::create([
                'nama_diagnosa_emergency' => $request->nama_diagnosa_emergency,
                'deskripsi' => $request->deskripsi,
            ]);

            // Attach obat rekomendasi if provided
            if ($request->has('obat_rekomendasi') && !empty($request->obat_rekomendasi)) {
                $filteredObats = array_filter($request->obat_rekomendasi, function($value) {
                    return !empty($value);
                });
                if (!empty($filteredObats)) {
                    $diagnosaEmergency->obats()->attach($filteredObats);
                }
            }

            DB::commit();
            return redirect()->route('diagnosa-emergency.index')
                ->with('success', 'Diagnosa emergency berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menambahkan diagnosa emergency: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(DiagnosaEmergency $diagnosaEmergency)
    {
        $diagnosaEmergency->load(['obats', 'keluhans' => function($query) {
            $query->with(['user', 'rekamMedisEmergency.externalEmployee', 'rekamMedis.keluarga.karyawan'])
                  ->orderBy('created_at', 'desc');
        }]);
        
        return view('diagnosa-emergency.show', compact('diagnosaEmergency'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DiagnosaEmergency $diagnosaEmergency)
    {
        $obats = Obat::orderBy('nama_obat')->get();
        $selectedObats = $diagnosaEmergency->obats->pluck('id_obat')->toArray();
        
        return view('diagnosa-emergency.edit', compact('diagnosaEmergency', 'obats', 'selectedObats'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DiagnosaEmergency $diagnosaEmergency)
    {
        $validator = Validator::make($request->all(), [
            'nama_diagnosa_emergency' => 'required|string|max:255|unique:diagnosa_emergency,nama_diagnosa_emergency,' . $diagnosaEmergency->id_diagnosa_emergency . ',id_diagnosa_emergency',
            'deskripsi' => 'nullable|string',
            'obat_rekomendasi' => 'nullable|array',
            'obat_rekomendasi.*' => 'exists:obat,id_obat',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $diagnosaEmergency->update([
                'nama_diagnosa_emergency' => $request->nama_diagnosa_emergency,
                'deskripsi' => $request->deskripsi,
            ]);

            // Sync obat rekomendasi
            if ($request->has('obat_rekomendasi')) {
                $filteredObats = array_filter($request->obat_rekomendasi, function($value) {
                    return !empty($value);
                });
                $diagnosaEmergency->obats()->sync($filteredObats);
            } else {
                $diagnosaEmergency->obats()->detach();
            }

            DB::commit();
            return redirect()->route('diagnosa-emergency.index')
                ->with('success', 'Diagnosa emergency berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memperbarui diagnosa emergency: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DiagnosaEmergency $diagnosaEmergency)
    {
        try {
            // Check if the diagnosa is being used in any keluhan
            $keluhanCount = $diagnosaEmergency->keluhans()->count();
            if ($keluhanCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Diagnosa emergency tidak dapat dihapus karena sudah digunakan dalam {$keluhanCount} rekam medis."
                ], 400);
            }

            $diagnosaEmergency->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Diagnosa emergency berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus diagnosa emergency: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk delete diagnosa emergency
     */
    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'exists:diagnosa_emergency,id_diagnosa_emergency',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'ID yang dipilih tidak valid'
            ], 400);
        }

        DB::beginTransaction();
        try {
            $ids = $request->ids;
            $deletedCount = 0;
            $cannotDeleteCount = 0;
            
            foreach ($ids as $id) {
                $diagnosaEmergency = DiagnosaEmergency::find($id);
                if ($diagnosaEmergency) {
                    $keluhanCount = $diagnosaEmergency->keluhans()->count();
                    if ($keluhanCount > 0) {
                        $cannotDeleteCount++;
                    } else {
                        $diagnosaEmergency->delete();
                        $deletedCount++;
                    }
                }
            }

            DB::commit();
            
            $message = "Berhasil menghapus {$deletedCount} diagnosa emergency";
            if ($cannotDeleteCount > 0) {
                $message .= ". {$cannotDeleteCount} diagnosa tidak dapat dihapus karena sudah digunakan dalam rekam medis.";
            }
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'deleted_count' => $deletedCount,
                'cannot_delete_count' => $cannotDeleteCount
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus diagnosa emergency: ' . $e->getMessage()
            ], 500);
        }
    }
}