<?php

namespace App\Http\Controllers;

use App\Models\Keluarga;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class FingerprintController extends Controller
{
    /**
     * Display fingerprint management page
     */
    public function index(Request $request)
    {
        $query = Keluarga::whereNotNull('fingerprint_template')
            ->with(['karyawan', 'hubungan']);

        // Handle sorting
        $sort = $request->get('sort', 'fingerprint_enrolled_at');
        $direction = $request->get('direction', 'desc');

        switch ($sort) {
            case 'nama_keluarga':
                $query->orderBy('nama_keluarga', $direction);
                break;
            case 'karyawan.nama_karyawan':
                $query->orderByHas('karyawan', function ($q) use ($direction) {
                    $q->orderBy('nama_karyawan', $direction);
                });
                break;
            case 'fingerprint_enrolled_at':
            default:
                $query->orderBy('fingerprint_enrolled_at', $direction);
                break;
        }

        $keluargas = $query->get();

        return view('fingerprint.index', compact('keluargas'));
    }

    /**
     * Get all family members for fingerprint enrollment
     */
    public function getFamilyMembers(): JsonResponse
    {
        $keluargas = Keluarga::with(['karyawan', 'hubungan'])
            ->orderBy('nama_keluarga')
            ->get();

        return response()->json($keluargas);
    }

    /**
     * Search family members by name or NIK
     */
    public function searchFamilyMembers(Request $request): JsonResponse
    {
        $search = $request->get('search', '');

        $keluargas = Keluarga::with(['karyawan', 'hubungan'])
            ->where(function($query) use ($search) {
                $query->where('nama_keluarga', 'like', '%' . $search . '%')
                      ->orWhereHas('karyawan', function($q) use ($search) {
                          $q->where('nik_karyawan', 'like', '%' . $search . '%');
                      });
            })
            ->orderBy('nama_keluarga')
            ->limit(20)
            ->get();

        return response()->json($keluargas);
    }

    /**
     * Save fingerprint template for family member
     */
    public function saveFingerprint(Request $request): JsonResponse
    {
        $request->validate([
            'id_keluarga' => 'required|exists:keluarga,id_keluarga',
            'fingerprint_template' => 'required|string'
        ]);

        $keluarga = Keluarga::find($request->id_keluarga);
        $keluarga->fingerprint_template = $request->fingerprint_template;
        $keluarga->fingerprint_enrolled_at = now();
        $keluarga->save();

        return response()->json([
            'success' => true,
            'message' => 'Fingerprint berhasil disimpan untuk ' . $keluarga->nama_keluarga,
            'data' => $keluarga->load('karyawan')
        ]);
    }

    /**
     * Get all fingerprint templates for verification
     */
    public function getFingerprintTemplates(): JsonResponse
    {
        $templates = Keluarga::whereNotNull('fingerprint_template')
            ->with(['karyawan', 'hubungan'])
            ->get(['id_keluarga', 'nama_keluarga', 'fingerprint_template', 'id_karyawan', 'kode_hubungan']);

        return response()->json($templates);
    }

    /**
     * Delete fingerprint template
     */
    public function deleteFingerprint(Request $request): JsonResponse
    {
        $request->validate([
            'id_keluarga' => 'required|exists:keluarga,id_keluarga'
        ]);

        $keluarga = Keluarga::find($request->id_keluarga);
        $keluarga->fingerprint_template = null;
        $keluarga->fingerprint_enrolled_at = null;
        $keluarga->save();

        return response()->json([
            'success' => true,
            'message' => 'Fingerprint berhasil dihapus untuk ' . $keluarga->nama_keluarga
        ]);
    }
}
